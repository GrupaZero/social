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
     * Login using social service
     *
     * @param $serviceName
     * @param $response
     */
    public function login($serviceName, $response)
    {
        $userId = $this->repo->getUserIdBySocialId($response['id'], $serviceName);
        if ($userId) {
            $this->auth->loginUsingId($userId);
        } else {
            $user = $this->repo->createNewUser($serviceName, $response);
            $this->auth->login($user);
        }
    }
}
