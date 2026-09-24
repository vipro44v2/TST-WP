<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tst_home_block_types() {
    return array(
        'hero'     => __( 'Banner', 'tst-custom' ),
        'tabs'     => __( 'Slider sản phẩm', 'tst-custom' ),
        'lifestyle' => __( 'Banner dấu ấn riêng', 'tst-custom' ),
        'craft'     => __( 'Câu chuyện thủ công', 'tst-custom' ),
        'business'  => __( 'Khách hàng doanh nghiệp', 'tst-custom' ),
        'promo_pair' => __( 'Hai banner bộ sưu tập', 'tst-custom' ),
        'category_grid' => __( 'Bốn danh mục hình ảnh', 'tst-custom' ),
        'newsletter' => __( 'Đăng ký bản tin', 'tst-custom' ),
        'contact'    => __( 'Liên hệ và góp ý', 'tst-custom' ),
        'featured' => __( 'Sản phẩm nổi bật', 'tst-custom' ),
    );
}

function tst_home_product_collections( $hide_empty = false ) {
    if ( ! taxonomy_exists( 'product_cat' ) ) {
        return array();
    }

    $default_id = absint( get_option( 'default_product_cat', 0 ) );
    $terms = get_terms(
        array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => $hide_empty,
            'orderby'    => $hide_empty ? 'count' : 'name',
            'order'      => $hide_empty ? 'DESC' : 'ASC',
            'exclude'    => $default_id ? array( $default_id ) : array(),
        )
    );

    if ( is_wp_error( $terms ) ) {
        return array();
    }

    return array_values(
        array_filter(
            $terms,
            static function ( $term ) {
                return 'uncategorized' !== $term->slug;
            }
        )
    );
}

function tst_home_default_blocks() {
    $legacy_order = tst_get_setting( 'hero_slide_order', '1,2' );

    return array(
        array(
            'id'                => 'hero-default',
            'type'              => 'hero',
            'enabled'           => (int) tst_get_setting( 'hero_enabled', 1 ),
            'title'             => tst_get_setting( 'hero_heading', 'Designed for everyday living.' ),
            'button_text'       => tst_get_setting( 'hero_cta_text', 'Tìm Hiểu Thêm' ),
            'button_url'        => tst_get_setting( 'hero_cta_url', '' ),
            'image_1'           => absint( tst_get_setting( 'hero_image_1', 0 ) ),
            'image_1_alt'       => tst_get_setting( 'hero_alt_1', '' ),
            'image_2'           => absint( tst_get_setting( 'hero_image_2', 0 ) ),
            'image_2_alt'       => tst_get_setting( 'hero_alt_2', '' ),
            'slide_order'       => tst_home_slide_order( $legacy_order ),
            'autoplay'          => 1,
            'autoplay_interval' => 6000,
        ),
        array(
            'id'               => 'tabs-default',
            'type'             => 'tabs',
            'enabled'          => (int) tst_get_setting( 'tabs_enabled', 1 ),
            'title'            => tst_get_setting( 'tabs_heading', '' ),
            'new_label'        => tst_get_setting( 'tabs_new_label', 'Sản phẩm mới' ),
            'best_label'       => tst_get_setting( 'tabs_best_label', 'Best Seller' ),
            'more_enabled'     => (int) tst_get_setting( 'tabs_show_more', 1 ),
            'more_text'        => 'Xem thêm',
            'more_url'         => tst_get_setting( 'tabs_more_url', '' ),
            'products_per_tab' => tst_get_setting( 'tabs_products_per_tab', 8 ),
            'collection_show_all' => 1,
            'collection_ids'   => array(),
            'desktop_columns'  => tst_get_setting( 'tabs_desktop', 4 ),
            'tablet_columns'   => tst_get_setting( 'tabs_tablet', 3 ),
            'mobile_columns'   => tst_get_setting( 'tabs_mobile', 2 ),
            'show_badges'      => (int) tst_get_setting( 'tabs_show_badges', 1 ),
            'show_swatches'    => (int) tst_get_setting( 'tabs_show_swatches', 1 ),
            'show_sizes'       => (int) tst_get_setting( 'tabs_show_sizes', 1 ),
            'image_background' => tst_get_setting( 'tabs_image_bg', '#f5f5f5' ),
            'card_radius'      => tst_get_setting( 'tabs_radius', 0 ),
            'top_spacing'      => tst_get_setting( 'tabs_top_spacing', 80 ),
            'bottom_spacing'   => tst_get_setting( 'tabs_bottom_spacing', 80 ),
        ),
        tst_home_lifestyle_default_block(),
        tst_home_craft_default_block(),
        tst_home_business_default_block(),
        tst_home_promo_pair_default_block(),
        tst_home_category_grid_default_block(),
        tst_home_newsletter_default_block(),
        tst_home_contact_default_block(),
        array(
            'id'      => 'featured-default',
            'type'    => 'featured',
            'title'   => __( 'Featured products', 'tst-custom' ),
            'count'   => 8,
            'collection_id' => 0,
            'enabled' => 1,
        ),
    );
}

function tst_home_lifestyle_default_block() {
    return array(
        'id'          => 'lifestyle-default',
        'type'        => 'lifestyle',
        'enabled'     => 1,
        'title'       => 'DẤU ẤN RIÊNG',
        'subtitle'    => 'Túi đựng điều bạn yêu – giày nâng bước bạn đi',
        'button_text' => 'Khám Phá Ngay',
        'button_url'  => '',
        'image_id'    => 0,
        'image_alt'   => '',
    );
}

function tst_home_craft_default_block() {
    return array(
        'id'          => 'craft-default',
        'type'        => 'craft',
        'enabled'     => 1,
        'eyebrow'     => 'ĐÔI TAY VÀNG',
        'title'       => 'Biểu Tượng Thời Gian',
        'point_1'     => 'Sản phẩm thủ công tinh xảo từ nghệ nhân của Đồng Hải.',
        'point_2'     => 'Tuyệt tác thời trang tạo nên vẻ lịch lãm, sang trọng độc đáo.',
        'point_3'     => 'Khẳng định vị thế từ vẻ lịch lãm và phong độ theo thời gian không thể khước từ.',
        'button_text' => 'Tìm Hiểu Thêm',
        'button_url'  => '',
        'image_1'     => 0,
        'image_1_alt' => '',
        'image_2'     => 0,
        'image_2_alt' => '',
    );
}

