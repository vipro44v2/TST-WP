<?php
if (
    ! class_exists( 'WooCommerce' ) ||
    ! class_exists( 'WC_Product' ) ||
    ! function_exists( 'wc_get_product' )
) {
    return;
}

$product = $args['product'] ?? wc_get_product( get_the_ID() );

if ( ! ( $product instanceof WC_Product ) ) {
    return;
}
?>
<article class="tst-product-card">
  <a class="tst-product-card__media" href="<?php echo esc_url( $product->get_permalink() ); ?>">
    <?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
    <?php if ( $product->is_on_sale() ) : ?>
      <span class="tst-badge"><?php esc_html_e( 'Sale', 'tst-custom' ); ?></span>
    <?php endif; ?>
  </a>

  <div class="tst-product-card__body">
    <h3 class="tst-product-card__title">
      <a href="<?php echo esc_url( $product->get_permalink() ); ?>">
        <?php echo esc_html( $product->get_name() ); ?>
      </a>
    </h3>
    <div class="tst-product-card__price">
      <?php echo wp_kses_post( $product->get_price_html() ); ?>
    </div>
    <div class="tst-product-card__actions">
      <?php if ( $product->is_purchasable() && $product->is_in_stock() && $product->is_type( 'simple' ) ) : ?>
        <button
          class="tst-button tst-button--small tst-ajax-add"
          data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
        >
          <?php esc_html_e( 'Quick add', 'tst-custom' ); ?>
        </button>
      <?php else : ?>
        <a class="tst-button tst-button--small" href="<?php echo esc_url( $product->get_permalink() ); ?>">
          <?php esc_html_e( 'View product', 'tst-custom' ); ?>
        </a>
      <?php endif; ?>
    </div>
  </div>
</article>
