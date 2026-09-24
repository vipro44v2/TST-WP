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

function tst_get_hero_slide_order( $value ) {
    if ( ! is_string( $value ) ) {
        return array( 1, 2 );
    }

    $order = array_map( 'absint', explode( ',', $value ) );

    if (
        2 !== count( $order ) ||
        2 !== count( array_unique( $order ) ) ||
        ! in_array( 1, $order, true ) ||
        ! in_array( 2, $order, true )
    ) {
        return array( 1, 2 );
    }

    return $order;
}

function tst_enqueue_settings_assets( $hook_suffix ) {
    if ( 'appearance_page_tst-settings' !== $hook_suffix ) {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_style(
        'tst-theme-settings',
        TST_URI . '/assets/css/admin/theme-settings.css',
        array(),
        TST_VERSION
    );
    wp_enqueue_script(
        'tst-theme-settings',
        TST_URI . '/assets/js/admin/theme-settings.js',
        array( 'media-views' ),
        TST_VERSION,
        true
    );
    wp_localize_script(
        'tst-theme-settings',
        'tstHeroSettings',
        array(
            'title'       => __( 'Chọn ảnh banner', 'tst-custom' ),
            'button'      => __( 'Sử dụng ảnh này', 'tst-custom' ),
            'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
            'nonce'       => wp_create_nonce( 'tst_hero_image_upload' ),
            'uploading'   => __( 'Đang tải ảnh...', 'tst-custom' ),
            'saved'       => __( 'Ảnh đã tải lên. Hãy bấm Lưu thay đổi.', 'tst-custom' ),
            'invalidFile' => __( 'Vui lòng chọn một tệp ảnh.', 'tst-custom' ),
            'uploadError' => __( 'Không thể tải ảnh lên. Vui lòng thử lại.', 'tst-custom' ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'tst_enqueue_settings_assets' );

function tst_ajax_upload_hero_image() {
    if ( ! current_user_can( 'manage_options' ) || ! current_user_can( 'upload_files' ) ) {
        wp_send_json_error(
            array( 'message' => __( 'Bạn không có quyền tải ảnh lên.', 'tst-custom' ) ),
            403
        );
    }

    if ( ! check_ajax_referer( 'tst_hero_image_upload', 'nonce', false ) ) {
        wp_send_json_error(
            array( 'message' => __( 'Phiên làm việc đã hết hạn. Hãy tải lại trang.', 'tst-custom' ) ),
            403
        );
    }

    $file = $_FILES['image'] ?? null;

    if (
        ! is_array( $file ) ||
        ! isset( $file['name'], $file['tmp_name'], $file['error'] ) ||
        UPLOAD_ERR_OK !== (int) $file['error'] ||
        ! is_string( $file['name'] ) ||
        ! is_string( $file['tmp_name'] )
    ) {
        wp_send_json_error(
            array( 'message' => __( 'Không nhận được ảnh hợp lệ.', 'tst-custom' ) ),
            400
        );
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $allowed_mimes = array(
        'jpg|jpeg|jpe' => 'image/jpeg',
        'png'          => 'image/png',
        'gif'          => 'image/gif',
        'webp'         => 'image/webp',
        'avif'         => 'image/avif',
    );
    $filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $allowed_mimes );

    if ( empty( $filetype['type'] ) ) {
        wp_send_json_error(
            array( 'message' => __( 'Chỉ hỗ trợ ảnh JPG, PNG, GIF, WebP hoặc AVIF.', 'tst-custom' ) ),
            415
        );
    }

    $attachment_id = media_handle_upload(
        'image',
        0,
        array(),
        array(
            'test_form' => false,
            'mimes'     => $allowed_mimes,
        )
    );

    if ( is_wp_error( $attachment_id ) ) {
        wp_send_json_error(
            array( 'message' => $attachment_id->get_error_message() ),
            400
        );
    }

    $preview_url = wp_get_attachment_image_url( $attachment_id, 'medium' );

    wp_send_json_success(
        array(
            'id'  => $attachment_id,
            'url' => $preview_url ?: wp_get_attachment_url( $attachment_id ),
        )
    );
}
add_action( 'wp_ajax_tst_upload_hero_image', 'tst_ajax_upload_hero_image' );

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
        'hero_heading',
        'hero_cta_text',
        'hero_alt_1',
        'hero_alt_2',
    );
    foreach ( $text_keys as $key ) {
        $out[ $key ] = isset( $input[ $key ] )
            ? sanitize_text_field( wp_unslash( $input[ $key ] ) )
            : '';
    }

    $out['hero_cta_url'] = isset( $input['hero_cta_url'] ) && is_string( $input['hero_cta_url'] )
        ? esc_url_raw( wp_unslash( $input['hero_cta_url'] ) )
        : '';
    $out['hero_slide_order'] = implode(
        ',',
        tst_get_hero_slide_order( $input['hero_slide_order'] ?? '' )
    );

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
        'hero_enabled',
    );
    foreach ( $boolean_keys as $key ) {
        $out[ $key ] = empty( $input[ $key ] ) ? 0 : 1;
    }

    $number_keys = array(
        'tabs_products_per_tab',
        'tabs_desktop',
        'tabs_tablet',
        'tabs_mobile',
        'hero_image_1',
        'hero_image_2',
    );
    foreach ( $number_keys as $key ) {
        $out[ $key ] = isset( $input[ $key ] ) ? absint( $input[ $key ] ) : 0;
    }

    return $out;
}

function tst_settings_media_field( $key, $label, $settings, $default_url, $slide_number ) {
    $image_id = absint( $settings[ $key ] ?? 0 );
    $image_url = $image_id && wp_attachment_is_image( $image_id )
        ? wp_get_attachment_image_url( $image_id, 'medium' )
        : '';
    $preview_url = $image_url ?: $default_url;
    $handle_label = sprintf( __( 'Kéo để sắp xếp slide %d', 'tst-custom' ), $slide_number );
    ?>
<tr>
  <th>
    <label for="tst_<?php echo esc_attr( $key ); ?>">
      <?php echo esc_html( $label ); ?>
    </label>
    <div class="tst-settings__slide-controls">
      <button
        class="button tst-settings__handle"
        type="button"
        draggable="true"
        data-tst-slide-handle
        aria-label="<?php echo esc_attr( $handle_label ); ?>"
      >↕ <?php esc_html_e( 'Kéo', 'tst-custom' ); ?></button>
      <button class="button" type="button" data-tst-slide-up>
        <?php esc_html_e( '↑ Lên', 'tst-custom' ); ?>
      </button>
      <button class="button" type="button" data-tst-slide-down>
        <?php esc_html_e( '↓ Xuống', 'tst-custom' ); ?>
      </button>
    </div>
  </th>
  <td>
    <div
      class="tst-settings__media"
      data-tst-media-field
      data-tst-default-url="<?php echo esc_url( $default_url ); ?>"
    >
      <input
        id="tst_<?php echo esc_attr( $key ); ?>"
        type="hidden"
        name="tst_settings[<?php echo esc_attr( $key ); ?>]"
        value="<?php echo esc_attr( $image_id ); ?>"
        data-tst-media-id
      >
      <div class="tst-settings__dropzone" data-tst-dropzone>
        <img
          src="<?php echo esc_url( $preview_url ); ?>"
          alt=""
          width="320"
          data-tst-media-preview
        >
        <p><?php esc_html_e( 'Kéo ảnh từ máy tính thả vào đây.', 'tst-custom' ); ?></p>
      </div>
      <button class="button" type="button" data-tst-media-select>
        <?php esc_html_e( 'Chọn ảnh', 'tst-custom' ); ?>
      </button>
      <button class="button" type="button" data-tst-media-clear>
        <?php esc_html_e( 'Dùng ảnh mặc định', 'tst-custom' ); ?>
      </button>
      <p class="description">
        <?php esc_html_e( 'Nên dùng ảnh ngang tỷ lệ gần 2.7:1.', 'tst-custom' ); ?>
      </p>
      <p class="tst-settings__status" role="status" data-tst-media-status></p>
    </div>
  </td>
</tr>
    <?php
}

function tst_settings_slide_alt_field( $slide_number, $settings ) {
    $key = 'hero_alt_' . $slide_number;
    ?>
<tr>
  <th>
    <label for="tst_<?php echo esc_attr( $key ); ?>">
      <?php echo esc_html( sprintf( __( 'Mô tả ảnh slide %d', 'tst-custom' ), $slide_number ) ); ?>
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
    <?php
}

function tst_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Bạn không có quyền chỉnh sửa cài đặt này.', 'tst-custom' ) );
    }

    $settings = get_option( 'tst_settings', array() );

    if ( ! is_array( $settings ) ) {
        $settings = array();
    }

    $hero_order = tst_get_hero_slide_order( $settings['hero_slide_order'] ?? '1,2' );

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
    <input
      type="hidden"
      name="tst_settings[hero_slide_order]"
      value="<?php echo esc_attr( implode( ',', $hero_order ) ); ?>"
      data-tst-slide-order
    >
    <h2><?php esc_html_e( 'Banner trang chủ', 'tst-custom' ); ?></h2>
    <table class="form-table tst-settings__slides" data-tst-slide-list>
      <tr>
        <th><?php esc_html_e( 'Hiển thị banner', 'tst-custom' ); ?></th>
        <td>
          <label>
            <input
              type="checkbox"
              name="tst_settings[hero_enabled]"
              value="1"
              <?php checked( $settings['hero_enabled'] ?? 1, 1 ); ?>
            >
            <?php esc_html_e( 'Bật carousel trên trang chủ', 'tst-custom' ); ?>
          </label>
        </td>
      </tr>
      <tr>
        <th>
          <label for="tst_hero_heading">
            <?php esc_html_e( 'Tiêu đề hỗ trợ truy cập', 'tst-custom' ); ?>
          </label>
        </th>
        <td>
          <input
            class="regular-text"
            id="tst_hero_heading"
            name="tst_settings[hero_heading]"
            value="<?php echo esc_attr( $settings['hero_heading'] ?? 'Designed for everyday living.' ); ?>"
          >
          <p class="description">
            <?php esc_html_e( 'Tiêu đề chỉ dành cho trình đọc màn hình.', 'tst-custom' ); ?>
          </p>
        </td>
      </tr>
      <tr>
        <th>
          <label for="tst_hero_cta_text">
            <?php esc_html_e( 'Chữ trên nút', 'tst-custom' ); ?>
          </label>
        </th>
        <td>
          <input
            class="regular-text"
            id="tst_hero_cta_text"
            name="tst_settings[hero_cta_text]"
            value="<?php echo esc_attr( $settings['hero_cta_text'] ?? 'Tìm Hiểu Thêm' ); ?>"
          >
        </td>
      </tr>
      <tr>
        <th>
          <label for="tst_hero_cta_url">
            <?php esc_html_e( 'Liên kết của nút', 'tst-custom' ); ?>
          </label>
        </th>
        <td>
          <input
            class="regular-text"
            id="tst_hero_cta_url"
            type="url"
            name="tst_settings[hero_cta_url]"
            value="<?php echo esc_attr( $settings['hero_cta_url'] ?? '' ); ?>"
            placeholder="<?php echo esc_attr( home_url( '/' ) ); ?>"
          >
          <p class="description">
            <?php esc_html_e( 'Để trống để dẫn đến trang cửa hàng.', 'tst-custom' ); ?>
          </p>
        </td>
      </tr>
      <?php foreach ( $hero_order as $slide_number ) : ?>
        <tbody class="tst-settings__slide" data-tst-slide="<?php echo esc_attr( $slide_number ); ?>">
          <?php
          tst_settings_media_field(
              'hero_image_' . $slide_number,
              sprintf( __( 'Ảnh slide %d', 'tst-custom' ), $slide_number ),
              $settings,
              TST_URI . '/assets/images/tst-footwear-hero-' . $slide_number . '.jpg',
              $slide_number
          );
          tst_settings_slide_alt_field( $slide_number, $settings );
          ?>
        </tbody>
      <?php endforeach; ?>
    </table>

    <h2><?php esc_html_e( 'Các cài đặt khác', 'tst-custom' ); ?></h2>
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
