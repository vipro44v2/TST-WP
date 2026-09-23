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
}
add_action( 'wp', 'tst_woocommerce_hooks' );

function tst_shop_per_page() {
    return 16;
}
