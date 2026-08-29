<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/stripe.php';

header('Content-Type: application/json');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);

if (!is_array($payload) || empty($payload['items']) || !is_array($payload['items'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Your cart is empty.']);
    exit;
}

// Line items are rebuilt entirely from the server-side product catalogue —
// the client only tells us which slugs and quantities it wants. Never trust
// a price sent from the browser.
$lineItems = [];
foreach ($payload['items'] as $item) {
    $slug = is_array($item) ? (string) ($item['slug'] ?? '') : '';
    $quantity = is_array($item) ? (int) ($item['quantity'] ?? 0) : 0;

    if ($slug === '' || $quantity < 1 || $quantity > 99) {
        continue;
    }

    $product = get_product_by_slug($products, $slug);
    if (!$product) {
        continue;
    }

    $lineItems[] = [
        'quantity' => $quantity,
        'price_data' => [
            'currency' => 'gbp',
            'unit_amount' => (int) round($product['price'] * 100),
            'product_data' => [
                'name' => $product['name'],
            ],
        ],
    ];
}

if (empty($lineItems)) {
    http_response_code(400);
    echo json_encode(['error' => 'Your cart is empty or those items are no longer available.']);
    exit;
}

$baseUrl = site_base_url();

try {
    $session = stripe_request('POST', 'checkout/sessions', [
        'mode' => 'payment',
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'shipping_address_collection' => ['allowed_countries' => ['GB']],
        'success_url' => $baseUrl . '/checkout-success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $baseUrl . '/cart.php',
    ]);
} catch (StripeApiException $e) {
    http_response_code(502);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

echo json_encode(['url' => $session['url']]);
