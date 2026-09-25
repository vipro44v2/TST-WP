<?php get_header(); ?>
<div class="tst-container tst-content">
  <?php while ( have_posts() ) : ?>
    <?php the_post(); ?>
    <?php
    $tst_use_classic_cart = class_exists( 'WooCommerce' ) &&
        function_exists( 'is_cart' ) &&
        is_cart() &&
        has_block( 'woocommerce/cart', get_the_ID() );
    $tst_use_classic_checkout = class_exists( 'WooCommerce' ) &&
        function_exists( 'is_checkout' ) &&
        is_checkout() &&
        has_block( 'woocommerce/checkout', get_the_ID() );
    ?>
    <h1 class="<?php echo esc_attr( $tst_use_classic_checkout ? 'screen-reader-text' : '' ); ?>"><?php the_title(); ?></h1>
    <?php if ( $tst_use_classic_cart ) : ?>
      <?php echo do_shortcode( '[woocommerce_cart]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    <?php elseif ( $tst_use_classic_checkout ) : ?>
      <div class="tst-checkout-page">
        <?php echo do_shortcode( '[woocommerce_checkout]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </div>
    <?php else : ?>
      <?php the_content(); ?>
    <?php endif; ?>
  <?php endwhile; ?>
</div>
<?php get_footer(); ?>
