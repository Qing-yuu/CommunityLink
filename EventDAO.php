<?php

namespace classes;

use PDO;

class EventDAO
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(?string $q = null): array
    {
        $baseSelect = "
        SELECT
            e.event_id, e.title, e.location, e.description, e.`date`,
            e.created_at, e.organisation_id, o.org_name
        FROM `events` e
        LEFT JOIN `organisations` o ON o.organisation_id = e.organisation_id
    ";

        if ($q !== null && $q !== '') {
            $sql = $baseSelect . "
            WHERE e.title       LIKE :q1
               OR e.location    LIKE :q2
               OR e.description LIKE :q3
               OR o.org_name    LIKE :q4
            ORDER BY e.`date` DESC, e.event_id DESC
        ";
            $st = $this->pdo->prepare($sql);
            $like = '%'.$q.'%';
            $st->bindValue(':q1', $like, PDO::PARAM_STR);
            $st->bindValue(':q2', $like, PDO::PARAM_STR);
            $st->bindValue(':q3', $like, PDO::PARAM_STR);
            $st->bindValue(':q4', $like, PDO::PARAM_STR);
            $st->execute();
            return $st->fetchAll(PDO::FETCH_ASSOC);
        }

        $sql = $baseSelect . " ORDER BY e.`date` DESC, e.event_id DESC";
        $st = $this->pdo->query($sql);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }


    public function find(int $id): ?array
    {
        $st = $this->pdo->prepare(
            "SELECT e.*, o.org_name 
             FROM `events` e 
             LEFT JOIN `organisations` o ON o.organisation_id = e.organisation_id
             WHERE e.event_id = :id"
        );
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function volunteersForEvent(int $eventId): array
    {
        $st = $this->pdo->prepare(
            "SELECT v.volunteer_id, v.full_name, v.email, v.status
             FROM `volunteer_event` ve
             JOIN `volunteers` v ON v.volunteer_id = ve.volunteer_id
             WHERE ve.event_id = :id
             ORDER BY v.full_name"
        );
        $st->execute([':id' => $eventId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $d, array $volunteerIds = []): int
    {
        $this->pdo->beginTransaction();
        try {
            $st = $this->pdo->prepare(
                "INSERT INTO `events`(title, location, description, `date`, organisation_id)
                 VALUES(:title,:location,:description,:date,:org)"
            );
            $st->execute([
                ':title' => $d['title'],
                ':location' => $d['location'],
                ':description' => $d['description'] ?: null,
                ':date' => $d['date'],
                ':org' => $d['organisation_id'] ?: null,
            ]);
            $id = (int)$this->pdo->lastInsertId();

            if ($volunteerIds) {
                $ins = $this->pdo->prepare(
                    "INSERT INTO `volunteer_event`(event_id, volunteer_id)
                     VALUES(:eid, :vid)"
                );
                foreach ($volunteerIds as $vid) {
                    $ins->execute([':eid' => $id, ':vid' => (int)$vid]);
                }
            }

            $this->pdo->commit();
            return $id;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $d, array $volunteerIds = []): void
    {
        $this->pdo->beginTransaction();
        try {
            $st = $this->pdo->prepare(
                "UPDATE `events` SET
                   title=:title, location=:location, description=:description,
                   `date`=:date, organisation_id=:org
                 WHERE event_id=:id"
            );
            $st->execute([
                ':title' => $d['title'],
                ':location' => $d['location'],
                ':description' => $d['description'] ?: null,
                ':date' => $d['date'],
                ':org' => $d['organisation_id'] ?: null,
                ':id' => $id,
            ]);


            $this->pdo->prepare("DELETE FROM `volunteer_event` WHERE event_id=:id")->execute([':id' => $id]);
            if ($volunteerIds) {
                $ins = $this->pdo->prepare(
                    "INSERT INTO `volunteer_event`(event_id, volunteer_id)
                     VALUES(:eid, :vid)"
                );
                foreach ($volunteerIds as $vid) {
                    $ins->execute([':eid' => $id, ':vid' => (int)$vid]);
                }
            }

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare("DELETE FROM `volunteer_event` WHERE event_id=:id")->execute([':id' => $id]);
            $this->pdo->prepare("DELETE FROM `events` WHERE event_id=:id")->execute([':id' => $id]);
            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function recentWithOrg(int $limit = 6): array
    {
        $sql = "SELECT e.event_id, e.title, e.location, e.description, e.`date`,
                       e.organisation_id, o.org_name
                FROM `events` e
                LEFT JOIN `organisations` o ON o.organisation_id = e.organisation_id
                ORDER BY e.`date` ASC, e.event_id DESC
                LIMIT :lim";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(':lim', $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
