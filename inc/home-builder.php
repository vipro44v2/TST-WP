<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tst_home_block_types() {
    return array(
        'hero'     => __( 'Banner', 'tst-custom' ),
        'tabs'     => __( 'Slider sản phẩm', 'tst-custom' ),
        'featured' => __( 'Sản phẩm nổi bật', 'tst-custom' ),
    );
}

function tst_home_default_blocks() {
    return array(
        array(
            'id'    => 'hero-default',
            'type'  => 'hero',
            'title' => tst_get_setting( 'hero_heading', 'Designed for everyday living.' ),
            'text'  => tst_get_setting( 'hero_cta_text', 'Tìm Hiểu Thêm' ),
            'url'   => tst_get_setting( 'hero_cta_url', '' ),
            'image_1' => absint( tst_get_setting( 'hero_image_1', 0 ) ),
            'image_2' => absint( tst_get_setting( 'hero_image_2', 0 ) ),
            'enabled' => (int) tst_get_setting( 'hero_enabled', 1 ),
        ),
        array(
            'id'      => 'tabs-default',
            'type'    => 'tabs',
            'title'   => tst_get_setting( 'tabs_heading', '' ),
            'enabled' => (int) tst_get_setting( 'tabs_enabled', 1 ),
        ),
        array(
            'id'      => 'featured-default',
            'type'    => 'featured',
            'title'   => __( 'Featured products', 'tst-custom' ),
            'count'   => 8,
            'enabled' => 1,
        ),
    );
}

function tst_get_home_blocks() {
    $blocks = get_option( 'tst_home_blocks', false );

    return is_array( $blocks ) ? $blocks : tst_home_default_blocks();
}

function tst_sanitize_home_blocks( $input ) {
    if ( ! is_array( $input ) ) {
        return array();
    }

    $blocks = array();
    $seen = array();
    $types = tst_home_block_types();

    foreach ( array_slice( $input, 0, 30 ) as $block ) {
        if ( ! is_array( $block ) ) {
            continue;
        }

        $id = isset( $block['id'] ) && is_string( $block['id'] )
            ? sanitize_key( wp_unslash( $block['id'] ) )
            : '';
        $type = isset( $block['type'] ) && is_string( $block['type'] )
            ? sanitize_key( wp_unslash( $block['type'] ) )
            : '';

        if ( ! $id || isset( $seen[ $id ] ) || ! isset( $types[ $type ] ) ) {
            continue;
        }

        $seen[ $id ] = true;
        $title = is_string( $block['title'] ?? null ) ? wp_unslash( $block['title'] ) : '';
        $text = is_string( $block['text'] ?? null ) ? wp_unslash( $block['text'] ) : '';
        $url = is_string( $block['url'] ?? null ) ? wp_unslash( $block['url'] ) : '';

        $blocks[] = array(
            'id'      => $id,
            'type'    => $type,
            'enabled' => empty( $block['enabled'] ) ? 0 : 1,
            'title'   => sanitize_text_field( $title ),
            'text'    => sanitize_text_field( $text ),
            'url'     => esc_url_raw( $url ),
            'image_1' => absint( $block['image_1'] ?? 0 ),
            'image_2' => absint( $block['image_2'] ?? 0 ),
            'count'   => min( 24, max( 1, absint( $block['count'] ?? 8 ) ) ),
        );
    }

    return $blocks;
}

function tst_home_builder_init() {
    register_setting(
        'tst_home_builder',
        'tst_home_blocks',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'tst_sanitize_home_blocks',
        )
    );
}
add_action( 'admin_init', 'tst_home_builder_init' );

function tst_home_builder_menu() {
    add_theme_page(
        __( 'TST Trang chủ', 'tst-custom' ),
        __( 'TST Trang chủ', 'tst-custom' ),
        'manage_options',
        'tst-home-builder',
        'tst_home_builder_page'
    );
}
add_action( 'admin_menu', 'tst_home_builder_menu' );

