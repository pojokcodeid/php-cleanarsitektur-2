<?php

namespace Web\Controller;

use Application\UseCase\User\CreateUser;
use Application\UseCase\User\GetAllUsers;
use Application\UseCase\User\GetUserById;
use Application\UseCase\User\UpdateUser;
use Application\UseCase\User\DeleteUser;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Infrastructure\Persistence\UserRepository;
use Infrastructure\Persistence\DatabaseConnection;
use Validator\UserValidator;
use Psr\Container\ContainerInterface;

class UserController
{
  private $createUser;
  private $getAllUsers;
  private $getUserById;
  private $updateUser;
  private $deleteUser;
  private $logger;

  public function __construct(ContainerInterface $container)
  {
    $databaseConnection = new DatabaseConnection();
    $userRepository = new UserRepository($databaseConnection->getConnection());
    $this->createUser = new CreateUser($userRepository);
    $this->getAllUsers = new GetAllUsers($userRepository);
    $this->getUserById = new GetUserById($userRepository);
    $this->updateUser = new UpdateUser($userRepository);
    $this->deleteUser = new DeleteUser($userRepository);
    $this->logger = $container->get('logger');
  }

  public function createUser(Request $request, Response $response, $args)
  {
    $data = $request->getParsedBody();

    // Validasi data
    $errors = UserValidator::validate($data);

    if ($errors) {
      $this->logger->warning('Validation errors on create user', $errors);
      $response->getBody()->write(UserValidator::validateToJson($data));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    try {
      $user = $this->createUser->execute($data['name'], $data['email']);
      $this->logger->info('User created', ['user' => $user]);
      $response->getBody()->write(json_encode([
        'message' => 'User created',
        'user' => [
          'id' => $user->getId(),
          'name' => $user->getName(),
          'email' => $user->getEmail(),
          'created_at' => $user->getCreatedAt()
        ]
      ]));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    } catch (\Exception $e) {
      $this->logger->error('Error creating user', ['exception' => $e]);
      $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(409); // Conflict
    }
  }

  public function listUsers(Request $request, Response $response, $args)
  {
    try {
      $users = $this->getAllUsers->execute();
      $this->logger->info('Listed all users', ['count' => count($users)]);
      $userList = [];

      foreach ($users as $user) {
        $userList[] = [
          'id' => $user->getId(),
          'name' => $user->getName(),
          'email' => $user->getEmail(),
          'created_at' => $user->getCreatedAt()
        ];
      }

      $response->getBody()->write(json_encode($userList));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } catch (\Exception $e) {
      $this->logger->error('Error listing users', ['exception' => $e]);
      $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
  }

  public function getUser(Request $request, Response $response, $args)
  {
    $id = $args['id'];
    try {
      $user = $this->getUserById->execute($id);

      if ($user) {
        $this->logger->info('Retrieved user', ['user' => $user]);
        $response->getBody()->write(json_encode([
          'id' => $user->getId(),
          'name' => $user->getName(),
          'email' => $user->getEmail(),
          'created_at' => $user->getCreatedAt()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
      } else {
        $this->logger->warning('User not found', ['id' => $id]);
        $response->getBody()->write(json_encode(['message' => 'User not found']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
      }
    } catch (\Exception $e) {
      $this->logger->error('Error retrieving user', ['exception' => $e]);
      $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
  }

  public function updateUser(Request $request, Response $response, $args)
  {
    $id = $args['id'];
    $data = $request->getParsedBody();

    // Validasi data
    $errors = UserValidator::validate($data);

    if ($errors) {
      $this->logger->warning('Validation errors on update user', $errors);
      $response->getBody()->write(UserValidator::validateToJson($data));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    try {
      $user = $this->updateUser->execute($id, $data['name'], $data['email'], date('Y-m-d H:i:s'));
      $this->logger->info('User updated', ['user' => $user]);
      $response->getBody()->write(json_encode([
        'message' => 'User updated',
        'user' => [
          'id' => $user->getId(),
          'name' => $user->getName(),
          'email' => $user->getEmail(),
          'created_at' => $user->getCreatedAt()
        ]
      ]));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } catch (\Exception $e) {
      $this->logger->error('Error updating user', ['exception' => $e]);
      $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(409); // Conflict
    }
  }

  public function deleteUser(Request $request, Response $response, $args)
  {
    $id = $args['id'];
    try {
      $this->deleteUser->execute($id);
      $this->logger->info('User deleted', ['id' => $id]);
      $response->getBody()->write(json_encode(['message' => 'User deleted']));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } catch (\Exception $e) {
      $this->logger->error('Error deleting user', ['exception' => $e]);
      $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
  }
}
