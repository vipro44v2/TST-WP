<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tst_footer_company = tst_get_setting( 'footer_company', '' );
$tst_footer_business_address = tst_get_setting( 'footer_business_address', '' );
$tst_footer_registration = tst_get_setting( 'footer_registration', '' );
$tst_footer_contact_address = tst_get_setting( 'footer_contact_address', '' );
$tst_footer_logo_url = tst_get_setting( 'footer_logo_image_url', '' );
$tst_footer_badge_url = tst_get_setting( 'footer_badge_image_url', '' );
$tst_footer_badge_link = tst_get_setting( 'footer_badge_link_url', '' );
$tst_footer_copyright = tst_get_setting( 'footer_copyright', get_bloginfo( 'name' ) );
$tst_footer_socials = array(
    'instagram' => 'Instagram',
    'tiktok'    => 'TikTok',
    'facebook'  => 'Facebook',
    'youtube'   => 'YouTube',
    'zalo'      => 'Zalo',
);
$tst_newsletter_status = isset( $_GET['tst_newsletter'] ) && is_string( $_GET['tst_newsletter'] )
    ? sanitize_key( wp_unslash( $_GET['tst_newsletter'] ) )
    : '';
$tst_newsletter_source = isset( $_GET['tst_newsletter_source'] ) && is_string( $_GET['tst_newsletter_source'] )
    ? sanitize_key( wp_unslash( $_GET['tst_newsletter_source'] ) )
    : '';