function tst_home_business_default_block() {
    return array(
        'id'           => 'business-default',
        'type'         => 'business',
        'enabled'      => 1,
        'title'        => 'Khách Hàng Doanh Nghiệp',
        'subtitle'     => 'B2B Đồng Hải',
        'card_1_title' => 'Thiết Kế Theo Yêu Cầu',
        'card_2_title' => 'Tư Vấn Chọn Mẫu',
        'button_text'  => 'Tìm Hiểu Thêm',
        'button_url'   => '',
        'image_1'      => 0,
        'image_1_alt'  => '',
        'image_2'      => 0,
        'image_2_alt'  => '',
        'image_3'      => 0,
        'image_3_alt'  => '',
    );
}

function tst_home_promo_pair_default_block() {
    return array(
        'id'            => 'promo-pair-default',
        'type'          => 'promo_pair',
        'enabled'       => 1,
        'title'         => '',
        'label_1'       => 'ZUCIANI',
        'heading_1'     => 'Simplicity & Elegance',
        'button_text_1' => 'Khám Phá Ngay',
        'button_url_1'  => '',
        'image_1'       => 0,
        'image_1_alt'   => '',
        'label_2'       => 'ZUCIANI',
        'heading_2'     => 'New Collection',
        'button_text_2' => 'Khám Phá Ngay',
        'button_url_2'  => '',
        'image_2'       => 0,
        'image_2_alt'   => '',
    );
}

function tst_home_category_grid_default_block() {
    return array(
        'id'          => 'category-grid-default',
        'type'        => 'category_grid',
        'enabled'     => 1,
        'title'       => '',
        'label_1'     => 'TẤT CẢ SẢN PHẨM',
        'url_1'       => '',
        'image_1'     => 0,
        'image_1_alt' => '',
        'label_2'     => 'NEW ARRIVAL',
        'url_2'       => '',
        'image_2'     => 0,
        'image_2_alt' => '',
        'label_3'     => 'BEST SELLER',
        'url_3'       => '',
        'image_3'     => 0,
        'image_3_alt' => '',
        'label_4'     => 'TÚI XÁCH NỮ',
        'url_4'       => '',
        'image_4'     => 0,
        'image_4_alt' => '',
    );
}

function tst_home_newsletter_default_block() {
    return array(
        'id'          => 'newsletter-default',
        'type'        => 'newsletter',
        'enabled'     => 1,
        'title'       => 'Đăng Ký Nhận Bản Tin',
        'description' => 'Đăng ký để nhận cập nhật thông tin về sản phẩm mới và các chương trình ưu đãi!',
        'placeholder' => 'Nhập email của bạn',
    );
}

function tst_home_contact_default_block() {
    return array(
        'id'             => 'contact-default',
        'type'           => 'contact',
        'enabled'        => 1,
        'title'          => 'Đồng Hải lắng nghe bạn!',
        'description'    => 'Chúng tôi luôn trân trọng và mong đợi nhận được mọi ý kiến đóng góp từ khách hàng để có thể nâng cấp trải nghiệm dịch vụ và sản phẩm tốt hơn nữa.',
        'button_text'    => 'Đóng Góp Ý Kiến',
        'button_url'     => '',
        'phone'          => '(028) 7109 2229',
        'email'          => 'cskh@shopdonghai.com',
        'social_heading' => '(Hoặc chat trực tuyến)',
        'facebook_url'   => '',
        'zalo_url'       => '',
        'instagram_url'  => '',
        'youtube_url'    => '',
        'tiktok_url'     => '',
    );
}

function tst_home_add_contact_block_once() {
    if ( get_option( 'tst_contact_block_added', false ) ) {
        return;
    }

    $blocks = get_option( 'tst_home_blocks', false );

    if ( is_array( $blocks ) ) {
        $has_contact = false;
        $insert_at = count( $blocks );

        foreach ( $blocks as $index => $block ) {
            if ( ! is_array( $block ) ) {
                continue;
            }

            if ( 'contact' === ( $block['type'] ?? '' ) ) {
                $has_contact = true;
                break;
            }

            if ( 'newsletter' === ( $block['type'] ?? '' ) ) {
                $insert_at = $index + 1;
            }
        }

        if ( ! $has_contact ) {
            array_splice( $blocks, $insert_at, 0, array( tst_home_contact_default_block() ) );
            update_option( 'tst_home_blocks', $blocks );
        }
    }

    update_option( 'tst_contact_block_added', 1 );
}
add_action( 'init', 'tst_home_add_contact_block_once', 16 );

function tst_home_add_newsletter_block_once() {
    if ( get_option( 'tst_newsletter_block_added', false ) ) {
        return;
    }

    $blocks = get_option( 'tst_home_blocks', false );

    if ( is_array( $blocks ) ) {
        $has_newsletter = false;
        $insert_at = count( $blocks );

        foreach ( $blocks as $index => $block ) {
            if ( ! is_array( $block ) ) {
                continue;
            }

            if ( 'newsletter' === ( $block['type'] ?? '' ) ) {
                $has_newsletter = true;
                break;
            }

            if ( 'category_grid' === ( $block['type'] ?? '' ) ) {
                $insert_at = $index + 1;
            }
        }

        if ( ! $has_newsletter ) {
            array_splice( $blocks, $insert_at, 0, array( tst_home_newsletter_default_block() ) );
            update_option( 'tst_home_blocks', $blocks );
        }
    }

    update_option( 'tst_newsletter_block_added', 1 );
}
add_action( 'init', 'tst_home_add_newsletter_block_once', 15 );

function tst_home_add_category_grid_block_once() {
    if ( get_option( 'tst_category_grid_block_added', false ) ) {
        return;
    }

    $blocks = get_option( 'tst_home_blocks', false );

    if ( is_array( $blocks ) ) {
        $has_category_grid = false;
        $insert_at = count( $blocks );

        foreach ( $blocks as $index => $block ) {
            if ( ! is_array( $block ) ) {
                continue;
            }

            if ( 'category_grid' === ( $block['type'] ?? '' ) ) {
                $has_category_grid = true;
                break;
            }

            if ( 'promo_pair' === ( $block['type'] ?? '' ) ) {
                $insert_at = $index + 1;
            }
        }

        if ( ! $has_category_grid ) {
            array_splice( $blocks, $insert_at, 0, array( tst_home_category_grid_default_block() ) );
            update_option( 'tst_home_blocks', $blocks );
        }
    }

    update_option( 'tst_category_grid_block_added', 1 );
}
add_action( 'init', 'tst_home_add_category_grid_block_once', 14 );

function tst_home_add_promo_pair_block_once() {
    if ( get_option( 'tst_promo_pair_block_added', false ) ) {
        return;
    }

    $blocks = get_option( 'tst_home_blocks', false );

    if ( is_array( $blocks ) ) {
        $has_promo_pair = false;
        $insert_at = count( $blocks );

        foreach ( $blocks as $index => $block ) {
            if ( ! is_array( $block ) ) {
                continue;
            }

            if ( 'promo_pair' === ( $block['type'] ?? '' ) ) {
                $has_promo_pair = true;
                break;
            }

            if ( 'business' === ( $block['type'] ?? '' ) ) {
                $insert_at = $index + 1;
            }
        }

        if ( ! $has_promo_pair ) {
            array_splice( $blocks, $insert_at, 0, array( tst_home_promo_pair_default_block() ) );
            update_option( 'tst_home_blocks', $blocks );
        }
    }

    update_option( 'tst_promo_pair_block_added', 1 );
}
add_action( 'init', 'tst_home_add_promo_pair_block_once', 13 );

