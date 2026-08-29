<?php

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/stripe.php';

$sessionId = (string) ($_GET['session_id'] ?? '');
$session = null;
$error = null;

if ($sessionId !== '') {
    try {
        $session = stripe_request('GET', 'checkout/sessions/' . urlencode($sessionId));
    } catch (StripeApiException $e) {
        $error = $e->getMessage();
    }
}

$paid = $session && ($session['payment_status'] ?? '') === 'paid';

$page_title = 'Order Confirmed | Armadio';
require __DIR__ . '/includes/header.php';
?>

<section class="section bg-ivory" style="padding-top: 8rem; min-height: 50vh;">
  <div class="container" style="max-width: 40rem;">
    <?php if ($paid): ?>
      <div class="cart-success">
        <p class="cart-success__title">Thank you — your order is confirmed.</p>
        <p class="cart-success__desc">
          A receipt has been sent to <?= e($session['customer_details']['email'] ?? 'your email address') ?>.
          We'll be in touch with shipping updates shortly.
        </p>
      </div>
    <?php elseif ($error): ?>
      <div class="cart-empty">
        <p class="cart-empty__title">We couldn't confirm your order</p>
        <p class="cart-empty__desc">Something went wrong retrieving your payment status. If you were charged and don't hear from us within one business day, please contact support and quote reference <?= e($sessionId) ?>.</p>
      </div>
    <?php else: ?>
      <div class="cart-empty">
        <p class="cart-empty__title">Payment not completed</p>
        <p class="cart-empty__desc">It looks like this order wasn't completed. If you'd still like to check out, return to your cart and try again.</p>
      </div>
    <?php endif; ?>
    <p style="margin-top: 1.5rem; text-align: center;"><?= render_button('Continue Shopping', '/shop.php', 'primary') ?></p>
  </div>
</section>

<?php if ($paid): ?>
<script>
  try { window.localStorage.removeItem('armadio-cart'); } catch (e) {}
</script>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
