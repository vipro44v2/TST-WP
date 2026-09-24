<?php
/** Cart page layout. */

defined( 'ABSPATH' ) || exit;

if (
    ! class_exists( 'WooCommerce' ) ||
    ! class_exists( 'WC_Product' ) ||
    ! function_exists( 'WC' ) ||
    ! function_exists( 'wc_get_cart_url' ) ||
    ! function_exists( 'wc_get_cart_remove_url' )
) {
    return;
}

$tst_woocommerce = WC();
$tst_cart        = $tst_woocommerce && isset( $tst_woocommerce->cart ) ? $tst_woocommerce->cart : null;

if ( ! $tst_cart ) {
    return;
}

$tst_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '';
$tst_shop_url = $tst_shop_url ?: home_url( '/' );
$tst_account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : '';
$tst_account_url = $tst_account_url ?: wp_login_url();

do_action( 'woocommerce_before_cart' );
?>
<div class="tst-cart-layout">
  <form class="woocommerce-cart-form tst-cart-main" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
    <?php do_action( 'woocommerce_before_cart_table' ); ?>

    <div class="tst-cart-table woocommerce-cart-form__contents">
      <div class="tst-cart-table__head" aria-hidden="true">
        <span><?php esc_html_e( 'Sản phẩm', 'tst-custom' ); ?></span>
        <span><?php esc_html_e( 'Đơn giá', 'tst-custom' ); ?></span>
        <span><?php esc_html_e( 'Số lượng', 'tst-custom' ); ?></span>
        <span><?php esc_html_e( 'Thành tiền', 'tst-custom' ); ?></span>
        <span><?php esc_html_e( 'Xóa', 'tst-custom' ); ?></span>
      </div>

      <?php do_action( 'woocommerce_before_cart_contents' ); ?>

      <?php foreach ( $tst_cart->get_cart() as $cart_item_key => $cart_item ) : ?>
        <?php
        if ( ! is_array( $cart_item ) ) {
            continue;
        }

        $tst_product  = $cart_item['data'] ?? null;
        $tst_quantity = isset( $cart_item['quantity'] ) ? absint( $cart_item['quantity'] ) : 0;

        if (
            ! $tst_product instanceof WC_Product ||
            ! $tst_product->exists() ||
            ! $tst_quantity ||
            ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key )
        ) {
            continue;
        }

        $tst_product_name = apply_filters(
            'woocommerce_cart_item_name',
            $tst_product->get_name(),
            $cart_item,
            $cart_item_key
        );
        $tst_product_url  = apply_filters(
            'woocommerce_cart_item_permalink',
            $tst_product->is_visible() ? $tst_product->get_permalink( $cart_item ) : '',
            $cart_item,
            $cart_item_key
        );
        $tst_thumbnail = apply_filters(
            'woocommerce_cart_item_thumbnail',
            $tst_product->get_image( 'woocommerce_thumbnail' ),
            $cart_item,
            $cart_item_key
        );
        $tst_max_quantity = $tst_product->get_max_purchase_quantity();
        ?>
        <div class="tst-cart-item woocommerce-cart-form__cart-item">
          <div class="tst-cart-item__product">
            <?php if ( $tst_product_url ) : ?>
              <a class="tst-cart-item__image" href="<?php echo esc_url( $tst_product_url ); ?>">
                <?php echo wp_kses_post( $tst_thumbnail ); ?>
              </a>
            <?php else : ?>
              <span class="tst-cart-item__image"><?php echo wp_kses_post( $tst_thumbnail ); ?></span>
            <?php endif; ?>

            <div class="tst-cart-item__details">
              <?php if ( $tst_product_url ) : ?>
                <a class="tst-cart-item__name" href="<?php echo esc_url( $tst_product_url ); ?>">
                  <?php echo wp_kses_post( $tst_product_name ); ?>
                </a>
              <?php else : ?>
                <span class="tst-cart-item__name"><?php echo wp_kses_post( $tst_product_name ); ?></span>
              <?php endif; ?>

              <?php if ( function_exists( 'wc_get_formatted_cart_item_data' ) ) : ?>
                <div class="tst-cart-item__meta">
                  <?php echo wp_kses_post( wc_get_formatted_cart_item_data( $cart_item ) ); ?>
                </div>
              <?php endif; ?>

              <?php do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key ); ?>
            </div>
          </div>

          <div class="tst-cart-item__price" data-label="<?php esc_attr_e( 'Đơn giá', 'tst-custom' ); ?>">
            <?php
            echo wp_kses_post(
                apply_filters(
                    'woocommerce_cart_item_price',
                    $tst_cart->get_product_price( $tst_product ),
                    $cart_item,
                    $cart_item_key
                )
            );
            ?>
          </div>

          <div class="tst-cart-item__quantity" data-label="<?php esc_attr_e( 'Số lượng', 'tst-custom' ); ?>">
            <div class="tst-cart-qty">
              <button type="button" class="tst-cart-qty__button" data-tst-cart-qty="decrease" aria-label="<?php esc_attr_e( 'Giảm số lượng', 'tst-custom' ); ?>" <?php disabled( $tst_product->is_sold_individually() ); ?>>−</button>
              <input
                class="tst-cart-qty__input qty"
                type="number"
                name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]"
                value="<?php echo esc_attr( $tst_quantity ); ?>"
                min="1"
                <?php if ( $tst_max_quantity > 0 ) : ?>max="<?php echo esc_attr( $tst_max_quantity ); ?>"<?php endif; ?>
                aria-label="<?php echo esc_attr( sprintf( __( 'Số lượng của %s', 'tst-custom' ), wp_strip_all_tags( $tst_product_name ) ) ); ?>"
                <?php if ( $tst_product->is_sold_individually() ) : ?>readonly<?php endif; ?>
              >
              <button type="button" class="tst-cart-qty__button" data-tst-cart-qty="increase" aria-label="<?php esc_attr_e( 'Tăng số lượng', 'tst-custom' ); ?>" <?php disabled( $tst_product->is_sold_individually() ); ?>>+</button>
            </div>
          </div>

          <div class="tst-cart-item__subtotal" data-label="<?php esc_attr_e( 'Thành tiền', 'tst-custom' ); ?>">
            <?php
            echo wp_kses_post(
                apply_filters(
                    'woocommerce_cart_item_subtotal',
                    $tst_cart->get_product_subtotal( $tst_product, $tst_quantity ),
                    $cart_item,
                    $cart_item_key
                )
            );
            ?>
          </div>

          <div class="tst-cart-item__remove-cell product-remove">
            <a
              class="tst-cart-item__remove"
              href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>"
              aria-label="<?php echo esc_attr( sprintf( __( 'Xóa %s khỏi giỏ hàng', 'tst-custom' ), wp_strip_all_tags( $tst_product_name ) ) ); ?>"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                <path d="M4 7h16M9 7V4h6v3m-9 0 1 13h10l1-13M10 11v6m4-6v6" />
              </svg>
            </a>
          </div>
        </div>
      <?php endforeach; ?>

      <?php do_action( 'woocommerce_cart_contents' ); ?>

      <div class="tst-cart-actions">
        <?php if ( function_exists( 'wc_coupons_enabled' ) && wc_coupons_enabled() ) : ?>
          <div class="tst-cart-actions__coupon coupon">
            <label class="screen-reader-text" for="coupon_code"><?php esc_html_e( 'Mã giảm giá', 'tst-custom' ); ?></label>
            <input type="text" name="coupon_code" id="coupon_code" placeholder="<?php esc_attr_e( 'Nhập mã giảm giá của bạn', 'tst-custom' ); ?>">
            <button type="submit" name="apply_coupon" value="1"><?php esc_html_e( 'Áp dụng', 'tst-custom' ); ?></button>
            <?php do_action( 'woocommerce_cart_coupon' ); ?>
          </div>
        <?php endif; ?>

        <div class="tst-cart-actions__buttons">
          <a href="<?php echo esc_url( $tst_shop_url ); ?>"><?php esc_html_e( 'Tiếp tục mua sắm', 'tst-custom' ); ?></a>
          <button type="submit" name="update_cart" value="1"><?php esc_html_e( 'Cập nhật giỏ hàng', 'tst-custom' ); ?></button>
        </div>
      </div>

      <?php do_action( 'woocommerce_cart_actions' ); ?>
      <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
      <?php do_action( 'woocommerce_after_cart_contents' ); ?>
    </div>

    <?php do_action( 'woocommerce_after_cart_table' ); ?>
  </form>

  <aside class="tst-cart-summary" aria-label="<?php esc_attr_e( 'Tóm tắt đơn hàng', 'tst-custom' ); ?>">
    <h2 class="tst-cart-summary__title"><?php esc_html_e( 'Tóm tắt đơn hàng', 'tst-custom' ); ?></h2>

    <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
    <?php if ( function_exists( 'woocommerce_cart_totals' ) ) : ?>
      <?php woocommerce_cart_totals(); ?>
    <?php endif; ?>

    <?php if ( ! is_user_logged_in() ) : ?>
      <div class="tst-cart-summary__account">
        <strong><?php esc_html_e( 'Ưu đãi dành cho bạn', 'tst-custom' ); ?></strong>
        <p><?php esc_html_e( 'Đăng nhập để theo dõi đơn hàng và nhận ưu đãi.', 'tst-custom' ); ?></p>
        <a href="<?php echo esc_url( $tst_account_url ); ?>">
          <?php esc_html_e( 'Đăng nhập / Đăng ký', 'tst-custom' ); ?>
        </a>
      </div>
    <?php endif; ?>

    <?php if ( function_exists( 'wc_get_checkout_url' ) ) : ?>
      <a class="tst-cart-summary__quick-buy" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
        <?php esc_html_e( 'Mua nhanh', 'tst-custom' ); ?>
      </a>
    <?php endif; ?>

    <div class="tst-cart-summary__benefits">
      <p><strong><?php esc_html_e( 'Thanh toán an toàn', 'tst-custom' ); ?></strong><span><?php esc_html_e( 'Bảo mật thông tin của bạn', 'tst-custom' ); ?></span></p>
      <p><strong><?php esc_html_e( 'Giao hàng toàn quốc', 'tst-custom' ); ?></strong><span><?php esc_html_e( 'Phí vận chuyển hiển thị khi thanh toán', 'tst-custom' ); ?></span></p>
      <p><strong><?php esc_html_e( 'Hỗ trợ đơn hàng', 'tst-custom' ); ?></strong><span><?php esc_html_e( 'Liên hệ với chúng tôi khi cần trợ giúp', 'tst-custom' ); ?></span></p>
    </div>
  </aside>
