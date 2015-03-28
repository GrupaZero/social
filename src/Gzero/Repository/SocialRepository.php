<?php namespace Gzero\Repository;

use Illuminate\Database\Query\Builder;

/**
 * This file is part of the GZERO CMS package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Class SocialRepository
 *
 * @package    Gzero\Repository
 * @author     Adrian Skierniewski <adrian.skierniewski@gmail.com>
 * @copyright  Copyright (c) 2015, Adrian Skierniewski
 */
class SocialRepository {

    /**
     * @var string
     */
    private $table = 'SocialIntegrations';
    /**
     * @var Builder
     */
    private $builder;
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->builder = \App::make('db');
        $this->userRepo = $userRepo;
    }

    /**
     * @param $socialId
     * @param $serviceName
     *
     * @return mixed|static
     */
    public function getUserIdBySocialId($socialId, $serviceName)
    {
        return $this->newQB()->where('socialId', '=', $serviceName . '_' . $socialId)->first();
    }

    /**
     * @param $serviceName
     * @param $response
     */
    public function createNewUser($serviceName, $response)
    {
        $data = $this->parseServiceResponse($serviceName, $response);
        $user = $this->userRepo->create($data);
        $this->newQB()->insert(['userId' => $user->id, 'socialId' => $serviceName . '_' . $response['id']]);
        return $user;
    }

    /**
     * @return Builder
     */
    private function newQB()
    {
        return $this->builder->table($this->table);
    }

    /**
     * @param $serviceName
     * @param $response
     *
     * @return array
     */
    private function parseServiceResponse($serviceName, $response)
    {
        return [
            'hasSocialIntegrations' => true,
            'email' => 'test@test.pl',
            'password' => '',
            'firstName' => '',
            'lastName' => '',
        ];
    }
}
