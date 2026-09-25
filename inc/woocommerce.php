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

function tst_checkout_fields( $fields ) {
    if ( ! is_array( $fields ) ) {
        return $fields;
    }

    $billing_fields = array(
        'billing_first_name' => array(
            'label'       => __( 'Họ', 'tst-custom' ),
            'placeholder' => __( 'Nhập họ', 'tst-custom' ),
            'priority'    => 10,
        ),
        'billing_last_name' => array(
            'label'       => __( 'Tên', 'tst-custom' ),
            'placeholder' => __( 'Nhập tên', 'tst-custom' ),
            'priority'    => 20,
        ),
        'billing_phone' => array(
            'label'       => __( 'Số điện thoại', 'tst-custom' ),
            'placeholder' => __( 'Nhập số điện thoại', 'tst-custom' ),
            'priority'    => 30,
        ),
        'billing_email' => array(
            'label'       => __( 'Địa chỉ email', 'tst-custom' ),
            'placeholder' => __( 'Nhập địa chỉ email', 'tst-custom' ),
            'priority'    => 40,
        ),
        'billing_country' => array(
            'label'    => __( 'Quốc gia/Khu vực', 'tst-custom' ),
            'priority' => 50,
        ),
        'billing_state' => array(
            'label'    => __( 'Tỉnh/Thành phố', 'tst-custom' ),
            'priority' => 60,
        ),
        'billing_city' => array(
            'label'    => __( 'Quận/Huyện', 'tst-custom' ),
            'priority' => 70,
        ),
        'billing_address_1' => array(
            'label'       => __( 'Địa chỉ', 'tst-custom' ),
            'placeholder' => __( 'Số nhà, tên đường...', 'tst-custom' ),
            'priority'    => 80,
        ),
        'billing_address_2' => array(
            'label'    => __( 'Địa chỉ bổ sung', 'tst-custom' ),
            'priority' => 90,
        ),
        'billing_postcode' => array(
            'label'    => __( 'Mã bưu chính', 'tst-custom' ),
            'priority' => 100,
        ),
        'billing_company' => array(
            'label'    => __( 'Tên công ty', 'tst-custom' ),
            'priority' => 110,
        ),
    );

    foreach ( $billing_fields as $field_name => $settings ) {
        if ( isset( $fields['billing'][ $field_name ] ) ) {
            $fields['billing'][ $field_name ] = array_merge( $fields['billing'][ $field_name ], $settings );
        }
    }

    if ( isset( $fields['order']['order_comments'] ) ) {
        $fields['order']['order_comments']['label'] = __( 'Ghi chú đơn hàng (tùy chọn)', 'tst-custom' );
        $fields['order']['order_comments']['placeholder'] = __( 'Thời gian giao hàng hoặc chỉ dẫn thêm...', 'tst-custom' );
    }

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'tst_checkout_fields' );

function tst_validate_registration_phone( $errors ) {
    if (
        ! function_exists( 'is_account_page' ) ||
        ! is_account_page() ||
        ! isset( $_POST['register'] )
    ) {
        return $errors;
    }

    $phone = isset( $_POST['tst_register_phone'] ) && is_string( $_POST['tst_register_phone'] )
        ? sanitize_text_field( wp_unslash( $_POST['tst_register_phone'] ) )
        : '';

    if ( '' === $phone ) {
        $errors->add( 'tst_phone_required', __( 'Vui lòng nhập số điện thoại.', 'tst-custom' ) );
    } elseif ( ! preg_match( '/^[0-9+().\s-]{8,20}$/', $phone ) ) {
        $errors->add( 'tst_phone_invalid', __( 'Số điện thoại không hợp lệ.', 'tst-custom' ) );
    }

    return $errors;
}
add_filter( 'woocommerce_registration_errors', 'tst_validate_registration_phone' );

function tst_save_registration_phone( $customer_id ) {
    if ( ! isset( $_POST['tst_register_phone'] ) || ! is_string( $_POST['tst_register_phone'] ) ) {
        return;
    }

    $phone = sanitize_text_field( wp_unslash( $_POST['tst_register_phone'] ) );
    update_user_meta( $customer_id, 'billing_phone', $phone );
}
add_action( 'woocommerce_created_customer', 'tst_save_registration_phone' );

function tst_checkout_gettext( $translated, $text, $domain ) {
    if ( 'woocommerce' !== $domain || ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
        return $translated;
    }

    $labels = array(
        'Billing details'              => __( 'Thông tin thanh toán', 'tst-custom' ),
        'Billing & Shipping'           => __( 'Thông tin thanh toán', 'tst-custom' ),
        'Billing &amp; Shipping'       => __( 'Thông tin thanh toán', 'tst-custom' ),
        'Ship to a different address?' => __( 'Giao hàng đến một địa chỉ khác?', 'tst-custom' ),
        'Your order'                   => __( 'Đơn hàng của bạn', 'tst-custom' ),
        'Create an account?'           => __( 'Tạo tài khoản mới?', 'tst-custom' ),
        'Additional information'       => __( 'Thông tin bổ sung', 'tst-custom' ),
        'Product'                      => __( 'Sản phẩm', 'tst-custom' ),
        'Subtotal'                     => __( 'Tạm tính', 'tst-custom' ),
        'Total'                        => __( 'Tổng', 'tst-custom' ),
        'Shipping'                     => __( 'Vận chuyển', 'tst-custom' ),
        'optional'                     => __( 'tùy chọn', 'tst-custom' ),
    );

    return $labels[ $text ] ?? $translated;
}
add_filter( 'gettext', 'tst_checkout_gettext', 10, 3 );
