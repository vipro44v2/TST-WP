<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tst_register_newsletter_subscriber_type() {
    register_post_type(
        'tst_subscriber',
        array(
            'label'               => __( 'Người đăng ký bản tin', 'tst-custom' ),
            'public'              => false,
            'show_ui'             => false,
            'show_in_rest'        => false,
            'exclude_from_search' => true,
            'supports'            => array( 'title' ),
        )
    );
}
add_action( 'init', 'tst_register_newsletter_subscriber_type' );

function tst_newsletter_redirect( $status ) {
    $raw_location = $_POST['tst_newsletter_location'] ?? '';
    $location = is_string( $raw_location )
        ? sanitize_key( wp_unslash( $raw_location ) )
        : '';
    $location = in_array( $location, array( 'footer', 'product' ), true ) ? $location : 'home';
    $return_url = home_url( '/' );

    if ( 'home' !== $location ) {
        $referer = wp_get_referer();

        if ( $referer ) {
            $return_url = wp_validate_redirect( $referer, $return_url );
        }
    }

    $return_url = remove_query_arg( array( 'tst_newsletter', 'tst_newsletter_source' ), $return_url );
    $url = add_query_arg(
        array(
            'tst_newsletter'        => $status,
            'tst_newsletter_source' => $location,
        ),
        $return_url
    );
    $fragment = 'footer' === $location ? '#tst-footer-newsletter' : '#tst-newsletter';

    wp_safe_redirect( $url . $fragment );
    exit;
}

function tst_newsletter_subscribe() {
    $nonce = isset( $_POST['tst_newsletter_nonce'] ) && is_string( $_POST['tst_newsletter_nonce'] )
        ? sanitize_text_field( wp_unslash( $_POST['tst_newsletter_nonce'] ) )
        : '';

    if ( ! wp_verify_nonce( $nonce, 'tst_newsletter_subscribe' ) ) {
        tst_newsletter_redirect( 'error' );
    }

    if ( ! empty( $_POST['tst_newsletter_website'] ) ) {
        tst_newsletter_redirect( 'success' );
    }

    $raw_email = $_POST['tst_newsletter_email'] ?? '';
    $email = is_string( $raw_email )
        ? strtolower( sanitize_email( wp_unslash( $raw_email ) ) )
        : '';

    if ( ! is_email( $email ) ) {
        tst_newsletter_redirect( 'invalid' );
    }

    $existing = get_posts(
        array(
            'post_type'      => 'tst_subscriber',
            'post_status'    => 'private',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_key'       => 'tst_subscriber_email',
            'meta_value'     => $email,
        )
    );

    if ( $existing ) {
        tst_newsletter_redirect( 'exists' );
    }

    $post_id = wp_insert_post(
        array(
            'post_type'   => 'tst_subscriber',
            'post_status' => 'private',
            'post_title'  => $email,
            'post_author' => 0,
            'meta_input'  => array(
                'tst_subscriber_email' => $email,
            ),
        ),
        true
    );

    tst_newsletter_redirect( is_wp_error( $post_id ) || ! $post_id ? 'error' : 'success' );
}
add_action( 'admin_post_nopriv_tst_newsletter_subscribe', 'tst_newsletter_subscribe' );
add_action( 'admin_post_tst_newsletter_subscribe', 'tst_newsletter_subscribe' );

function tst_newsletter_admin_menu() {
    add_theme_page(
        __( 'Người đăng ký bản tin', 'tst-custom' ),
        __( 'Người đăng ký bản tin', 'tst-custom' ),
        'manage_options',
        'tst-newsletter-subscribers',
        'tst_newsletter_admin_page'
    );
}
add_action( 'admin_menu', 'tst_newsletter_admin_menu' );

function tst_newsletter_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'Bạn không có quyền xem trang này.', 'tst-custom' ) );
    }

    $page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
    $query = new WP_Query(
        array(
            'post_type'      => 'tst_subscriber',
            'post_status'    => 'private',
            'posts_per_page' => 50,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );
    ?>
    <div class="wrap">
      <h1><?php esc_html_e( 'Người đăng ký bản tin', 'tst-custom' ); ?></h1>
      <p><?php esc_html_e( 'Danh sách email đã đăng ký từ trang chủ.', 'tst-custom' ); ?></p>
      <table class="widefat striped">
        <thead>
          <tr>
            <th><?php esc_html_e( 'Email', 'tst-custom' ); ?></th>
            <th><?php esc_html_e( 'Ngày đăng ký', 'tst-custom' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if ( $query->have_posts() ) : ?>
            <?php foreach ( $query->posts as $subscriber ) : ?>
              <tr>
                <td><?php echo esc_html( get_post_meta( $subscriber->ID, 'tst_subscriber_email', true ) ); ?></td>
                <td><?php echo esc_html( get_the_date( 'd/m/Y H:i', $subscriber ) ); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="2"><?php esc_html_e( 'Chưa có email đăng ký.', 'tst-custom' ); ?></td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
      <?php
      $pagination = paginate_links(
          array(
              'base'    => add_query_arg( 'paged', '%#%' ),
              'current' => $page,
              'total'   => $query->max_num_pages,
          )
      );

      if ( is_string( $pagination ) ) {
          echo wp_kses_post( $pagination );
      }
      ?>
    </div>
    <?php
}
