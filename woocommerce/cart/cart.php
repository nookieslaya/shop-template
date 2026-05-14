<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart');
?>
<form class="woocommerce-cart-form cart-table" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
    <?php do_action('woocommerce_before_cart_table'); ?>

    <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
        <thead>
            <tr>
                <th class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
                <th class="product-price"><?php esc_html_e('Price', 'woocommerce'); ?></th>
                <th class="product-quantity"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
                <th class="product-subtotal"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php do_action('woocommerce_before_cart_contents'); ?>
            <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) : ?>
                <?php
                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                if (!$_product || !$_product->exists() || $cart_item['quantity'] <= 0) {
                    continue;
                }
                ?>
                <tr class="woocommerce-cart-form__cart-item">
                    <td class="product-name" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                        <?php echo wp_kses_post($_product->get_name()); ?>
                    </td>
                    <td class="product-price" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                        <?php echo wp_kses_post(WC()->cart->get_product_price($_product)); ?>
                    </td>
                    <td class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                        <?php
                        echo woocommerce_quantity_input([
                            'input_name'  => "cart[{$cart_item_key}][qty]",
                            'input_value' => $cart_item['quantity'],
                        ], $_product, false);
                        ?>
                    </td>
                    <td class="product-subtotal" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
                        <?php echo wp_kses_post(WC()->cart->get_product_subtotal($_product, $cart_item['quantity'])); ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php do_action('woocommerce_cart_contents'); ?>

            <tr>
                <td colspan="6" class="actions">
                    <button type="submit" class="button" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>">
                        <?php esc_html_e('Update cart', 'woocommerce'); ?>
                    </button>
                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                </td>
            </tr>

            <?php do_action('woocommerce_after_cart_contents'); ?>
        </tbody>
    </table>

    <?php do_action('woocommerce_after_cart_table'); ?>
</form>

<div class="cart-collaterals">
    <?php do_action('woocommerce_cart_collaterals'); ?>
</div>

<?php do_action('woocommerce_after_cart'); ?>
