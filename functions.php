<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'TST_VERSION', '1.0.0' );
define( 'TST_DIR', get_template_directory() );
define( 'TST_URI', get_template_directory_uri() );

require_once TST_DIR . '/inc/setup.php';
require_once TST_DIR . '/inc/enqueue.php';
require_once TST_DIR . '/inc/helpers.php';
require_once TST_DIR . '/inc/theme-settings.php';
require_once TST_DIR . '/inc/home-builder.php';
require_once TST_DIR . '/inc/woocommerce.php';
require_once TST_DIR . '/inc/ajax-cart.php';
