<?php
get_header();

$tst_has_woocommerce = class_exists( 'WooCommerce' );
$tst_shop_url        = home_url( '/' );

if ( $tst_has_woocommerce && function_exists( 'wc_get_page_permalink' ) ) {
    $tst_shop_url = wc_get_page_permalink( 'shop' );
}
?>
<?php
foreach ( tst_get_home_blocks() as $tst_block ) {
    if ( ! is_array( $tst_block ) || empty( $tst_block['enabled'] ) ) {
        continue;
    }

    $tst_type = $tst_block['type'] ?? '';

    if ( 'hero' === $tst_type ) {
        get_template_part(
            'template-parts/home/hero-carousel',
            null,
            array(
                'url'   => $tst_shop_url,
                'block' => $tst_block,
            )
        );
        continue;
    }

    if ( ! $tst_has_woocommerce ) {
        continue;
    }

    if ( 'tabs' === $tst_type ) {
        get_template_part( 'template-parts/home/product-tabs-slider', null, array( 'block' => $tst_block ) );
    }

    if ( 'featured' === $tst_type && function_exists( 'wc_get_product' ) ) {
        get_template_part( 'template-parts/home/featured-products', null, array( 'block' => $tst_block ) );
    }
}
?>
<?php get_footer(); ?>
