<?php
get_header();

$tst_has_woocommerce = class_exists( 'WooCommerce' );
$tst_shop_url        = home_url( '/' );

if ( $tst_has_woocommerce && function_exists( 'wc_get_page_permalink' ) ) {
    $tst_shop_url = wc_get_page_permalink( 'shop' );
}
?>
<section class="tst-hero">
  <div class="tst-container">
    <p class="tst-eyebrow">TST CUSTOM</p>
    <h1><?php esc_html_e( 'Designed for everyday living.', 'tst-custom' ); ?></h1>
    <a class="tst-button" href="<?php echo esc_url( $tst_shop_url ); ?>">
      <?php esc_html_e( 'Shop now', 'tst-custom' ); ?>
    </a>
  </div>
</section>

<?php
if ( $tst_has_woocommerce && tst_get_setting( 'tabs_enabled', 1 ) ) {
    get_template_part( 'template-parts/home/product-tabs-slider' );
}
?>

<?php if ( $tst_has_woocommerce && function_exists( 'wc_get_product' ) ) : ?>
<section class="tst-container tst-section">
  <h2><?php esc_html_e( 'Featured products', 'tst-custom' ); ?></h2>
  <div class="tst-product-grid">
    <?php
    $query = new WP_Query(
        array(
            'post_type'      => 'product',
            'posts_per_page' => 8,
        )
    );

    while ( $query->have_posts() ) :
        $query->the_post();
        tst_get_product_card();
    endwhile;
    wp_reset_postdata();
    ?>
  </div>
</section>
<?php endif; ?>
<?php get_footer(); ?>
