<?php namespace Gzero\Repository;

use Gzero\Entity\User;
use Gzero\Social\SocialException;
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
    private $table = 'social_integrations';
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
        return $this->newQB()->where('social_id', '=', $socialId)->first();
    }

    /**
     * Function retrieve a user by the given social id and social service name.
     *
     * @param $socialId    int user social id
     * @param $serviceName string name of social service
     *
     * @return User
     */
    public function getUserIdBySocialId($socialId, $serviceName)
    {
        return $this->newQB()->where('social_id', '=', $serviceName . '_' . $socialId)->value('user_id');
    }

    /**
     * Function retrieve social services by the given user id.
     *
     * @param $userId    int user id
     *
     * @return \Illuminate\Support\Collection
     */
    public function getUserSocialIds($userId)
    {
        return $this->newQB()->where('user_id', '=', $userId)->pluck('social_id');
    }

    /**
     * Function creates new user based on social service response and create relation with social integration in database.
     *
     * @param $serviceName    string name of social service
     * @param $response       AbstractUser response data
     *
     * @return User
     * @throws SocialException
     */
    public function createNewUser($serviceName, AbstractUser $response)
    {
        $data         = $this->parseServiceResponse($response);
        $existingUser = $this->userRepo->getByEmail($data['email']);
        // duplicated user verification
        if ($existingUser === null) {
            $data['password'] = str_random(40);
            $user = $this->userRepo->create($data);
            // create relation for new user and social integration
            $this->addSocialRelation($user, $serviceName, $response);
            return $user;
        } else {
            session()->put('url.intended', route('register'));
            throw new SocialException(
                trans('gzero-social::common.email_already_in_use_message', ['service_name' => title_case($serviceName)])
            );
        }
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
        $user->has_social_integrations = true;
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
                'user_id'    => $user->id,
                'social_id'  => $serviceName . '_' . $response->id,
                'created_at' => \DB::raw('NOW()')
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
     * @param $response AbstractUser response data
     *
     * @return array parsed user data for database insertion
     */
    private function parseServiceResponse(AbstractUser $response)
    {
        $userData = [
            'has_social_integrations' => true,
            'nick'                    => $response->getNickname()
        ];
        // set user email if exists (twitter returns as null)
        if ($response->getEmail()) {
            $userData['email'] = $response->getEmail();
        } else {
            $userData['email'] = uniqid('social_', true); // set unique email placeholder
        }
        // set user first and last name
        $name = explode(" ", $response->getName());
        if (count($name) >= 2) {
            $userData['first_name'] = $name[0];
            $userData['last_name']  = $name[1];
        }

        return $userData;
    }
}
