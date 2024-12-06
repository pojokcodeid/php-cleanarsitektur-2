<?php

namespace Domain\Repository;
use Domain\Entity\User;
interface UserRepositoryInterface
{
  public function save(User $user);
  public function findAll();
  public function findById($id);
  public function update(User $user);
  public function delete($id);
}