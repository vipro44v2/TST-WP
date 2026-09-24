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

function tst_get_product_card( $product = null, $options = array() ) {
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

    get_template_part(
        'template-parts/collection/product-card',
        null,
        array(
            'product' => $product,
            'options' => $options,
        )
    );
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

function tst_product_attribute_kind( $key ) {
    $key = strtolower( remove_accents( (string) $key ) );

    if ( preg_match( '/(^|[_-])(color|colour|mau)([_-]|$)/', $key ) ) {
        return 'color';
    }

    if ( preg_match( '/(^|[_-])(size|kich)([_-]|$)/', $key ) ) {
        return 'size';
    }

    return '';
}

function tst_product_attribute_label( $attribute_key, $value ) {
    if ( taxonomy_exists( $attribute_key ) ) {
        $term = get_term_by( 'slug', $value, $attribute_key );

        if ( $term && ! is_wp_error( $term ) ) {
            return $term->name;
        }
    }

    return $value;
}

function tst_product_color_hex( $attribute_key, $value ) {
    if ( ! taxonomy_exists( $attribute_key ) ) {
        return '';
    }

    $term = get_term_by( 'slug', $value, $attribute_key );

    if ( ! $term || is_wp_error( $term ) ) {
        return '';
    }

    foreach ( array( 'tst_color_swatch', 'color', 'swatch_color', 'product_attribute_color' ) as $key ) {
        $value = get_term_meta( $term->term_id, $key, true );
        $hex = is_string( $value ) ? sanitize_hex_color( $value ) : '';

        if ( $hex ) {
            return $hex;
        }
    }

    return '';
}

function tst_get_product_color_options( $product ) {
    if ( ! class_exists( 'WC_Product_Variable' ) || ! $product instanceof WC_Product_Variable ) {
        return array();
    }

    $colors = array();

    foreach ( $product->get_variation_attributes() as $attribute_key => $values ) {
        if ( 'color' !== tst_product_attribute_kind( $attribute_key ) || ! is_array( $values ) ) {
            continue;
        }

        foreach ( $values as $value ) {
            if ( ! is_string( $value ) || '' === $value ) {
                continue;
            }

            $label = tst_product_attribute_label( $attribute_key, $value );
            $colors[ $label ] = tst_product_color_hex( $attribute_key, $value );
        }
    }

    return $colors;
}

function tst_get_product_quick_variations( $product ) {
    if ( ! class_exists( 'WC_Product_Variable' ) || ! $product instanceof WC_Product_Variable ) {
        return array();
    }

    $choices = array();
    $variations = $product->get_available_variations( 'objects' );

    foreach ( $variations as $variation ) {
        if ( ! $variation->is_purchasable() || ! $variation->is_in_stock() ) {
            continue;
        }

        $attributes = $variation->get_variation_attributes();
        $color = '';
        $color_hex = '';
        $size = '';
        $valid = true;

        foreach ( $attributes as $key => $value ) {
            if ( '' === $value ) {
                $valid = false;
                break;
            }

            $kind = tst_product_attribute_kind( $key );

            if ( 'color' === $kind ) {
                $attribute_key = substr( $key, 10 );
                $color = tst_product_attribute_label( $attribute_key, $value );
                $color_hex = tst_product_color_hex( $attribute_key, $value );
            }

            if ( 'size' === $kind ) {
                $size = tst_product_attribute_label( substr( $key, 10 ), $value );
            }

            if ( '' === $kind ) {
                $valid = false;
                break;
            }
        }

        if ( ! $valid || '' === $size ) {
            continue;
        }

        $choices[] = array(
            'id'       => $variation->get_id(),
            'color'    => $color,
            'color_hex' => $color_hex,
            'size'     => $size,
        );
    }

    return $choices;
}
