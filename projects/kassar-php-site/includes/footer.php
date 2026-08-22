<?php
$year = date('Y');

$footer_columns = [
    [
        'title' => 'Company',
        'links' => [
            ['href' => '/about.php', 'label' => 'About Armadio'],
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
            <img src="/assets/images/logo.png" alt="Armadio logo" width="40" height="40">
          </span>
          <span class="site-header__brand-name">Armadio</span>
        </a>
        <p class="site-footer__blurb">One platform to source, purchase, supply and shop — connecting trade buyers and retail customers with vetted brands.</p>
        <div class="site-footer__social">
          <a href="#" class="site-footer__social-link" aria-label="Armadio on Facebook">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06C2 17.08 5.66 21.23 10.44 22v-7.03H7.9v-2.91h2.54V9.85c0-2.51 1.49-3.9 3.77-3.9 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.44 2.91h-2.34V22C18.34 21.23 22 17.08 22 12.06z"/></svg>
          </a>
          <a href="#" class="site-footer__social-link" aria-label="Armadio on Instagram">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4.2"/><circle cx="17.3" cy="6.7" r="1" fill="currentColor" stroke="none"/></svg>
          </a>
          <a href="#" class="site-footer__social-link" aria-label="Armadio on TikTok">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16.6 5.2c-.8-.7-1.3-1.7-1.35-2.9h-3v13.1a2.6 2.6 0 1 1-1.8-2.48V9.72a5.6 5.6 0 1 0 4.8 5.54V9.5a7.1 7.1 0 0 0 4.1 1.3V7.8a4.4 4.4 0 0 1-2.75-2.6z"/></svg>
          </a>
          <a href="#" class="site-footer__social-link" aria-label="Armadio on Twitter">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 5.9c-.7.3-1.5.6-2.3.7.8-.5 1.4-1.3 1.7-2.3-.8.5-1.7.8-2.6 1a4.1 4.1 0 0 0-7 3.7A11.6 11.6 0 0 1 3.4 4.6a4 4 0 0 0 1.3 5.5c-.6 0-1.2-.2-1.8-.5v.1c0 2 1.4 3.7 3.3 4a4.2 4.2 0 0 1-1.9.1 4.1 4.1 0 0 0 3.9 2.9A8.3 8.3 0 0 1 2 18.6a11.7 11.7 0 0 0 6.3 1.8c7.5 0 11.7-6.3 11.7-11.7v-.5c.8-.6 1.5-1.3 2-2.3z"/></svg>
          </a>
        </div>
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
<button type="button" class="back-to-top" id="back-to-top" aria-label="Back to top">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="back-to-top__icon">
    <line x1="12" y1="19" x2="12" y2="5"></line>
    <polyline points="5 12 12 5 19 12"></polyline>
  </svg>
</button>
<script src="<?= e(asset_url('assets/js/cart.js')) ?>"></script>
<script src="<?= e(asset_url('assets/js/main.js')) ?>"></script>
</body>
</html>
