<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tst_get_setting( $key, $fallback = '' ) {
    $settings = get_option( 'tst_settings', array() );

    return isset( $settings[ $key ] ) ? $settings[ $key ] : $fallback;
}

function tst_get_product_card( $product = null ) {
    $product = $product instanceof WC_Product ? $product : wc_get_product( get_the_ID() );

    if ( ! $product ) {
        return;
    }

    get_template_part( 'template-parts/collection/product-card', null, array( 'product' => $product ) );
}

function tst_cart_count() {
    return function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
}
