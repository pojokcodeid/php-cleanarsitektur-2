<?php

namespace Application\UseCase\User;

use Domain\Repository\UserRepositoryInterface;

class GetAllUsers
{
  private $userRepository;

  public function __construct(UserRepositoryInterface $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  public function execute()
  {
    return $this->userRepository->findAll();
  }
}
