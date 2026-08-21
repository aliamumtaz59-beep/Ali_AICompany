<?php

declare(strict_types=1);

header('Content-Type: application/json');

// Where enquiries are recorded. To send email instead, replace the file
// write below with a mail()/PHPMailer call to your sales inbox.
const ENQUIRY_LOG = __DIR__ . '/../storage/enquiries.log';
const NOTIFY_EMAIL = ''; // set to an address to also send via mail()

const EMAIL_RE = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);

if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request body.']);
    exit;
}

$type = $payload['type'] ?? null;
unset($payload['type']);

if (!is_string($type) || $type === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Enquiry type is required.']);
    exit;
}

foreach ($payload as $key => $value) {
    if (stripos((string) $key, 'email') !== false && $value !== '' && !preg_match(EMAIL_RE, (string) $value)) {
        http_response_code(400);
        echo json_encode(['error' => 'Please provide a valid email address.']);
        exit;
    }
}

$entry = [
    'type' => $type,
    'received_at' => date('c'),
    'fields' => $payload,
];

$storageDir = dirname(ENQUIRY_LOG);
if (!is_dir($storageDir)) {
    @mkdir($storageDir, 0775, true);
}
@file_put_contents(ENQUIRY_LOG, json_encode($entry) . PHP_EOL, FILE_APPEND | LOCK_EX);

if (NOTIFY_EMAIL !== '') {
    $subject = 'New Kassar enquiry: ' . $type;
    $body = "New enquiry received\n\nType: {$type}\n\n";
    foreach ($payload as $key => $value) {
        $body .= "{$key}: {$value}\n";
    }
    @mail(NOTIFY_EMAIL, $subject, $body);
}

echo json_encode(['ok' => true]);
