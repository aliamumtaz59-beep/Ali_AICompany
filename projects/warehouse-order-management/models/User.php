<?php

class User
{
    public static function all(): array
    {
        $rows = db()->query("SELECT id, name, username, role, permissions, status, created_at FROM users ORDER BY name ASC")->fetchAll();
        foreach ($rows as &$row) {
            $row['permissions'] = json_decode($row['permissions'] ?? '', true) ?: [];
        }
        return $rows;
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        $row['permissions'] = json_decode($row['permissions'] ?? '', true) ?: [];
        return $row;
    }

    public static function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM users WHERE username = ?";
        $params = [$username];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    public static function create(array $data): int
    {
        $stmt = db()->prepare("INSERT INTO users (name, username, password_hash, role, permissions, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['username'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'],
            json_encode($data['permissions'] ?? []),
            $data['status'],
        ]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $permissions = json_encode($data['permissions'] ?? []);
        if (!empty($data['password'])) {
            $stmt = db()->prepare("UPDATE users SET name=?, username=?, password_hash=?, role=?, permissions=?, status=? WHERE id=?");
            $stmt->execute([$data['name'], $data['username'], password_hash($data['password'], PASSWORD_DEFAULT), $data['role'], $permissions, $data['status'], $id]);
        } else {
            $stmt = db()->prepare("UPDATE users SET name=?, username=?, role=?, permissions=?, status=? WHERE id=?");
            $stmt->execute([$data['name'], $data['username'], $data['role'], $permissions, $data['status'], $id]);
        }
    }
}
