<?php
require __DIR__ . '/includes/bootstrap.php';
$page_title = 'Contact | Armadio';
$page_description = "Get in touch with Armadio — whether you're buying in bulk, supplying a brand, or shopping retail.";
require __DIR__ . '/includes/header.php';

$modes = [
    ['id' => 'buy', 'label' => 'I want to buy'],
    ['id' => 'supply', 'label' => "I want to supply"],
    ['id' => 'retail', 'label' => "I'm a retail customer"],
];

$fieldsByMode = [
    'buy' => [
        ['name' => 'companyName', 'label' => 'Company Name', 'required' => true, 'span' => 'half'],
        ['name' => 'contactName', 'label' => 'Contact Name', 'required' => true, 'span' => 'half'],
        ['name' => 'email', 'label' => 'Work Email', 'type' => 'email', 'required' => true, 'span' => 'half'],
        ['name' => 'phone', 'label' => 'Phone Number', 'type' => 'tel', 'span' => 'half'],
        ['name' => 'quantity', 'label' => 'Estimated Quantity', 'placeholder' => 'e.g. 500 units', 'span' => 'half'],
        ['name' => 'category', 'label' => 'Category of Interest', 'placeholder' => 'e.g. Grocery & FMCG', 'span' => 'half'],
        ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'span' => 'full', 'required' => true],
    ],
    'supply' => [
        ['name' => 'brandName', 'label' => 'Brand / Company Name', 'required' => true, 'span' => 'half'],
        ['name' => 'contactName', 'label' => 'Contact Name', 'required' => true, 'span' => 'half'],
        ['name' => 'email', 'label' => 'Work Email', 'type' => 'email', 'required' => true, 'span' => 'half'],
        ['name' => 'phone', 'label' => 'Phone Number', 'type' => 'tel', 'span' => 'half'],
        ['name' => 'category', 'label' => 'Product Category', 'placeholder' => 'e.g. Beauty & Personal Care', 'span' => 'full'],
        ['name' => 'message', 'label' => 'Tell us about your brand', 'type' => 'textarea', 'span' => 'full', 'required' => true],
    ],
    'retail' => [
        ['name' => 'fullName', 'label' => 'Full Name', 'required' => true, 'span' => 'half'],
        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true, 'span' => 'half'],
        ['name' => 'orderNumber', 'label' => 'Order Number (if applicable)', 'span' => 'half'],
        ['name' => 'phone', 'label' => 'Phone Number', 'type' => 'tel', 'span' => 'half'],
        ['name' => 'message', 'label' => 'How can we help?', 'type' => 'textarea', 'span' => 'full', 'required' => true],
    ],
];

$enquiryTypeByMode = [
    'buy' => 'contact-buyer',
    'supply' => 'contact-supplier',
    'retail' => 'contact-retail',
];
?>

<?= render_page_header('Get in Touch', 'How can we help?', "Choose the option that best describes you and we'll route your message to the right team.", 'assets/images/photos/hero-contact.jpg') ?>

<section class="section bg-ivory">
  <div class="container" style="max-width: 48rem;">
    <div class="contact-panel">
      <div class="mode-tabs">
        <?php foreach ($modes as $i => $mode): ?>
          <button type="button" class="mode-tab<?= $i === 0 ? ' is-active' : '' ?>" data-contact-mode="<?= e($mode['id']) ?>"><?= e($mode['label']) ?></button>
        <?php endforeach; ?>
      </div>

      <div style="margin-top: 2rem;">
        <?php foreach ($modes as $i => $mode): ?>
          <div data-contact-panel="<?= e($mode['id']) ?>"<?= $i === 0 ? '' : ' hidden' ?>>
            <?= render_enquiry_form($enquiryTypeByMode[$mode['id']], $fieldsByMode[$mode['id']], 'Send Message') ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
