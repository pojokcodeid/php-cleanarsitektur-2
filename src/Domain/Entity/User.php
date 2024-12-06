<?php
// src/Domain/Entity/User.php
namespace Domain\Entity;

class User
{
  private $id;
  private $name;
  private $email;
  private $created_at;

  public function __construct($id, $name, $email, $created_at)
  {
    $this->id = $id;
    $this->name = $name;
    $this->email = $email;
    $this->created_at = $created_at;
  }

  public static function fromArray(array $data)
  {
    return new self(
      $data['id'],
      $data['name'],
      $data['email'],
      $data['created_at']
    );
  }

  public function getId()
  {
    return $this->id;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getEmail()
  {
    return $this->email;
  }

  public function getCreatedAt()
  {
    return $this->created_at;
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function setEmail($email)
  {
    $this->email = $email;
  }

  public function setCreatedAt($created_at)
  {
    $this->created_at = $created_at;
  }
}
