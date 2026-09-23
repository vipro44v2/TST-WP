<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_template_part' ) ) {
    return;
}

get_header( 'shop' );
?>
<div class="tst-container tst-content tst-single-product">
  <?php while ( have_posts() ) : ?>
    <?php
    the_post();
    wc_get_template_part( 'content', 'single-product' );
    ?>
  <?php endwhile; ?>
</div>
<?php get_footer( 'shop' ); ?>
