<?php
require __DIR__ . '/includes/bootstrap.php';
$page_title = 'About | Kassar';
$page_description = 'Kassar is a unified trading platform connecting wholesale buyers, retail shoppers and vetted brands under one roof.';
require __DIR__ . '/includes/header.php';

$values = [
    [
        'title' => 'One network, two markets',
        'description' => 'We built Kassar so a single sourcing relationship can serve both a trade account and a retail storefront — no duplicated effort.',
    ],
    [
        'title' => 'Vetted, not just listed',
        'description' => 'Every brand on Kassar passes a quality and compliance review before it reaches buyers or shoppers.',
    ],
    [
        'title' => 'Transparent by default',
        'description' => "Pricing, MOQs and lead times are agreed up front, whether you're ordering a pallet or a single item.",
    ],
];

$stats = [
    ['label' => 'Brands trading', 'value' => '60+'],
    ['label' => 'Categories covered', 'value' => '4'],
    ['label' => 'Pallets moved monthly', 'value' => '3,400'],
    ['label' => 'Avg. enquiry response', 'value' => '< 1 day'],
];
?>

<?= render_page_header('About Kassar', 'Trading, made for both sides of the counter', "Kassar started with a simple observation: wholesale buyers and retail shoppers often want the same brands — just in different quantities. So we built one platform for both.") ?>

<section class="section bg-ivory">
  <div class="container" style="max-width: 56rem;">
    <div class="reveal">
      <?= render_section_heading('Our Story', "Built by people who've sat on both sides of a purchase order", "Our founding team spent years sourcing for independent retailers and negotiating wholesale terms for growing brands. Kassar exists to remove the friction between the two — one sourcing pipeline, one quality bar, two ways to buy.") ?>
    </div>

    <div class="grid grid--3">
      <?php foreach ($values as $value): ?>
        <div class="reveal">
          <div class="value-card">
            <h3 class="value-card__title"><?= e($value['title']) ?></h3>
            <p class="value-card__desc"><?= e($value['description']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section bg-navy">
  <div class="container" style="max-width: 64rem;">
    <div class="reveal">
      <div class="stats-grid">
        <?php foreach ($stats as $stat): ?>
          <div>
            <p class="stats-grid__value"><?= e($stat['value']) ?></p>
            <p class="stats-grid__label"><?= e($stat['label']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<section class="section bg-ivory">
  <div class="container" style="max-width: 56rem; text-align: center;">
    <div class="reveal">
      <h2 style="font-family: var(--font-display); font-size: 1.875rem; font-weight: 500; color: var(--color-navy);">Curious how Kassar could work for you?</h2>
      <div class="cta-banner__actions" style="margin-top: 2rem;">
        <?= render_button('Buy in Bulk', '/buy-in-bulk.php', 'primary') ?>
        <?= render_button('Supply to Us', '/supply-to-us.php', 'ghost') ?>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
