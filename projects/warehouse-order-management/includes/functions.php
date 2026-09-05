<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/permissions.php';

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
 * Appends a cache-busting query string (the file's last-modified time) to a
 * local asset path, so browsers (mobile especially) always fetch the latest
 * CSS/JS after a deploy instead of serving a stale cached copy indefinitely.
 */
function asset_url(string $path): string
{
    $full = __DIR__ . '/../' . $path;
    $version = is_file($full) ? filemtime($full) : time();
    return $path . '?v=' . $version;
}

function is_ajax_request(): bool
{
    return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
}

/**
 * Resolves a dashboard date-range shortcut (today/this_week/this_month/...) into
 * concrete [dateFrom, dateTo] strings. For 'custom', uses the given from/to as-is.
 */
function resolve_date_range(string $range, ?string $customFrom, ?string $customTo): array
{
    $today = date('Y-m-d');
    switch ($range) {
        case 'today':
            return [$today, $today];
        case 'yesterday':
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            return [$yesterday, $yesterday];
        case 'this_week':
            return [date('Y-m-d', strtotime('monday this week')), $today];
        case 'last_month':
            return [date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('last day of last month'))];
        case 'custom':
            return [$customFrom ?: date('Y-m-01'), $customTo ?: $today];
        case 'this_month':
        default:
            return [date('Y-m-01'), $today];
    }
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
