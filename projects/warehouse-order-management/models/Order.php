<?php

class Order
{
    public static function orderNumberExists(string $number, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM orders WHERE order_number = ?";
        $params = [$number];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    public static function create(array $header, array $items, int $userId): int
    {
        $pdo = db();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO orders (order_number, order_date, shop_id, barcode_no, remarks, created_by) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$header['order_number'], $header['order_date'], $header['shop_id'], $header['barcode_no'], $header['remarks'], $userId]);
            $orderId = (int) $pdo->lastInsertId();

            $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit, remarks) VALUES (?, ?, ?, ?, ?)");
            foreach ($items as $item) {
                $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['unit'], $item['remarks']]);
            }

            $pdo->commit();
            return $orderId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function update(int $orderId, array $header, array $items): void
    {
        $pdo = db();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE orders SET order_number=?, order_date=?, shop_id=?, barcode_no=?, remarks=? WHERE id=?");
            $stmt->execute([$header['order_number'], $header['order_date'], $header['shop_id'], $header['barcode_no'], $header['remarks'], $orderId]);

            $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$orderId]);

            $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit, remarks) VALUES (?, ?, ?, ?, ?)");
            foreach ($items as $item) {
                $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['unit'], $item['remarks']]);
            }

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function delete(int $orderId): void
    {
        db()->prepare("DELETE FROM orders WHERE id = ?")->execute([$orderId]);
    }

    public static function find(int $orderId): ?array
    {
        $stmt = db()->prepare("
            SELECT o.*, s.shop_name, s.owner_name AS shop_owner_name, s.contact_number AS shop_contact_number
            FROM orders o
            LEFT JOIN shops s ON s.id = o.shop_id
            WHERE o.id = ?
        ");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        if (!$order) return null;

        $itemStmt = db()->prepare("
            SELECT oi.*, p.product_code, p.product_name
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = ?
            ORDER BY oi.id ASC
        ");
        $itemStmt->execute([$orderId]);
        $order['items'] = $itemStmt->fetchAll();
        return $order;
    }

    public static function paginated(array $filters, int $page = 1, int $perPage = 15): array
    {
        $where = "1=1";
        $params = [];

        if (!empty($filters['order_number'])) {
            $where .= " AND o.order_number LIKE ?";
            $params[] = "%{$filters['order_number']}%";
        }
        if (!empty($filters['date_from'])) {
            $where .= " AND o.order_date >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where .= " AND o.order_date <= ?";
            $params[] = $filters['date_to'];
        }
        if (!empty($filters['product_id'])) {
            $where .= " AND EXISTS (SELECT 1 FROM order_items oi2 WHERE oi2.order_id = o.id AND oi2.product_id = ?)";
            $params[] = $filters['product_id'];
        }

        $countStmt = db()->prepare("SELECT COUNT(*) FROM orders o WHERE $where");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);
        $sql = "
            SELECT o.*, s.shop_name,
                (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count,
                (SELECT COALESCE(SUM(oi.quantity),0) FROM order_items oi WHERE oi.order_id = o.id) AS total_quantity
            FROM orders o
            LEFT JOIN shops s ON s.id = o.shop_id
            WHERE $where
            ORDER BY o.order_date DESC, o.id DESC
            LIMIT $perPage OFFSET $offset
        ";
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        return [
            'data' => $rows,
            'total' => $total,
            'pages' => (int) ceil($total / $perPage),
        ];
    }

    public static function dashboardStats(): array
    {
        $pdo = db();
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');

        $stats = [];
        $stmt = $pdo->prepare("SELECT COUNT(*) c, COALESCE(SUM(oi.quantity),0) q FROM orders o LEFT JOIN order_items oi ON oi.order_id = o.id WHERE o.order_date = ?");
        $stmt->execute([$today]);
        $row = $stmt->fetch();
        $stats['today_orders'] = (int) $row['c'];
        $stats['today_qty'] = (float) $row['q'];

        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT o.id) c, COALESCE(SUM(oi.quantity),0) q FROM orders o LEFT JOIN order_items oi ON oi.order_id = o.id WHERE o.order_date >= ?");
        $stmt->execute([$monthStart]);
        $row = $stmt->fetch();
        $stats['month_orders'] = (int) $row['c'];
        $stats['month_qty'] = (float) $row['q'];

        $stats['active_products'] = (int) $pdo->query("SELECT COUNT(*) FROM products WHERE status='active'")->fetchColumn();
        $stats['active_shops'] = (int) $pdo->query("SELECT COUNT(*) FROM shops WHERE status='active'")->fetchColumn();

        $stats['recent_orders'] = $pdo->query("
            SELECT o.*, s.shop_name,
                (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id=o.id) item_count,
                (SELECT COALESCE(SUM(oi.quantity),0) FROM order_items oi WHERE oi.order_id=o.id) total_quantity
            FROM orders o
            LEFT JOIN shops s ON s.id = o.shop_id
            ORDER BY o.created_at DESC LIMIT 8
        ")->fetchAll();

        return $stats;
    }

    public static function salesByShop(string $dateFrom, string $dateTo, int $limit = 10): array
    {
        $stmt = db()->prepare("
            SELECT s.shop_name, COUNT(DISTINCT o.id) order_count, COALESCE(SUM(oi.quantity),0) total_qty
            FROM orders o
            JOIN shops s ON s.id = o.shop_id
            LEFT JOIN order_items oi ON oi.order_id = o.id
            WHERE o.order_date BETWEEN ? AND ?
            GROUP BY s.id
            ORDER BY total_qty DESC
            LIMIT $limit
        ");
        $stmt->execute([$dateFrom, $dateTo]);
        return $stmt->fetchAll();
    }

    public static function topProducts(string $dateFrom, string $dateTo, int $limit = 10): array
    {
        $stmt = db()->prepare("
            SELECT p.product_code, p.product_name, COUNT(DISTINCT oi.order_id) order_count, SUM(oi.quantity) total_qty
            FROM order_items oi
            JOIN orders o ON o.id = oi.order_id
            JOIN products p ON p.id = oi.product_id
            WHERE o.order_date BETWEEN ? AND ?
            GROUP BY p.id
            ORDER BY total_qty DESC
            LIMIT $limit
        ");
        $stmt->execute([$dateFrom, $dateTo]);
        return $stmt->fetchAll();
    }

    public static function trend(string $dateFrom, string $dateTo): array
    {
        $stmt = db()->prepare("
            SELECT o.order_date, COUNT(DISTINCT o.id) orders, COALESCE(SUM(oi.quantity),0) quantity
            FROM orders o
            LEFT JOIN order_items oi ON oi.order_id = o.id
            WHERE o.order_date BETWEEN ? AND ?
            GROUP BY o.order_date
            ORDER BY o.order_date ASC
        ");
        $stmt->execute([$dateFrom, $dateTo]);
        return $stmt->fetchAll();
    }

    public static function dailyReport(string $dateFrom, string $dateTo): array
    {
        return self::trend($dateFrom, $dateTo);
    }

    public static function monthlyReport(string $dateFrom, string $dateTo): array
    {
        $stmt = db()->prepare("
            SELECT DATE_FORMAT(o.order_date, '%Y-%m') month, COUNT(DISTINCT o.id) orders, COALESCE(SUM(oi.quantity),0) quantity
            FROM orders o
            LEFT JOIN order_items oi ON oi.order_id = o.id
            WHERE o.order_date BETWEEN ? AND ?
            GROUP BY month
            ORDER BY month ASC
        ");
        $stmt->execute([$dateFrom, $dateTo]);
        return $stmt->fetchAll();
    }

    public static function productReport(string $dateFrom, string $dateTo): array
    {
        $stmt = db()->prepare("
            SELECT p.product_code, p.product_name, COUNT(DISTINCT oi.order_id) order_count, COALESCE(SUM(oi.quantity),0) total_qty
            FROM products p
            LEFT JOIN order_items oi ON oi.product_id = p.id
            LEFT JOIN orders o ON o.id = oi.order_id AND o.order_date BETWEEN ? AND ?
            GROUP BY p.id
            ORDER BY total_qty DESC
        ");
        $stmt->execute([$dateFrom, $dateTo]);
        return $stmt->fetchAll();
    }

    public static function customReport(string $dateFrom, string $dateTo): array
    {
        $pdo = db();
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT o.id) orders, COALESCE(SUM(oi.quantity),0) quantity FROM orders o LEFT JOIN order_items oi ON oi.order_id=o.id WHERE o.order_date BETWEEN ? AND ?");
        $stmt->execute([$dateFrom, $dateTo]);
        $totals = $stmt->fetch();

        return [
            'totals' => $totals,
            'daily' => self::dailyReport($dateFrom, $dateTo),
            'products' => self::productReport($dateFrom, $dateTo),
        ];
    }
}
