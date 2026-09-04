<?php
/**
 * Renders pagination links. $baseParams is the $_GET array minus 'page'.
 */
function render_pagination(int $currentPage, int $totalPages, array $baseParams = []): void
{
    if ($totalPages <= 1) return;
    echo '<nav><ul class="pagination">';
    for ($i = 1; $i <= $totalPages; $i++) {
        $params = array_merge($baseParams, ['page' => $i]);
        $active = $i === $currentPage ? ' active' : '';
        echo '<li class="page-item' . $active . '"><a class="page-link" href="?' . http_build_query($params) . '">' . $i . '</a></li>';
    }
    echo '</ul></nav>';
}
