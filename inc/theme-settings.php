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
    $existing = get_option( 'tst_settings', array() );
    $out = is_array( $existing ) ? $existing : array();

    if ( ! is_array( $input ) ) {
        return $out;
    }

    foreach ( array( 'container_width', 'logo_width', 'support_phone', 'checkout_heading', 'announcement' ) as $key ) {
        $value = $input[ $key ] ?? '';
        $out[ $key ] = is_string( $value ) ? sanitize_text_field( wp_unslash( $value ) ) : '';
    }

    foreach ( array( 'primary_color', 'background_color', 'text_color', 'border_color' ) as $key ) {
        $value = $input[ $key ] ?? '';
        $out[ $key ] = is_string( $value ) ? sanitize_hex_color( $value ) : '';
    }

    foreach ( array( 'sticky_header' ) as $key ) {
        $out[ $key ] = empty( $input[ $key ] ) ? 0 : 1;
    }

    foreach ( array( 'footer_menu_1_heading', 'footer_menu_2_heading', 'footer_company', 'footer_copyright' ) as $key ) {
        $value = $input[ $key ] ?? '';
        $out[ $key ] = is_string( $value ) ? sanitize_text_field( wp_unslash( $value ) ) : '';
    }

    foreach ( array( 'footer_business_address', 'footer_registration', 'footer_contact_address' ) as $key ) {
        $value = $input[ $key ] ?? '';
        $out[ $key ] = is_string( $value ) ? sanitize_textarea_field( wp_unslash( $value ) ) : '';
    }

    $footer_url_keys = array(
        'footer_logo_image_url',
        'footer_badge_image_url',
        'footer_badge_link_url',
        'footer_instagram_url',
        'footer_tiktok_url',
        'footer_facebook_url',
        'footer_youtube_url',
        'footer_zalo_url',
    );

    foreach ( $footer_url_keys as $key ) {
        $value = $input[ $key ] ?? '';
        $out[ $key ] = is_string( $value ) ? esc_url_raw( wp_unslash( $value ) ) : '';
    }

    return $out;
}

function tst_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Bạn không có quyền chỉnh sửa cài đặt này.', 'tst-custom' ) );
    }

    $settings = get_option( 'tst_settings', array() );
    $settings = is_array( $settings ) ? $settings : array();
    $text_fields = array(
        'container_width'  => __( 'Container width', 'tst-custom' ),
        'logo_width'       => __( 'Logo width', 'tst-custom' ),
        'support_phone'    => __( 'Support phone', 'tst-custom' ),
        'checkout_heading' => __( 'Checkout heading', 'tst-custom' ),
        'announcement'     => __( 'Announcement', 'tst-custom' ),
    );
    $color_fields = array(
        'primary_color'    => __( 'Primary color', 'tst-custom' ),
        'background_color' => __( 'Background color', 'tst-custom' ),
        'text_color'       => __( 'Text color', 'tst-custom' ),
        'border_color'     => __( 'Border color', 'tst-custom' ),
    );
    $checkbox_fields = array(
        'sticky_header' => __( 'Sticky header', 'tst-custom' ),
    );
    $footer_fields = array(
        'footer_menu_1_heading'   => __( 'Tiêu đề cột menu 1', 'tst-custom' ),
        'footer_menu_2_heading'   => __( 'Tiêu đề cột menu 2', 'tst-custom' ),
        'footer_company'          => __( 'Tên công ty', 'tst-custom' ),
        'footer_business_address' => __( 'Địa chỉ doanh nghiệp', 'tst-custom' ),
        'footer_registration'     => __( 'Thông tin đăng ký kinh doanh', 'tst-custom' ),
        'footer_contact_address'  => __( 'Địa chỉ liên hệ', 'tst-custom' ),
        'footer_logo_image_url'   => __( 'URL ảnh logo footer', 'tst-custom' ),
        'footer_badge_image_url'  => __( 'URL ảnh huy hiệu chứng nhận', 'tst-custom' ),
        'footer_badge_link_url'   => __( 'URL xác minh huy hiệu', 'tst-custom' ),
        'footer_instagram_url'    => 'Instagram URL',
        'footer_tiktok_url'       => 'TikTok URL',
        'footer_facebook_url'     => 'Facebook URL',
        'footer_youtube_url'      => 'YouTube URL',
        'footer_zalo_url'         => 'Zalo URL',
        'footer_copyright'        => __( 'Dòng bản quyền', 'tst-custom' ),
    );
    ?>
    <div class="wrap">
      <h1><?php esc_html_e( 'TST Theme Settings', 'tst-custom' ); ?></h1>
      <p>
        <?php esc_html_e( 'Chỉnh nội dung tại Giao diện → TST Trang chủ.', 'tst-custom' ); ?>
      </p>
      <form method="post" action="options.php">
        <?php settings_fields( 'tst_settings_group' ); ?>
        <table class="form-table">
          <?php foreach ( $text_fields as $key => $label ) : ?>
            <tr>
              <th><label for="tst_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
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
              <th><label for="tst_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
              <td>
                <input
                  type="color"
                  id="tst_<?php echo esc_attr( $key ); ?>"
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
        <h2><?php esc_html_e( 'Footer', 'tst-custom' ); ?></h2>
        <p>
          <?php
          esc_html_e(
              'Gán hai menu tại Giao diện → Menu. Chỉ thêm huy hiệu chứng nhận chính thức của bạn.',
              'tst-custom'
          );
          ?>
        </p>
        <table class="form-table">
          <?php foreach ( $footer_fields as $key => $label ) : ?>
            <tr>
              <th>
                <label for="tst_<?php echo esc_attr( $key ); ?>">
                  <?php echo esc_html( $label ); ?>
                </label>
              </th>
              <td>
                <?php
                $is_textarea = in_array(
                    $key,
                    array( 'footer_business_address', 'footer_registration', 'footer_contact_address' ),
                    true
                );
                ?>
                <?php if ( $is_textarea ) : ?>
                  <textarea
                    class="large-text"
                    id="tst_<?php echo esc_attr( $key ); ?>"
                    name="tst_settings[<?php echo esc_attr( $key ); ?>]"
                    rows="3"
                  ><?php echo esc_textarea( $settings[ $key ] ?? '' ); ?></textarea>
                <?php else : ?>
                  <input
                    class="regular-text"
                    id="tst_<?php echo esc_attr( $key ); ?>"
                    name="tst_settings[<?php echo esc_attr( $key ); ?>]"
                    type="<?php echo esc_attr( str_ends_with( $key, '_url' ) ? 'url' : 'text' ); ?>"
                    value="<?php echo esc_attr( $settings[ $key ] ?? '' ); ?>"
                  >
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
        <?php submit_button(); ?>
      </form>
    </div>
    <?php
}
