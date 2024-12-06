<?php

namespace Application\UseCase\User;

use Domain\Entity\User;
use Domain\Repository\UserRepositoryInterface;

class UpdateUser
{
  private $userRepository;

  public function __construct(UserRepositoryInterface $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  public function execute($id, $name, $email, $created_at)
  {
    $user = new User($id, $name, $email, $created_at);
    $this->userRepository->update($user);
    return $user;
  }
}
