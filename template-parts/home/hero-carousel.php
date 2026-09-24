<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tst_block       = is_array( $args['block'] ?? null ) ? $args['block'] : array();
$tst_default_url = $args['url'] ?? home_url( '/' );
$tst_saved_url   = $tst_block['button_url'] ?? '';
$tst_hero_url    = is_string( $tst_saved_url ) && '' !== $tst_saved_url
    ? $tst_saved_url
    : $tst_default_url;
$tst_heading     = $tst_block['title'] ?? '';
$tst_cta_text    = $tst_block['button_text'] ?? '';

if ( ! is_string( $tst_heading ) || '' === trim( $tst_heading ) ) {
    $tst_heading = __( 'Designed for everyday living.', 'tst-custom' );
}

if ( ! is_string( $tst_cta_text ) || '' === trim( $tst_cta_text ) ) {
    $tst_cta_text = __( 'Tìm Hiểu Thêm', 'tst-custom' );
}

$tst_slide_defaults = array(
    1 => array(
        'image'   => TST_URI . '/assets/images/tst-footwear-hero-1.jpg',
        'alt'     => __( 'Five summer footwear looks in warm neutral tones', 'tst-custom' ),
        'width'   => 2048,
        'height'  => 768,
        'srcset'  => '',
    ),
    2 => array(
        'image'   => TST_URI . '/assets/images/tst-footwear-hero-2.jpg',
        'alt'     => __( 'Five elegant sandal and heel looks in natural settings', 'tst-custom' ),
        'width'   => 2073,
        'height'  => 758,
        'srcset'  => '',
    ),
);
$tst_slide_order = '2,1' === ( $tst_block['slide_order'] ?? '' ) ? array( 2, 1 ) : array( 1, 2 );
$tst_slides      = array();

foreach ( $tst_slide_order as $slide_number ) {
    $tst_slides[] = $tst_slide_defaults[ $slide_number ];
}

foreach ( $tst_slides as $tst_index => &$tst_slide ) {
    $image_key = 'image_' . $tst_slide_order[ $tst_index ];
    $image_id = absint( $tst_block[ $image_key ] ?? 0 );

    if ( $image_id && wp_attachment_is_image( $image_id ) ) {
        $image = wp_get_attachment_image_src( $image_id, 'full' );

        if ( $image ) {
            $tst_slide['image']  = $image[0];
            $tst_slide['width']  = $image[1];
            $tst_slide['height'] = $image[2];
            $tst_slide['srcset'] = wp_get_attachment_image_srcset( $image_id, 'full' );
            $attachment_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );

            if ( $attachment_alt ) {
                $tst_slide['alt'] = $attachment_alt;
            }
        }
    }

    $custom_alt = $tst_block[ $image_key . '_alt' ] ?? '';

    if ( is_string( $custom_alt ) && '' !== trim( $custom_alt ) ) {
        $tst_slide['alt'] = $custom_alt;
    }
}
unset( $tst_slide );
?>
<section
  class="tst-hero"
  role="region"
  aria-roledescription="carousel"
  aria-label="<?php esc_attr_e( 'Featured footwear collections', 'tst-custom' ); ?>"
  data-tst-hero
  data-tst-hero-autoplay="<?php echo empty( $tst_block['autoplay'] ) ? '0' : '1'; ?>"
  data-tst-hero-interval="<?php echo esc_attr( $tst_block['autoplay_interval'] ?? 6000 ); ?>"
>
  <h1 class="tst-hero__title">
    <?php echo esc_html( $tst_heading ); ?>
  </h1>

  <?php foreach ( $tst_slides as $index => $slide ) : ?>
    <div
      class="tst-hero__slide<?php echo 0 === $index ? ' is-active' : ''; ?>"
      role="group"
      aria-roledescription="slide"
      aria-label="<?php echo esc_attr( ( $index + 1 ) . ' / ' . count( $tst_slides ) ); ?>"
      aria-hidden="<?php echo 0 === $index ? 'false' : 'true'; ?>"
      data-tst-hero-slide
    >
      <img
        class="tst-hero__image"
        draggable="false"
        src="<?php echo esc_url( $slide['image'] ); ?>"
        alt="<?php echo esc_attr( $slide['alt'] ); ?>"
        width="<?php echo esc_attr( $slide['width'] ); ?>"
        height="<?php echo esc_attr( $slide['height'] ); ?>"
        <?php if ( $slide['srcset'] ) : ?>
          srcset="<?php echo esc_attr( $slide['srcset'] ); ?>"
          sizes="100vw"
        <?php endif; ?>
        decoding="async"
        <?php echo 0 === $index ? 'fetchpriority="high"' : 'loading="lazy"'; ?>
      >
    </div>
  <?php endforeach; ?>

  <div class="tst-hero__overlay">
    <a class="tst-hero__cta" href="<?php echo esc_url( $tst_hero_url ); ?>">
      <?php echo esc_html( $tst_cta_text ); ?>
    </a>

    <div class="tst-hero__pagination" aria-label="<?php esc_attr_e( 'Choose a slide', 'tst-custom' ); ?>">
      <?php foreach ( $tst_slides as $index => $slide ) : ?>
        <button
          class="tst-hero__dot<?php echo 0 === $index ? ' is-active' : ''; ?>"
          type="button"
          aria-label="<?php echo esc_attr( sprintf( __( 'Show slide %d', 'tst-custom' ), $index + 1 ) ); ?>"
          aria-pressed="<?php echo 0 === $index ? 'true' : 'false'; ?>"
          data-tst-hero-dot="<?php echo esc_attr( $index ); ?>"
        ></button>
      <?php endforeach; ?>
    </div>
  </div>
</section>
