<?php

namespace Application\UseCase\User;

use Domain\Repository\UserRepositoryInterface;

class DeleteUser
{
  private $userRepository;

  public function __construct(UserRepositoryInterface $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  public function execute($id)
  {
    $this->userRepository->delete($id);
  }
}
