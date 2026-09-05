<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/Attachment.php';
require_permission('orders.manage');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    Attachment::delete((int)$_POST['attachment_id']);
    flash('success', 'Attachment removed.');
}

redirect('order_form.php?id=' . (int)($_POST['order_id'] ?? 0));
