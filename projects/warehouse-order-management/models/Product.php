<?php

class Product
{
    public static function all(?string $search = null, ?string $status = null): array
    {
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];
        if ($search) {
            $sql .= " AND (product_code LIKE ? OR product_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY product_name ASC";
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function active(): array
    {
        return db()->query("SELECT * FROM products WHERE status = 'active' ORDER BY product_name ASC")->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function codeExists(string $code, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM products WHERE product_code = ?";
        $params = [$code];
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
        $stmt = db()->prepare("INSERT INTO products (product_code, product_name, description, unit, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['product_code'], $data['product_name'], $data['description'], $data['unit'], $data['status']]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = db()->prepare("UPDATE products SET product_code=?, product_name=?, description=?, unit=?, status=? WHERE id=?");
        $stmt->execute([$data['product_code'], $data['product_name'], $data['description'], $data['unit'], $data['status'], $id]);
    }

    public static function toggleStatus(int $id): void
    {
        db()->prepare("UPDATE products SET status = IF(status='active','inactive','active') WHERE id = ?")->execute([$id]);
    }
}
