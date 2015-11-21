<?php namespace Gzero\Repository;

use Gzero\Entity\User;
use Illuminate\Database\Query\Builder;
use Laravel\Socialite\AbstractUser;

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
     * Social repository constructor.
     *
     * @param UserRepository $userRepo User model
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->builder  = \App::make('db');
        $this->userRepo = $userRepo;
    }

    /**
     * Function retrieve social services by the given user id.
     *
     * @param $socialId    int social id
     *
     * @return mixed|static
     */
    public function getSocialId($socialId)
    {
        return $this->newQB()->where('socialId', '=', $socialId)->first();
    }

    /**
     * Function retrieve a user by the given social id and social service name.
     *
     * @param $socialId    int user social id
     * @param $serviceName string name of social service
     *
     * @return mixed|static
     */
    public function getUserIdBySocialId($socialId, $serviceName)
    {
        return $this->newQB()->where('socialId', '=', $serviceName . '_' . $socialId)->pluck('userId');
    }

    /**
     * Function retrieve social services by the given user id.
     *
     * @param $userId    int user id
     *
     * @return mixed|static
     */
    public function getUserSocialIds($userId)
    {
        return $this->newQB()->where('userId', '=', $userId)->lists('socialId');
    }

    /**
     * Function creates new user based on social service response and create relation with social integration in database.
     *
     * @param $serviceName    string name of social service
     * @param $response       AbstractUser response data
     *
     * @return User
     */
    public function createNewUser($serviceName, AbstractUser $response)
    {
        $data = $this->parseServiceResponse($response);
        $user = $this->userRepo->create($data);
        // create relation for new user and social integration
        $this->addSocialRelation($user, $serviceName, $response);
        return $user;
    }

    /**
     * Function adds new social account for existing user.
     *
     * @param $user        User user entity
     * @param $serviceName string name of social service
     * @param $response    AbstractUser response data
     *
     * @return User
     */
    public function addUserSocialAccount(User $user, $serviceName, AbstractUser $response)
    {
        $user->hasSocialIntegrations = true;
        $user->save();
        // create relation for new user and social integration
        $this->addSocialRelation($user, $serviceName, $response);
        return $user;
    }

    /**
     * Function creates relation for given user and social integration.
     *
     * @param $user        User user entity
     * @param $serviceName string name of social service
     * @param $response    AbstractUser response data
     *
     * @return mixed
     */
    public function addSocialRelation(User $user, $serviceName, AbstractUser $response)
    {
        // create relation for new user and social integration
        return $this->newQB()->insert(
            [
                'userId'    => $user->id,
                'socialId'  => $serviceName . '_' . $response->id,
                'createdAt' => \DB::raw('NOW()')
            ]
        );
    }

    /**
     * Database query builder.
     *
     * @return Builder
     */
    private function newQB()
    {
        return $this->builder->table($this->table);
    }

    /**
     * Function parses social service response and prepares user data to insert to database.
     *
     * @param $response        AbstractUser response data
     *
     * @return array parsed user data for database insertion
     */
    private function parseServiceResponse(AbstractUser $response)
    {
        $userData = [
            'hasSocialIntegrations' => true,
            'email'                 => uniqid('social_', true) // set unique email placeholder
        ];

        $name = explode(" ", $response->getName());
        if (count($name) >= 2) {
            $userData['firstName'] = $name[0];
            $userData['lastName']  = $name[1];
        } else {
            $userData['firstName'] = 'John';
            $userData['lastName']  = 'Doe';
        }
        return $userData;
    }
}
