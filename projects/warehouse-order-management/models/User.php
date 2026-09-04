<?php

class User
{
    public static function all(): array
    {
        return db()->query("SELECT id, name, username, role, status, created_at FROM users ORDER BY name ASC")->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
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
        $stmt = db()->prepare("INSERT INTO users (name, username, password_hash, role, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['username'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'],
            $data['status'],
        ]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        if (!empty($data['password'])) {
            $stmt = db()->prepare("UPDATE users SET name=?, username=?, password_hash=?, role=?, status=? WHERE id=?");
            $stmt->execute([$data['name'], $data['username'], password_hash($data['password'], PASSWORD_DEFAULT), $data['role'], $data['status'], $id]);
        } else {
            $stmt = db()->prepare("UPDATE users SET name=?, username=?, role=?, status=? WHERE id=?");
            $stmt->execute([$data['name'], $data['username'], $data['role'], $data['status'], $id]);
        }
    }
}
