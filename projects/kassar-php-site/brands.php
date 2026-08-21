<?php
require __DIR__ . '/includes/bootstrap.php';
$page_title = 'Brands | Kassar';
$page_description = 'Explore the vetted brands trading through Kassar across power tools, beauty, grocery and lifestyle categories.';
require __DIR__ . '/includes/header.php';
?>

<?= render_page_header('Our Network', 'Brands trading on Kassar', "Every brand on Kassar is vetted for quality, compliance and reliable supply — whether you're buying a pallet or a single unit.") ?>

<section class="section bg-ivory">
  <div class="container" data-brands-directory>
    <div class="brands-filter">
      <div class="brands-filter__cats">
        <?php foreach ($brand_categories as $i => $cat): ?>
          <button type="button" class="brands-filter__cat<?= $i === 0 ? ' is-active' : '' ?>" data-brand-cat="<?= e($cat) ?>"><?= e($cat) ?></button>
        <?php endforeach; ?>
      </div>
      <input type="search" class="brands-filter__search" data-brand-search placeholder="Search brands…">
    </div>

    <p class="brands-count" data-brand-count><?= count($brands) ?> brands found</p>

    <div class="grid grid--3">
      <?php foreach ($brands as $brand): ?>
        <a
          href="/brand.php?slug=<?= e(urlencode($brand['slug'])) ?>"
          class="brand-card"
          data-brand-card
          data-category="<?= e($brand['category']) ?>"
          data-name="<?= e($brand['name']) ?>"
          data-description="<?= e($brand['description']) ?>"
        >
          <div class="brand-card__head">
            <span class="brand-card__avatar <?= e($brand['color']) ?>"><?= e($brand['initials']) ?></span>
            <div>
              <h3 class="brand-card__name"><?= e($brand['name']) ?></h3>
              <p class="brand-card__origin"><?= e($brand['origin']) ?></p>
            </div>
          </div>
          <p class="brand-card__desc"><?= e($brand['description']) ?></p>
          <div class="brand-card__meta">
            <span class="brand-card__cat"><?= e($brand['category']) ?></span>
            <span class="brand-card__moq">MOQ: <?= e($brand['moq']) ?></span>
          </div>
          <span class="brand-card__link">View brand →</span>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="brands-empty" data-brand-empty hidden>
      No brands match your search. Try a different category or keyword.
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
