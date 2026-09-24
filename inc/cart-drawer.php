<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tst_cart_drawer_enabled() {
    return class_exists( 'WooCommerce' )
        && class_exists( 'WC_Product' )
        && function_exists( 'WC' )
        && function_exists( 'wc_get_cart_url' );
}

function tst_cart_drawer_available() {
    if ( ! tst_cart_drawer_enabled() ) {
        return false;
    }

    $woocommerce = WC();

    if (
        $woocommerce &&
        ! isset( $woocommerce->cart ) &&
        function_exists( 'wc_load_cart' ) &&
        did_action( 'before_woocommerce_init' )
    ) {
        wc_load_cart();
    }

    return $woocommerce && isset( $woocommerce->cart );
}

function tst_cart_drawer_content() {
    if ( ! tst_cart_drawer_available() ) {
        return '';
    }

    ob_start();
    get_template_part( 'template-parts/cart/drawer-content' );

    return (string) ob_get_clean();
}

function tst_cart_drawer_ajax_response() {
    wp_send_json_success(
        array(
            'count' => tst_cart_count(),
            'html'  => tst_cart_drawer_content(),
        )
    );
}

function tst_cart_drawer_refresh() {
    check_ajax_referer( 'tst_cart', 'nonce' );

    if ( ! tst_cart_drawer_available() ) {
        wp_send_json_error(
            array( 'message' => __( 'Giỏ hàng hiện không khả dụng.', 'tst-custom' ) ),
            503
        );
    }

    tst_cart_drawer_ajax_response();
}
add_action( 'wp_ajax_tst_cart_drawer_refresh', 'tst_cart_drawer_refresh' );
add_action( 'wp_ajax_nopriv_tst_cart_drawer_refresh', 'tst_cart_drawer_refresh' );

function tst_cart_drawer_remove() {
    check_ajax_referer( 'tst_cart', 'nonce' );

    if ( ! tst_cart_drawer_available() ) {
        wp_send_json_error(
            array( 'message' => __( 'Giỏ hàng hiện không khả dụng.', 'tst-custom' ) ),
            503
        );
    }

    $raw_key = $_POST['cart_item_key'] ?? '';
    $cart_item_key = is_string( $raw_key )
        ? sanitize_text_field( wp_unslash( $raw_key ) )
        : '';
    $cart = WC()->cart;
    $cart_items = $cart->get_cart();

    if ( ! $cart_item_key || ! isset( $cart_items[ $cart_item_key ] ) ) {
        wp_send_json_error(
            array( 'message' => __( 'Không tìm thấy sản phẩm trong giỏ hàng.', 'tst-custom' ) ),
            400
        );
    }

    if ( ! $cart->remove_cart_item( $cart_item_key ) ) {
        wp_send_json_error(
            array( 'message' => __( 'Không thể xóa sản phẩm.', 'tst-custom' ) ),
            400
        );
    }

    $cart->calculate_totals();
    tst_cart_drawer_ajax_response();
}
add_action( 'wp_ajax_tst_cart_drawer_remove', 'tst_cart_drawer_remove' );
add_action( 'wp_ajax_nopriv_tst_cart_drawer_remove', 'tst_cart_drawer_remove' );
