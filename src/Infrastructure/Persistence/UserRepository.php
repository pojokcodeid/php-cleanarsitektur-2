<?php
// src/Infrastructure/Persistence/UserRepository.php
namespace Infrastructure\Persistence;

use Domain\Entity\User;
use Domain\Repository\UserRepositoryInterface;
use PDO;
use PDOException;

class UserRepository implements UserRepositoryInterface
{
    private $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(User $user)
    {
        $name = $user->getName();
        $email = $user->getEmail();

        try {
            $stmt = $this->connection->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Dapatkan ID yang dihasilkan dan created_at dari database
            $user->setId($this->connection->lastInsertId());

            // Ambil nilai created_at yang dihasilkan oleh MySQL
            $stmt = $this->connection->prepare("SELECT created_at FROM users WHERE id = :id");
            $id = $user->getId();
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $created_at = $stmt->fetchColumn();

            $user->setCreatedAt($created_at);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                throw new \Exception("Duplicate entry for email: $email");
            }
            throw $e;
        }
    }

    public function findAll()
    {
        $stmt = $this->connection->query("SELECT id, name, email, created_at FROM users");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        foreach ($results as $row) {
            $users[] = User::fromArray($row);
        }

        return $users;
    }

    public function findById($id)
    {
        $stmt = $this->connection->prepare("SELECT id, name, email, created_at FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return User::fromArray($row);
        }

        return null;
    }

    public function update(User $user)
    {
        $id = $user->getId();
        $name = $user->getName();
        $email = $user->getEmail();

        $stmt = $this->connection->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->connection->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}
