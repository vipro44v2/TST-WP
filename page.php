<?php get_header(); ?>
<div class="tst-container tst-content">
  <?php while ( have_posts() ) : ?>
    <?php the_post(); ?>
    <?php
    $tst_use_classic_cart = class_exists( 'WooCommerce' ) &&
        function_exists( 'is_cart' ) &&
        is_cart() &&
        has_block( 'woocommerce/cart', get_the_ID() );
    ?>
    <h1><?php the_title(); ?></h1>
    <?php if ( $tst_use_classic_cart ) : ?>
      <?php echo do_shortcode( '[woocommerce_cart]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    <?php else : ?>
      <?php the_content(); ?>
    <?php endif; ?>
  <?php endwhile; ?>
</div>
<?php get_footer(); ?>
