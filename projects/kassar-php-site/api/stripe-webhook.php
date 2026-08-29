<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/stripe.php';

header('Content-Type: application/json');

// This is the source of truth for "did the customer actually pay" — the
// success_url redirect in api/checkout.php is only for the customer's UX
// and can be missed entirely (closed tab, dropped connection, browser
// crash) even after a successful payment. Stripe retries this webhook
// until it gets a 2xx, so it's safe to rely on for order records.

$payload = file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    $event = stripe_verify_webhook($payload, $sigHeader);
} catch (StripeApiException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

if (($event['type'] ?? '') === 'checkout.session.completed') {
    $session = $event['data']['object'] ?? [];
    $sessionId = (string) ($session['id'] ?? '');

    $ordersLog = __DIR__ . '/../storage/orders.log';

    $alreadyLogged = false;
    if ($sessionId !== '' && is_file($ordersLog)) {
        $existing = file_get_contents($ordersLog);
        $alreadyLogged = $existing !== false && strpos($existing, '"' . $sessionId . '"') !== false;
    }

    if (!$alreadyLogged) {
        $entry = [
            'session_id' => $sessionId,
            'received_at' => date('c'),
            'amount_total' => $session['amount_total'] ?? null,
            'currency' => $session['currency'] ?? null,
            'customer_email' => $session['customer_details']['email'] ?? null,
            'payment_status' => $session['payment_status'] ?? null,
            'shipping' => $session['shipping_details'] ?? null,
        ];

        $storageDir = dirname($ordersLog);
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0775, true);
        }
        @file_put_contents($ordersLog, json_encode($entry) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

echo json_encode(['received' => true]);
