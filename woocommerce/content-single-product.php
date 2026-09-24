<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'WC_Product' ) ) {
    return;
}

global $product;

if ( ! $product instanceof WC_Product ) {
    return;
}

$tst_image_ids = array_values(
    array_filter(
        array_unique(
            array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() )
        )
    )
);
$tst_first_image = $tst_image_ids ? wp_get_attachment_image_url( $tst_image_ids[0], 'large' ) : '';
$tst_color_map = array();

if ( $product->is_type( 'variable' ) ) {
    foreach ( $product->get_variation_attributes() as $tst_attribute => $tst_values ) {
        if ( 'color' !== tst_product_attribute_kind( $tst_attribute ) ) {
            continue;
        }

        foreach ( $tst_values as $tst_value ) {
            $tst_color_map[ $tst_value ] = tst_product_color_hex( $tst_attribute, $tst_value );
        }
    }
}

do_action( 'woocommerce_before_single_product' );

if ( function_exists( 'woocommerce_breadcrumb' ) ) {
    woocommerce_breadcrumb(
        array(
            'delimiter'   => ' / ',
            'wrap_before' => '<nav class="tst-single-product__breadcrumb" aria-label="' . esc_attr__( 'Đường dẫn', 'tst-custom' ) . '">',
            'wrap_after'  => '</nav>',
        )
    );
}
?>
<article class="tst-product-layout" data-tst-single-product data-tst-color-map="<?php echo esc_attr( wp_json_encode( $tst_color_map ) ); ?>">
  <div class="tst-product-gallery<?php echo count( $tst_image_ids ) < 2 ? ' tst-product-gallery--single' : ''; ?>" data-tst-product-gallery>
    <?php if ( count( $tst_image_ids ) > 1 ) : ?>
      <div class="tst-product-gallery__thumbnails" aria-label="<?php esc_attr_e( 'Ảnh sản phẩm', 'tst-custom' ); ?>">
        <?php foreach ( $tst_image_ids as $tst_index => $tst_image_id ) : ?>
          <?php $tst_large_image = wp_get_attachment_image_url( $tst_image_id, 'large' ); ?>
          <button
            class="tst-product-gallery__thumbnail<?php echo 0 === $tst_index ? ' is-active' : ''; ?>"
            type="button"
            data-tst-gallery-image="<?php echo esc_url( $tst_large_image ); ?>"
            aria-label="<?php echo esc_attr( sprintf( __( 'Xem ảnh %d', 'tst-custom' ), $tst_index + 1 ) ); ?>"
            aria-pressed="<?php echo 0 === $tst_index ? 'true' : 'false'; ?>"
          >
            <?php echo wp_kses_post( wp_get_attachment_image( $tst_image_id, 'thumbnail' ) ); ?>
          </button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="tst-product-gallery__stage">
      <?php if ( $tst_first_image ) : ?>
        <img
          class="tst-product-gallery__main-image"
          src="<?php echo esc_url( $tst_first_image ); ?>"
          alt="<?php echo esc_attr( $product->get_name() ); ?>"
          data-tst-gallery-main
        >
        <img
          class="tst-product-gallery__transition-image"
          alt=""
          aria-hidden="true"
          data-tst-gallery-transition
        >
      <?php elseif ( function_exists( 'wc_placeholder_img' ) ) : ?>
        <?php echo wp_kses_post( wc_placeholder_img( 'woocommerce_single' ) ); ?>
      <?php endif; ?>
      <?php if ( count( $tst_image_ids ) > 1 ) : ?>
        <div class="tst-product-gallery__arrows">
          <button type="button" data-tst-gallery-prev aria-label="<?php esc_attr_e( 'Ảnh trước', 'tst-custom' ); ?>">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M15 5 8 12l7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
          <button type="button" data-tst-gallery-next aria-label="<?php esc_attr_e( 'Ảnh tiếp theo', 'tst-custom' ); ?>">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="tst-product-info">
    <h1 class="tst-product-info__title"><?php echo esc_html( $product->get_name() ); ?></h1>
    <div class="tst-product-info__price" data-tst-product-price>
      <?php echo wp_kses_post( $product->get_price_html() ); ?>
    </div>
    <?php if ( $product->get_sku() ) : ?>
      <p class="tst-product-info__sku">
        <?php echo esc_html( sprintf( __( 'SKU: %s', 'tst-custom' ), $product->get_sku() ) ); ?>
      </p>
    <?php endif; ?>

    <?php if ( function_exists( 'woocommerce_template_single_add_to_cart' ) ) : ?>
      <div class="tst-product-info__purchase">
        <?php woocommerce_template_single_add_to_cart(); ?>
      </div>
    <?php endif; ?>

    <div class="tst-product-info__services">
      <div class="tst-product-info__service tst-product-info__service--shipping">
        <span class="tst-product-info__service-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M2 6h12v11H2zM14 10h4l4 4v3h-8z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
            <circle cx="6" cy="18" r="2" stroke="currentColor" stroke-width="1.5" />
            <circle cx="18" cy="18" r="2" stroke="currentColor" stroke-width="1.5" />
          </svg>
        </span>
        <span><?php esc_html_e( 'Giao Hàng Hỏa Tốc 4H (nội thành TP.HCM và Hà Nội)', 'tst-custom' ); ?></span>
      </div>
      <div class="tst-product-info__service">
        <span class="tst-product-info__service-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M6 2 3 5c0 8 8 16 16 16l3-3-5-4-3 2a15 15 0 0 1-6-6l2-3-4-5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
          </svg>
        </span>
        <span>
          <a href="tel:18009029">18009029</a>
          <?php esc_html_e( '(Miễn phí cước gọi) - Hotline đặt hàng (08h30 - 21h30)', 'tst-custom' ); ?>
        </span>
      </div>
      <div class="tst-product-info__service">
        <span class="tst-product-info__service-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none">
            <rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.5" />
            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5" />
            <path d="M4 9c2 0 3-1 3-2m10 10c0-1 1-2 3-2" stroke="currentColor" stroke-width="1.5" />
          </svg>
        </span>
        <span><?php esc_html_e( 'Thanh toán tiện lợi với nhiều hình thức', 'tst-custom' ); ?></span>
      </div>
      <div class="tst-product-info__service">
        <span class="tst-product-info__service-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="m12 2 8 3v6c0 5-3 8-8 11-5-3-8-6-8-11V5l8-3Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
            <path d="m8 12 3 3 5-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </span>
        <span><?php esc_html_e( 'Bảo hành trong suốt thời gian sử dụng do lỗi kỹ thuật', 'tst-custom' ); ?></span>
      </div>
      <div class="tst-product-info__service">
        <span class="tst-product-info__service-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M4 12a8 8 0 0 1 14-5l2 2M20 12a8 8 0 0 1-14 5l-2-2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
            <path d="M20 4v5h-5M4 20v-5h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </span>
        <span><?php esc_html_e( 'Đổi sản phẩm trong 7 ngày', 'tst-custom' ); ?></span>
      </div>
    </div>

    <div class="tst-product-info__accordion">
      <details class="tst-product-info__accordion-item">
        <summary><?php esc_html_e( 'Tính năng nổi bật', 'tst-custom' ); ?></summary>
        <div class="tst-product-info__feature-content">
          <div class="tst-product-info__feature-mark" aria-hidden="true">
            <?php esc_html_e( 'êm như bông', 'tst-custom' ); ?>
          </div>
          <div class="tst-product-info__feature-text">
            <p>
              <?php esc_html_e( 'Sản phẩm gắn nhãn Êm Như Bông được tuyển chọn và cải tiến dựa trên phom chân người Việt, nhằm mang đến sự thoải mái và êm ái trong nhiều giờ liền.', 'tst-custom' ); ?>
            </p>
            <h3><?php esc_html_e( 'Lót hỗ trợ vòm chân', 'tst-custom' ); ?></h3>
            <p><?php esc_html_e( 'Chuẩn phom chân người Việt, nâng đỡ vòm tự nhiên và giảm mỏi khi đi/đứng lâu.', 'tst-custom' ); ?></p>
            <h3><?php esc_html_e( 'Chất liệu tuyển chọn', 'tst-custom' ); ?></h3>
            <p><?php esc_html_e( 'Chất da thật hoặc PU cao cấp được kiểm định kỹ lưỡng, tự động thích nghi theo dáng chân.', 'tst-custom' ); ?></p>
            <h3><?php esc_html_e( 'Đế hấp thụ lực', 'tst-custom' ); ?></h3>
            <p><?php esc_html_e( 'Đế đúc với các rãnh linh hoạt, hấp thụ lực, bảo vệ gót chân.', 'tst-custom' ); ?></p>
            <h3><?php esc_html_e( 'Lót gót chống phồng', 'tst-custom' ); ?></h3>
            <p><?php esc_html_e( 'Giúp giảm ma sát tại điểm tiếp xúc, hạn chế phồng rộp.', 'tst-custom' ); ?></p>
            <h3><?php esc_html_e( 'Ôm chân tự nhiên', 'tst-custom' ); ?></h3>
            <p><?php esc_html_e( 'Thiết kế ôm nhẹ không gây siết, cho ngón chân cử động thoải mái.', 'tst-custom' ); ?></p>
            <?php if ( $product->get_short_description() ) : ?>
              <div class="tst-product-info__short-description">
                <?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </details>
      <details class="tst-product-info__accordion-item">
        <summary><?php esc_html_e( 'Chi tiết Sản Phẩm', 'tst-custom' ); ?></summary>
        <div class="tst-product-info__accordion-content">
          <?php if ( $product->get_description() ) : ?>
            <?php echo wp_kses_post( apply_filters( 'the_content', $product->get_description() ) ); ?>
          <?php else : ?>
            <p><?php esc_html_e( 'Thông tin chi tiết đang được cập nhật.', 'tst-custom' ); ?></p>
          <?php endif; ?>
        </div>
      </details>
      <details class="tst-product-info__accordion-item">
        <summary><?php esc_html_e( 'Bảo Hành & Đổi', 'tst-custom' ); ?></summary>
        <div class="tst-product-info__accordion-content">
          <p><?php esc_html_e( 'Bảo hành trong suốt thời gian sử dụng đối với lỗi kỹ thuật.', 'tst-custom' ); ?></p>
          <p><?php esc_html_e( 'Đổi sản phẩm trong 7 ngày.', 'tst-custom' ); ?></p>
        </div>
      </details>
      <details class="tst-product-info__accordion-item">
        <summary><?php esc_html_e( 'Thông Tin Giao Nhận', 'tst-custom' ); ?></summary>
        <div class="tst-product-info__accordion-content">
          <p><?php esc_html_e( 'Giao hàng hỏa tốc 4H tại khu vực nội thành TP.HCM và Hà Nội.', 'tst-custom' ); ?></p>
          <p><?php esc_html_e( 'Phí vận chuyển được tính khi thanh toán.', 'tst-custom' ); ?></p>
        </div>
      </details>
    </div>
  </div>
</article>
<?php
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
do_action( 'woocommerce_after_single_product_summary' );
?>
