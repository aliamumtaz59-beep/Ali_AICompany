<?php
require __DIR__ . '/includes/bootstrap.php';
$page_title = 'Buy in Bulk | Armadio';
$page_description = 'Request a wholesale quote from Armadio for palletised, bulk and trade-account orders across our brand network.';
require __DIR__ . '/includes/header.php';

$bulkFields = [
    ['name' => 'companyName', 'label' => 'Company Name', 'required' => true, 'span' => 'half'],
    ['name' => 'contactName', 'label' => 'Contact Name', 'required' => true, 'span' => 'half'],
    ['name' => 'email', 'label' => 'Work Email', 'type' => 'email', 'required' => true, 'span' => 'half'],
    ['name' => 'phone', 'label' => 'Phone Number', 'type' => 'tel', 'span' => 'half'],
    ['name' => 'brand', 'label' => 'Brand of Interest', 'placeholder' => 'e.g. DeWalt', 'span' => 'half'],
    ['name' => 'product', 'label' => 'Product / SKU', 'placeholder' => 'e.g. 18V Combi Drill', 'span' => 'half'],
    ['name' => 'quantity', 'label' => 'Estimated Quantity', 'type' => 'number', 'required' => true, 'placeholder' => 'e.g. 500 units', 'span' => 'half'],
    ['name' => 'frequency', 'label' => 'Order Frequency', 'placeholder' => 'One-off / Monthly / Quarterly', 'span' => 'half'],
    ['name' => 'message', 'label' => 'Additional Details', 'type' => 'textarea', 'span' => 'full', 'placeholder' => 'Delivery location, target price, timelines…'],
];

$perks = [
    ['title' => 'Volume-based pricing', 'description' => 'Tiered pricing that improves as order quantities scale.'],
    ['title' => 'Dedicated account manager', 'description' => 'One point of contact for sourcing, logistics and reorders.'],
    ['title' => 'Flexible lead times', 'description' => 'From express dispatch to scheduled seasonal drops.'],
];
?>

<?= render_page_header('B2B Wholesale', 'Buy in bulk, backed by a trusted network', 'Submit your requirements and our sourcing team will confirm pricing, MOQs and lead times — typically within one business day.', 'assets/images/photos/hero-buy-in-bulk.jpg') ?>

<section class="section bg-ivory">
  <div class="container" style="max-width: 72rem;">
    <div class="grid grid--3">
      <?php foreach ($perks as $perk): ?>
        <div class="reveal">
          <div class="perk-card">
            <h3 class="perk-card__title"><?= e($perk['title']) ?></h3>
            <p class="perk-card__desc"><?= e($perk['description']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="form-panel">
      <?= render_section_heading('Request a Quote', "Tell us what you're looking to order", 'Fill in as much detail as you can — the more context we have, the faster we can confirm terms.') ?>
      <?= render_enquiry_form('bulk-enquiry', $bulkFields, 'Request Quote') ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
