<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tst_ajax_add_to_cart() {
    if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'WC' ) ) {
        wp_send_json_error(
            array( 'message' => __( 'Cart is currently unavailable.', 'tst-custom' ) ),
            503
        );
    }

    $woocommerce = WC();

    if ( ! $woocommerce || ! isset( $woocommerce->cart ) ) {
        wp_send_json_error(
            array( 'message' => __( 'Cart is currently unavailable.', 'tst-custom' ) ),
            503
        );
    }

    check_ajax_referer( 'tst_cart', 'nonce' );

    $product_id = absint( $_POST['product_id'] ?? 0 );
    $qty        = max( 1, absint( $_POST['quantity'] ?? 1 ) );
    $variation_id = absint( $_POST['variation_id'] ?? 0 );
    $attributes = array();

    if ( $variation_id ) {
        if ( ! function_exists( 'wc_get_product' ) || ! class_exists( 'WC_Product_Variation' ) ) {
            wp_send_json_error(
                array( 'message' => __( 'This variation is unavailable.', 'tst-custom' ) ),
                400
            );
        }

        $variation = wc_get_product( $variation_id );

        if (
            ! $variation instanceof WC_Product_Variation ||
            $variation->get_parent_id() !== $product_id ||
            ! $variation->is_purchasable() ||
            ! $variation->is_in_stock()
        ) {
            wp_send_json_error(
                array( 'message' => __( 'This variation is unavailable.', 'tst-custom' ) ),
                400
            );
        }

        $attributes = $variation->get_variation_attributes();

        if ( in_array( '', $attributes, true ) ) {
            wp_send_json_error(
                array( 'message' => __( 'Please choose all product options.', 'tst-custom' ) ),
                400
            );
        }
    }

    if ( ! $product_id || ! $woocommerce->cart->add_to_cart( $product_id, $qty, $variation_id, $attributes ) ) {
        wp_send_json_error(
            array( 'message' => __( 'Unable to add this product.', 'tst-custom' ) )
        );
    }

    wp_send_json_success(
        array(
            'count'     => tst_cart_count(),
            'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array() ),
            'drawer'    => function_exists( 'tst_cart_drawer_content' )
                ? tst_cart_drawer_content()
                : '',
        )
    );
}
add_action( 'wp_ajax_tst_add_to_cart', 'tst_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_tst_add_to_cart', 'tst_ajax_add_to_cart' );
