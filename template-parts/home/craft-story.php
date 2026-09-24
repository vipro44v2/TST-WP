<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tst_block = is_array( $args['block'] ?? null ) ? $args['block'] : array();
$tst_button_url = ! empty( $tst_block['button_url'] )
    ? $tst_block['button_url']
    : ( $args['url'] ?? home_url( '/' ) );
$tst_images = array();

foreach ( array( 1, 2 ) as $tst_number ) {
    $tst_id = absint( $tst_block[ 'image_' . $tst_number ] ?? 0 );
    $tst_attachment = $tst_id && wp_attachment_is_image( $tst_id )
        ? wp_get_attachment_image_src( $tst_id, 'large' )
        : false;
    $tst_images[ $tst_number ] = array(
        'url'    => $tst_attachment
            ? $tst_attachment[0]
            : TST_URI . '/assets/images/tst-craft-' . ( 1 === $tst_number ? 'artisan' : 'shoes' ) . '.png',
        'width'  => $tst_attachment ? $tst_attachment[1] : 1122,
        'height' => $tst_attachment ? $tst_attachment[2] : 1402,
        'alt'    => $tst_block[ 'image_' . $tst_number . '_alt' ] ?? '',
    );
}
?>
<section class="tst-craft" aria-label="<?php echo esc_attr( $tst_block['title'] ?? '' ); ?>">
  <div class="tst-craft__inner">
    <div class="tst-craft__images">
      <?php foreach ( $tst_images as $tst_number => $tst_image ) : ?>
        <img
          class="tst-craft__image tst-craft__image--<?php echo esc_attr( $tst_number ); ?>"
          src="<?php echo esc_url( $tst_image['url'] ); ?>"
          alt="<?php echo esc_attr( $tst_image['alt'] ); ?>"
          width="<?php echo esc_attr( $tst_image['width'] ); ?>"
          height="<?php echo esc_attr( $tst_image['height'] ); ?>"
          loading="lazy"
        >
      <?php endforeach; ?>
    </div>
    <div class="tst-craft__content">
      <?php if ( ! empty( $tst_block['eyebrow'] ) ) : ?>
        <p class="tst-craft__eyebrow"><?php echo esc_html( $tst_block['eyebrow'] ); ?></p>
      <?php endif; ?>
      <?php if ( ! empty( $tst_block['title'] ) ) : ?>
        <h2 class="tst-craft__title"><?php echo esc_html( $tst_block['title'] ); ?></h2>
      <?php endif; ?>
      <ul class="tst-craft__points">
        <?php foreach ( array( 1, 2, 3 ) as $tst_number ) : ?>
          <?php $tst_point = $tst_block[ 'point_' . $tst_number ] ?? ''; ?>
          <?php if ( $tst_point ) : ?>
            <li class="tst-craft__point"><?php echo esc_html( $tst_point ); ?></li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
      <?php if ( ! empty( $tst_block['button_text'] ) ) : ?>
        <a class="tst-craft__button" href="<?php echo esc_url( $tst_button_url ); ?>">
          <?php echo esc_html( $tst_block['button_text'] ); ?>
          <span class="tst-craft__arrow" aria-hidden="true">➜</span>
        </a>
      <?php endif; ?>
    </div>
  </div>
</section>
