<?php namespace Gzero\Social;

use Gzero\Repository\SocialRepository;
use Gzero\Repository\UserRepository;
use Illuminate\Auth\AuthManager;
use Laravel\Socialite\AbstractUser;

/**
 * This file is part of the GZERO CMS package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class SocialLoginService
 *
 * @package    Gzero\Social
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2015, Adrian Skierniewski
 */
class SocialLoginService {

    /**
     * @var UserRepository
     */
    private $repo;

    /**
     * @var AuthManager
     */
    private $auth;

    /**
     * @param SocialRepository $socialRepo
     * @param AuthManager      $auth
     */
    public function __construct(SocialRepository $socialRepo, AuthManager $auth)
    {
        $this->repo = $socialRepo;
        $this->auth = $auth;
    }

    /**
     * Login using social service.
     *
     * @param $serviceName string social service name
     * @param $response    AbstractUser response data
     *
     * @throws SocialException
     */
    public function login($serviceName, AbstractUser $response)
    {
        $userId = $this->repo->getUserIdBySocialId($response->id, $serviceName);
        if (auth()->check()) { // user already logged and service has not been connected
            $user = auth()->user();
            if ($userId) { // This service has already been connected
                session()->put('url.intended', route('connectedServices'));
                throw new SocialException(
                    trans(
                        'gzero-social::common.serviceAlreadyConnectedMessage',
                        ['serviceName' => title_case($serviceName)]
                    )
                );
            } else { // create connection for new service
                $this->repo->addUserSocialAccount($user, $serviceName, $response);
            }
        } else {
            if ($userId) { // login user with this service
                $this->auth->loginUsingId($userId);
            } else { // create new user
                $user = $this->repo->createNewUser($serviceName, $response);
                $this->auth->login($user);
                session()->put('showWelcomePage', true);
                session()->put('url.intended', route('account.welcome', ['method' => title_case($serviceName)]));
            }
        }
    }
}
