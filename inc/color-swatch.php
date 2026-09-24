<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tst_color_swatch_add_field() {
    ?>
    <div class="form-field term-tst-color-swatch-wrap">
      <label for="tst-color-swatch">
        <?php esc_html_e( 'Swatch color', 'tst-custom' ); ?>
      </label>
      <input
        id="tst-color-swatch"
        class="tst-color-swatch-field"
        type="text"
        name="tst_color_swatch"
        value=""
        data-default-color=""
      >
      <p><?php esc_html_e( 'Choose the color shown on product cards.', 'tst-custom' ); ?></p>
    </div>
    <?php
}
add_action( 'pa_color_add_form_fields', 'tst_color_swatch_add_field' );

function tst_color_swatch_edit_field( $term ) {
    $color = get_term_meta( $term->term_id, 'tst_color_swatch', true );
    $color = is_string( $color ) ? sanitize_hex_color( $color ) : '';
    ?>
    <tr class="form-field term-tst-color-swatch-wrap">
      <th scope="row">
        <label for="tst-color-swatch">
          <?php esc_html_e( 'Swatch color', 'tst-custom' ); ?>
        </label>
      </th>
      <td>
        <input
          id="tst-color-swatch"
          class="tst-color-swatch-field"
          type="text"
          name="tst_color_swatch"
          value="<?php echo esc_attr( $color ?: '' ); ?>"
          data-default-color=""
        >
        <p class="description">
          <?php esc_html_e( 'Choose the color shown on product cards.', 'tst-custom' ); ?>
        </p>
      </td>
    </tr>
    <?php
}
add_action( 'pa_color_edit_form_fields', 'tst_color_swatch_edit_field' );

function tst_save_color_swatch( $term_id ) {
    if ( ! current_user_can( 'edit_term', $term_id ) ) {
        return;
    }

    if ( ! isset( $_POST['tst_color_swatch'] ) || ! is_string( $_POST['tst_color_swatch'] ) ) {
        return;
    }

    $raw_color = trim( wp_unslash( $_POST['tst_color_swatch'] ) );

    if ( '' === $raw_color ) {
        delete_term_meta( $term_id, 'tst_color_swatch' );
        return;
    }

    $color = sanitize_hex_color( $raw_color );

    if ( $color ) {
        update_term_meta( $term_id, 'tst_color_swatch', $color );
    }
}
add_action( 'created_pa_color', 'tst_save_color_swatch' );
add_action( 'edited_pa_color', 'tst_save_color_swatch' );

function tst_enqueue_color_swatch_admin_assets( $hook_suffix ) {
    if ( ! in_array( $hook_suffix, array( 'edit-tags.php', 'term.php' ), true ) ) {
        return;
    }

    $screen = get_current_screen();

    if ( ! $screen || 'pa_color' !== $screen->taxonomy ) {
        return;
    }

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script(
        'tst-color-swatch-admin',
        TST_URI . '/assets/js/admin/color-swatch.js',
        array( 'wp-color-picker' ),
        TST_VERSION,
        true
    );
}
add_action( 'admin_enqueue_scripts', 'tst_enqueue_color_swatch_admin_assets' );
