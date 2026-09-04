<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../models/Attachment.php';
require_login();

$attachment = Attachment::find((int)($_GET['id'] ?? 0));
if (!$attachment) {
    http_response_code(404);
    exit('File not found.');
}

$path = Attachment::path($attachment);
if (!is_file($path)) {
    http_response_code(404);
    exit('File not found.');
}

$safeName = str_replace(['"', "\r", "\n"], '', $attachment['original_name']);

header('Content-Type: ' . ($attachment['mime_type'] ?: 'application/octet-stream'));
header('Content-Length: ' . filesize($path));
header('Content-Disposition: inline; filename="' . $safeName . '"');
readfile($path);
exit;
