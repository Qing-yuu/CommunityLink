<?php

namespace classes;

use PDO;

class ContactDAO
{
    public function __construct(private PDO $pdo) {}
    public function create(array $d): int
    {
        $sql = "INSERT INTO contact_messages (name, email, subject, message)
                VALUES (:name, :email, :subject, :message)";
        $st = $this->pdo->prepare($sql);
        $st->execute([
            ':name'    => $d['name'],
            ':email'   => $d['email'],
            ':subject' => $d['subject'],
            ':message' => $d['message'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function all(): array
    {
        $st = $this->pdo->query("SELECT * FROM contact_messages ORDER BY id DESC");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $st = $this->pdo->prepare("SELECT * FROM contact_messages WHERE id = :id");
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updateRead(int $id, bool $isRead): void
    {
        $sql = "UPDATE contact_messages SET is_read = :is_read WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        $st->execute([
            ':is_read' => $isRead ? 'read' : 'unread',
            ':id'      => $id,
        ]);
    }
    public function delete(int $id): void
    {
        $st = $this->pdo->prepare("DELETE FROM contact_messages WHERE id = :id");
        $st->execute([':id' => $id]);
    }
}
