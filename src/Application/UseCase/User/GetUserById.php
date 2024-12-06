<?php

namespace Application\UseCase\User;

use Domain\Repository\UserRepositoryInterface;

class GetUserById
{
  private $userRepository;

  public function __construct(UserRepositoryInterface $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  public function execute($id)
  {
    return $this->userRepository->findById($id);
  }
}
