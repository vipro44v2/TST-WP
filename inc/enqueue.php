<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tst_enqueue_assets() {
    wp_enqueue_style( 'tst-style', get_stylesheet_uri(), array(), TST_VERSION );
    wp_enqueue_script( 'tst-main', TST_URI . '/assets/js/main.js', array(), TST_VERSION, true );

    if ( is_front_page() ) {
        wp_enqueue_style(
            'tst-product-tabs-slider',
            TST_URI . '/assets/css/components/product-tabs-slider.css',
            array( 'tst-style' ),
            TST_VERSION
        );
        wp_enqueue_script(
            'tst-product-tabs-slider',
            TST_URI . '/assets/js/components/product-tabs-slider.js',
            array(),
            TST_VERSION,
            true
        );
    }

    if ( class_exists( 'WooCommerce' ) ) {
        wp_enqueue_script( 'tst-cart', TST_URI . '/assets/js/cart.js', array(), TST_VERSION, true );
        wp_localize_script(
            'tst-cart',
            'tstData',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'tst_cart' ),
            )
        );
    }
}

add_action( 'wp_enqueue_scripts', 'tst_enqueue_assets' );
