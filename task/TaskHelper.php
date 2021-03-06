<?php

namespace go1\util\task;

use Doctrine\DBAL\Connection;
use go1\util\DB;

class TaskHelper
{
    public static function addTask(Connection $db, string $name, int $instanceId, int $userId, int $status, array $data)
    {
        $task = [
            'instance_id' => $instanceId,
            'user_id'     => $userId,
            'created'     => time(),
            'data'        => json_encode($data),
            'updated'     => time(),
            'status'      => $status,
        ];
        $db->insert($name, $task);
    }

    public static function updateTaskStatus(Connection $db, int $id, string $status, string $name)
    {
        $db->update($name, ['status' => $status], ['id' => $id]);
    }

    public static function updateTaskData(Connection $db, int $id, array $data, string $name)
    {
        $db->update($name, ['data' => json_encode($data)], ['id' => $id]);
    }

    public static function loadTask(Connection $db, int $id, string $name)
    {
        $row = $db->executeQuery("SELECT * FROM {$name} WHERE id = ?", [$id])
            ->fetch(DB::OBJ);

        if (!$row) {
            return null;
        }

        $row->name = $name;

        return Task::create($row);
    }

    public static function loadTaskByStatus(Connection $db, int $status, string $name)
    {
        $sql = "SELECT * FROM {$name} WHERE status = ? ORDER BY id ASC";
        $row = $db->executeQuery($sql, [$status])
            ->fetch(DB::OBJ);

        if (!$row) {
            return null;
        }

        $row->name = $name;

        return Task::create($row);
    }

    public static function addTaskItem(Connection $db, string $name, int $taskId, int $status, array $data)
    {
        $item = [
            'task_id' => $taskId,
            'created' => time(),
            'data'    => json_encode($data),
            'status'  => $status,
        ];
        $db->insert($name, $item);
    }

    public static function loadTaskItem(Connection $db, int $id, string $name)
    {
        $row = $db->executeQuery("SELECT * FROM {$name} WHERE id = ?", [$id])
            ->fetch(DB::OBJ);

        if (!$row) {
            return null;
        }

        $row->name = $name;

        return TaskItem::create($row);
    }

    public static function loadTaskItemByStatus(Connection $db, int $taskId, int $status, string $name)
    {
        $sql = "SELECT * FROM {$name} WHERE task_id = ? AND status = ? ORDER BY id ASC";
        $row = $db->executeQuery($sql, [$taskId, $status])
            ->fetch(DB::OBJ);

        if (!$row) {
            return null;
        }

        $row->name = $name;

        return TaskItem::create($row);
    }
}
