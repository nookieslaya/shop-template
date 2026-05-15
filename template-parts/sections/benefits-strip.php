<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$bg_color = trim((string) get_theme_mod('shop_theme_benefits_bg_color', '#ececf0'));
$padding_top = (int) get_theme_mod('shop_theme_benefits_padding_top', 56);
$padding_bottom = (int) get_theme_mod('shop_theme_benefits_padding_bottom', 56);

$default_items = [
    ['title' => __('Free Shipping', 'shop-theme'), 'text' => __('On orders over $50', 'shop-theme')],
    ['title' => __('Easy Returns', 'shop-theme'), 'text' => __('30-day return policy', 'shop-theme')],
    ['title' => __('Quality Guarantee', 'shop-theme'), 'text' => __('Premium products only', 'shop-theme')],
    ['title' => __('Secure Packaging', 'shop-theme'), 'text' => __('Safe delivery guaranteed', 'shop-theme')],
];

function shop_theme_benefits_default_icon(int $index): string
{
    $icons = [
        '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3.5 7.5h9v7.8h-9zM12.5 10h3.9l2.1 2.3v3h-6\" stroke=\"currentColor\" stroke-width=\"1.9\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/><circle cx=\"7\" cy=\"18\" r=\"1.4\" fill=\"currentColor\"/><circle cx=\"16.8\" cy=\"18\" r=\"1.4\" fill=\"currentColor\"/></svg>',
        '<svg viewBox=\"0 0 24 24\" fill=\"none\" aria-hidden=\"true\"><path d=\"M20 6v6h-6\" stroke=\"currentColor\" stroke-width=\"1.9\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/><path d=\"M4 18v-6h6\" stroke=\"currentColor\" stroke-width=\"1.9\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/><path d=\"M18.5 12A6.8 6.8 0 0 0 6.9 8.6M5.5 12A6.8 6.8 0 0 0 17.1 15.4\" stroke=\"currentColor\" stroke-width=\"1.9\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/></svg>',
        '<svg viewBox=\"0 0 24 24\" fill=\"none\" aria-hidden=\"true\"><circle cx=\"12\" cy=\"8\" r=\"3.2\" stroke=\"currentColor\" stroke-width=\"1.9\"/><path d=\"M9 12.8 6.6 21 12 18.2 17.4 21 15 12.8\" stroke=\"currentColor\" stroke-width=\"1.9\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/></svg>',
        '<svg viewBox=\"0 0 24 24\" fill=\"none\" aria-hidden=\"true\"><path d=\"m12 3 7 4.1v9.8L12 21l-7-4.1V7.1z\" stroke=\"currentColor\" stroke-width=\"1.9\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/><path d=\"m5 7.3 7 4 7-4M12 11.3v9.4\" stroke=\"currentColor\" stroke-width=\"1.9\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/></svg>',
    ];

    return $icons[$index] ?? $icons[0];
}
?>
<section class="benefits-strip section" style="--benefits-bg-color: <?php echo esc_attr($bg_color); ?>; --benefits-padding-top: <?php echo esc_attr((string) max(0, min(200, $padding_top))); ?>px; --benefits-padding-bottom: <?php echo esc_attr((string) max(0, min(200, $padding_bottom))); ?>px;">
    <div class="container">
        <div class="benefits-strip__grid">
            <?php foreach ($default_items as $i => $defaults) : ?>
                <?php
                $index = $i + 1;
                $title = trim((string) get_theme_mod("shop_theme_benefit_{$index}_title", $defaults['title']));
                $text = trim((string) get_theme_mod("shop_theme_benefit_{$index}_text", $defaults['text']));
                $icon_url = trim((string) get_theme_mod("shop_theme_benefit_{$index}_icon", ''));
                ?>
                <article class="benefits-strip__item">
                    <span class="benefits-strip__icon-wrap">
                        <?php if ($icon_url !== '') : ?>
                            <img class="benefits-strip__icon-image" src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($title); ?>">
                        <?php else : ?>
                            <span class="benefits-strip__icon-svg"><?php echo wp_kses(shop_theme_benefits_default_icon($i), ['svg' => ['viewBox' => true, 'fill' => true, 'aria-hidden' => true], 'path' => ['d' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true], 'circle' => ['cx' => true, 'cy' => true, 'r' => true, 'fill' => true]]); ?></span>
                        <?php endif; ?>
                    </span>

                    <?php if ($title !== '') : ?>
                        <h3 class="benefits-strip__title"><?php echo esc_html($title); ?></h3>
                    <?php endif; ?>

                    <?php if ($text !== '') : ?>
                        <p class="benefits-strip__text"><?php echo esc_html($text); ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