function tst_home_add_business_block_once() {
    if ( get_option( 'tst_business_block_added', false ) ) {
        return;
    }

    $blocks = get_option( 'tst_home_blocks', false );

    if ( is_array( $blocks ) ) {
        $has_business = false;
        $insert_at = count( $blocks );

        foreach ( $blocks as $index => $block ) {
            if ( ! is_array( $block ) ) {
                continue;
            }

            if ( 'business' === ( $block['type'] ?? '' ) ) {
                $has_business = true;
                break;
            }

            if ( 'craft' === ( $block['type'] ?? '' ) ) {
                $insert_at = $index + 1;
            }
        }

        if ( ! $has_business ) {
            array_splice( $blocks, $insert_at, 0, array( tst_home_business_default_block() ) );
            update_option( 'tst_home_blocks', $blocks );
        }
    }

    update_option( 'tst_business_block_added', 1 );
}
add_action( 'init', 'tst_home_add_business_block_once', 12 );

function tst_home_add_craft_block_once() {
    if ( get_option( 'tst_craft_block_added', false ) ) {
        return;
    }

    $blocks = get_option( 'tst_home_blocks', false );

    if ( is_array( $blocks ) ) {
        $has_craft = false;
        $insert_at = count( $blocks );

        foreach ( $blocks as $index => $block ) {
            if ( ! is_array( $block ) ) {
                continue;
            }

            if ( 'craft' === ( $block['type'] ?? '' ) ) {
                $has_craft = true;
                break;
            }

            if ( 'lifestyle' === ( $block['type'] ?? '' ) ) {
                $insert_at = $index + 1;
            }
        }

        if ( ! $has_craft ) {
            array_splice( $blocks, $insert_at, 0, array( tst_home_craft_default_block() ) );
            update_option( 'tst_home_blocks', $blocks );
        }
    }

    update_option( 'tst_craft_block_added', 1 );
}
add_action( 'init', 'tst_home_add_craft_block_once', 11 );

function tst_home_add_lifestyle_block_once() {
    if ( get_option( 'tst_lifestyle_block_added', false ) ) {
        return;
    }

    $blocks = get_option( 'tst_home_blocks', false );

    if ( is_array( $blocks ) ) {
        $has_lifestyle = false;
        $insert_at = count( $blocks );

        foreach ( $blocks as $index => $block ) {
            if ( ! is_array( $block ) ) {
                continue;
            }

            if ( 'lifestyle' === ( $block['type'] ?? '' ) ) {
                $has_lifestyle = true;
                break;
            }

            if ( 'tabs' === ( $block['type'] ?? '' ) ) {
                $insert_at = $index + 1;
            }
        }

        if ( ! $has_lifestyle ) {
            array_splice( $blocks, $insert_at, 0, array( tst_home_lifestyle_default_block() ) );
            update_option( 'tst_home_blocks', $blocks );
        }
    }

    update_option( 'tst_lifestyle_block_added', 1 );
}
add_action( 'init', 'tst_home_add_lifestyle_block_once' );

function tst_home_slide_order( $value ) {
    return '2,1' === $value ? '2,1' : '1,2';
}

function tst_home_has_block_type( $type ) {
    foreach ( tst_get_home_blocks() as $block ) {
        if ( is_array( $block ) && ! empty( $block['enabled'] ) && ( $block['type'] ?? '' ) === $type ) {
            return true;
        }
    }

    return false;
}

function tst_get_home_blocks() {
    $blocks = get_option( 'tst_home_blocks', false );

    if ( ! is_array( $blocks ) ) {
        return tst_home_default_blocks();
    }

    $legacy = false;

    foreach ( $blocks as $block ) {
        if ( ! is_array( $block ) ) {
            continue;
        }

        if (
            ( 'hero' === ( $block['type'] ?? '' ) && ! isset( $block['button_text'], $block['slide_order'] ) ) ||
            ( 'tabs' === ( $block['type'] ?? '' ) && ! isset( $block['new_label'], $block['desktop_columns'] ) )
        ) {
            $legacy = true;
            break;
        }
    }

    if ( ! $legacy ) {
        return $blocks;
    }

    $by_type = array_column( tst_home_default_blocks(), null, 'type' );

    foreach ( $blocks as &$block ) {
        if ( ! is_array( $block ) || ! isset( $by_type[ $block['type'] ?? '' ] ) ) {
            continue;
        }

        if ( 'hero' === $block['type'] ) {
            $block['button_text'] = $block['button_text'] ?? $block['text'] ?? $by_type['hero']['button_text'];
            $block['button_url'] = $block['button_url'] ?? $block['url'] ?? $by_type['hero']['button_url'];
        }

        $block = array_merge( $by_type[ $block['type'] ], $block );
    }
    unset( $block );

    return $blocks;
}

