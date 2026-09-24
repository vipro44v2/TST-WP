<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tst_block = is_array( $args['block'] ?? null ) ? $args['block'] : array();
$tst_phone = $tst_block['phone'] ?? '';
$tst_phone_href = preg_replace( '/[^0-9+]/', '', $tst_phone );
$tst_email = $tst_block['email'] ?? '';
$tst_button_url = $tst_block['button_url'] ?? '';

if ( ! $tst_button_url && is_email( $tst_email ) ) {
    $tst_button_url = 'mailto:' . $tst_email . '?subject=' . rawurlencode( 'Góp ý' );
}

$tst_socials = array(
    'facebook'  => 'Facebook',
    'zalo'      => 'Zalo',
    'instagram' => 'Instagram',
    'youtube'   => 'YouTube',
    'tiktok'    => 'TikTok',
);
?>
<section class="tst-contact-feedback" aria-label="<?php echo esc_attr( $tst_block['title'] ?? '' ); ?>">
  <div class="tst-contact-feedback__inner">
    <div class="tst-contact-feedback__intro">
      <?php if ( ! empty( $tst_block['title'] ) ) : ?>
        <h2 class="tst-contact-feedback__title"><?php echo esc_html( $tst_block['title'] ); ?></h2>
      <?php endif; ?>
      <?php if ( ! empty( $tst_block['description'] ) ) : ?>
        <p class="tst-contact-feedback__description">
          <?php echo esc_html( $tst_block['description'] ); ?>
        </p>
      <?php endif; ?>
      <?php if ( ! empty( $tst_block['button_text'] ) && $tst_button_url ) : ?>
        <a class="tst-contact-feedback__button" href="<?php echo esc_url( $tst_button_url ); ?>">
          <?php echo esc_html( $tst_block['button_text'] ); ?>
          <span class="tst-contact-feedback__button-arrow" aria-hidden="true">➜</span>
        </a>
      <?php endif; ?>
    </div>
    <div class="tst-contact-feedback__details">
      <?php if ( $tst_phone && $tst_phone_href ) : ?>
        <div class="tst-contact-feedback__detail">
          <svg class="tst-contact-feedback__detail-icon" viewBox="0 0 32 32" fill="none" aria-hidden="true">
            <path d="M7 3l5 6-3 4c2 5 5 8 10 10l4-3 6 5-3 4C13 28 4 19 3 6l4-3z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
          </svg>
          <div>
            <strong><?php esc_html_e( 'Hotline', 'tst-custom' ); ?></strong>
            <a href="<?php echo esc_url( 'tel:' . $tst_phone_href ); ?>">
              <?php echo esc_html( $tst_phone ); ?>
            </a>
          </div>
        </div>
      <?php endif; ?>
      <?php if ( is_email( $tst_email ) ) : ?>
        <div class="tst-contact-feedback__detail">
          <svg class="tst-contact-feedback__detail-icon" viewBox="0 0 32 32" fill="none" aria-hidden="true">
            <rect x="3" y="6" width="26" height="20" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="M4 8l12 10L28 8" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
          </svg>
          <div>
            <strong><?php esc_html_e( 'Email', 'tst-custom' ); ?></strong>
            <a href="<?php echo esc_url( 'mailto:' . $tst_email ); ?>">
              <?php echo esc_html( $tst_email ); ?>
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <div class="tst-contact-feedback__social">
      <?php if ( ! empty( $tst_block['social_heading'] ) ) : ?>
        <p class="tst-contact-feedback__social-heading">
          <?php echo esc_html( $tst_block['social_heading'] ); ?>
        </p>
      <?php endif; ?>
      <div class="tst-contact-feedback__social-list">
        <?php foreach ( $tst_socials as $tst_platform => $tst_label ) : ?>
          <?php
          $tst_url = $tst_block[ $tst_platform . '_url' ] ?? '';
          $tst_icon_url = TST_URI . '/assets/icons/tst-social-' . $tst_platform . '.svg';
          ?>
          <?php if ( $tst_url ) : ?>
            <a
              class="tst-contact-feedback__social-link tst-contact-feedback__social-link--<?php echo esc_attr( $tst_platform ); ?>"
              href="<?php echo esc_url( $tst_url ); ?>"
              aria-label="<?php echo esc_attr( $tst_label ); ?>"
              target="_blank"
              rel="noopener noreferrer"
            >
              <img
                src="<?php echo esc_url( $tst_icon_url ); ?>"
                alt=""
                width="40"
                height="40"
                loading="lazy"
              >
            </a>
          <?php else : ?>
            <span
              class="tst-contact-feedback__social-link tst-contact-feedback__social-link--<?php echo esc_attr( $tst_platform ); ?>"
              aria-hidden="true"
            >
              <img
                src="<?php echo esc_url( $tst_icon_url ); ?>"
                alt=""
                width="40"
                height="40"
                loading="lazy"
              >
            </span>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
