<?php namespace Gzero\Social;

use Gzero\Repository\SocialRepository;
use Gzero\Repository\UserRepository;
use Illuminate\Auth\AuthManager;

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
     * @param $response    array response data
     *
     * @throws SocialException
     */
    public function login($serviceName, $response)
    {
        $userId = $this->repo->getUserIdBySocialId($response['id'], $serviceName);
        if (\Auth::check()) { // user already logged and service has not been connected
            $user = \Auth::user();
            if ($userId) { // This service has already been connected
                \Session::put('url.intended', \URL::route('connectedServices'));
                throw new SocialException(
                    \Lang::get(
                        'gzero-social::common.serviceAlreadyConnectedMessage',
                        ['serviceName' => \Str::title($serviceName)]
                    )
                );
            } else { // create connection for new service
                $this->repo->addSocialRelation($user, $serviceName, $response);
            }
        } else {
            if ($userId) { // login user with this service
                $this->auth->loginUsingId($userId);
            } else { // create new user
                $user = $this->repo->createNewUser($serviceName, $response);
                $this->auth->login($user);
            }
        }
    }
}