</div>
<?php get_template_part( 'template-parts/cart/assurance' ); ?>
<?php
if (
    function_exists( 'wc_get_product' ) &&
    function_exists( 'wc_get_products' ) &&
    function_exists( 'tst_get_product_card' )
) {
    $tst_cart_product_ids = array();

    foreach ( $tst_cart->get_cart() as $tst_cart_item ) {
        $tst_cart_product_ids[] = absint( $tst_cart_item['product_id'] ?? 0 );
    }

    $tst_recommended_products = array();
    $tst_cross_sell_ids = array_diff( $tst_cart->get_cross_sells(), $tst_cart_product_ids );

    foreach ( $tst_cross_sell_ids as $tst_product_id ) {
        $tst_recommended_product = wc_get_product( $tst_product_id );

        if ( $tst_recommended_product instanceof WC_Product && $tst_recommended_product->is_visible() ) {
            $tst_recommended_products[] = $tst_recommended_product;
        }

        if ( count( $tst_recommended_products ) >= 8 ) {
            break;
        }
    }

    if ( count( $tst_recommended_products ) < 8 ) {
        $tst_excluded_ids = $tst_cart_product_ids;

        foreach ( $tst_recommended_products as $tst_recommended_product ) {
            $tst_excluded_ids[] = $tst_recommended_product->get_id();
        }

        $tst_recent_products = wc_get_products(
            array(
                'status'  => 'publish',
                'limit'   => 8,
                'exclude' => $tst_excluded_ids,
                'orderby' => 'date',
                'order'   => 'DESC',
            )
        );

        foreach ( $tst_recent_products as $tst_recent_product ) {
            if ( $tst_recent_product instanceof WC_Product && $tst_recent_product->is_visible() ) {
                $tst_recommended_products[] = $tst_recent_product;
            }

            if ( count( $tst_recommended_products ) >= 8 ) {
                break;
            }
        }
    }

    if ( $tst_recommended_products ) {
        get_template_part(
            'template-parts/product/related-products',
            null,
            array(
                'products' => $tst_recommended_products,
                'title'    => __( 'YOU MAY ALSO LIKE', 'tst-custom' ),
                'id'       => 'tst-cart-recommendations',
            )
        );
    }
}
?>
<?php do_action( 'woocommerce_after_cart' ); ?>
