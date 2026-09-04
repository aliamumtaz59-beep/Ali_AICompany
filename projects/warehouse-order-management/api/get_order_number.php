<?php
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!current_user()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$date = $_GET['date'] ?? date('Y-m-d');
echo json_encode(['order_number' => generate_order_number(db(), $date)]);
