<?php

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function money(float $amount): string
{
    return '£' . number_format($amount, 2);
}

function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return $path ?: '/';
}

function is_nav_active(string $href): bool
{
    $current = current_path();
    $target = parse_url($href, PHP_URL_PATH);
    if ($target === '/index.php' || $target === '/') {
        return $current === '/' || $current === '/index.php';
    }
    return $current === $target || str_starts_with($current, rtrim($target, '/') . '/');
}

function render_button(string $label, string $href, string $variant = 'primary', string $extraClass = ''): string
{
    $class = 'btn btn--' . $variant;
    if ($extraClass !== '') {
        $class .= ' ' . $extraClass;
    }
    return '<a href="' . e($href) . '" class="' . e($class) . '">' . e($label) . '</a>';
}

function render_section_heading(?string $eyebrow, string $title, ?string $description = null, string $align = 'left', bool $dark = false): string
{
    $html = '<div class="section-heading section-heading--' . e($align) . ($dark ? ' section-heading--dark' : '') . '">';
    if ($eyebrow) {
        $html .= '<span class="section-heading__eyebrow">' . e($eyebrow) . '</span>';
    }
    $html .= '<h2 class="section-heading__title">' . e($title) . '</h2>';
    if ($description) {
        $html .= '<p class="section-heading__description">' . e($description) . '</p>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Returns a `style="--photo-src:url('...')"` attribute value that points at
 * a real photo path. Until a file exists at that path the browser simply
 * can't paint that background layer, so the duotone/gradient layer beneath
 * it (set in CSS) shows through instead of a broken-image icon. Drop a photo
 * at the same path later (see README "Photo checklist") and it appears
 * automatically — no code changes required.
 */
function photo_style(string $path, array $extraVars = []): string
{
    $vars = ["--photo-src:url('" . e($path) . "')"];
    foreach ($extraVars as $name => $value) {
        $vars[] = e($name) . ':' . e($value);
    }
    return ' style="' . implode(';', $vars) . '"';
}

/** Maps a category name to one of the four duotone photo-fallback classes. */
function category_duotone(string $category): string
{
    $map = [
        'Power Tools & Trade Supplies' => 'photo--duotone-1',
        'Beauty & Personal Care' => 'photo--duotone-2',
        'Grocery & FMCG' => 'photo--duotone-3',
        'Home & Lifestyle' => 'photo--duotone-4',
    ];
    return $map[$category] ?? 'photo--warm';
}

function render_page_header(string $eyebrow, string $title, ?string $description = null, ?string $photo = null): string
{
    $html = '<section class="page-header">';
    if ($photo) {
        $html .= '<div class="page-header__photo"' . photo_style($photo) . '></div>';
    }
    $html .= '<div class="page-header__inner">';
    $html .= '<span class="page-header__eyebrow">' . e($eyebrow) . '</span>';
    $html .= '<h1 class="page-header__title">' . e($title) . '</h1>';
    if ($description) {
        $html .= '<p class="page-header__description">' . e($description) . '</p>';
    }
    $html .= '</div></section>';
    return $html;
}

function render_cta_banner(?string $photo = null): string
{
    $html = '<section class="cta-banner">';
    if ($photo) {
        $html .= '<div class="cta-banner__photo"' . photo_style($photo) . '></div>';
    }
    $html .= '<div class="cta-banner__inner reveal">';
    $html .= '<h2 class="cta-banner__title">Ready to <span class="italic text-amber-light">trade</span> with Kassar?</h2>';
    $html .= '<p class="cta-banner__desc">Tell us whether you\'re buying in bulk, shopping retail, or looking to supply your own brand — we\'ll route you to the right team within one business day.</p>';
    $html .= '<div class="cta-banner__actions">';
    $html .= render_button('Contact Us', '/contact.php', 'primary');
    $html .= render_button('Supply to Kassar', '/supply-to-us.php', 'secondary');
    $html .= '</div></div></section>';
    return $html;
}

/**
 * Renders an enquiry form + its (initially hidden) success message.
 *
 * @param array $fields Each item: name, label, type (text|email|tel|textarea|number),
 *                       required (bool), placeholder, span (full|half)
 */
function render_enquiry_form(string $type, array $fields, string $submitLabel = 'Submit Enquiry'): string
{
    $html = '<form class="enquiry-form" data-enquiry-form data-enquiry-type="' . e($type) . '">';

    foreach ($fields as $field) {
        $name = $field['name'];
        $label = $field['label'];
        $inputType = $field['type'] ?? 'text';
        $required = !empty($field['required']);
        $placeholder = $field['placeholder'] ?? '';
        $span = ($field['span'] ?? 'half') === 'full' ? ' enquiry-form__field--full' : '';

        $html .= '<div class="' . trim('enquiry-form__field' . $span) . '">';
        $html .= '<label for="field-' . e($name) . '">' . e($label);
        if ($required) {
            $html .= ' <span class="req">*</span>';
        }
        $html .= '</label>';

        if ($inputType === 'textarea') {
            $html .= '<textarea id="field-' . e($name) . '" name="' . e($name) . '" rows="4"'
                . ($placeholder ? ' placeholder="' . e($placeholder) . '"' : '')
                . ($required ? ' required' : '') . '></textarea>';
        } else {
            $html .= '<input id="field-' . e($name) . '" name="' . e($name) . '" type="' . e($inputType) . '"'
                . ($placeholder ? ' placeholder="' . e($placeholder) . '"' : '')
                . ($required ? ' required' : '') . '>';
        }
        $html .= '</div>';
    }

    $html .= '<p class="enquiry-form__error" data-enquiry-error hidden></p>';
    $html .= '<div class="enquiry-form__submit-row">';
    $html .= '<button type="submit" class="enquiry-form__submit" data-enquiry-submit>' . e($submitLabel) . '</button>';
    $html .= '</div>';
    $html .= '</form>';

    $html .= '<div class="enquiry-success" data-enquiry-success hidden>';
    $html .= '<p class="enquiry-success__title">Thanks — your enquiry is in.</p>';
    $html .= '<p class="enquiry-success__desc">A member of the Kassar team will be in touch within one business day.</p>';
    $html .= '<button type="button" class="enquiry-success__again" data-enquiry-again>Submit another enquiry</button>';
    $html .= '</div>';

    return $html;
}
