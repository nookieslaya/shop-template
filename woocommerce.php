<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<section class="woocommerce-page section">
    <div class="container woocommerce-page__inner">
        <?php woocommerce_content(); ?>
    </div>
</section>
<?php
get_footer();
