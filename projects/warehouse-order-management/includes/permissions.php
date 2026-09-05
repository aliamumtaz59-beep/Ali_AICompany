<?php

const PERMISSIONS = [
    'products.view' => 'View Products',
    'products.manage' => 'Add/Edit Products',
    'orders.view' => 'View Orders',
    'orders.manage' => 'Add/Edit Orders',
    'orders.delete' => 'Delete Orders',
    'shops.view' => 'View Shops',
    'shops.manage' => 'Add/Edit Shops',
    'reports.view' => 'View Reports',
    'users.manage' => 'Manage Users',
];

/**
 * Admins implicitly have every permission. Other users only have what's
 * explicitly stored in their permissions list.
 */
function user_has_permission(string $permission): bool
{
    $user = current_user();
    if (!$user) return false;
    if ($user['role'] === 'admin') return true;
    return in_array($permission, $user['permissions'] ?? [], true);
}

function require_permission(string $permission): void
{
    require_login();
    if (!user_has_permission($permission)) {
        http_response_code(403);
        die('Access denied. You do not have permission to perform this action.');
    }
}
