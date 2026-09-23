<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tst_has_woocommerce = class_exists( 'WooCommerce' );
$tst_account_url     = wp_login_url();
$tst_cart_url        = home_url( '/' );

if ( $tst_has_woocommerce && function_exists( 'wc_get_page_permalink' ) ) {
    $tst_account_url = wc_get_page_permalink( 'myaccount' );
}

if ( $tst_has_woocommerce && function_exists( 'wc_get_cart_url' ) ) {
    $tst_cart_url = wc_get_cart_url();
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
<div class="tst-announcement">
  <?php
    echo esc_html(
        tst_get_setting(
            'announcement',
            __( 'Free shipping on orders over $50', 'tst-custom' )
        )
    );
    ?>
</div>
<header class="tst-header">
  <div class="tst-container tst-header__inner">
    <a class="tst-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
      <?php if ( has_custom_logo() ) : ?>
        <?php the_custom_logo(); ?>
      <?php else : ?>
        <?php bloginfo( 'name' ); ?>
      <?php endif; ?>
    </a>

    <nav class="tst-header__nav" aria-label="<?php esc_attr_e( 'Primary menu', 'tst-custom' ); ?>">
      <?php
        wp_nav_menu(
            array(
                'theme_location' => 'primary',
                'fallback_cb'    => false,
                'container'      => false,
            )
        );
        ?>
    </nav>

    <div class="tst-header__actions">
      <a href="<?php echo esc_url( $tst_account_url ); ?>">Account</a>
      <?php if ( $tst_has_woocommerce && function_exists( 'wc_get_cart_url' ) ) : ?>
        <a href="<?php echo esc_url( $tst_cart_url ); ?>">
          Cart (<span class="tst-cart-count"><?php echo esc_html( tst_cart_count() ); ?></span>)
        </a>
      <?php endif; ?>
      <button class="tst-menu-toggle" type="button" aria-expanded="false">Menu</button>
    </div>
  </div>
</header>
<main class="tst-main">
