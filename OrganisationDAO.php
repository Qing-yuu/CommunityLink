<?php

namespace classes;

use PDO;
use RuntimeException;
use InvalidArgumentException;

class OrganisationDAO
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(array $d): int
    {
        $st = $this->pdo->prepare("
            INSERT INTO organisations (org_name, contact_person_full_name, email, phone)
            VALUES (:org, :contact, :email, :phone)
        ");
        $st->execute([
            ':org'     => $d['org_name'],
            ':contact' => $d['contact_person_full_name'],
            ':email'   => $d['email'] ?: null,
            ':phone'   => $d['phone'] ?: null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function all(): array
    {
        $st = $this->pdo->query("SELECT * FROM organisations ORDER BY organisation_id DESC");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $st = $this->pdo->prepare("SELECT * FROM organisations WHERE organisation_id = :id");
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function update(int $id, array $d): void
    {
        $old = $this->find($id);
        if (!$old) {
            throw new RuntimeException("Organisation not found");
        }

        $st = $this->pdo->prepare("
            UPDATE organisations
            SET org_name = :org,
                contact_person_full_name = :contact,
                email = :email,
                phone = :phone
            WHERE organisation_id = :id
        ");
        $st->execute([
            ':org'     => $d['org_name'],
            ':contact' => $d['contact_person_full_name'],
            ':email'   => $d['email'] ?: null,
            ':phone'   => $d['phone'] ?: null,
            ':id'      => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $old = $this->find($id);
        if (!$old) {
            return;
        }

        $st = $this->pdo->prepare("DELETE FROM organisations WHERE organisation_id = :id");
        $st->execute([':id' => $id]);
    }
}
