<?php
if ( ! defined( 'ABSPATH' ) || ! class_exists( 'WooCommerce' ) ) {
    return;
}

$tst_block = is_array( $args['block'] ?? null ) ? $args['block'] : array();
$tst_title = $tst_block['title'] ?? __( 'Featured products', 'tst-custom' );
$tst_count = min( 24, max( 1, absint( $tst_block['count'] ?? 8 ) ) );
$tst_query = new WP_Query(
    array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $tst_count,
    )
);
?>
<section class="tst-container tst-section">
  <?php if ( $tst_title ) : ?>
    <h2><?php echo esc_html( $tst_title ); ?></h2>
  <?php endif; ?>
  <div class="tst-product-grid">
    <?php
    while ( $tst_query->have_posts() ) :
        $tst_query->the_post();
        tst_get_product_card();
    endwhile;
    wp_reset_postdata();
    ?>
  </div>
</section>
