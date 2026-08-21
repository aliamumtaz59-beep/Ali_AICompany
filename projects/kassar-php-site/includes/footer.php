<?php
$year = date('Y');

$footer_columns = [
    [
        'title' => 'Company',
        'links' => [
            ['href' => '/about.php', 'label' => 'About Kassar'],
            ['href' => '/categories.php', 'label' => 'Categories'],
            ['href' => '/brands.php', 'label' => 'Our Brands'],
            ['href' => '/contact.php', 'label' => 'Contact'],
        ],
    ],
    [
        'title' => 'Trade',
        'links' => [
            ['href' => '/buy-in-bulk.php', 'label' => 'Buy in Bulk'],
            ['href' => '/supply-to-us.php', 'label' => 'Supply to Us'],
        ],
    ],
    [
        'title' => 'Shop',
        'links' => [
            ['href' => '/shop.php', 'label' => 'Retail Shop'],
            ['href' => '/contact.php', 'label' => 'Customer Support'],
        ],
    ],
];
?>
</main>
<footer class="site-footer">
  <div class="site-footer__inner">
    <div class="site-footer__grid">
      <div class="site-footer__brand-col">
        <a href="/index.php" class="site-header__brand">
          <span class="site-header__logo">
            <img src="/assets/images/logo.svg" alt="Kassar logo" width="40" height="40">
          </span>
          <span class="site-header__brand-name">Kassar</span>
        </a>
        <p class="site-footer__blurb">One platform to source, purchase, supply and shop — connecting trade buyers and retail customers with vetted brands.</p>
      </div>

      <?php foreach ($footer_columns as $col): ?>
        <div>
          <h3 class="site-footer__col-title"><?= e($col['title']) ?></h3>
          <ul class="site-footer__col-links">
            <?php foreach ($col['links'] as $link): ?>
              <li><a href="<?= e($link['href']) ?>"><?= e($link['label']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="site-footer__bottom">
      <p>&copy; <?= e((string) $year) ?> Armadio Trading Ltd. All rights reserved.</p>
      <nav class="site-footer__bottom-nav">
        <?php foreach (array_slice($nav_links, 0, 4) as $link): ?>
          <a href="<?= e($link['href']) ?>"><?= e($link['label']) ?></a>
        <?php endforeach; ?>
      </nav>
    </div>
  </div>
</footer>
<script src="/assets/js/cart.js"></script>
<script src="/assets/js/main.js"></script>
</body>
</html>
