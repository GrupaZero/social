<?php namespace Gzero\Social\Controller;

use Gzero\OAuth\OAuth;
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
     * @var OAuth
     */
    protected $oauth;

    /**
     * @var SocialLoginService
     */
    protected $authService;

    public function __construct(SocialLoginService $auth, SocialRepository $socialRepo)
    {
        $this->oauth       = App::make('oauth');
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
        $service = $this->makeSocialService($serviceName);
        if ($serviceName === 'twitter') { // OAuth1 needs different approach
            $token = $service->requestRequestToken();
            $url   = $service->getAuthorizationUri(['oauth_token' => $token->getRequestToken()]);
        } else {
            if ($service) {
                $url = (string) $service->getAuthorizationUri();
            } else {
                return Redirect::to('/');
            }
        }
        return Redirect::to((string) $url);
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
            $code          = Input::get('code');
            $oauthToken    = Input::get('oauth_token');
            $oauthVerifier = Input::get('oauth_verifier');
            $service       = $this->makeSocialService($serviceName);
            if ($serviceName == 'twitter') { // OAuth1 needs different approach
                if (!empty($oauthToken) and !empty($oauthVerifier)) {
                    $token = $service->getStorage()->retrieveAccessToken('Twitter');
                    $service->requestAccessToken( // This was a callback request from twitter, get the token
                        $oauthToken,
                        $oauthVerifier,
                        $token->getRequestTokenSecret()
                    );
                }
                $result = (array) json_decode($service->request('account/verify_credentials.json'));
            } elseif (!empty($code)) { // OAuth2
                $service->requestAccessToken($code);
                switch ($serviceName) {
                    case 'facebook':
                        $result = (array) json_decode($service->request('/me'), true);
                        break;
                    case 'google':
                        $result = (array) json_decode(
                            $service->request('https://www.googleapis.com/oauth2/v1/userinfo'),
                            true
                        );
                        break;
                    default:
                        throw new SocialException('Unsupported OAuth2 service');
                }
            } else {
                throw new SocialException('Social login failed');
            }
            $this->authService->login($serviceName, $result);
            return Redirect::to(Session::get('url.intended'));
        } catch (SocialException $e) {
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
        return $this->oauth->init(
            $serviceName,
            URL::route(Config::get('gzero-social::callback_route'), [$serviceName])
        );
    }
}
