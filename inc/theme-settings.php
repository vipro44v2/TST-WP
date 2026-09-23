<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tst_settings_menu() {
    add_theme_page(
        __( 'TST Theme Settings', 'tst-custom' ),
        __( 'TST Theme Settings', 'tst-custom' ),
        'manage_options',
        'tst-settings',
        'tst_settings_page'
    );
}
add_action( 'admin_menu', 'tst_settings_menu' );

function tst_settings_init() {
    register_setting(
        'tst_settings_group',
        'tst_settings',
        array( 'sanitize_callback' => 'tst_sanitize_settings' )
    );
}
add_action( 'admin_init', 'tst_settings_init' );

function tst_sanitize_settings( $input ) {
    if ( ! is_array( $input ) ) {
        return array();
    }

    $out = array();

    $text_keys = array(
        'container_width',
        'logo_width',
        'support_phone',
        'checkout_heading',
        'announcement',
        'tabs_heading',
        'tabs_new_label',
        'tabs_best_label',
        'tabs_more_url',
        'tabs_image_bg',
        'tabs_radius',
        'tabs_top_spacing',
        'tabs_bottom_spacing',
    );
    foreach ( $text_keys as $key ) {
        $out[ $key ] = isset( $input[ $key ] ) ? sanitize_text_field( $input[ $key ] ) : '';
    }

    $color_keys = array( 'primary_color', 'background_color', 'text_color', 'border_color' );
    foreach ( $color_keys as $key ) {
        $out[ $key ] = isset( $input[ $key ] ) ? sanitize_hex_color( $input[ $key ] ) : '';
    }

    $boolean_keys = array(
        'sticky_header',
        'cart_drawer',
        'tabs_enabled',
        'tabs_show_more',
        'tabs_show_badges',
        'tabs_show_swatches',
        'tabs_show_sizes',
    );
    foreach ( $boolean_keys as $key ) {
        $out[ $key ] = empty( $input[ $key ] ) ? 0 : 1;
    }

    $number_keys = array(
        'tabs_products_per_tab',
        'tabs_desktop',
        'tabs_tablet',
        'tabs_mobile',
    );
    foreach ( $number_keys as $key ) {
        $out[ $key ] = isset( $input[ $key ] ) ? absint( $input[ $key ] ) : 0;
    }

    return $out;
}

function tst_settings_page() {
    $settings = get_option( 'tst_settings', array() );

    if ( ! is_array( $settings ) ) {
        $settings = array();
    }

    $text_fields = array(
        'container_width'  => 'Container width',
        'logo_width'       => 'Logo width',
        'support_phone'    => 'Support phone',
        'checkout_heading' => 'Checkout heading',
        'announcement'     => 'Announcement',
        'tabs_heading'     => 'Slider heading',
        'tabs_new_label'   => 'New products label',
        'tabs_best_label'  => 'Best sellers label',
        'tabs_more_url'    => 'More link URL',
        'tabs_image_bg'    => 'Image background color',
        'tabs_radius'      => 'Card radius',
        'tabs_top_spacing' => 'Top spacing',
        'tabs_bottom_spacing' => 'Bottom spacing',
    );
    $number_fields = array(
        'tabs_products_per_tab' => 'Products per tab',
        'tabs_desktop'          => 'Desktop cards visible',
        'tabs_tablet'           => 'Tablet cards visible',
        'tabs_mobile'           => 'Mobile cards visible',
    );
    $color_fields = array(
        'primary_color'    => 'Primary color',
        'background_color' => 'Background color',
        'text_color'       => 'Text color',
        'border_color'     => 'Border color',
    );
    $checkbox_fields = array(
        'sticky_header'     => 'Sticky header',
        'cart_drawer'       => 'Enable cart drawer',
        'tabs_enabled'      => 'Enable product tabs slider',
        'tabs_show_more'    => 'Show more link',
        'tabs_show_badges'  => 'Show badges',
        'tabs_show_swatches' => 'Show color swatches',
        'tabs_show_sizes'   => 'Show quick sizes',
    );
    ?>
<div class="wrap">
  <h1><?php esc_html_e( 'TST Theme Settings', 'tst-custom' ); ?></h1>
  <form method="post" action="options.php">
    <?php settings_fields( 'tst_settings_group' ); ?>
    <table class="form-table">
      <?php foreach ( $text_fields as $key => $label ) : ?>
        <tr>
          <th>
            <label for="tst_<?php echo esc_attr( $key ); ?>">
              <?php echo esc_html( $label ); ?>
            </label>
          </th>
          <td>
            <input
              class="regular-text"
              id="tst_<?php echo esc_attr( $key ); ?>"
              name="tst_settings[<?php echo esc_attr( $key ); ?>]"
              value="<?php echo esc_attr( $settings[ $key ] ?? '' ); ?>"
            >
          </td>
        </tr>
      <?php endforeach; ?>

      <?php foreach ( $number_fields as $key => $label ) : ?>
        <tr>
          <th>
            <label for="tst_<?php echo esc_attr( $key ); ?>">
              <?php echo esc_html( $label ); ?>
            </label>
          </th>
          <td>
            <input
              class="regular-text"
              id="tst_<?php echo esc_attr( $key ); ?>"
              name="tst_settings[<?php echo esc_attr( $key ); ?>]"
              value="<?php echo esc_attr( $settings[ $key ] ?? '' ); ?>"
            >
          </td>
        </tr>
      <?php endforeach; ?>

      <?php foreach ( $color_fields as $key => $label ) : ?>
        <tr>
          <th><?php echo esc_html( $label ); ?></th>
          <td>
            <input
              type="color"
              name="tst_settings[<?php echo esc_attr( $key ); ?>]"
              value="<?php echo esc_attr( $settings[ $key ] ?? '#111111' ); ?>"
            >
          </td>
        </tr>
      <?php endforeach; ?>

      <?php foreach ( $checkbox_fields as $key => $label ) : ?>
        <tr>
          <th><?php echo esc_html( $label ); ?></th>
          <td>
            <input
              type="checkbox"
              name="tst_settings[<?php echo esc_attr( $key ); ?>]"
              value="1"
              <?php checked( $settings[ $key ] ?? 0, 1 ); ?>
            >
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
    <?php submit_button(); ?>
  </form>
</div>
    <?php
}
