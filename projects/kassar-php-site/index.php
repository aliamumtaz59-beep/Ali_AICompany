<?php
require __DIR__ . '/includes/bootstrap.php';
$page_title = 'Kassar — Source. Purchase. Supply. Shop.';
$page_description = "Kassar is a unified B2B and B2C trading platform — buy in bulk as a wholesale partner, or shop retail as a customer, all in one place.";
require __DIR__ . '/includes/header.php';

$marqueeItems = ["SOURCE", "PURCHASE", "SUPPLY", "SHOP"];
$marqueeDoubled = array_merge($marqueeItems, $marqueeItems);

$twoSides = [
    [
        "href" => "/buy-in-bulk.php",
        "tag" => "For Wholesale Buyers",
        "title" => "Buy in Bulk",
        "description" => "Palletised orders, negotiated MOQs and dedicated trade accounts across power tools, beauty, grocery and lifestyle categories.",
        "points" => ["Volume pricing", "Dedicated account manager", "Flexible lead times"],
        "cta" => "Start a bulk enquiry",
        "dark" => true,
        "photo" => "assets/images/photos/two-sides-bulk.jpg",
    ],
    [
        "href" => "/shop.php",
        "tag" => "For Retail Shoppers",
        "title" => "Shop Retail",
        "description" => "A curated storefront of the same trusted brands, sold in everyday quantities with fast, simple checkout.",
        "points" => ["Single-unit pricing", "Curated collections", "Secure checkout"],
        "cta" => "Browse the shop",
        "dark" => false,
        "photo" => "assets/images/photos/two-sides-retail.jpg",
    ],
];
?>

<section class="hero">
  <div class="hero__photo"<?= photo_style('assets/images/photos/hero-home.jpg') ?>></div>
  <div class="hero__inner">
    <div>
      <span class="hero__badge">Trade &amp; Retail, Unified</span>
      <h1 class="hero__title">One trading platform.<br><span class="italic text-amber-light">Two ways to buy.</span></h1>
      <p class="hero__lede">Whether you're stocking a warehouse or filling a basket, Kassar puts the same vetted brands within reach — bulk pricing for trade accounts, simple checkout for everyone else.</p>
      <div class="hero__actions">
        <?= render_button('Buy in Bulk (B2B)', '/buy-in-bulk.php', 'primary') ?>
        <?= render_button('Shop Retail (B2C)', '/shop.php', 'secondary') ?>
      </div>
    </div>
  </div>
</section>

<div class="marquee">
  <div class="marquee__track">
    <?php foreach ($marqueeDoubled as $item): ?>
      <span class="marquee__item"><?= e($item) ?> <span>◆</span></span>
    <?php endforeach; ?>
  </div>
</div>

<section class="section bg-ivory">
  <div class="container">
    <div class="reveal">
      <?= render_section_heading('Two sides, one platform', "However you trade, Kassar has a lane for you.", "Whether you're stocking a warehouse or filling a basket, the same vetted brands and supply chain stand behind every order.") ?>
    </div>

    <div class="two-sides__grid">
      <?php foreach ($twoSides as $side): ?>
        <div class="reveal">
          <div class="side-card <?= $side['dark'] ? 'side-card--dark' : 'side-card--light' ?>">
            <div class="side-card__photo"<?= photo_style($side['photo']) ?>></div>
            <span class="side-card__tag"><?= e($side['tag']) ?></span>
            <h3 class="side-card__title"><?= e($side['title']) ?></h3>
            <p class="side-card__description"><?= e($side['description']) ?></p>
            <ul class="side-card__points">
              <?php foreach ($side['points'] as $point): ?>
                <li><span>◆</span> <?= e($point) ?></li>
              <?php endforeach; ?>
            </ul>
            <a href="<?= e($side['href']) ?>" class="side-card__cta"><?= e($side['cta']) ?> →</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="partners-strip">
  <div class="container">
    <p class="partners-strip__label">Trusted by brands across our trading network</p>
    <div class="partners-strip__list">
      <?php foreach ($partners as $partner): ?>
        <span><?= e($partner) ?></span>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section bg-ivory">
  <div class="container">
    <div class="reveal section-flex-header">
      <?= render_section_heading('Categories', 'Built around the categories that move.', 'A snapshot of the trading categories Kassar specialises in — each backed by vetted brands ready for bulk or retail.') ?>
      <?= render_button('View all categories', '/categories.php', 'ghost') ?>
    </div>

    <div class="grid grid--3">
      <?php foreach (array_slice($categories, 0, 3) as $category): ?>
        <div class="reveal">
          <a href="/categories.php#<?= e($category['slug']) ?>" class="category-card">
            <div class="category-card__media <?= e($category['gradient']) ?>"<?= photo_style($category['photo']) ?>>
              <span class="category-card__stat"><?= e($category['stat']['value']) ?> <?= e($category['stat']['label']) ?></span>
            </div>
            <div class="category-card__body">
              <h3 class="category-card__title"><?= e($category['name']) ?></h3>
              <p class="category-card__tagline"><?= e($category['tagline']) ?></p>
              <p class="category-card__desc"><?= e($category['description']) ?></p>
              <span class="category-card__link">Explore category →</span>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section bg-ivory-dim">
  <div class="container">
    <div class="reveal">
      <?= render_section_heading('How it works', 'From enquiry to reorder, in four steps.', null, 'center') ?>
    </div>
    <div class="grid grid--4">
      <?php foreach ($how_it_works as $item): ?>
        <div class="reveal">
          <div class="how-card">
            <span class="how-card__step"><?= e($item['step']) ?></span>
            <h3 class="how-card__title"><?= e($item['title']) ?></h3>
            <p class="how-card__desc"><?= e($item['description']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?= render_cta_banner('assets/images/photos/cta-banner.jpg') ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
