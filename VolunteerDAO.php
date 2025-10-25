<?php
namespace classes;

use PDO;
use RuntimeException;
use InvalidArgumentException;

class VolunteerDAO
{
    public function __construct(private PDO $pdo) {}

    public function create(array $d): int
    {
        $st = $this->pdo->prepare("
            INSERT INTO volunteers (user_id, full_name, phone, skills, status, profile_picture, created_at)
            VALUES (:user_id, :full_name, :phone, :skills, :status, :profile_picture, NOW())
        ");
        $st->execute([
            ':user_id'         => $d['user_id'] ?? null,
            ':full_name'       => $d['full_name'],
            ':phone'           => $d['phone'] ?: null,
            ':skills'          => $d['skills'] ?: null,
            ':status'          => $d['status'] ?? 'unhired',
            ':profile_picture' => $d['profile_picture'] ?: null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function all(): array
    {
        $st = $this->pdo->query("
        SELECT v.*, u.email
        FROM volunteers v
        LEFT JOIN users u ON u.user_id = v.user_id
        ORDER BY v.volunteer_id DESC
    ");
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }




    public function find(int $id): ?array
    {
        $st = $this->pdo->prepare("SELECT * FROM volunteers WHERE volunteer_id=:id");
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function update(int $id, array $d): void
    {
        $old = $this->find($id);
        if (!$old) throw new RuntimeException("Volunteer not found");

        $st = $this->pdo->prepare("
            UPDATE volunteers SET
                full_name = :full_name,
                phone = :phone,
                skills = :skills,
                status = :status,
                profile_picture = :profile_picture
            WHERE volunteer_id = :id
        ");
        $st->execute([
            ':full_name'       => $d['full_name'],
            ':phone'           => $d['phone'] ?: null,
            ':skills'          => $d['skills'] ?: null,
            ':status'          => $d['status'] ?? 'unhired',
            ':profile_picture' => $d['profile_picture'] ?? $old['profile_picture'],
            ':id'              => $id
        ]);

        if (isset($d['profile_picture']) && $d['profile_picture'] !== $old['profile_picture']) {
            $this->unlinkIfExists($old['profile_picture']);
        }
    }

    public function delete(int $id): void
    {
        $old = $this->find($id);
        if (!$old) return;
        $st = $this->pdo->prepare("DELETE FROM volunteers WHERE volunteer_id=:id");
        $st->execute([':id' => $id]);
        $this->unlinkIfExists($old['profile_picture']);
    }

    public function setStatus(int $id, string $status): void
    {
        $allowed = ['unhired', 'hired'];
        if (!in_array($status, $allowed, true)) {
            throw new InvalidArgumentException("Invalid status");
        }
        $st = $this->pdo->prepare("UPDATE volunteers SET status=:s WHERE volunteer_id=:id");
        $st->execute([':s' => $status, ':id' => $id]);
    }

    private function unlinkIfExists(?string $relPath): void
    {
        if (!$relPath) return;
        $root = dirname(__DIR__, 2);
        $path = $root . '/' . ltrim($relPath, '/');
        if (!is_file($path)) {
            $alt = $root . '/volunteer/' . ltrim($relPath, '/');
            if (is_file($alt)) @unlink($alt);
            return;
        }
        @unlink($path);
    }
}
