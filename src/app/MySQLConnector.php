<?php


namespace App;


class MySQLConnector {
    private static string $host = 'localhost';
    private static string $dbname = 'db_my_calendar';
    private static string $username = 'admin';
    private static string $password = 'admin123';

    private \PDO $connection;

    public function __construct() {
        $this->connection = new \PDO(
            'mysql:host=' . self::$host . ';dbname=' . self::$dbname,
            self::$username,
            self::$password,
        );
    }

    public function getConnection(): \PDO {
        return $this->connection;
    }

    public function get_db_types(): array {
        $sql = $this->connection->prepare("select * from `types`");
        $sql->execute();
        $records = $sql->fetchAll();
        $types = array();

        foreach ($records as $record) {
            $types[] = [
                'id' => $record['id'],
                'name' => $record['name'],
                'label' => $record['label'],
            ];
        }

        return $types;
    }

    public function get_type(int $id = null, string $name = null, string $label = null): ?array {
        $sql = $this->connection->prepare(
            "select * from `types` where id = :id or name = :name or label = :label"
        );
        $sql->execute([
            ':id' => $id,
            ':name' => $name,
            ':label' => $label,
        ]);
        $type = $sql->fetch();

        if (!$type) {
            return null;
        } else {
            return [
                'id' => $type['id'],
                'name' => $type['name'],
            ];
        }
    }

    public function get_tasks(int $filter = null, string $start = null, string $end = null): array {
        if (is_null($filter)) {
            $query = "select * from `tasks` order by `datetime`";
            $sql = $this->connection->prepare($query);
            $sql->execute();
        } else {
            switch ($filter) {
                case CalendarFilters::$current:
                    $query = "select * from `tasks` where is_completed = 0 and timestampdiff(minute, now(), `datetime`) > 0 order by `datetime`";
                    $sql = $this->connection->prepare($query);
                    $sql->execute();
                    break;
                case CalendarFilters::$old:
                    $query = "select * from `tasks` where is_completed = 0 and timestampdiff(minute, now(), `datetime`) <= 0 order by `datetime`";
                    $sql = $this->connection->prepare($query);
                    $sql->execute();
                    break;
                case CalendarFilters::$completed:
                    $query = "select * from `tasks` where is_completed = 1 order by `datetime`";
                    $sql = $this->connection->prepare($query);
                    $sql->execute();
                    break;
                case CalendarFilters::$other:
                    $query = "select * from `tasks` where `datetime` between :start and :end order by `datetime`";
                    $sql = $this->connection->prepare($query);
                    $sql->execute([
                        ':start' => $start,
                        ':end' => $end,
                    ]);
                    break;
                case CalendarFilters::$all:
                    $query = "select * from `tasks` order by `datetime`";
                    $sql = $this->connection->prepare($query);
                    $sql->execute();
                    break;
                default:
                    throw new \Exception("Unknown filter: $filter");
            }
        }

        return $sql->fetchAll();
    }

    public function get_task(int $id = null): ?array {
        $sql = $this->connection->prepare(
            "select * from `tasks` where id = :id"
        );
        $sql->execute([
            ':id' => $id,
        ]);
        $task = $sql->fetch();

        if (!$task) {
            return null;
        } else {
            $datetime = explode(' ', $task['datetime']);
            return [
                'id' => $task['id'],
                'theme' => $task['theme'],
                'type_id' => $task['type_id'],
                'place' => $task['place'],
                'date' => $datetime[0],
                'time' => $datetime[1],
                'duration' => $task['duration'],
                'is_completed' => $task['is_completed'],
                'comment' => $task['comment'],
            ];
        }
    }

    public function add_task(
        string $theme,
        string $type,
        string $place,
        string $datetime,
        string $duration,
        string $comment
    ): void {
        $db_type = $this->get_type(label: $type);
        if (is_null($db_type)) {
            throw new \Exception("Неизвестный тип задачи: $type");
        }
        $sql = $this->connection->prepare(
            "insert into `tasks`(`theme`,`type_id`,`place`,`datetime`,`duration`,`comment`) 
                        values (:theme, :type_id, :place, :datetime, :duration, :comment)"
        );
        $sql->execute([
            ':theme' => $theme,
            ':type_id' => $db_type['id'],
            ':place' => $place,
            ':datetime' => $datetime,
            ':duration' => $duration,
            ':comment' => $comment,
        ]);
    }

    public function update_task(
        int $id,
        string $theme,
        string $type,
        string $place,
        string $datetime,
        string $duration,
        string $comment
    ): void {
        $db_type = $this->get_type(label: $type);
        if (is_null($db_type)) {
            throw new \Exception("Неизвестный тип задачи: $type");
        }
        $sql = $this->connection->prepare(
            "update `tasks` set theme = :theme, type_id = :type_id, place = :place, datetime = :datetime,
                    duration = :duration, comment = :comment where id = :id limit 1"
        );
        $sql->execute([
            ':theme' => $theme,
            ':type_id' => $db_type['id'],
            ':place' => $place,
            ':datetime' => $datetime,
            ':duration' => $duration,
            ':comment' => $comment,
            ':id' => $id
        ]);
    }

    public function set_task_completeness(int $task_id, int $new_status): void {
        $sql = $this->connection->prepare(
            "update `tasks` set is_completed = :is_completed where id = :id limit 1"
        );
        $sql->execute([
            ':id' => $task_id,
            ':is_completed' => $new_status,
        ]);
    }
}