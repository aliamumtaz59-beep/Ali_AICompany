<?php
/**
 * Expects $page_title and $page_description to be set by the including page.
 * Requires includes/bootstrap.php to already be loaded (for $nav_links).
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title ?? 'Armadio — Source. Purchase. Supply. Shop.') ?></title>
<meta name="description" content="<?= e($page_description ?? "Armadio is a unified B2B and B2C trading platform — buy in bulk as a wholesale partner, or shop retail as a customer, all in one place.") ?>">
<link rel="icon" href="/assets/images/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header" id="site-header">
  <div class="site-header__row">
    <a href="/index.php" class="site-header__brand">
      <span class="site-header__logo">
        <img src="/assets/images/logo.png" alt="Armadio logo" width="40" height="40">
      </span>
      <span class="site-header__brand-name">Armadio</span>
    </a>

    <nav class="site-header__nav">
      <?php foreach ($nav_links as $link): ?>
        <a href="<?= e($link['href']) ?>" class="site-header__link<?= is_nav_active($link['href']) ? ' is-active' : '' ?>"><?= e($link['label']) ?></a>
      <?php endforeach; ?>
    </nav>

    <div class="site-header__actions">
      <a href="/cart.php" class="cart-icon" aria-label="Cart">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="cart-icon__svg">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="19" cy="21" r="1"></circle>
          <path d="M2.25 3h1.5l2.4 12.15a2 2 0 0 0 2 1.6h8.4a2 2 0 0 0 2-1.6L21.75 7H5.25"></path>
        </svg>
        <span class="cart-icon__badge" id="cart-badge" hidden>0</span>
      </a>
      <a href="/contact.php" class="btn btn--primary site-header__cta">Get in Touch</a>
      <button type="button" class="site-header__toggle" id="nav-toggle" aria-label="Toggle menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>

  <div class="site-header__mobile" id="mobile-nav" hidden>
    <nav class="site-header__mobile-nav">
      <?php foreach ($nav_links as $link): ?>
        <a href="<?= e($link['href']) ?>" class="site-header__mobile-link<?= is_nav_active($link['href']) ? ' is-active' : '' ?>"><?= e($link['label']) ?></a>
      <?php endforeach; ?>
    </nav>
    <a href="/contact.php" class="btn btn--primary site-header__mobile-cta">Get in Touch</a>
  </div>
</header>
<main>
