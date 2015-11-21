<?php namespace Gzero\Social\Controller;

use Gzero\Repository\SocialRepository;
use Gzero\Social\SocialException;
use Gzero\Social\SocialLoginService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Laravel\Socialite\Contracts\Factory as Socialite;

/**
 * This file is part of the GZERO CMS package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class SocialAuthController
 *
 * @package    Gzero\Social\Controller
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2015, Adrian Skierniewski
 */
class SocialAuthController extends Controller {

    /**
     * @var Socialite
     */
    protected $socialite;

    /**
     * @var SocialLoginService
     */
    protected $authService;

    public function __construct(Socialite $socialite, SocialLoginService $auth, SocialRepository $socialRepo)
    {
        $this->socialite   = $socialite;
        $this->repo        = $socialRepo;
        $this->authService = $auth;
    }

    /**
     * Function responsible for login the user by the given social service.
     *
     * @param $serviceName string social service name
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function socialLogin($serviceName)
    {
        if (config('services.' . $serviceName)) {
            $this->setDynamicRedirectUrl($serviceName);
            return $this->socialite->driver($serviceName)->redirect();
        }
        return Redirect::to('/');
    }

    /**
     * Function responsible for handle a callback request from the given social service.
     *
     * @param $serviceName string social service name
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function socialCallback($serviceName)
    {
        try {
            $this->setDynamicRedirectUrl($serviceName);
            $user = $this->socialite->driver($serviceName)->user();
            $this->authService->login($serviceName, $user);
            return Redirect::to(Session::get('url.intended'));
        } catch (\Exception $e) {
            dd($e);
            Log::error('Social login failed: ' . print_r(Input::all(), true));
            if (Session::has('url.intended')) { // If redirect url exists show translated error to the user
                $reditectUrl = Session::get('url.intended');
                Session::forget('url.intended'); // remove intended url
                // Set flash message
                Session::flash(
                    'messages',
                    [
                        [
                            'code' => 'error',
                            'text' => $e->getMessage()
                        ]
                    ]
                );
                return Redirect::to($reditectUrl);
            } else {
                return App::abort(500, $e->getMessage());
            }
        }
    }

    /**
     * User account page
     *
     */
    public function connectedServices()
    {
        /**@TODO we need proper user menu method */
        return \View::make(
            'gzero-social::connectedServices',
            [
                'menu'           => App::make('user.menu')->getMenu(),
                'services'       => Config::get('gzero-social::services'),
                'activeServices' => $this->repo->getUserSocialIds(Auth::user()->id)
            ]
        );
    }

    /**
     * Function responsible for bootstrap the given social service.
     *
     * @param $serviceName string social service name
     *
     * @return \Gzero\Oauth\Oauth
     */
    protected function makeSocialService($serviceName)
    {
        return $this->socialite->init(
            $serviceName,
            URL::route('socialCallback', [$serviceName])
        );
    }

    private function setDynamicRedirectUrl($serviceName)
    {
        config(['services.' . $serviceName . '.redirect' => route('socialCallback', ['service' => $serviceName])]);
    }
}
