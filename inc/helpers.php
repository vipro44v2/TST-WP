<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tst_get_setting( $key, $fallback = '' ) {
    $settings = get_option( 'tst_settings', array() );

    return is_array( $settings ) && isset( $settings[ $key ] )
        ? $settings[ $key ]
        : $fallback;
}

function tst_get_product_card( $product = null ) {
    if (
        ! class_exists( 'WooCommerce' ) ||
        ! class_exists( 'WC_Product' ) ||
        ! function_exists( 'wc_get_product' )
    ) {
        return;
    }

    $product = $product instanceof WC_Product
        ? $product
        : wc_get_product( get_the_ID() );

    if ( ! ( $product instanceof WC_Product ) ) {
        return;
    }

    get_template_part( 'template-parts/collection/product-card', null, array( 'product' => $product ) );
}

function tst_cart_count() {
    if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'WC' ) ) {
        return 0;
    }

    $woocommerce = WC();

    return $woocommerce && isset( $woocommerce->cart )
        ? $woocommerce->cart->get_cart_contents_count()
        : 0;
}
