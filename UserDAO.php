<?php
namespace classes;

use PDO;
use PDOException;

class UserDAO
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function all(?string $q = null): array
    {
        $sql = "SELECT user_id, email, identity, is_active FROM users";
        $bind = [];
        if ($q !== null && $q !== '') {
            $sql .= " WHERE (email LIKE :q OR identity LIKE :q)";
            $bind[':q'] = "%{$q}%";
        }
        $sql .= " ORDER BY user_id DESC";
        $st = $this->pdo->prepare($sql);
        $st->execute($bind);
        return $st->fetchAll();
    }

    public function find(int $id): ?array
    {
        $st = $this->pdo->prepare(
            "SELECT user_id, email, identity, is_active 
             FROM users WHERE user_id = :id"
        );
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId) {
            $st = $this->pdo->prepare(
                "SELECT COUNT(*) FROM users WHERE email = :email AND user_id <> :id"
            );
            $st->execute([':email' => $email, ':id' => $excludeId]);
        } else {
            $st = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $st->execute([':email' => $email]);
        }
        return (int)$st->fetchColumn() > 0;
    }

    public function create(array $d): int
    {
        $sql = "INSERT INTO users (email, identity, is_active, password_hash)
                VALUES (:email, :identity, :is_active, :password_hash)";
        $st = $this->pdo->prepare($sql);
        $st->execute([
            ':email' => $d['email'],
            ':identity' => $d['identity'],
            ':is_active' => (int)($d['is_active'] ?? 1),
            ':password_hash' => password_hash((string)$d['password'], PASSWORD_DEFAULT),
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $d): bool
    {
        $sets = [
            "email = :email",
            "identity = :identity",
            "is_active = :is_active",
        ];
        $bind = [
            ':email' => $d['email'],
            ':identity' => $d['identity'],
            ':is_active' => (int)($d['is_active'] ?? 1),
            ':id' => $id,
        ];

        if (!empty($d['password'])) {
            $sets[] = "password_hash = :password_hash";
            $bind[':password_hash'] = password_hash((string)$d['password'], PASSWORD_DEFAULT);
        }

        $sql = "UPDATE users SET " . implode(', ', $sets) . " WHERE user_id = :id";
        $st = $this->pdo->prepare($sql);
        return $st->execute($bind);
    }

    public function delete(int $id): bool
    {
        $st = $this->pdo->prepare("DELETE FROM users WHERE user_id = :id");
        return $st->execute([':id' => $id]);
    }


    public function setActive(int $id, bool $active): bool
    {
        $st = $this->pdo->prepare("UPDATE users SET is_active = :a WHERE user_id = :id");
        return $st->execute([':a' => (int)$active, ':id' => $id]);
    }
}

