<?php
defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
?>
<div class="tst-container tst-content">
  <h1><?php esc_html_e( 'Your cart', 'tst-custom' ); ?></h1>

  <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
    <div class="tst-cart-layout">
      <div class="tst-cart-main">
        <div class="tst-cart-table">
          <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : ?>
            <?php
            $_product = $cart_item['data'];

            if ( ! $_product || ! $_product->exists() ) {
                continue;
            }
            ?>
            <div class="tst-cart-item">
              <div>
                <?php echo wp_kses_post( $_product->get_image( 'woocommerce_thumbnail' ) ); ?>
              </div>
              <div>
                <a href="<?php echo esc_url( $_product->get_permalink( $cart_item ) ); ?>">
                  <?php echo esc_html( $_product->get_name() ); ?>
                </a>
                <p><?php echo wp_kses_post( WC()->cart->get_product_price( $_product ) ); ?></p>
              </div>
              <div class="tst-qty">
                <input
                  type="number"
                  min="1"
                  name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]"
                  value="<?php echo esc_attr( $cart_item['quantity'] ); ?>"
                >
              </div>
              <div>
                <?php
                echo wp_kses_post(
                    WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] )
                );
                ?>
              </div>
              <div>
                <?php echo wp_kses_post( wc_get_formatted_cart_item_data( $cart_item ) ); ?>
                <a
                  href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>"
                  aria-label="<?php esc_attr_e( 'Remove item', 'tst-custom' ); ?>"
                >&times;</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <?php
        do_action( 'woocommerce_cart_actions' );
        wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' );
        ?>
      </div>

      <aside class="tst-cart-summary">
        <?php do_action( 'woocommerce_cart_collaterals' ); ?>
      </aside>
    </div>
  </form>
</div>
<?php get_footer( 'shop' ); ?>
