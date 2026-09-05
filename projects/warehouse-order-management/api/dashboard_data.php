<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Order.php';

header('Content-Type: application/json');

if (!current_user()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$range = $_GET['range'] ?? 'this_month';
[$dateFrom, $dateTo] = resolve_date_range($range, $_GET['date_from'] ?? null, $_GET['date_to'] ?? null);

$trend = Order::trend($dateFrom, $dateTo);
$topProducts = Order::topProducts($dateFrom, $dateTo);
$shopSales = Order::salesByShop($dateFrom, $dateTo);

echo json_encode([
    'date_from' => $dateFrom,
    'date_to' => $dateTo,
    'date_from_label' => format_date($dateFrom),
    'date_to_label' => format_date($dateTo),
    'trend' => array_map(fn($r) => [
        'label' => date('d-M', strtotime($r['order_date'])),
        'orders' => (int) $r['orders'],
        'date' => $r['order_date'],
    ], $trend),
    'top_products' => array_map(fn($p) => [
        'id' => (int) $p['id'],
        'label' => $p['product_code'] . ' - ' . $p['product_name'],
        'qty' => (float) $p['total_qty'],
        'order_count' => (int) $p['order_count'],
    ], $topProducts),
    'shop_sales' => array_map(fn($s) => [
        'id' => (int) $s['id'],
        'label' => $s['shop_name'],
        'qty' => (float) $s['total_qty'],
    ], $shopSales),
]);
