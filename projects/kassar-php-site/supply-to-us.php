<?php
require __DIR__ . '/includes/bootstrap.php';
$page_title = 'Supply to Us | Kassar';
$page_description = 'Partner with Kassar as a brand or manufacturer and reach both wholesale buyers and retail shoppers through one platform.';
require __DIR__ . '/includes/header.php';

$supplyFields = [
    ['name' => 'brandName', 'label' => 'Brand / Company Name', 'required' => true, 'span' => 'half'],
    ['name' => 'contactName', 'label' => 'Contact Name', 'required' => true, 'span' => 'half'],
    ['name' => 'email', 'label' => 'Work Email', 'type' => 'email', 'required' => true, 'span' => 'half'],
    ['name' => 'phone', 'label' => 'Phone Number', 'type' => 'tel', 'span' => 'half'],
    ['name' => 'category', 'label' => 'Product Category', 'placeholder' => 'e.g. Beauty & Personal Care', 'span' => 'half'],
    ['name' => 'productionCapacity', 'label' => 'Monthly Production Capacity', 'placeholder' => 'e.g. 10,000 units', 'span' => 'half'],
    ['name' => 'message', 'label' => 'Tell us about your brand', 'type' => 'textarea', 'span' => 'full', 'required' => true, 'placeholder' => 'Product range, certifications, current distribution…'],
];

$reasons = [
    ['title' => 'Reach two markets at once', 'description' => 'List once and reach both trade buyers ordering in bulk and retail shoppers browsing the storefront.'],
    ['title' => 'Compliance & quality checks', 'description' => 'Our team supports you through labelling, certification and quality review before you go live.'],
    ['title' => 'Transparent terms', 'description' => 'Clear commission structures and payment terms agreed before your first order ships.'],
];
?>

<?= render_page_header('B2B Partnership', 'Supply your brand through Kassar', 'Join a growing network of manufacturers and brands trading across power tools, beauty, grocery and lifestyle categories.') ?>

<section class="section bg-ivory">
  <div class="container" style="max-width: 72rem;">
    <div class="grid grid--3">
      <?php foreach ($reasons as $reason): ?>
        <div class="reveal">
          <div class="perk-card">
            <h3 class="perk-card__title"><?= e($reason['title']) ?></h3>
            <p class="perk-card__desc"><?= e($reason['description']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="form-panel">
      <?= render_section_heading('Partner Application', 'Tell us about your brand', 'Share a few details and our partnerships team will follow up to discuss fit, categories and next steps.') ?>
      <?= render_enquiry_form('supplier-application', $supplyFields, 'Submit Application') ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
