<?php
require_once __DIR__ . '/auth.php';

function e(?string $v): string
{
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(400);
        die('Invalid CSRF token.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function generate_order_number(PDO $pdo, ?string $date = null): string
{
    $date = $date ?: date('Y-m-d');
    $prefix = 'ORD-' . date('Ymd', strtotime($date)) . '-';
    $stmt = $pdo->prepare("SELECT order_number FROM orders WHERE order_number LIKE ? ORDER BY order_number DESC LIMIT 1");
    $stmt->execute([$prefix . '%']);
    $last = $stmt->fetchColumn();
    $seq = $last ? ((int)substr($last, -3)) + 1 : 1;
    return $prefix . str_pad((string)$seq, 3, '0', STR_PAD_LEFT);
}

function format_date(?string $date): string
{
    if (!$date) return '';
    return date('d-M-Y', strtotime($date));
}

/**
 * Flattens a multi-file $_FILES['field'] entry into a list of individual file arrays,
 * skipping empty slots (no file selected).
 */
function normalize_files(array $files): array
{
    if (empty($files['name'])) return [];
    $names = (array) $files['name'];
    $normalized = [];
    foreach ($names as $i => $name) {
        if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
        $normalized[] = [
            'name' => $name,
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i],
        ];
    }
    return $normalized;
}
