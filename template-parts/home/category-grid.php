<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tst_block = is_array( $args['block'] ?? null ) ? $args['block'] : array();
$tst_fallback_url = $args['url'] ?? home_url( '/' );
?>
<section class="tst-category-grid" aria-label="<?php esc_attr_e( 'Khám phá danh mục', 'tst-custom' ); ?>">
  <div class="tst-category-grid__inner">
    <?php foreach ( array( 1, 2, 3, 4 ) as $tst_number ) : ?>
      <?php
      $tst_image_id = absint( $tst_block[ 'image_' . $tst_number ] ?? 0 );
      $tst_label = $tst_block[ 'label_' . $tst_number ] ?? '';
      $tst_url = ! empty( $tst_block[ 'url_' . $tst_number ] )
          ? $tst_block[ 'url_' . $tst_number ]
          : $tst_fallback_url;
      ?>
      <a class="tst-category-grid__item" href="<?php echo esc_url( $tst_url ); ?>">
        <?php if ( $tst_image_id && wp_attachment_is_image( $tst_image_id ) ) : ?>
          <?php
          echo wp_kses_post(
              wp_get_attachment_image(
                  $tst_image_id,
                  'large',
                  false,
                  array(
                      'class'   => 'tst-category-grid__image',
                      'alt'     => $tst_block[ 'image_' . $tst_number . '_alt' ] ?? '',
                      'loading' => 'lazy',
                  )
              )
          );
          ?>
        <?php endif; ?>
        <span class="tst-category-grid__shade" aria-hidden="true"></span>
        <?php if ( $tst_label ) : ?>
          <span class="tst-category-grid__label"><?php echo esc_html( $tst_label ); ?></span>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </div>
</section>
