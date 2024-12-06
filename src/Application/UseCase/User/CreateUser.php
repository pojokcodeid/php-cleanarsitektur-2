<?php

namespace Application\UseCase\User;

use Domain\Entity\User;
use Domain\Repository\UserRepositoryInterface;

class CreateUser
{
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute($name, $email)
    {
        $user = new User(null, $name, $email, null);
        $this->userRepository->save($user);
        return $user;
    }
}
