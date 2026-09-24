<?php

if ( ! defined( 'ABSPATH' ) || ! function_exists( 'tst_cart_drawer_available' ) ) {
    return;
}

if ( ! tst_cart_drawer_available() ) {
    return;
}

$tst_cart = WC()->cart;
$tst_cart_url = wc_get_cart_url();
$tst_checkout_url = function_exists( 'wc_get_checkout_url' )
    ? wc_get_checkout_url()
    : $tst_cart_url;
?>
<?php if ( $tst_cart->is_empty() ) : ?>
  <div class="tst-cart-drawer__empty">
    <p><?php esc_html_e( 'Giỏ hàng của bạn đang trống.', 'tst-custom' ); ?></p>
    <button type="button" data-tst-cart-close>
      <?php esc_html_e( 'Tiếp tục mua sắm', 'tst-custom' ); ?>
    </button>
    <a class="tst-cart-drawer__view-cart" href="<?php echo esc_url( $tst_cart_url ); ?>">
      <?php esc_html_e( 'Đi tới trang giỏ hàng', 'tst-custom' ); ?>
    </a>
  </div>
<?php else : ?>
  <div class="tst-cart-drawer__items">
    <?php foreach ( $tst_cart->get_cart() as $tst_item_key => $tst_item ) : ?>
      <?php
      $tst_product = $tst_item['data'] ?? null;
      $tst_quantity = absint( $tst_item['quantity'] ?? 0 );

      if ( ! ( $tst_product instanceof WC_Product ) || ! $tst_product->exists() ) {
          continue;
      }

      $tst_remove_label = sprintf(
          __( 'Xóa %s khỏi giỏ hàng', 'tst-custom' ),
          $tst_product->get_name()
      );
      ?>
      <div class="tst-cart-drawer__item">
        <a class="tst-cart-drawer__image" href="<?php echo esc_url( $tst_product->get_permalink( $tst_item ) ); ?>">
          <?php echo wp_kses_post( $tst_product->get_image( 'woocommerce_thumbnail' ) ); ?>
        </a>
        <div class="tst-cart-drawer__item-detail">
          <a class="tst-cart-drawer__item-name" href="<?php echo esc_url( $tst_product->get_permalink( $tst_item ) ); ?>">
            <?php echo esc_html( $tst_product->get_name() ); ?>
          </a>
          <?php if ( function_exists( 'wc_get_formatted_cart_item_data' ) ) : ?>
            <div class="tst-cart-drawer__variation">
              <?php echo wp_kses_post( wc_get_formatted_cart_item_data( $tst_item ) ); ?>
            </div>
          <?php endif; ?>
          <p class="tst-cart-drawer__quantity">
            <?php echo esc_html( sprintf( __( 'Số lượng: %d', 'tst-custom' ), $tst_quantity ) ); ?>
          </p>
          <p class="tst-cart-drawer__price">
            <?php echo wp_kses_post( $tst_cart->get_product_subtotal( $tst_product, $tst_quantity ) ); ?>
          </p>
        </div>
        <button
          class="tst-cart-drawer__remove"
          type="button"
          data-tst-cart-remove="<?php echo esc_attr( $tst_item_key ); ?>"
          aria-label="<?php echo esc_attr( $tst_remove_label ); ?>"
        >&times;</button>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="tst-cart-drawer__summary">
    <div class="tst-cart-drawer__subtotal">
      <span><?php esc_html_e( 'Tạm tính', 'tst-custom' ); ?></span>
      <strong><?php echo wp_kses_post( $tst_cart->get_cart_subtotal() ); ?></strong>
    </div>
    <p class="tst-cart-drawer__shipping">
      <?php esc_html_e( 'Phí vận chuyển được tính khi thanh toán.', 'tst-custom' ); ?>
    </p>
    <a class="tst-cart-drawer__checkout" href="<?php echo esc_url( $tst_checkout_url ); ?>">
      <?php esc_html_e( 'Thanh toán', 'tst-custom' ); ?>
    </a>
    <a class="tst-cart-drawer__view-cart" href="<?php echo esc_url( $tst_cart_url ); ?>">
      <?php esc_html_e( 'Đi tới trang giỏ hàng', 'tst-custom' ); ?>
    </a>
  </div>
<?php endif; ?>
