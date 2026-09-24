<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tst_block = is_array( $args['block'] ?? null ) ? $args['block'] : array();
$tst_fallback_url = $args['url'] ?? home_url( '/' );
?>
<section class="tst-promo-pair" aria-label="<?php esc_attr_e( 'Bộ sưu tập nổi bật', 'tst-custom' ); ?>">
  <div class="tst-promo-pair__grid">
    <?php foreach ( array( 1, 2 ) as $tst_number ) : ?>
      <?php
      $tst_image_id = absint( $tst_block[ 'image_' . $tst_number ] ?? 0 );
      $tst_heading = $tst_block[ 'heading_' . $tst_number ] ?? '';
      $tst_label = $tst_block[ 'label_' . $tst_number ] ?? '';
      $tst_button_text = $tst_block[ 'button_text_' . $tst_number ] ?? '';
      $tst_button_url = ! empty( $tst_block[ 'button_url_' . $tst_number ] )
          ? $tst_block[ 'button_url_' . $tst_number ]
          : $tst_fallback_url;
      ?>
      <article class="tst-promo-pair__card tst-promo-pair__card--<?php echo esc_attr( $tst_number ); ?>">
        <?php if ( $tst_image_id && wp_attachment_is_image( $tst_image_id ) ) : ?>
          <?php
          echo wp_kses_post(
              wp_get_attachment_image(
                  $tst_image_id,
                  'large',
                  false,
                  array(
                      'class'   => 'tst-promo-pair__image',
                      'alt'     => $tst_block[ 'image_' . $tst_number . '_alt' ] ?? '',
                      'loading' => 'lazy',
                  )
              )
          );
          ?>
        <?php endif; ?>
        <div class="tst-promo-pair__shade" aria-hidden="true"></div>
        <div class="tst-promo-pair__content">
          <?php if ( $tst_label ) : ?>
            <p class="tst-promo-pair__label"><?php echo esc_html( $tst_label ); ?></p>
          <?php endif; ?>
          <?php if ( $tst_heading ) : ?>
            <h2 class="tst-promo-pair__heading"><?php echo esc_html( $tst_heading ); ?></h2>
          <?php endif; ?>
          <?php if ( $tst_button_text ) : ?>
            <a class="tst-promo-pair__button" href="<?php echo esc_url( $tst_button_url ); ?>">
              <?php echo esc_html( $tst_button_text ); ?>
              <span class="tst-promo-pair__arrow" aria-hidden="true">➜</span>
            </a>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
