<?php

require_once __DIR__ . '/stripe-config.php';

class StripeApiException extends RuntimeException
{
}

/**
 * Minimal Stripe REST API client using cURL — no SDK/Composer required, to
 * match the rest of this plain-PHP site. Handles the handful of endpoints
 * the storefront needs (Checkout Sessions).
 */
function stripe_request(string $method, string $path, array $params = []): array
{
    if (STRIPE_SECRET_KEY === '') {
        throw new StripeApiException('Stripe is not configured yet. Add your secret key to includes/stripe-config.php.');
    }

    $method = strtoupper($method);
    $url = 'https://api.stripe.com/v1/' . ltrim($path, '/');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($method !== 'GET' && $params) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    } elseif ($method === 'GET' && $params) {
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
    }
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . STRIPE_SECRET_KEY],
        CURLOPT_TIMEOUT => 20,
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new StripeApiException('Could not reach Stripe: ' . $error);
    }

    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $body = json_decode($response, true);
    if (!is_array($body)) {
        throw new StripeApiException('Stripe returned an unexpected response.');
    }

    if ($status >= 400) {
        throw new StripeApiException($body['error']['message'] ?? 'Stripe request failed.');
    }

    return $body;
}

/**
 * Verifies a Stripe webhook's signature and decodes its payload, following
 * Stripe's documented manual-verification algorithm (used here instead of
 * the SDK's Webhook::constructEvent to avoid a Composer dependency):
 * https://docs.stripe.com/webhooks#verify-manually
 */
function stripe_verify_webhook(string $payload, string $sigHeader): array
{
    if (STRIPE_WEBHOOK_SECRET === '') {
        throw new StripeApiException('Webhook secret is not configured.');
    }

    $parts = [];
    foreach (explode(',', $sigHeader) as $piece) {
        [$key, $value] = array_pad(explode('=', trim($piece), 2), 2, null);
        if ($key !== null) {
            $parts[$key] = $value;
        }
    }

    $timestamp = $parts['t'] ?? null;
    $signature = $parts['v1'] ?? null;
    if (!$timestamp || !$signature) {
        throw new StripeApiException('Missing Stripe-Signature header.');
    }

    if (abs(time() - (int) $timestamp) > 300) {
        throw new StripeApiException('Stripe signature timestamp is too old.');
    }

    $expected = hash_hmac('sha256', $timestamp . '.' . $payload, STRIPE_WEBHOOK_SECRET);
    if (!hash_equals($expected, $signature)) {
        throw new StripeApiException('Stripe signature verification failed.');
    }

    $event = json_decode($payload, true);
    if (!is_array($event)) {
        throw new StripeApiException('Invalid webhook payload.');
    }

    return $event;
}
