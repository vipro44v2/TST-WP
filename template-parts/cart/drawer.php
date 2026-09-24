<?php

if ( ! defined( 'ABSPATH' ) || ! function_exists( 'tst_cart_drawer_available' ) ) {
    return;
}

if ( ! tst_cart_drawer_available() ) {
    return;
}
?>
<div id="tst-cart-drawer" class="tst-cart-drawer" data-tst-cart-drawer aria-hidden="true" inert>
  <button
    class="tst-cart-drawer__backdrop"
    type="button"
    data-tst-cart-close
    tabindex="-1"
    aria-label="<?php esc_attr_e( 'Đóng giỏ hàng', 'tst-custom' ); ?>"
  ></button>
  <aside class="tst-cart-drawer__panel" role="dialog" aria-modal="true" aria-labelledby="tst-cart-drawer-title">
    <div class="tst-cart-drawer__header">
      <h2 id="tst-cart-drawer-title"><?php esc_html_e( 'Giỏ hàng của bạn', 'tst-custom' ); ?></h2>
      <button
        class="tst-cart-drawer__close"
        type="button"
        data-tst-cart-close
        aria-label="<?php esc_attr_e( 'Đóng giỏ hàng', 'tst-custom' ); ?>"
      >&times;</button>
    </div>
    <div class="tst-cart-drawer__content" data-tst-cart-content aria-live="polite">
      <?php
      // Output is escaped by drawer-content.php.
      echo tst_cart_drawer_content(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
      ?>
    </div>
  </aside>
</div>
