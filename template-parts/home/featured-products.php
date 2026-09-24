<?php
if ( ! defined( 'ABSPATH' ) || ! class_exists( 'WooCommerce' ) ) {
    return;
}

$tst_block = is_array( $args['block'] ?? null ) ? $args['block'] : array();
$tst_title = $tst_block['title'] ?? __( 'Featured products', 'tst-custom' );
$tst_count = min( 24, max( 1, absint( $tst_block['count'] ?? 8 ) ) );
$tst_query_args = array(
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => $tst_count,
);
$tst_collection_id = absint( $tst_block['collection_id'] ?? 0 );

if ( $tst_collection_id && taxonomy_exists( 'product_cat' ) ) {
    $tst_term = get_term( $tst_collection_id, 'product_cat' );

    if ( $tst_term && ! is_wp_error( $tst_term ) ) {
        $tst_query_args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $tst_collection_id,
            ),
        );
    }
}

$tst_query = new WP_Query( $tst_query_args );
?>
<section class="tst-container tst-section">
  <?php if ( $tst_title ) : ?>
    <h2><?php echo esc_html( $tst_title ); ?></h2>
  <?php endif; ?>
  <div class="tst-product-grid">
    <?php
    while ( $tst_query->have_posts() ) :
        $tst_query->the_post();
        tst_get_product_card(
            null,
            array(
                'show_sizes'    => 1,
                'show_swatches' => 1,
            )
        );
    endwhile;
    wp_reset_postdata();
    ?>
  </div>
</section>
