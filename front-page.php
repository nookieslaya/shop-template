<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();

get_template_part('template-parts/sections/hero');
get_template_part('template-parts/sections/featured-products');
get_template_part('template-parts/sections/categories');

get_footer();
