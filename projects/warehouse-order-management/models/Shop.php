<?php

class Shop
{
    public static function all(?string $search = null, ?string $status = null): array
    {
        $sql = "SELECT * FROM shops WHERE 1=1";
        $params = [];
        if ($search) {
            $sql .= " AND (shop_name LIKE ? OR owner_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY shop_name ASC";
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function active(): array
    {
        return db()->query("SELECT * FROM shops WHERE status = 'active' ORDER BY shop_name ASC")->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare("SELECT * FROM shops WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = db()->prepare("INSERT INTO shops (shop_name, owner_name, contact_number, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['shop_name'], $data['owner_name'], $data['contact_number'], $data['status']]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = db()->prepare("UPDATE shops SET shop_name=?, owner_name=?, contact_number=?, status=? WHERE id=?");
        $stmt->execute([$data['shop_name'], $data['owner_name'], $data['contact_number'], $data['status'], $id]);
    }

    public static function toggleStatus(int $id): void
    {
        db()->prepare("UPDATE shops SET status = IF(status='active','inactive','active') WHERE id = ?")->execute([$id]);
    }
}
