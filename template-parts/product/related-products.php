<?php

if ( ! defined( 'ABSPATH' ) || ! function_exists( 'tst_get_product_card' ) ) {
    return;
}

$tst_related_products = $args['products'] ?? array();
$tst_raw_slider_id = $args['id'] ?? 'tst-related-products';
$tst_slider_id = is_string( $tst_raw_slider_id )
    ? sanitize_html_class( $tst_raw_slider_id )
    : 'tst-related-products';
$tst_slider_title = $args['title'] ?? __( 'CÓ THỂ BẠN SẼ THÍCH', 'tst-custom' );

if (
    ! is_array( $tst_related_products ) ||
    ! $tst_related_products ||
    ! is_string( $tst_slider_title )
) {
    return;
}
?>
<section class="tst-related-products" aria-labelledby="<?php echo esc_attr( $tst_slider_id ); ?>-title" data-tst-related-products>
  <h2 id="<?php echo esc_attr( $tst_slider_id ); ?>-title" class="tst-related-products__title">
    <?php echo esc_html( $tst_slider_title ); ?>
  </h2>
  <div class="tst-related-products__slider">
    <button
      class="tst-related-products__arrow tst-related-products__arrow--prev"
      type="button"
      aria-label="<?php esc_attr_e( 'Sản phẩm trước', 'tst-custom' ); ?>"
      data-tst-related-prev
      disabled
    >
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="m15 5-7 7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </button>
    <div class="tst-related-products__track" data-tst-related-track>
      <?php foreach ( $tst_related_products as $tst_related_product ) : ?>
        <?php
        tst_get_product_card(
            $tst_related_product,
            array(
                'show_swatches' => true,
                'show_sizes'    => true,
            )
        );
        ?>
      <?php endforeach; ?>
    </div>
    <button
      class="tst-related-products__arrow tst-related-products__arrow--next"
      type="button"
      aria-label="<?php esc_attr_e( 'Sản phẩm tiếp theo', 'tst-custom' ); ?>"
      data-tst-related-next
    >
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </button>
  </div>
</section>
