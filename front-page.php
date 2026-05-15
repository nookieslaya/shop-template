<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$default_sections = [
    'hero',
    'featured-products',
    'product-categories',
    'promo-banner',
    'product-showcase-tabs',
    'benefits-strip',
];

$sections_order = get_post_meta((int) get_the_ID(), '_shop_theme_front_page_sections_order', true);

if (!is_array($sections_order)) {
    $sections_order = $default_sections;
}

$sections_order = array_values(array_intersect($sections_order, $default_sections));
$sections_order = array_values(array_unique(array_merge($sections_order, $default_sections)));

foreach ($sections_order as $section_slug) {
    get_template_part('template-parts/sections/' . $section_slug);
}

get_footer();
