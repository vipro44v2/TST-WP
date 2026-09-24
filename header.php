<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tst_has_woocommerce = class_exists( 'WooCommerce' );
$tst_account_url     = wp_login_url();
$tst_announcement    = tst_get_setting( 'announcement', '' );
$tst_drawer_enabled  = function_exists( 'tst_cart_drawer_enabled' ) && tst_cart_drawer_enabled();

if ( $tst_has_woocommerce && function_exists( 'wc_get_page_permalink' ) ) {
    $tst_woocommerce_account_url = wc_get_page_permalink( 'myaccount' );

    if ( $tst_woocommerce_account_url ) {
        $tst_account_url = $tst_woocommerce_account_url;
    }
}

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class( 'tst-site' ); ?>>
<?php wp_body_open(); ?>
<?php if ( $tst_announcement ) : ?>
<div class="tst-announcement">
  <?php echo esc_html( $tst_announcement ); ?>
</div>
<?php endif; ?>
<header class="tst-header">
  <div class="tst-container tst-header__inner">
    <div class="tst-header__logo">
      <?php if ( has_custom_logo() ) : ?>
        <?php the_custom_logo(); ?>
      <?php else : ?>
        <a class="tst-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
          <span class="tst-header__brand-mark" aria-hidden="true">ĐH</span>
          <span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
        </a>
      <?php endif; ?>
    </div>

    <nav
      id="tst-primary-nav"
      class="tst-header__nav<?php echo has_nav_menu( 'mobile' ) ? ' tst-header__nav--has-mobile' : ''; ?>"
      aria-label="<?php esc_attr_e( 'Menu chính', 'tst-custom' ); ?>"
    >
      <div class="tst-header__desktop-menu">
        <?php
        if ( has_nav_menu( 'primary' ) ) {
            wp_nav_menu(
                array(
                    'theme_location' => 'primary',
                    'fallback_cb'    => false,
                    'container'      => false,
                    'menu_class'     => 'tst-header__menu',
                )
            );
        } else {
            tst_header_fallback_menu();
        }
        ?>
      </div>
      <?php if ( has_nav_menu( 'mobile' ) ) : ?>
        <div class="tst-header__mobile-menu">
          <?php
          wp_nav_menu(
              array(
                  'theme_location' => 'mobile',
                  'fallback_cb'    => false,
                  'container'      => false,
                  'menu_class'     => 'tst-header__menu',
              )
          );
          ?>
        </div>
      <?php endif; ?>
    </nav>

    <div class="tst-header__actions">
      <button
        class="tst-header__icon-button"
        type="button"
        aria-label="<?php esc_attr_e( 'Tìm kiếm', 'tst-custom' ); ?>"
        aria-expanded="false"
        aria-controls="tst-header-search"
        data-tst-search-toggle
      >
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <circle cx="10.5" cy="10.5" r="6.5" stroke="currentColor" stroke-width="1.6"/>
          <path d="m15.5 15.5 5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
      </button>
      <a class="tst-header__icon-button" href="<?php echo esc_url( $tst_account_url ); ?>" aria-label="<?php esc_attr_e( 'Tài khoản', 'tst-custom' ); ?>">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <circle cx="12" cy="7" r="3.3" stroke="currentColor" stroke-width="1.6"/>
          <path d="M4.5 21v-2.5a7.5 7.5 0 0 1 15 0V21h-15Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
        </svg>
      </a>
      <?php if ( $tst_drawer_enabled ) : ?>
        <button
          class="tst-header__icon-button tst-header__cart"
          type="button"
          aria-label="<?php esc_attr_e( 'Giỏ hàng', 'tst-custom' ); ?>"
          aria-haspopup="dialog"
          aria-controls="tst-cart-drawer"
          data-tst-cart-open
        >
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="3.5" y="7" width="17" height="14" rx="2" stroke="currentColor" stroke-width="1.6"/>
            <path d="M8 9V6a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
          </svg>
          <span class="tst-header__cart-count tst-cart-count"><?php echo esc_html( tst_cart_count() ); ?></span>
        </button>
      <?php endif; ?>
      <button class="tst-menu-toggle" type="button" aria-expanded="false" aria-controls="tst-primary-nav" aria-label="<?php esc_attr_e( 'Mở menu', 'tst-custom' ); ?>">
        <span></span><span></span><span></span>
      </button>
    </div>
    <form id="tst-header-search" class="tst-header__search-panel" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" hidden>
      <label class="screen-reader-text" for="tst-header-search-input">
        <?php esc_html_e( 'Tìm kiếm', 'tst-custom' ); ?>
      </label>
      <input id="tst-header-search-input" type="search" name="s" placeholder="<?php esc_attr_e( 'Tìm sản phẩm...', 'tst-custom' ); ?>" required>
      <?php if ( $tst_has_woocommerce ) : ?>
        <input type="hidden" name="post_type" value="product">
      <?php endif; ?>
      <button type="submit"><?php esc_html_e( 'Tìm kiếm', 'tst-custom' ); ?></button>
    </form>
  </div>
</header>
<main class="tst-main">