function tst_home_builder_assets( $hook_suffix ) {
    if ( 'appearance_page_tst-home-builder' !== $hook_suffix ) {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_style(
        'tst-home-builder',
        TST_URI . '/assets/css/admin/home-builder.css',
        array(),
        TST_VERSION
    );
    wp_enqueue_script(
        'tst-home-builder',
        TST_URI . '/assets/js/admin/home-builder.js',
        array( 'media-views' ),
        TST_VERSION,
        true
    );
    wp_localize_script(
        'tst-home-builder',
        'tstHomeBuilder',
        array(
            'remove' => __( 'Xóa block này?', 'tst-custom' ),
            'media'  => __( 'Chọn ảnh banner', 'tst-custom' ),
            'use'    => __( 'Dùng ảnh này', 'tst-custom' ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'tst_home_builder_assets' );

function tst_home_builder_field( $index, $key, $label, $value, $type = 'text' ) {
    $name = 'tst_home_blocks[' . $index . '][' . $key . ']';
    ?>
    <label class="tst-builder__field">
      <span><?php echo esc_html( $label ); ?></span>
      <input
        type="<?php echo esc_attr( $type ); ?>"
        name="<?php echo esc_attr( $name ); ?>"
        value="<?php echo esc_attr( $value ); ?>"
        <?php echo 'number' === $type ? 'min="1" max="24"' : ''; ?>
      >
    </label>
    <?php
}

function tst_home_builder_image_field( $index, $number, $block ) {
    $key = 'image_' . $number;
    $id = absint( $block[ $key ] ?? 0 );
    $image_url = $id && wp_attachment_is_image( $id )
        ? wp_get_attachment_image_url( $id, 'medium' )
        : '';
    ?>
    <div class="tst-builder__image" data-tst-builder-image>
      <span><?php echo esc_html( sprintf( __( 'Ảnh slide %d', 'tst-custom' ), $number ) ); ?></span>
      <input
        type="hidden"
        name="tst_home_blocks[<?php echo esc_attr( $index ); ?>][<?php echo esc_attr( $key ); ?>]"
        value="<?php echo esc_attr( $id ); ?>"
        data-tst-builder-image-id
      >
      <img
        src="<?php echo esc_url( $image_url ); ?>"
        alt=""
        data-tst-builder-preview
        <?php echo $image_url ? '' : 'hidden'; ?>
      >
      <button type="button" class="button" data-tst-builder-pick>
        <?php esc_html_e( 'Chọn ảnh', 'tst-custom' ); ?>
      </button>
      <button type="button" class="button" data-tst-builder-clear>
        <?php esc_html_e( 'Ảnh mặc định', 'tst-custom' ); ?>
      </button>
    </div>
    <?php
}

function tst_home_builder_block( $block, $index ) {
    $type = $block['type'] ?? '';
    $types = tst_home_block_types();

    if ( ! isset( $types[ $type ] ) ) {
        return;
    }
    ?>
    <section class="tst-builder__block" data-tst-builder-block draggable="true">
      <div class="tst-builder__toolbar">
        <span class="tst-builder__handle" aria-hidden="true">☰</span>
        <strong><?php echo esc_html( $types[ $type ] ); ?></strong>
        <button type="button" class="button" data-tst-builder-up>↑</button>
        <button type="button" class="button" data-tst-builder-down>↓</button>
        <button type="button" class="button-link-delete" data-tst-builder-remove>
          <?php esc_html_e( 'Xóa', 'tst-custom' ); ?>
        </button>
      </div>
      <div class="tst-builder__fields">
        <input type="hidden" name="tst_home_blocks[<?php echo esc_attr( $index ); ?>][id]"
          value="<?php echo esc_attr( $block['id'] ?? '' ); ?>" data-tst-builder-name="id">
        <input type="hidden" name="tst_home_blocks[<?php echo esc_attr( $index ); ?>][type]"
          value="<?php echo esc_attr( $type ); ?>" data-tst-builder-name="type">
        <label class="tst-builder__enabled">
          <input type="checkbox" name="tst_home_blocks[<?php echo esc_attr( $index ); ?>][enabled]"
            value="1" <?php checked( $block['enabled'] ?? 1, 1 ); ?> data-tst-builder-name="enabled">
          <?php esc_html_e( 'Hiển thị', 'tst-custom' ); ?>
        </label>
        <?php
        tst_home_builder_field( $index, 'title', __( 'Tiêu đề', 'tst-custom' ), $block['title'] ?? '' );

        if ( 'hero' === $type ) {
            tst_home_builder_field( $index, 'text', __( 'Chữ trên nút', 'tst-custom' ), $block['text'] ?? '' );
            tst_home_builder_field( $index, 'url', __( 'Liên kết nút', 'tst-custom' ), $block['url'] ?? '', 'url' );
            tst_home_builder_image_field( $index, 1, $block );
            tst_home_builder_image_field( $index, 2, $block );
        }

        if ( 'featured' === $type ) {
            tst_home_builder_field(
                $index,
                'count',
                __( 'Số sản phẩm', 'tst-custom' ),
                $block['count'] ?? 8,
                'number'
            );
        }
        ?>
      </div>
    </section>
    <?php
}

function tst_home_builder_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Bạn không có quyền chỉnh sửa trang này.', 'tst-custom' ) );
    }

    $blocks = tst_get_home_blocks();
    ?>
    <div class="wrap tst-builder">
      <h1><?php esc_html_e( 'TST Trang chủ', 'tst-custom' ); ?></h1>
      <p>
        <?php esc_html_e( 'Kéo để sắp xếp, thêm hoặc xóa block. Bấm Lưu thay đổi khi hoàn tất.', 'tst-custom' ); ?>
      </p>
      <form method="post" action="options.php" data-tst-builder-form>
        <?php settings_fields( 'tst_home_builder' ); ?>
        <input type="hidden" name="tst_home_blocks[_empty]" value="1">
        <div class="tst-builder__list" data-tst-builder-list>
          <?php foreach ( $blocks as $index => $block ) : ?>
            <?php tst_home_builder_block( $block, $index ); ?>
          <?php endforeach; ?>
        </div>
        <div class="tst-builder__actions">
          <select data-tst-builder-type aria-label="<?php esc_attr_e( 'Loại block', 'tst-custom' ); ?>">
            <?php foreach ( tst_home_block_types() as $type => $label ) : ?>
              <option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
          </select>
          <button type="button" class="button" data-tst-builder-add>
            <?php esc_html_e( 'Thêm block', 'tst-custom' ); ?>
          </button>
        </div>
        <?php submit_button(); ?>
      </form>
      <template data-tst-builder-template="hero">
        <?php tst_home_builder_block( array( 'id' => '', 'type' => 'hero', 'enabled' => 1 ), '__INDEX__' ); ?>
      </template>
      <template data-tst-builder-template="tabs">
        <?php tst_home_builder_block( array( 'id' => '', 'type' => 'tabs', 'enabled' => 1 ), '__INDEX__' ); ?>
      </template>
      <template data-tst-builder-template="featured">
        <?php tst_home_builder_block( array( 'id' => '', 'type' => 'featured', 'enabled' => 1 ), '__INDEX__' ); ?>
      </template>
    </div>
    <?php
}
