<?php
namespace classes;

use PDO;

class VolunteerEventDAO
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function assign(int $eventId, int $volunteerId, string $role = 'helper'): bool
    {
        $sql = "INSERT IGNORE INTO volunteer_event (event_id, volunteer_id, role)
                VALUES (?,?,?)";
        $st = $this->pdo->prepare($sql);
        return $st->execute([$eventId, $volunteerId, $role]);
    }

    public function unassign(int $eventId, int $volunteerId): bool
    {
        $sql = "DELETE FROM volunteer_event WHERE event_id=? AND volunteer_id=?";
        $st = $this->pdo->prepare($sql);
        return $st->execute([$eventId, $volunteerId]);
    }

    public function volunteersForEvent(int $eventId): array {
        $sql = "SELECT 
                v.volunteer_id AS id,
                v.full_name AS name,
                v.phone,
                v.skills,
                v.status,
                ve.role,
                ve.assigned_at
            FROM volunteer_event ve
            JOIN volunteers v ON v.volunteer_id = ve.volunteer_id
            WHERE ve.event_id = ?
            ORDER BY v.full_name";
        $st = $this->pdo->prepare($sql);
        $st->execute([$eventId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}

