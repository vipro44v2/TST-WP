<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
    return;
}

global $product;
tst_get_product_card( $product );
