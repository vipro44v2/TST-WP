<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tst_collection_filter_value( $key ) {
    $value = $_GET[ $key ] ?? '';

    return is_string( $value ) ? sanitize_title( wp_unslash( $value ) ) : '';
}

function tst_collection_brand_taxonomy() {
    foreach ( array( 'product_brand', 'pa_brand' ) as $taxonomy ) {
        if ( taxonomy_exists( $taxonomy ) ) {
            return $taxonomy;
        }
    }

    return '';
}

function tst_collection_filter_products( $query ) {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    $filters = array(
        'tst_color' => 'pa_color',
        'tst_size'  => 'pa_size',
        'tst_brand' => tst_collection_brand_taxonomy(),
    );
    $tax_query = (array) $query->get( 'tax_query' );

    foreach ( $filters as $key => $taxonomy ) {
        $slug = tst_collection_filter_value( $key );

        if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) || ! $slug ) {
            continue;
        }

        $tax_query[] = array(
            'taxonomy' => $taxonomy,
            'field'    => 'slug',
            'terms'    => array( $slug ),
        );
    }

    $query->set( 'tax_query', $tax_query );
}
add_action( 'woocommerce_product_query', 'tst_collection_filter_products' );
