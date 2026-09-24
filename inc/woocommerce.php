<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tst_woocommerce_hooks() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
    remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
    add_filter( 'loop_shop_per_page', 'tst_shop_per_page' );

    if ( function_exists( 'is_product' ) && is_product() ) {
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
        add_action( 'woocommerce_after_single_product_summary', 'tst_related_products', 20 );
        add_action( 'woocommerce_after_single_product_summary', 'tst_recently_viewed_products', 25 );
    }
}
add_action( 'wp', 'tst_woocommerce_hooks' );

function tst_shop_per_page() {
    return 16;
}

function tst_related_products() {
    if (
        ! class_exists( 'WC_Product' ) ||
        ! function_exists( 'wc_get_related_products' ) ||
        ! function_exists( 'wc_get_product' )
    ) {
        return;
    }

    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $related_ids = wc_get_related_products( $product->get_id(), 12 );
    $related_products = array();

    foreach ( $related_ids as $related_id ) {
        $related_product = wc_get_product( $related_id );

        if ( $related_product instanceof WC_Product && $related_product->is_visible() ) {
            $related_products[] = $related_product;
        }
    }

    if ( ! $related_products ) {
        return;
    }

    get_template_part(
        'template-parts/product/related-products',
        null,
        array(
            'products' => $related_products,
            'title'    => __( 'CÓ THỂ BẠN SẼ THÍCH', 'tst-custom' ),
            'id'       => 'tst-related-products',
        )
    );
}

function tst_recently_viewed_ids() {
    $raw_ids = $_COOKIE['tst_recently_viewed'] ?? '';

    if ( ! is_string( $raw_ids ) || strlen( $raw_ids ) > 512 ) {
        return array();
    }

    $ids = array_values(
        array_unique(
            array_filter( array_map( 'absint', explode( '|', $raw_ids ) ) )
        )
    );

    return array_slice( $ids, 0, 12 );
}

function tst_track_product_view() {
    if (
        ! class_exists( 'WooCommerce' ) ||
        ! function_exists( 'is_product' ) ||
        ! is_product() ||
        headers_sent()
    ) {
        return;
    }

    $product_id = absint( get_queried_object_id() );

    if ( ! $product_id ) {
        return;
    }

    $ids = array_values( array_diff( tst_recently_viewed_ids(), array( $product_id ) ) );
    array_unshift( $ids, $product_id );

    setcookie(
        'tst_recently_viewed',
        implode( '|', array_slice( $ids, 0, 12 ) ),
        array(
            'expires'  => time() + 30 * DAY_IN_SECONDS,
            'path'     => defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/',
            'secure'   => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax',
        )
    );
}
add_action( 'template_redirect', 'tst_track_product_view' );

function tst_recently_viewed_products() {
    if ( ! class_exists( 'WC_Product' ) || ! function_exists( 'wc_get_product' ) ) {
        return;
    }

    global $product;

    if ( ! $product instanceof WC_Product ) {
        return;
    }

    $recent_products = array();

    foreach ( tst_recently_viewed_ids() as $product_id ) {
        if ( $product_id === $product->get_id() ) {
            continue;
        }

        $recent_product = wc_get_product( $product_id );

        if ( $recent_product instanceof WC_Product && $recent_product->is_visible() ) {
            $recent_products[] = $recent_product;
        }
    }

    if ( ! $recent_products ) {
        return;
    }

    get_template_part(
        'template-parts/product/related-products',
        null,
        array(
            'products' => $recent_products,
            'title'    => __( 'SẢN PHẨM ĐÃ XEM', 'tst-custom' ),
            'id'       => 'tst-recently-viewed',
        )
    );
}

function tst_single_buy_now_button() {
    if ( ! function_exists( 'is_product' ) || ! is_product() ) {
        return;
    }

    global $product;

    if ( ! $product instanceof WC_Product || ! $product->is_type( array( 'simple', 'variable' ) ) ) {
        return;
    }

    if ( $product->is_type( 'simple' ) ) {
        echo '<input type="hidden" name="add-to-cart" value="' . esc_attr( $product->get_id() ) . '">';
    }

    echo '<button class="tst-product-info__buy-now" type="submit" name="tst_buy_now" value="1">';
    esc_html_e( 'Mua Nhanh', 'tst-custom' );
    echo '</button>';
}
add_action( 'woocommerce_after_add_to_cart_button', 'tst_single_buy_now_button' );

function tst_single_buy_now_redirect( $url ) {
    if (
        ! isset( $_POST['tst_buy_now'] ) ||
        ! is_scalar( $_POST['tst_buy_now'] ) ||
        '1' !== (string) wp_unslash( $_POST['tst_buy_now'] ) ||
        ! function_exists( 'wc_get_checkout_url' )
    ) {
        return $url;
    }

    return wc_get_checkout_url();
}
add_filter( 'woocommerce_add_to_cart_redirect', 'tst_single_buy_now_redirect' );