$tst_newsletter_messages = array(
    'success' => __( 'Đăng ký thành công. Cảm ơn bạn!', 'tst-custom' ),
    'exists'  => __( 'Email này đã được đăng ký.', 'tst-custom' ),
    'invalid' => __( 'Vui lòng nhập email hợp lệ.', 'tst-custom' ),
    'error'   => __( 'Không thể đăng ký lúc này. Vui lòng thử lại.', 'tst-custom' ),
);
?>
</main>
<footer class="tst-footer">
  <div class="tst-footer__main">
    <div class="tst-footer__brand">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
        <?php if ( $tst_footer_logo_url ) : ?>
          <img
            class="tst-footer__logo"
            src="<?php echo esc_url( $tst_footer_logo_url ); ?>"
            alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
            loading="lazy"
          >
        <?php else : ?>
          <span class="tst-footer__mark" aria-hidden="true">ĐH</span>
        <?php endif; ?>
      </a>
    </div>
    <div class="tst-footer__column">
      <h2 class="tst-footer__heading">
        <?php echo esc_html( tst_get_setting( 'footer_menu_1_heading', get_bloginfo( 'name' ) ) ); ?>
      </h2>
      <?php if ( has_nav_menu( 'footer-1' ) ) : ?>
        <?php
        wp_nav_menu(
            array(
                'theme_location' => 'footer-1',
                'fallback_cb'    => false,
                'container'      => false,
                'menu_class'     => 'tst-footer__menu',
            )
        );
        ?>
      <?php else : ?>
        <?php tst_footer_fallback_menu( 'footer-1' ); ?>
      <?php endif; ?>
    </div>
    <div class="tst-footer__column">
      <h2 class="tst-footer__heading">
        <?php echo esc_html( tst_get_setting( 'footer_menu_2_heading', __( 'Hỗ trợ khách hàng', 'tst-custom' ) ) ); ?>
      </h2>
      <?php if ( has_nav_menu( 'footer-2' ) ) : ?>
        <?php
        wp_nav_menu(
            array(
                'theme_location' => 'footer-2',
                'fallback_cb'    => false,
                'container'      => false,
                'menu_class'     => 'tst-footer__menu',
            )
        );
        ?>
      <?php else : ?>
        <?php tst_footer_fallback_menu( 'footer-2' ); ?>
      <?php endif; ?>
    </div>
    <div class="tst-footer__information">
      <?php if ( $tst_footer_badge_url ) : ?>
        <div class="tst-footer__badge">
          <?php if ( $tst_footer_badge_link ) : ?>
            <a href="<?php echo esc_url( $tst_footer_badge_link ); ?>" target="_blank" rel="noopener noreferrer">
          <?php endif; ?>
          <img
            src="<?php echo esc_url( $tst_footer_badge_url ); ?>"
            alt="<?php esc_attr_e( 'Huy hiệu chứng nhận', 'tst-custom' ); ?>"
            loading="lazy"
          >
          <?php if ( $tst_footer_badge_link ) : ?>
            </a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      <?php if ( $tst_footer_company || $tst_footer_business_address ) : ?>
        <p class="tst-footer__business">
          <?php if ( $tst_footer_company ) : ?>
            <?php echo esc_html( $tst_footer_company ); ?><br>
          <?php endif; ?>
          <?php echo nl2br( esc_html( $tst_footer_business_address ) ); ?>
        </p>
      <?php endif; ?>
      <?php if ( $tst_footer_registration ) : ?>
        <p class="tst-footer__registration">
          <?php echo nl2br( esc_html( $tst_footer_registration ) ); ?>
        </p>
      <?php endif; ?>
      <?php if ( $tst_footer_contact_address ) : ?>
        <div class="tst-footer__contact">
          <h2 class="tst-footer__heading"><?php esc_html_e( 'Liên hệ', 'tst-custom' ); ?></h2>
          <p><?php echo nl2br( esc_html( $tst_footer_contact_address ) ); ?></p>
        </div>
      <?php endif; ?>
      <div class="tst-footer__newsletter" id="tst-footer-newsletter">
        <h2 class="tst-footer__heading">
          <?php esc_html_e( 'Đăng ký nhận thông tin', 'tst-custom' ); ?>
        </h2>
        <form class="tst-footer__newsletter-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
          <input type="hidden" name="action" value="tst_newsletter_subscribe">
          <input type="hidden" name="tst_newsletter_location" value="footer">
          <?php wp_nonce_field( 'tst_newsletter_subscribe', 'tst_newsletter_nonce' ); ?>
          <label class="screen-reader-text" for="tst-footer-email">
            <?php esc_html_e( 'Địa chỉ email', 'tst-custom' ); ?>
          </label>
          <input
            id="tst-footer-email"
            type="email"
            name="tst_newsletter_email"
            placeholder="<?php esc_attr_e( 'Nhập email của bạn', 'tst-custom' ); ?>"
            autocomplete="email"
            required
          >
          <input
            class="tst-footer__honeypot"
            type="text"
            name="tst_newsletter_website"
            tabindex="-1"
            autocomplete="off"
            aria-hidden="true"
          >
          <button type="submit" aria-label="<?php esc_attr_e( 'Đăng ký nhận thông tin', 'tst-custom' ); ?>">›</button>
        </form>
        <?php if ( 'footer' === $tst_newsletter_source && isset( $tst_newsletter_messages[ $tst_newsletter_status ] ) ) : ?>
          <p class="tst-footer__newsletter-message" role="status">
            <?php echo esc_html( $tst_newsletter_messages[ $tst_newsletter_status ] ); ?>
          </p>
        <?php endif; ?>
      </div>
    </div>
    <?php if ( is_active_sidebar( 'footer' ) ) : ?>
      <div class="tst-footer__widgets">
        <?php dynamic_sidebar( 'footer' ); ?>
      </div>
    <?php endif; ?>
  </div>
  <div class="tst-footer__bottom">
    <div class="tst-footer__bottom-inner">
      <div class="tst-footer__socials">
        <?php foreach ( $tst_footer_socials as $tst_platform => $tst_label ) : ?>
          <?php
          $tst_social_url = tst_get_setting( 'footer_' . $tst_platform . '_url', '' );
          $tst_icon_url = TST_URI . '/assets/icons/tst-social-' . $tst_platform . '.svg';
          ?>
          <?php if ( $tst_social_url ) : ?>
            <a
              href="<?php echo esc_url( $tst_social_url ); ?>"
              aria-label="<?php echo esc_attr( $tst_label ); ?>"
              target="_blank"
              rel="noopener noreferrer"
            >
              <img src="<?php echo esc_url( $tst_icon_url ); ?>" alt="" width="28" height="28" loading="lazy">
            </a>
          <?php else : ?>
            <span aria-hidden="true">
              <img src="<?php echo esc_url( $tst_icon_url ); ?>" alt="" width="28" height="28" loading="lazy">
            </span>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <p class="tst-footer__copyright">
        &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
        <?php echo esc_html( $tst_footer_copyright ); ?>
      </p>
    </div>
  </div>
</footer>
<?php if ( function_exists( 'tst_cart_drawer_enabled' ) && tst_cart_drawer_enabled() ) : ?>
  <?php get_template_part( 'template-parts/cart/drawer' ); ?>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
