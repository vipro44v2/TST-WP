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

    if ( ! $product_id || ! $woocommerce->cart->add_to_cart( $product_id, $qty ) ) {
        wp_send_json_error(
            array( 'message' => __( 'Unable to add this product.', 'tst-custom' ) )
        );
    }

    wp_send_json_success(
        array(
            'count'     => tst_cart_count(),
            'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array() ),
        )
    );
}
add_action( 'wp_ajax_tst_add_to_cart', 'tst_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_tst_add_to_cart', 'tst_ajax_add_to_cart' );
