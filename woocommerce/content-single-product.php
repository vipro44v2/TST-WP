<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
    return;
}
?>
<article class="tst-product-layout">
  <div class="tst-product-gallery">
    <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
  </div>
  <div class="tst-product-info">
    <?php do_action( 'woocommerce_single_product_summary' ); ?>
  </div>
</article>
<?php do_action( 'woocommerce_after_single_product_summary' ); ?>