function tst_sanitize_home_blocks( $input ) {
    if ( ! is_array( $input ) ) {
        return array();
    }

    $blocks = array();
    $seen = array();
    $types = tst_home_block_types();

    foreach ( array_slice( $input, 0, 30 ) as $block ) {
        if ( ! is_array( $block ) ) {
            continue;
        }

        $id = isset( $block['id'] ) && is_string( $block['id'] )
            ? sanitize_key( wp_unslash( $block['id'] ) )
            : '';
        $type = isset( $block['type'] ) && is_string( $block['type'] )
            ? sanitize_key( wp_unslash( $block['type'] ) )
            : '';

        if ( ! $id || isset( $seen[ $id ] ) || ! isset( $types[ $type ] ) ) {
            continue;
        }

        $seen[ $id ] = true;
        $title = is_string( $block['title'] ?? null ) ? wp_unslash( $block['title'] ) : '';
        $clean = array(
            'id'      => $id,
            'type'    => $type,
            'enabled' => empty( $block['enabled'] ) ? 0 : 1,
            'title'   => sanitize_text_field( $title ),
        );

        if ( 'hero' === $type ) {
            $clean['button_text'] = tst_home_clean_text( $block, 'button_text', $block['text'] ?? '' );
            $clean['button_url'] = tst_home_clean_url( $block, 'button_url', $block['url'] ?? '' );
            $clean['image_1'] = absint( $block['image_1'] ?? 0 );
            $clean['image_1_alt'] = tst_home_clean_text( $block, 'image_1_alt' );
            $clean['image_2'] = absint( $block['image_2'] ?? 0 );
            $clean['image_2_alt'] = tst_home_clean_text( $block, 'image_2_alt' );
            $clean['slide_order'] = tst_home_slide_order( $block['slide_order'] ?? '1,2' );
            $clean['autoplay'] = empty( $block['autoplay'] ) ? 0 : 1;
            $clean['autoplay_interval'] = tst_home_clamp( $block['autoplay_interval'] ?? 6000, 2000, 20000 );
        }

        if ( 'tabs' === $type ) {
            foreach ( array( 'new_label', 'best_label', 'more_text' ) as $key ) {
                $clean[ $key ] = tst_home_clean_text( $block, $key );
            }

            $clean['more_url'] = tst_home_clean_url( $block, 'more_url' );
            $collection_ids = is_array( $block['collection_ids'] ?? null )
                ? $block['collection_ids']
                : array();
            $collection_ids = array_filter( $collection_ids, 'is_scalar' );
            $clean['collection_ids'] = array_slice(
                array_values( array_unique( array_filter( array_map( 'absint', $collection_ids ) ) ) ),
                0,
                6
            );

            foreach ( array( 'more_enabled', 'show_badges', 'show_swatches', 'show_sizes' ) as $key ) {
                $clean[ $key ] = empty( $block[ $key ] ) ? 0 : 1;
            }

            $clean['collection_show_all'] = empty( $block['collection_show_all'] ) ? 0 : 1;

            $ranges = array(
                'products_per_tab' => array( 2, 24 ),
                'desktop_columns'  => array( 1, 6 ),
                'tablet_columns'   => array( 1, 4 ),
                'mobile_columns'   => array( 1, 2 ),
                'card_radius'      => array( 0, 50 ),
                'top_spacing'      => array( 0, 200 ),
                'bottom_spacing'   => array( 0, 200 ),
            );

            foreach ( $ranges as $key => $range ) {
                $clean[ $key ] = tst_home_clamp( $block[ $key ] ?? $range[0], $range[0], $range[1] );
            }

            $color = $block['image_background'] ?? '';
            $clean['image_background'] = is_string( $color )
                ? ( sanitize_hex_color( $color ) ?: '#f5f5f5' )
                : '#f5f5f5';
        }

        if ( 'lifestyle' === $type ) {
            $clean['subtitle'] = tst_home_clean_text( $block, 'subtitle' );
            $clean['button_text'] = tst_home_clean_text( $block, 'button_text' );
            $clean['button_url'] = tst_home_clean_url( $block, 'button_url' );
            $clean['image_id'] = absint( $block['image_id'] ?? 0 );
            $clean['image_alt'] = tst_home_clean_text( $block, 'image_alt' );
        }

        if ( 'craft' === $type ) {
            foreach ( array( 'eyebrow', 'point_1', 'point_2', 'point_3', 'button_text', 'image_1_alt', 'image_2_alt' ) as $key ) {
                $clean[ $key ] = tst_home_clean_text( $block, $key );
            }

            $clean['button_url'] = tst_home_clean_url( $block, 'button_url' );
            $clean['image_1'] = absint( $block['image_1'] ?? 0 );
            $clean['image_2'] = absint( $block['image_2'] ?? 0 );
        }

        if ( 'business' === $type ) {
            foreach ( array( 'subtitle', 'card_1_title', 'card_2_title', 'button_text' ) as $key ) {
                $clean[ $key ] = tst_home_clean_text( $block, $key );
            }

            $clean['button_url'] = tst_home_clean_url( $block, 'button_url' );

            for ( $number = 1; $number <= 3; $number++ ) {
                $clean[ 'image_' . $number ] = absint( $block[ 'image_' . $number ] ?? 0 );
                $clean[ 'image_' . $number . '_alt' ] = tst_home_clean_text(
                    $block,
                    'image_' . $number . '_alt'
                );
            }
        }

        if ( 'promo_pair' === $type ) {
            for ( $number = 1; $number <= 2; $number++ ) {
                foreach ( array( 'label', 'heading', 'button_text', 'image_alt' ) as $field ) {
                    $key = 'image_alt' === $field
                        ? 'image_' . $number . '_alt'
                        : $field . '_' . $number;
                    $clean[ $key ] = tst_home_clean_text( $block, $key );
                }

                $clean[ 'button_url_' . $number ] = tst_home_clean_url(
                    $block,
                    'button_url_' . $number
                );
                $clean[ 'image_' . $number ] = absint( $block[ 'image_' . $number ] ?? 0 );
            }
        }

        if ( 'category_grid' === $type ) {
            for ( $number = 1; $number <= 4; $number++ ) {
                $clean[ 'label_' . $number ] = tst_home_clean_text( $block, 'label_' . $number );
                $clean[ 'url_' . $number ] = tst_home_clean_url( $block, 'url_' . $number );
                $clean[ 'image_' . $number ] = absint( $block[ 'image_' . $number ] ?? 0 );
                $clean[ 'image_' . $number . '_alt' ] = tst_home_clean_text(
                    $block,
                    'image_' . $number . '_alt'
                );
            }
        }

        if ( 'newsletter' === $type ) {
            $clean['description'] = tst_home_clean_text( $block, 'description' );
            $clean['placeholder'] = tst_home_clean_text( $block, 'placeholder' );
        }

        if ( 'contact' === $type ) {
            foreach ( array( 'description', 'button_text', 'phone', 'social_heading' ) as $key ) {
                $clean[ $key ] = tst_home_clean_text( $block, $key );
            }

            $email = $block['email'] ?? '';
            $clean['email'] = is_string( $email )
                ? sanitize_email( wp_unslash( $email ) )
                : '';

            foreach ( array( 'button_url', 'facebook_url', 'zalo_url', 'instagram_url', 'youtube_url', 'tiktok_url' ) as $key ) {
                $clean[ $key ] = tst_home_clean_url( $block, $key );
            }
        }

        if ( 'featured' === $type ) {
            $clean['count'] = tst_home_clamp( $block['count'] ?? 8, 1, 24 );
            $clean['collection_id'] = is_scalar( $block['collection_id'] ?? null )
                ? absint( $block['collection_id'] )
                : 0;
        }

        $blocks[] = $clean;
    }

    return $blocks;
}

function tst_home_clean_text( $block, $key, $fallback = '' ) {
    $value = $block[ $key ] ?? $fallback;

    return is_string( $value ) ? sanitize_text_field( wp_unslash( $value ) ) : '';
}

function tst_home_clean_url( $block, $key, $fallback = '' ) {
    $value = $block[ $key ] ?? $fallback;

    return is_string( $value ) ? esc_url_raw( wp_unslash( $value ) ) : '';
}

