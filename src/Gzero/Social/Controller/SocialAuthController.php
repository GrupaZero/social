<?php namespace Gzero\Social\Controller;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
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

    protected $oauth;

    public function __construct()
    {
        $this->oauth = App::make('oauth');
    }

    /**
     * @param $serviceName
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function socialLogin($serviceName)
    {
        if (Auth::check()) {
            return Redirect::to('/');
        }
        $service = $this->makeSocialService($serviceName);
        if ($serviceName === 'twitter') { // OAuth1 needs different approach
            $token = $service->requestRequestToken();
            $url = $service->getAuthorizationUri(['oauth_token' => $token->getRequestToken()]);
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
     * @param $serviceName
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function socialCallback($serviceName)
    {
        if (Auth::check()) {
            return Redirect::to('/');
        }
        try {
            $code = Input::get('code');
            $oauthToken = Input::get('oauth_token');
            $oauthVerifier = Input::get('oauth_verifier');
            $service = $this->makeSocialService($serviceName);
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
                        throw new Exception('Unsupported OAuth2 service');
                }
            } else {
                throw new Exception('Something wrong with the callback');
            }
            //User::socialLogin($serviceName . '_' . $result['id'], $result, $serviceName);
            return Redirect::to(Session::get('url.intended'));
        } catch (Exception $e) {
            /**@TODO Better way to handle exceptions on production */
            return App::abort(500, $e->getMessage());
        }
    }

    /**
     * @param $serviceName
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
