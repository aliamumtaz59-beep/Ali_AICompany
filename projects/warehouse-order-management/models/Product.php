<?php

class Product
{
    private const REMAINING_QTY_EXPR = "(p.quantity_pcs - COALESCE((SELECT SUM(oi.quantity) FROM order_items oi WHERE oi.product_id = p.id), 0))";
    const IMAGE_DIR = __DIR__ . '/../public/uploads/products/';
    const IMAGE_URL_PREFIX = 'public/uploads/products/';
    const ALLOWED_IMAGE_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5MB

    public static function all(?string $search = null, ?string $status = null): array
    {
        $sql = "SELECT p.*, s.shop_name, " . self::REMAINING_QTY_EXPR . " AS remaining_qty
                FROM products p LEFT JOIN shops s ON s.id = p.shop_id WHERE 1=1";
        $params = [];
        if ($search) {
            $sql .= " AND (p.product_code LIKE ? OR p.product_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status) {
            $sql .= " AND p.status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY p.product_name ASC";
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function active(): array
    {
        $sql = "SELECT p.*, " . self::REMAINING_QTY_EXPR . " AS remaining_qty
                FROM products p WHERE p.status = 'active' ORDER BY p.product_name ASC";
        return db()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $sql = "SELECT p.*, " . self::REMAINING_QTY_EXPR . " AS remaining_qty FROM products p WHERE p.id = ?";
        $stmt = db()->prepare($sql);
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
        $stmt = db()->prepare("INSERT INTO products (product_code, product_name, description, unit, shop_id, quantity_pcs, image_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['product_code'], $data['product_name'], $data['description'], $data['unit'], $data['shop_id'], $data['quantity_pcs'], $data['image_path'], $data['status']]);
        return (int) db()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = db()->prepare("UPDATE products SET product_code=?, product_name=?, description=?, unit=?, shop_id=?, quantity_pcs=?, image_path=?, status=? WHERE id=?");
        $stmt->execute([$data['product_code'], $data['product_name'], $data['description'], $data['unit'], $data['shop_id'], $data['quantity_pcs'], $data['image_path'], $data['status'], $id]);
    }

    public static function toggleStatus(int $id): void
    {
        db()->prepare("UPDATE products SET status = IF(status='active','inactive','active') WHERE id = ?")->execute([$id]);
    }

    /**
     * Stores an uploaded product image. Returns ['path' => string|null, 'error' => string|null].
     */
    public static function uploadImage(array $file): array
    {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) return ['path' => null, 'error' => null];
        if ($file['error'] !== UPLOAD_ERR_OK) return ['path' => null, 'error' => 'Image upload failed.'];
        if ($file['size'] > self::MAX_IMAGE_SIZE) return ['path' => null, 'error' => 'Image exceeds the 5MB limit.'];

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_IMAGE_EXT, true)) {
            return ['path' => null, 'error' => 'Image type ".' . $ext . '" is not allowed.'];
        }

        if (!is_dir(self::IMAGE_DIR)) {
            mkdir(self::IMAGE_DIR, 0755, true);
        }

        $storedName = bin2hex(random_bytes(16)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], self::IMAGE_DIR . $storedName)) {
            return ['path' => null, 'error' => 'Failed to save image.'];
        }

        return ['path' => self::IMAGE_URL_PREFIX . $storedName, 'error' => null];
    }

    public static function deleteImageFile(?string $path): void
    {
        if (!$path) return;
        $full = __DIR__ . '/../' . $path;
        if (is_file($full)) unlink($full);
    }
}
