<?php
if (
    ! class_exists( 'WooCommerce' ) ||
    ! class_exists( 'WC_Product' ) ||
    ! function_exists( 'wc_get_product' )
) {
    return;
}

$product = $args['product'] ?? wc_get_product( get_the_ID() );
$options = is_array( $args['options'] ?? null ) ? $args['options'] : array();

if ( ! ( $product instanceof WC_Product ) ) {
    return;
}

$show_sizes = ! empty( $options['show_sizes'] );
$show_swatches = ! empty( $options['show_swatches'] );
$quick_variations = $show_sizes ? tst_get_product_quick_variations( $product ) : array();
$colors = $show_swatches ? tst_get_product_color_options( $product ) : array();

foreach ( $quick_variations as $choice ) {
    if ( '' === $choice['color'] || ! $show_swatches ) {
        continue;
    }

    if ( ! isset( $colors[ $choice['color'] ] ) ) {
        $colors[ $choice['color'] ] = $choice['color_hex'];
    }
}

$quick_color_labels = array_values( array_unique( array_filter( array_column( $quick_variations, 'color' ) ) ) );

if ( ! $show_swatches && count( $quick_color_labels ) > 1 ) {
    $quick_variations = array();
}

$first_color = $quick_color_labels[0] ?? ( $colors ? array_key_first( $colors ) : '' );
$gallery_ids = $product->get_gallery_image_ids();
$hover_image_id = $gallery_ids[0] ?? 0;
?>
<article class="tst-product-card" data-tst-product-card>
  <div class="tst-product-card__media">
    <a class="tst-product-card__image-link" href="<?php echo esc_url( $product->get_permalink() ); ?>">
      <?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
      <?php if ( $hover_image_id ) : ?>
        <?php
        echo wp_kses_post(
            wp_get_attachment_image(
                $hover_image_id,
                'woocommerce_thumbnail',
                false,
                array(
                    'class'   => 'tst-product-card__hover-image',
                    'alt'     => '',
                    'loading' => 'lazy',
                )
            )
        );
        ?>
      <?php endif; ?>
    </a>
    <?php if ( $product->is_on_sale() && ( ! isset( $options['show_badges'] ) || $options['show_badges'] ) ) : ?>
      <span class="tst-badge"><?php esc_html_e( 'Sale', 'tst-custom' ); ?></span>
    <?php endif; ?>
    <?php if ( $quick_variations ) : ?>
      <div class="tst-product-card__size-panel" data-tst-size-panel>
        <span class="tst-product-card__size-heading">
          <?php esc_html_e( 'Chọn size', 'tst-custom' ); ?>
        </span>
        <div class="tst-product-card__sizes">
          <?php foreach ( $quick_variations as $choice ) : ?>
            <button
              class="tst-product-card__size tst-ajax-add"
              type="button"
              data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
              data-variation-id="<?php echo esc_attr( $choice['id'] ); ?>"
              data-tst-size-color="<?php echo esc_attr( $choice['color'] ); ?>"
              <?php echo $choice['color'] === $first_color ? '' : 'hidden'; ?>
            ><?php echo esc_html( $choice['size'] ); ?></button>
          <?php endforeach; ?>
        </div>
        <span class="tst-product-card__no-size" data-tst-no-size hidden>
          <?php esc_html_e( 'Màu này hiện hết size', 'tst-custom' ); ?>
        </span>
      </div>
    <?php elseif ( $product->is_purchasable() && $product->is_in_stock() && $product->is_type( 'simple' ) ) : ?>
      <div class="tst-product-card__size-panel tst-product-card__size-panel--simple">
        <button
          class="tst-product-card__quick-add tst-ajax-add"
          type="button"
          data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
        ><?php esc_html_e( 'Thêm vào giỏ', 'tst-custom' ); ?></button>
      </div>
    <?php endif; ?>
  </div>

  <div class="tst-product-card__body">
    <?php if ( $colors && $show_swatches ) : ?>
      <div class="tst-product-card__swatches" aria-label="<?php esc_attr_e( 'Chọn màu', 'tst-custom' ); ?>">
        <?php foreach ( $colors as $color => $hex ) : ?>
          <button
            class="tst-product-card__swatch<?php echo $color === $first_color ? ' is-active' : ''; ?>"
            type="button"
            aria-label="<?php echo esc_attr( $color ); ?>"
            title="<?php echo esc_attr( $color ); ?>"
            aria-pressed="<?php echo $color === $first_color ? 'true' : 'false'; ?>"
            data-tst-color="<?php echo esc_attr( $color ); ?>"
          >
            <span style="background-color: <?php echo esc_attr( $hex ?: '#d9d9d9' ); ?>;"></span>
          </button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <h3 class="tst-product-card__title">
      <a href="<?php echo esc_url( $product->get_permalink() ); ?>">
        <?php echo esc_html( $product->get_name() ); ?>
      </a>
    </h3>
    <div class="tst-product-card__price">
      <?php echo wp_kses_post( $product->get_price_html() ); ?>
    </div>
  </div>
</article>
