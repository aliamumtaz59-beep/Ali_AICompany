<?php

class Attachment
{
    const UPLOAD_DIR = __DIR__ . '/../uploads/orders/';
    const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
    const MAX_SIZE = 10 * 1024 * 1024; // 10MB

    public static function forOrder(int $orderId): array
    {
        $stmt = db()->prepare("SELECT * FROM order_attachments WHERE order_id = ? ORDER BY id ASC");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare("SELECT * FROM order_attachments WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Stores an uploaded file for an order. Returns an error message on failure, or null on success.
     */
    public static function upload(int $orderId, array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'Upload failed for "' . $file['name'] . '".';
        }
        if ($file['size'] > self::MAX_SIZE) {
            return 'File "' . $file['name'] . '" exceeds the 10MB limit.';
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            return 'File type ".' . $ext . '" is not allowed.';
        }

        $dir = self::UPLOAD_DIR . $orderId . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $storedName = bin2hex(random_bytes(16)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $dir . $storedName)) {
            return 'Failed to save file "' . $file['name'] . '".';
        }

        $stmt = db()->prepare("INSERT INTO order_attachments (order_id, original_name, stored_name, mime_type, size) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$orderId, $file['name'], $storedName, $file['type'], $file['size']]);
        return null;
    }

    public static function delete(int $id): void
    {
        $attachment = self::find($id);
        if (!$attachment) return;
        $path = self::path($attachment);
        if (is_file($path)) unlink($path);
        db()->prepare("DELETE FROM order_attachments WHERE id = ?")->execute([$id]);
    }

    public static function path(array $attachment): string
    {
        return self::UPLOAD_DIR . $attachment['order_id'] . '/' . $attachment['stored_name'];
    }
}
