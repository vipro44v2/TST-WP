<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tst_theme_setup() {
    load_theme_textdomain( 'tst-custom', TST_DIR . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    add_theme_support(
        'html5',
        array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
    );
    add_theme_support(
        'woocommerce',
        array(
            'thumbnail_image_width' => 600,
            'single_image_width'    => 900,
            'product_grid'          => array(
                'default_rows'    => 4,
                'default_columns' => 4,
            ),
        )
    );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    register_nav_menus(
        array(
            'primary'  => __( 'Primary Menu', 'tst-custom' ),
            'mobile'   => __( 'Mobile Menu', 'tst-custom' ),
            'footer-1' => __( 'Footer Menu 1', 'tst-custom' ),
            'footer-2' => __( 'Footer Menu 2', 'tst-custom' ),
        )
    );
}
add_action( 'after_setup_theme', 'tst_theme_setup' );

function tst_widgets_init() {
    register_sidebar(
        array(
            'name'          => __( 'Footer', 'tst-custom' ),
            'id'            => 'footer',
            'before_widget' => '<div class="tst-footer__widget">',
            'after_widget'  => '</div>',
        )
    );
}
add_action( 'widgets_init', 'tst_widgets_init' );
