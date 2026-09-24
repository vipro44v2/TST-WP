<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tst_block = is_array( $args['block'] ?? null ) ? $args['block'] : array();
$tst_image_id = absint( $tst_block['image_id'] ?? 0 );
$tst_image = $tst_image_id && wp_attachment_is_image( $tst_image_id )
    ? wp_get_attachment_image_src( $tst_image_id, 'full' )
    : false;
$tst_image_url = $tst_image ? $tst_image[0] : TST_URI . '/assets/images/tst-lifestyle-banner.png';
$tst_image_alt = $tst_block['image_alt'] ?? '';
$tst_button_url = ! empty( $tst_block['button_url'] )
    ? $tst_block['button_url']
    : ( $args['url'] ?? home_url( '/' ) );
?>
<section class="tst-lifestyle" aria-label="<?php echo esc_attr( $tst_block['title'] ?? '' ); ?>">
  <img
    class="tst-lifestyle__image"
    src="<?php echo esc_url( $tst_image_url ); ?>"
    alt="<?php echo esc_attr( $tst_image_alt ); ?>"
    loading="lazy"
    <?php if ( $tst_image ) : ?>
      width="<?php echo esc_attr( $tst_image[1] ); ?>"
      height="<?php echo esc_attr( $tst_image[2] ); ?>"
      srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( $tst_image_id, 'full' ) ); ?>"
      sizes="100vw"
    <?php else : ?>
      width="2164"
      height="726"
    <?php endif; ?>
  >
  <div class="tst-lifestyle__shade" aria-hidden="true"></div>
  <div class="tst-lifestyle__content">
    <?php if ( ! empty( $tst_block['title'] ) ) : ?>
      <h2 class="tst-lifestyle__title"><?php echo esc_html( $tst_block['title'] ); ?></h2>
    <?php endif; ?>
    <?php if ( ! empty( $tst_block['subtitle'] ) ) : ?>
      <p class="tst-lifestyle__subtitle"><?php echo esc_html( $tst_block['subtitle'] ); ?></p>
    <?php endif; ?>
    <?php if ( ! empty( $tst_block['button_text'] ) ) : ?>
      <a class="tst-lifestyle__button" href="<?php echo esc_url( $tst_button_url ); ?>">
        <?php echo esc_html( $tst_block['button_text'] ); ?>
        <span class="tst-lifestyle__arrow" aria-hidden="true">➜</span>
      </a>
    <?php endif; ?>
  </div>
</section>
