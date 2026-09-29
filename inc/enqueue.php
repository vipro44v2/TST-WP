<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tst_asset_version( $relative_path ) {
    $path = TST_DIR . '/' . ltrim( $relative_path, '/' );

    return is_file( $path ) ? (string) filemtime( $path ) : TST_VERSION;
}

function tst_enqueue_assets() {
    wp_enqueue_style( 'tst-style', get_stylesheet_uri(), array(), tst_asset_version( 'style.css' ) );
    $core_styles = array( 'base', 'components', 'header', 'checkout', 'footer' );
    $previous_handle = 'tst-style';

    foreach ( $core_styles as $name ) {
        wp_enqueue_style(
            'tst-' . $name,
            TST_URI . '/assets/css/' . $name . '.css',
            array( $previous_handle ),
            tst_asset_version( 'assets/css/' . $name . '.css' )
        );
        $previous_handle = 'tst-' . $name;
    }

    wp_enqueue_script(
        'tst-main',
        TST_URI . '/assets/js/main.js',
        array(),
        tst_asset_version( 'assets/js/main.js' ),
        true
    );
    $tst_is_product_page = class_exists( 'WooCommerce' ) && is_product();
    $tst_is_cart_page = class_exists( 'WooCommerce' ) && function_exists( 'is_cart' ) && is_cart();
    $tst_is_checkout_page = class_exists( 'WooCommerce' ) && function_exists( 'is_checkout' ) && is_checkout();

    if ( class_exists( 'WooCommerce' ) && function_exists( 'is_account_page' ) && is_account_page() && ! is_user_logged_in() ) {
        wp_enqueue_style(
            'tst-account',
            TST_URI . '/assets/css/account.css',
            array( 'tst-style' ),
            tst_asset_version( 'assets/css/account.css' )
        );
        wp_enqueue_script(
            'tst-account',
            TST_URI . '/assets/js/account.js',
            array(),
            tst_asset_version( 'assets/js/account.js' ),
            true
        );
    }

    if ( $tst_is_checkout_page ) {
        wp_enqueue_style(
            'tst-checkout-page',
            TST_URI . '/assets/css/checkout-page.css',
            array( 'tst-style' ),
            tst_asset_version( 'assets/css/checkout-page.css' )
        );
    }

    if ( $tst_is_cart_page ) {
        wp_enqueue_style(
            'tst-cart-page',
            TST_URI . '/assets/css/cart.css',
            array( 'tst-style' ),
            tst_asset_version( 'assets/css/cart.css' )
        );
        wp_enqueue_script(
            'tst-cart-page',
            TST_URI . '/assets/js/cart-page.js',
            array(),
            tst_asset_version( 'assets/js/cart-page.js' ),
            true
        );
    }

    if ( is_front_page() || $tst_is_product_page || $tst_is_cart_page ) {
        wp_enqueue_style(
            'tst-reveal',
            TST_URI . '/assets/css/reveal.css',
            array( 'tst-style' ),
            tst_asset_version( 'assets/css/reveal.css' )
        );
        wp_enqueue_script(
            'tst-reveal',
            TST_URI . '/assets/js/reveal.js',
            array(),
            tst_asset_version( 'assets/js/reveal.js' ),
            true
        );
    }

    if ( $tst_is_product_page || $tst_is_cart_page ) {
        wp_enqueue_style(
            'tst-single-product',
            TST_URI . '/assets/css/product.css',
            array( 'tst-style' ),
            tst_asset_version( 'assets/css/product.css' )
        );
        wp_enqueue_script(
            'tst-related-products',
            TST_URI . '/assets/js/related-products.js',
            array(),
            tst_asset_version( 'assets/js/related-products.js' ),
            true
        );
        wp_enqueue_script(
            'tst-product-quick-select',
            TST_URI . '/assets/js/components/product-quick-select.js',
            array(),
            tst_asset_version( 'assets/js/components/product-quick-select.js' ),
            true
        );
    }

    if ( $tst_is_product_page ) {
        wp_enqueue_script(
            'tst-single-product',
            TST_URI . '/assets/js/single-product.js',
            array( 'jquery' ),
            tst_asset_version( 'assets/js/single-product.js' ),
            true
        );
    }

    if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_product_taxonomy() ) ) {
        wp_enqueue_script(
            'tst-collection',
            TST_URI . '/assets/js/collection.js',
            array(),
            tst_asset_version( 'assets/js/collection.js' ),
            true
        );
        wp_enqueue_style(
            'tst-collection',
            TST_URI . '/assets/css/collection.css',
            array( 'tst-style' ),
            tst_asset_version( 'assets/css/collection.css' )
        );
        wp_enqueue_script(
            'tst-product-quick-select',
            TST_URI . '/assets/js/components/product-quick-select.js',
            array(),
            tst_asset_version( 'assets/js/components/product-quick-select.js' ),
            true
        );
    }

    if ( is_front_page() && tst_home_has_block_type( 'hero' ) ) {
        wp_enqueue_style(
            'tst-hero-carousel',
            TST_URI . '/assets/css/components/hero-carousel.css',
            array( 'tst-style' ),
            tst_asset_version( 'assets/css/components/hero-carousel.css' )
        );
        wp_enqueue_script(
            'tst-hero-carousel',
            TST_URI . '/assets/js/components/hero-carousel.js',
            array(),
            tst_asset_version( 'assets/js/components/hero-carousel.js' ),
            true
        );
    }

    if ( is_front_page() && tst_home_has_block_type( 'lifestyle' ) ) {
        wp_enqueue_style(
            'tst-lifestyle-banner',
            TST_URI . '/assets/css/components/lifestyle-banner.css',
            array( 'tst-style' ),
            tst_asset_version( 'assets/css/components/lifestyle-banner.css' )
        );
    }

    if ( is_front_page() && tst_home_has_block_type( 'craft' ) ) {
        wp_enqueue_style(
            'tst-craft-story',
            TST_URI . '/assets/css/components/craft-story.css',
            array( 'tst-style' ),
            tst_asset_version( 'assets/css/components/craft-story.css' )
        );
    }

    if ( is_front_page() && tst_home_has_block_type( 'business' ) ) {
        wp_enqueue_style(
            'tst-business-customers',
            TST_URI . '/assets/css/components/business-customers.css',
            array( 'tst-style' ),
            tst_asset_version( 'assets/css/components/business-customers.css' )
        );
    }

    if ( is_front_page() && tst_home_has_block_type( 'promo_pair' ) ) {
        wp_enqueue_style(
            'tst-promo-pair',
            TST_URI . '/assets/css/components/promo-pair.css',
            array( 'tst-style' ),
            tst_asset_version( 'assets/css/components/promo-pair.css' )
        );
    }

    if ( is_front_page() && tst_home_has_block_type( 'category_grid' ) ) {
        wp_enqueue_style(
            'tst-category-grid',
            TST_URI . '/assets/css/components/category-grid.css',
            array( 'tst-style' ),
            tst_asset_version( 'assets/css/components/category-grid.css' )
        );
    }

    if ( $tst_is_product_page || ( is_front_page() && tst_home_has_block_type( 'newsletter' ) ) ) {
        wp_enqueue_style(
            'tst-newsletter',
            TST_URI . '/assets/css/components/newsletter.css',
            array( 'tst-style' ),
            tst_asset_version( 'assets/css/components/newsletter.css' )
        );
    }

    if ( $tst_is_product_page || ( is_front_page() && tst_home_has_block_type( 'contact' ) ) ) {
        wp_enqueue_style(
            'tst-contact-feedback',
            TST_URI . '/assets/css/components/contact-feedback.css',
            array( 'tst-style' ),
            tst_asset_version( 'assets/css/components/contact-feedback.css' )
        );
    }

    if ( is_front_page() && class_exists( 'WooCommerce' ) && tst_home_has_block_type( 'tabs' ) ) {
        wp_enqueue_style(
            'tst-product-tabs-slider',
            TST_URI . '/assets/css/components/product-tabs-slider.css',
            array( 'tst-style' ),
            tst_asset_version( 'assets/css/components/product-tabs-slider.css' )
        );
        wp_enqueue_script(
            'tst-product-tabs-slider',
            TST_URI . '/assets/js/components/product-tabs-slider.js',
            array(),
            tst_asset_version( 'assets/js/components/product-tabs-slider.js' ),
            true
        );
    }

    if (
        is_front_page() &&
        class_exists( 'WooCommerce' ) &&
        ( tst_home_has_block_type( 'tabs' ) || tst_home_has_block_type( 'featured' ) )
    ) {
        wp_enqueue_script(
            'tst-product-quick-select',
            TST_URI . '/assets/js/components/product-quick-select.js',
            array(),
            tst_asset_version( 'assets/js/components/product-quick-select.js' ),
            true
        );
    }

    if ( class_exists( 'WooCommerce' ) ) {
        wp_enqueue_script(
            'tst-cart',
            TST_URI . '/assets/js/cart.js',
            array(),
            tst_asset_version( 'assets/js/cart.js' ),
            true
        );
        wp_localize_script(
            'tst-cart',
            'tstData',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'tst_cart' ),
            )
        );

        if ( function_exists( 'tst_cart_drawer_enabled' ) && tst_cart_drawer_enabled() ) {
            wp_enqueue_style(
                'tst-cart-drawer',
                TST_URI . '/assets/css/cart-drawer.css',
                array( 'tst-style' ),
                tst_asset_version( 'assets/css/cart-drawer.css' )
            );
        }
    }
}

add_action( 'wp_enqueue_scripts', 'tst_enqueue_assets' );
