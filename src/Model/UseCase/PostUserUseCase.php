<?php

namespace PwBox\Model\UseCase;

use PwBox\Model\User;
use PwBox\Model\UserRepository;


class PostUserUseCase{
    /** UserRepository */
    private $repo;

    /**
     * PostUserUseCase constructor.
     * @param $repo
     */
    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function __invoke(array $rawData)
    {
        $now = new \DateTime('now');
        $user = new User(
            null,
            $rawData['username'],
            $rawData['email'],
            $rawData['password'],
            $now,
            $now
        );
        $this->repo->save($user);
    }


}