function tst_home_clamp( $value, $minimum, $maximum ) {
    return min( $maximum, max( $minimum, absint( $value ) ) );
}

function tst_home_builder_init() {
    register_setting(
        'tst_home_builder',
        'tst_home_blocks',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'tst_sanitize_home_blocks',
        )
    );
}
add_action( 'admin_init', 'tst_home_builder_init' );

function tst_home_builder_menu() {
    add_theme_page(
        __( 'TST Trang chủ', 'tst-custom' ),
        __( 'TST Trang chủ', 'tst-custom' ),
        'manage_options',
        'tst-home-builder',
        'tst_home_builder_page'
    );
}
add_action( 'admin_menu', 'tst_home_builder_menu' );

function tst_home_builder_assets( $hook_suffix ) {
    if ( 'appearance_page_tst-home-builder' !== $hook_suffix ) {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_style(
        'tst-home-builder',
        TST_URI . '/assets/css/admin/home-builder.css',
        array(),
        TST_VERSION
    );
    wp_enqueue_script(
        'tst-home-builder',
        TST_URI . '/assets/js/admin/home-builder.js',
        array( 'media-views' ),
        TST_VERSION,
        true
    );
    wp_localize_script(
        'tst-home-builder',
        'tstHomeBuilder',
        array(
            'remove' => __( 'Xóa block này?', 'tst-custom' ),
            'media'  => __( 'Chọn ảnh banner', 'tst-custom' ),
            'use'    => __( 'Dùng ảnh này', 'tst-custom' ),
            'expand' => __( 'Mở rộng', 'tst-custom' ),
            'collapse' => __( 'Thu gọn', 'tst-custom' ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'tst_home_builder_assets' );

function tst_home_builder_field( $index, $key, $label, $value, $type = 'text', $min = null, $max = null ) {
    $name = 'tst_home_blocks[' . $index . '][' . $key . ']';
    ?>
    <label class="tst-builder__field">
      <span><?php echo esc_html( $label ); ?></span>
      <input
        type="<?php echo esc_attr( $type ); ?>"
        name="<?php echo esc_attr( $name ); ?>"
        value="<?php echo esc_attr( $value ); ?>"
        <?php if ( null !== $min ) : ?>min="<?php echo esc_attr( $min ); ?>"<?php endif; ?>
        <?php if ( null !== $max ) : ?>max="<?php echo esc_attr( $max ); ?>"<?php endif; ?>
      >
    </label>
    <?php
}

function tst_home_builder_checkbox( $index, $key, $label, $block, $default = 1 ) {
    ?>
    <label class="tst-builder__enabled">
      <input
        type="checkbox"
        name="tst_home_blocks[<?php echo esc_attr( $index ); ?>][<?php echo esc_attr( $key ); ?>]"
        value="1"
        <?php checked( $block[ $key ] ?? $default, 1 ); ?>
      >
      <?php echo esc_html( $label ); ?>
    </label>
    <?php
}

function tst_home_builder_image_field( $index, $number, $block ) {
    $key = is_numeric( $number ) ? 'image_' . $number : $number;
    $label = is_numeric( $number )
        ? sprintf( __( 'Ảnh slide %d', 'tst-custom' ), $number )
        : __( 'Ảnh banner', 'tst-custom' );
    $id = absint( $block[ $key ] ?? 0 );
    $image_url = $id && wp_attachment_is_image( $id )
        ? wp_get_attachment_image_url( $id, 'medium' )
        : '';
    ?>
    <div class="tst-builder__image" data-tst-builder-image>
      <span><?php echo esc_html( $label ); ?></span>
      <input
        type="hidden"
        name="tst_home_blocks[<?php echo esc_attr( $index ); ?>][<?php echo esc_attr( $key ); ?>]"
        value="<?php echo esc_attr( $id ); ?>"
        data-tst-builder-image-id
      >
      <img
        src="<?php echo esc_url( $image_url ); ?>"
        alt=""
        data-tst-builder-preview
        <?php echo $image_url ? '' : 'hidden'; ?>
      >
      <button type="button" class="button" data-tst-builder-pick>
        <?php esc_html_e( 'Chọn ảnh', 'tst-custom' ); ?>
      </button>
      <button type="button" class="button" data-tst-builder-clear>
        <?php esc_html_e( 'Ảnh mặc định', 'tst-custom' ); ?>
      </button>
    </div>
    <?php
}

function tst_home_builder_collections_field( $index, $block, $multiple = false ) {
    $terms = tst_home_product_collections();
    $selected_ids = $multiple
        ? (array) ( $block['collection_ids'] ?? array() )
        : array( absint( $block['collection_id'] ?? 0 ) );
    $selected_ids = array_map( 'absint', $selected_ids );

    if ( ! $terms ) {
        $name = 'tst_home_blocks[' . $index . '][';
        $name .= $multiple ? 'collection_ids][]' : 'collection_id]';

        foreach ( $selected_ids as $selected_id ) {
            if ( ! $selected_id ) {
                continue;
            }
            ?>
            <input
              type="hidden"
              name="<?php echo esc_attr( $name ); ?>"
              value="<?php echo esc_attr( $selected_id ); ?>"
            >
            <?php
        }

        echo '<p class="description">';
        esc_html_e( 'Chưa có collection WooCommerce để chọn.', 'tst-custom' );
        echo '</p>';
        return;
    }

    if ( $multiple ) {
        ?>
        <div class="tst-builder__field">
          <span><?php esc_html_e( 'Collection hiển thị thành tab', 'tst-custom' ); ?></span>
          <div class="tst-builder__collections">
            <?php foreach ( $terms as $term ) : ?>
              <label>
                <input
                  type="checkbox"
                  name="tst_home_blocks[<?php echo esc_attr( $index ); ?>][collection_ids][]"
                  value="<?php echo esc_attr( $term->term_id ); ?>"
                  <?php checked( in_array( $term->term_id, $selected_ids, true ) ); ?>
                >
                <?php echo esc_html( $term->name ); ?>
              </label>
            <?php endforeach; ?>
          </div>
          <span class="description">
            <?php esc_html_e( 'Chọn tối đa 6 danh mục. Để trống để tự chọn.', 'tst-custom' ); ?>
          </span>
        </div>
        <?php
        return;
    }
    ?>
    <label class="tst-builder__field">
      <span><?php esc_html_e( 'Collection sản phẩm', 'tst-custom' ); ?></span>
      <select name="tst_home_blocks[<?php echo esc_attr( $index ); ?>][collection_id]">
        <option value="0"><?php esc_html_e( 'Tất cả sản phẩm', 'tst-custom' ); ?></option>
        <?php foreach ( $terms as $term ) : ?>
          <option value="<?php echo esc_attr( $term->term_id ); ?>"
            <?php selected( $selected_ids[0], $term->term_id ); ?>>
            <?php echo esc_html( $term->name ); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <?php
}

function tst_home_builder_block( $block, $index ) {
    $type = $block['type'] ?? '';
    $types = tst_home_block_types();

    if ( ! isset( $types[ $type ] ) ) {
        return;
    }
    ?>
    <section class="tst-builder__block" data-tst-builder-block draggable="true">
      <div class="tst-builder__toolbar">
        <span class="tst-builder__handle" aria-hidden="true">☰</span>
        <strong><?php echo esc_html( $types[ $type ] ); ?></strong>
        <button type="button" class="button" data-tst-builder-toggle aria-expanded="true">
          <?php esc_html_e( 'Thu gọn', 'tst-custom' ); ?>
        </button>
        <button type="button" class="button" data-tst-builder-up>↑</button>
        <button type="button" class="button" data-tst-builder-down>↓</button>
        <button type="button" class="button-link-delete" data-tst-builder-remove>
          <?php esc_html_e( 'Xóa', 'tst-custom' ); ?>
        </button>
      </div>
      <div class="tst-builder__fields" data-tst-builder-fields>
        <input type="hidden" name="tst_home_blocks[<?php echo esc_attr( $index ); ?>][id]"
          value="<?php echo esc_attr( $block['id'] ?? '' ); ?>" data-tst-builder-name="id">
        <input type="hidden" name="tst_home_blocks[<?php echo esc_attr( $index ); ?>][type]"
          value="<?php echo esc_attr( $type ); ?>" data-tst-builder-name="type">
        <?php tst_home_builder_checkbox( $index, 'enabled', __( 'Hiển thị', 'tst-custom' ), $block ); ?>
        <?php
        if ( 'hero' === $type ) {
            ?>
            <fieldset class="tst-builder__group">
              <legend><?php esc_html_e( 'Nội dung', 'tst-custom' ); ?></legend>
            <?php
            tst_home_builder_field( $index, 'title', __( 'Tiêu đề', 'tst-custom' ), $block['title'] ?? '' );
            tst_home_builder_collections_field( $index, $block, true );
            tst_home_builder_field(
                $index,
                'button_text',
                __( 'Chữ trên nút', 'tst-custom' ),
                $block['button_text'] ?? ''
            );
            tst_home_builder_field(
                $index,
                'button_url',
                __( 'Liên kết nút', 'tst-custom' ),
                $block['button_url'] ?? '',
                'url'
            );
            echo '</fieldset><fieldset class="tst-builder__group"><legend>';
            esc_html_e( 'Hình ảnh', 'tst-custom' );
            echo '</legend>';
            tst_home_builder_image_field( $index, 1, $block );
            tst_home_builder_field(
                $index,
                'image_1_alt',
                __( 'Mô tả ảnh 1', 'tst-custom' ),
                $block['image_1_alt'] ?? ''
            );
            tst_home_builder_image_field( $index, 2, $block );
            tst_home_builder_field(
                $index,
                'image_2_alt',
                __( 'Mô tả ảnh 2', 'tst-custom' ),
                $block['image_2_alt'] ?? ''
            );
            echo '</fieldset><fieldset class="tst-builder__group"><legend>';
            esc_html_e( 'Trình chiếu', 'tst-custom' );
            echo '</legend>';
            ?>
            <label class="tst-builder__field">
              <span><?php esc_html_e( 'Thứ tự slide', 'tst-custom' ); ?></span>
              <select name="tst_home_blocks[<?php echo esc_attr( $index ); ?>][slide_order]">
                <option value="1,2" <?php selected( $block['slide_order'] ?? '1,2', '1,2' ); ?>>1 → 2</option>
                <option value="2,1" <?php selected( $block['slide_order'] ?? '1,2', '2,1' ); ?>>2 → 1</option>
              </select>
            </label>
            <?php
            tst_home_builder_checkbox( $index, 'autoplay', __( 'Tự chuyển slide', 'tst-custom' ), $block );
            tst_home_builder_field(
                $index,
                'autoplay_interval',
                __( 'Thời gian chuyển (ms)', 'tst-custom' ),
                $block['autoplay_interval'] ?? 6000,
                'number',
                2000,
                20000
            );
            echo '</fieldset>';
        }

        if ( 'tabs' === $type ) {
            ?>
            <fieldset class="tst-builder__group">
              <legend><?php esc_html_e( 'Nội dung', 'tst-custom' ); ?></legend>
            <?php
            tst_home_builder_field( $index, 'title', __( 'Tiêu đề', 'tst-custom' ), $block['title'] ?? '' );
            tst_home_builder_field(
                $index,
                'new_label',
                __( 'Nhãn dự phòng: sản phẩm mới', 'tst-custom' ),
                $block['new_label'] ?? 'Sản phẩm mới'
            );
            tst_home_builder_field(
                $index,
                'best_label',
                __( 'Nhãn dự phòng: bán chạy', 'tst-custom' ),
                $block['best_label'] ?? 'Best Seller'
            );
            tst_home_builder_checkbox(
                $index,
                'more_enabled',
                __( 'Hiện liên kết xem thêm', 'tst-custom' ),
                $block
            );
            tst_home_builder_field(
                $index,
                'more_text',
                __( 'Chữ xem thêm', 'tst-custom' ),
                $block['more_text'] ?? 'Xem thêm'
            );
            tst_home_builder_field(
                $index,
                'more_url',
                __( 'URL xem thêm', 'tst-custom' ),
                $block['more_url'] ?? '',
                'url'
            );
            echo '</fieldset><fieldset class="tst-builder__group"><legend>';
            esc_html_e( 'Sản phẩm và bố cục', 'tst-custom' );
            echo '</legend>';
            tst_home_builder_field(
                $index,
                'products_per_tab',
                __( 'Giới hạn sản phẩm mỗi tab', 'tst-custom' ),
                $block['products_per_tab'] ?? 8,
                'number',
                2,
                24
            );
            tst_home_builder_checkbox(
                $index,
                'collection_show_all',
                __( 'Hiển thị toàn bộ collection (tối đa 24 sản phẩm)', 'tst-custom' ),
                $block
            );
            tst_home_builder_field(
                $index,
                'desktop_columns',
                __( 'Cột desktop', 'tst-custom' ),
                $block['desktop_columns'] ?? 4,
                'number',
                1,
                6
            );
            tst_home_builder_field(
                $index,
                'tablet_columns',
                __( 'Cột tablet', 'tst-custom' ),
                $block['tablet_columns'] ?? 3,
                'number',
                1,
                4
            );
            tst_home_builder_field(
                $index,
                'mobile_columns',
                __( 'Cột mobile', 'tst-custom' ),
                $block['mobile_columns'] ?? 2,
                'number',
                1,
                2
            );
            echo '</fieldset><fieldset class="tst-builder__group"><legend>';
            esc_html_e( 'Hiển thị và khoảng cách', 'tst-custom' );
            echo '</legend>';
            tst_home_builder_checkbox( $index, 'show_badges', __( 'Hiện nhãn sản phẩm', 'tst-custom' ), $block );
            tst_home_builder_checkbox( $index, 'show_swatches', __( 'Hiện màu sắc', 'tst-custom' ), $block );
            tst_home_builder_checkbox( $index, 'show_sizes', __( 'Hiện kích thước', 'tst-custom' ), $block );
            tst_home_builder_field(
                $index,
                'image_background',
                __( 'Màu nền ảnh', 'tst-custom' ),
                $block['image_background'] ?? '#f5f5f5',
                'color'
            );
            tst_home_builder_field(
                $index,
                'card_radius',
                __( 'Bo góc thẻ (px)', 'tst-custom' ),
                $block['card_radius'] ?? 0,
                'number',
                0,
                50
            );
            tst_home_builder_field(
                $index,
                'top_spacing',
                __( 'Khoảng cách trên (px)', 'tst-custom' ),
                $block['top_spacing'] ?? 80,
                'number',
                0,
                200
            );
            tst_home_builder_field(
                $index,
                'bottom_spacing',
                __( 'Khoảng cách dưới (px)', 'tst-custom' ),
                $block['bottom_spacing'] ?? 80,
                'number',
                0,
                200
            );
            echo '</fieldset>';
        }

        if ( 'contact' === $type ) {
            foreach ( array( 'title', 'description', 'button_text', 'button_url', 'phone', 'email', 'social_heading' ) as $key ) {
                $labels = array(
                    'title'          => __( 'Tiêu đề', 'tst-custom' ),
                    'description'    => __( 'Mô tả', 'tst-custom' ),
                    'button_text'    => __( 'Chữ trên nút', 'tst-custom' ),
                    'button_url'     => __( 'Liên kết nút', 'tst-custom' ),
                    'phone'          => __( 'Hotline', 'tst-custom' ),
                    'email'          => __( 'Email', 'tst-custom' ),
                    'social_heading' => __( 'Nhãn mạng xã hội', 'tst-custom' ),
                );
                $input_type = in_array( $key, array( 'button_url' ), true )
                    ? 'url'
                    : ( 'email' === $key ? 'email' : 'text' );
                tst_home_builder_field(
                    $index,
                    $key,
                    $labels[ $key ],
                    $block[ $key ] ?? '',
                    $input_type
                );
            }

            foreach ( array( 'facebook', 'zalo', 'instagram', 'youtube', 'tiktok' ) as $platform ) {
                $key = $platform . '_url';
                tst_home_builder_field(
                    $index,
                    $key,
                    ucfirst( $platform ) . ' URL',
                    $block[ $key ] ?? '',
                    'url'
                );
            }
        }

        if ( 'newsletter' === $type ) {
            tst_home_builder_field( $index, 'title', __( 'Tiêu đề', 'tst-custom' ), $block['title'] ?? '' );
            tst_home_builder_field(
                $index,
                'description',
                __( 'Mô tả', 'tst-custom' ),
                $block['description'] ?? ''
            );
            tst_home_builder_field(
                $index,
                'placeholder',
                __( 'Gợi ý trong ô email', 'tst-custom' ),
                $block['placeholder'] ?? ''
            );
        }

        if ( 'category_grid' === $type ) {
            for ( $number = 1; $number <= 4; $number++ ) {
                ?>
                <fieldset class="tst-builder__group">
                  <legend>
                    <?php echo esc_html( sprintf( __( 'Ô danh mục %d', 'tst-custom' ), $number ) ); ?>
                  </legend>
                  <?php
                  tst_home_builder_field(
                      $index,
                      'label_' . $number,
                      __( 'Tên hiển thị', 'tst-custom' ),
                      $block[ 'label_' . $number ] ?? ''
                  );
                  tst_home_builder_field(
                      $index,
                      'url_' . $number,
                      __( 'Liên kết', 'tst-custom' ),
                      $block[ 'url_' . $number ] ?? '',
                      'url'
                  );
                  tst_home_builder_image_field( $index, $number, $block );
                  tst_home_builder_field(
                      $index,
                      'image_' . $number . '_alt',
                      __( 'Mô tả ảnh', 'tst-custom' ),
                      $block[ 'image_' . $number . '_alt' ] ?? ''
                  );
                  ?>
                </fieldset>
                <?php
            }
        }

        if ( 'promo_pair' === $type ) {
            for ( $number = 1; $number <= 2; $number++ ) {
                ?>
                <fieldset class="tst-builder__group">
                  <legend>
                    <?php
                    echo esc_html(
                        sprintf( __( 'Banner %d', 'tst-custom' ), $number )
                    );
                    ?>
                  </legend>
                  <?php
                  foreach ( array( 'label', 'heading', 'button_text' ) as $field ) {
                      $key = $field . '_' . $number;
                      $labels = array(
                          'label'       => __( 'Nhãn nhỏ', 'tst-custom' ),
                          'heading'     => __( 'Tiêu đề', 'tst-custom' ),
                          'button_text' => __( 'Chữ trên nút', 'tst-custom' ),
                      );
                      tst_home_builder_field( $index, $key, $labels[ $field ], $block[ $key ] ?? '' );
                  }

                  $url_key = 'button_url_' . $number;
                  tst_home_builder_field(
                      $index,
                      $url_key,
                      __( 'Liên kết nút', 'tst-custom' ),
                      $block[ $url_key ] ?? '',
                      'url'
                  );
                  tst_home_builder_image_field( $index, $number, $block );

                  $alt_key = 'image_' . $number . '_alt';
                  tst_home_builder_field(
                      $index,
                      $alt_key,
                      __( 'Mô tả ảnh', 'tst-custom' ),
                      $block[ $alt_key ] ?? ''
                  );
                  ?>
                </fieldset>
                <?php
            }
        }

        if ( 'business' === $type ) {
            tst_home_builder_field( $index, 'title', __( 'Tiêu đề', 'tst-custom' ), $block['title'] ?? '' );
            tst_home_builder_field( $index, 'subtitle', __( 'Mô tả', 'tst-custom' ), $block['subtitle'] ?? '' );
            tst_home_builder_field(
                $index,
                'card_1_title',
                __( 'Tiêu đề thẻ 1', 'tst-custom' ),
                $block['card_1_title'] ?? ''
            );
            tst_home_builder_field(
                $index,
                'card_2_title',
                __( 'Tiêu đề thẻ 2', 'tst-custom' ),
                $block['card_2_title'] ?? ''
            );
            tst_home_builder_field(
                $index,
                'button_text',
                __( 'Chữ trên nút', 'tst-custom' ),
                $block['button_text'] ?? ''
            );
            tst_home_builder_field(
                $index,
                'button_url',
                __( 'Liên kết nút', 'tst-custom' ),
                $block['button_url'] ?? '',
                'url'
            );

            for ( $image_number = 1; $image_number <= 3; $image_number++ ) {
                $alt_key = 'image_' . $image_number . '_alt';
                tst_home_builder_image_field( $index, $image_number, $block );
                tst_home_builder_field(
                    $index,
                    $alt_key,
                    sprintf( __( 'Mô tả ảnh %d', 'tst-custom' ), $image_number ),
                    $block[ $alt_key ] ?? ''
                );
            }
        }

        if ( 'craft' === $type ) {
            tst_home_builder_field( $index, 'eyebrow', __( 'Nhãn nhỏ', 'tst-custom' ), $block['eyebrow'] ?? '' );
            tst_home_builder_field( $index, 'title', __( 'Tiêu đề', 'tst-custom' ), $block['title'] ?? '' );

            for ( $point = 1; $point <= 3; $point++ ) {
                $key = 'point_' . $point;
                tst_home_builder_field(
                    $index,
                    $key,
                    sprintf( __( 'Dòng nội dung %d', 'tst-custom' ), $point ),
                    $block[ $key ] ?? ''
                );
            }

            tst_home_builder_field(
                $index,
                'button_text',
                __( 'Chữ trên nút', 'tst-custom' ),
                $block['button_text'] ?? ''
            );
            tst_home_builder_field(
                $index,
                'button_url',
                __( 'Liên kết nút', 'tst-custom' ),
                $block['button_url'] ?? '',
                'url'
            );

            for ( $image_number = 1; $image_number <= 2; $image_number++ ) {
                $key = 'image_' . $image_number . '_alt';
                tst_home_builder_image_field( $index, $image_number, $block );
                tst_home_builder_field(
                    $index,
                    $key,
                    sprintf( __( 'Mô tả ảnh %d', 'tst-custom' ), $image_number ),
                    $block[ $key ] ?? ''
                );
            }
        }

        if ( 'lifestyle' === $type ) {
            tst_home_builder_field( $index, 'title', __( 'Tiêu đề', 'tst-custom' ), $block['title'] ?? '' );
            tst_home_builder_field( $index, 'subtitle', __( 'Mô tả', 'tst-custom' ), $block['subtitle'] ?? '' );
            tst_home_builder_field( $index, 'button_text', __( 'Chữ trên nút', 'tst-custom' ), $block['button_text'] ?? '' );
            tst_home_builder_field( $index, 'button_url', __( 'Liên kết nút', 'tst-custom' ), $block['button_url'] ?? '', 'url' );
            tst_home_builder_image_field( $index, 'image_id', $block );
            tst_home_builder_field( $index, 'image_alt', __( 'Mô tả ảnh', 'tst-custom' ), $block['image_alt'] ?? '' );
        }

        if ( 'featured' === $type ) {
            tst_home_builder_field( $index, 'title', __( 'Tiêu đề', 'tst-custom' ), $block['title'] ?? '' );
            tst_home_builder_collections_field( $index, $block );
            tst_home_builder_field(
                $index,
                'count',
                __( 'Số sản phẩm', 'tst-custom' ),
                $block['count'] ?? 8,
                'number',
                1,
                24
            );
        }
        ?>
      </div>
    </section>
    <?php
}

function tst_home_builder_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Bạn không có quyền chỉnh sửa trang này.', 'tst-custom' ) );
    }

    $blocks = tst_get_home_blocks();
    ?>
    <div class="wrap tst-builder">
      <h1><?php esc_html_e( 'TST Trang chủ', 'tst-custom' ); ?></h1>
      <p>
        <?php
        esc_html_e(
            'Kéo để sắp xếp, thêm hoặc xóa block. Bấm Lưu thay đổi khi hoàn tất.',
            'tst-custom'
        );
        ?>
      </p>
      <form method="post" action="options.php" data-tst-builder-form>
        <?php settings_fields( 'tst_home_builder' ); ?>
        <input type="hidden" name="tst_home_blocks[_empty]" value="1">
        <div class="tst-builder__list" data-tst-builder-list>
          <?php foreach ( $blocks as $index => $block ) : ?>
            <?php tst_home_builder_block( $block, $index ); ?>
          <?php endforeach; ?>
        </div>
        <div class="tst-builder__actions">
          <select data-tst-builder-type aria-label="<?php esc_attr_e( 'Loại block', 'tst-custom' ); ?>">
            <?php foreach ( tst_home_block_types() as $type => $label ) : ?>
              <option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
          </select>
          <button type="button" class="button" data-tst-builder-add>
            <?php esc_html_e( 'Thêm block', 'tst-custom' ); ?>
          </button>
        </div>
        <?php submit_button(); ?>
      </form>
      <template data-tst-builder-template="hero">
        <?php tst_home_builder_block( array( 'id' => '', 'type' => 'hero', 'enabled' => 1 ), '__INDEX__' ); ?>
      </template>
      <template data-tst-builder-template="tabs">
        <?php tst_home_builder_block( array( 'id' => '', 'type' => 'tabs', 'enabled' => 1 ), '__INDEX__' ); ?>
      </template>
      <template data-tst-builder-template="lifestyle">
        <?php tst_home_builder_block( tst_home_lifestyle_default_block(), '__INDEX__' ); ?>
      </template>
      <template data-tst-builder-template="craft">
        <?php tst_home_builder_block( tst_home_craft_default_block(), '__INDEX__' ); ?>
      </template>
      <template data-tst-builder-template="business">
        <?php tst_home_builder_block( tst_home_business_default_block(), '__INDEX__' ); ?>
      </template>
      <template data-tst-builder-template="promo_pair">
        <?php tst_home_builder_block( tst_home_promo_pair_default_block(), '__INDEX__' ); ?>
      </template>
      <template data-tst-builder-template="category_grid">
        <?php tst_home_builder_block( tst_home_category_grid_default_block(), '__INDEX__' ); ?>
      </template>
      <template data-tst-builder-template="newsletter">
        <?php tst_home_builder_block( tst_home_newsletter_default_block(), '__INDEX__' ); ?>
      </template>
      <template data-tst-builder-template="contact">
        <?php tst_home_builder_block( tst_home_contact_default_block(), '__INDEX__' ); ?>
      </template>
      <template data-tst-builder-template="featured">
        <?php tst_home_builder_block( array( 'id' => '', 'type' => 'featured', 'enabled' => 1 ), '__INDEX__' ); ?>
      </template>
    </div>
    <?php
}
