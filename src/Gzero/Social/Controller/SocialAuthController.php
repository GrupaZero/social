<?php namespace Gzero\Social\Controller;

use Gzero\Repository\SocialRepository;
use Gzero\Core\Controllers\BaseController;
use Gzero\Social\SocialLoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
class SocialAuthController extends BaseController {

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
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function socialLogin($serviceName)
    {
        if (config('services.' . $serviceName)) {
            $this->setDynamicRedirectUrl($serviceName);
            return $this->socialite->driver($serviceName)->redirect();
        }
        return redirect('/');
    }

    /**
     * Function responsible for handle a callback request from the given social service.
     *
     * @param Request $request     Request object
     * @param string  $serviceName string social service name
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function socialCallback(Request $request, $serviceName)
    {
        try {
            $this->setDynamicRedirectUrl($serviceName);
            $user = $this->socialite->driver($serviceName)->user();
            $this->authService->login($serviceName, $user);
            return redirect(session('url.intended', '/'));
        } catch (\Exception $e) {
            Log::error('Social login failed: ' . $e->getMessage() . ' - ' . print_r($request->all(), true));
            if (session()->has('url.intended')) { // If redirect url exists show translated error to the user
                $reditectUrl = session('url.intended');
                session()->forget('url.intended'); // remove intended url
                // Set flash message
                session()->flash(
                    'messages',
                    [
                        [
                            'code' => 'error',
                            'text' => $e->getMessage()
                        ]
                    ]
                );
                return redirect($reditectUrl);
            } else {
                return app()->abort(500, $e->getMessage());
            }
        }
    }

    /**
     * User account page
     *
     */
    public function connectedServices()
    {
        return view(
            'gzero-social::connectedServices',
            [
                'services'       => config('services'),
                'activeServices' => $this->repo->getUserSocialIds(auth()->user()->id)
            ]
        );
    }

    private function setDynamicRedirectUrl($serviceName)
    {
        config(['services.' . $serviceName . '.redirect' => route('socialCallback', ['service' => $serviceName])]);
    }
}
