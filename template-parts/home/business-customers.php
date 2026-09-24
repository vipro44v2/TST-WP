<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tst_block = is_array( $args['block'] ?? null ) ? $args['block'] : array();
$tst_button_url = ! empty( $tst_block['button_url'] )
    ? $tst_block['button_url']
    : ( $args['url'] ?? home_url( '/' ) );
?>
<section class="tst-business" aria-label="<?php echo esc_attr( $tst_block['title'] ?? '' ); ?>">
  <div class="tst-business__inner">
    <div class="tst-business__lead-image">
      <?php
      $tst_image_id = absint( $tst_block['image_1'] ?? 0 );

      if ( $tst_image_id && wp_attachment_is_image( $tst_image_id ) ) {
          echo wp_kses_post( wp_get_attachment_image(
              $tst_image_id,
              'large',
              false,
              array(
                  'alt'     => $tst_block['image_1_alt'] ?? '',
                  'loading' => 'lazy',
              )
          ) );
      }
      ?>
    </div>
    <div class="tst-business__content">
      <?php if ( ! empty( $tst_block['title'] ) ) : ?>
        <h2 class="tst-business__title"><?php echo esc_html( $tst_block['title'] ); ?></h2>
      <?php endif; ?>
      <?php if ( ! empty( $tst_block['subtitle'] ) ) : ?>
        <p class="tst-business__subtitle"><?php echo esc_html( $tst_block['subtitle'] ); ?></p>
      <?php endif; ?>
      <div class="tst-business__cards">
        <?php foreach ( array( 2, 3 ) as $tst_number ) : ?>
          <?php
          $tst_image_id = absint( $tst_block[ 'image_' . $tst_number ] ?? 0 );
          $tst_card_title = $tst_block[ 'card_' . ( $tst_number - 1 ) . '_title' ] ?? '';
          ?>
          <div class="tst-business__card">
            <div class="tst-business__card-image">
              <?php
              if ( $tst_image_id && wp_attachment_is_image( $tst_image_id ) ) {
                  echo wp_kses_post( wp_get_attachment_image(
                      $tst_image_id,
                      'large',
                      false,
                      array(
                          'alt'     => $tst_block[ 'image_' . $tst_number . '_alt' ] ?? '',
                          'loading' => 'lazy',
                      )
                  ) );
              }
              ?>
            </div>
            <?php if ( $tst_card_title ) : ?>
              <h3 class="tst-business__card-title"><?php echo esc_html( $tst_card_title ); ?></h3>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <?php if ( ! empty( $tst_block['button_text'] ) ) : ?>
        <a class="tst-business__button" href="<?php echo esc_url( $tst_button_url ); ?>">
          <?php echo esc_html( $tst_block['button_text'] ); ?>
          <span class="tst-business__arrow" aria-hidden="true">➜</span>
        </a>
      <?php endif; ?>
    </div>
  </div>
</section>
