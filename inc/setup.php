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

function tst_header_fallback_menu() {
    $items = array();

    if ( taxonomy_exists( 'product_cat' ) ) {
        $default_category_id = absint( get_option( 'default_product_cat', 0 ) );
        $terms = get_terms(
            array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
                'parent'     => 0,
                'orderby'    => 'name',
                'exclude'    => $default_category_id ? array( $default_category_id ) : array(),
            )
        );

        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                if ( 'uncategorized' === $term->slug ) {
                    continue;
                }

                $url = get_term_link( $term );

                if ( ! is_wp_error( $url ) ) {
                    $items[] = array(
                        'label' => $term->name,
                        'url'   => $url,
                    );
                }
            }
        }
    }

    if ( ! $items ) {
        $pages = get_pages(
            array(
                'parent'      => 0,
                'sort_column' => 'menu_order,post_title',
                'number'      => 10,
            )
        );

        if ( is_array( $pages ) ) {
            foreach ( $pages as $page ) {
                $items[] = array(
                    'label' => get_the_title( $page ),
                    'url'   => get_permalink( $page ),
                );
            }
        }
    }

    if ( ! $items ) {
        $items[] = array(
            'label' => get_bloginfo( 'name' ),
            'url'   => home_url( '/' ),
        );
    }

    echo '<ul class="tst-header__menu">';

    foreach ( $items as $item ) {
        echo '<li><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a></li>';
    }

    echo '</ul>';
}

function tst_footer_fallback_menu( $location ) {
    static $pages = null;
    static $used_page_ids = array();

    $preferred_titles = array(
        'footer-1' => array(
            'Giới thiệu',
            'Hệ thống cửa hàng',
            'Êm Như Bông',
            'FAQ',
            'Blog',
            'Chương trình quà tặng',
            'Tuyển dụng',
            'Phiếu quà tặng',
            'Liên hệ',
        ),
        'footer-2' => array(
            'Điều khoản sử dụng',
            'Hướng dẫn chọn size',
            'Hướng dẫn mua hàng online',
            'Phương thức thanh toán',
            'Chính sách giao nhận',
            'Chính sách bảo hành',
            'Chính sách bảo mật thông tin',
            'Chính sách bảo mật thanh toán',
            'Chính sách đổi hàng',
            'Cảnh báo lừa đảo và hướng dẫn mua sắm an toàn',
        ),
    );

    if ( ! isset( $preferred_titles[ $location ] ) ) {
        return;
    }

    if ( null === $pages ) {
        $pages = get_pages(
            array(
                'sort_column' => 'menu_order,post_title',
            )
        );
    }

    if ( ! is_array( $pages ) || ! $pages ) {
        return;
    }

    $pages_by_title = array();

    foreach ( $pages as $page ) {
        $pages_by_title[ sanitize_title( $page->post_title ) ] = $page;
    }

    $selected_pages = array();

    foreach ( $preferred_titles[ $location ] as $title ) {
        $page = $pages_by_title[ sanitize_title( $title ) ] ?? null;

        if ( $page && ! isset( $used_page_ids[ $page->ID ] ) ) {
            $selected_pages[] = $page;
        }
    }

    if ( ! $selected_pages ) {
        $available_pages = array_values(
            array_filter(
                $pages,
                static function ( $page ) use ( $used_page_ids ) {
                    return ! isset( $used_page_ids[ $page->ID ] );
                }
            )
        );
        $limit = 'footer-1' === $location ? (int) ceil( count( $pages ) / 2 ) : 10;
        $selected_pages = array_slice( $available_pages, 0, min( 10, $limit ) );
    }

    if ( ! $selected_pages ) {
        return;
    }

    echo '<ul class="tst-footer__menu">';

    foreach ( $selected_pages as $page ) {
        $url = get_permalink( $page );

        if ( $url ) {
            $used_page_ids[ $page->ID ] = true;
            echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( get_the_title( $page ) ) . '</a></li>';
        }
    }

    echo '</ul>';
}

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
