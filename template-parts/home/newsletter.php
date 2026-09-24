<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tst_block = is_array( $args['block'] ?? null ) ? $args['block'] : array();
$tst_location = 'product' === ( $args['location'] ?? '' ) ? 'product' : 'home';
$tst_status = isset( $_GET['tst_newsletter'] ) && is_string( $_GET['tst_newsletter'] )
    ? sanitize_key( wp_unslash( $_GET['tst_newsletter'] ) )
    : '';
$tst_source = isset( $_GET['tst_newsletter_source'] ) && is_string( $_GET['tst_newsletter_source'] )
    ? sanitize_key( wp_unslash( $_GET['tst_newsletter_source'] ) )
    : '';
$tst_messages = array(
    'success' => __( 'Đăng ký thành công. Cảm ơn bạn!', 'tst-custom' ),
    'exists'  => __( 'Email này đã được đăng ký.', 'tst-custom' ),
    'invalid' => __( 'Vui lòng nhập email hợp lệ.', 'tst-custom' ),
    'error'   => __( 'Không thể đăng ký lúc này. Vui lòng thử lại.', 'tst-custom' ),
);
?>
<section id="tst-newsletter" class="tst-newsletter" aria-label="<?php echo esc_attr( $tst_block['title'] ?? '' ); ?>">
  <div class="tst-newsletter__inner">
    <div class="tst-newsletter__copy">
      <?php if ( ! empty( $tst_block['title'] ) ) : ?>
        <h2 class="tst-newsletter__title"><?php echo esc_html( $tst_block['title'] ); ?></h2>
      <?php endif; ?>
      <?php if ( ! empty( $tst_block['description'] ) ) : ?>
        <p class="tst-newsletter__description">
          <?php echo esc_html( $tst_block['description'] ); ?>
        </p>
      <?php endif; ?>
      <?php if ( $tst_location === $tst_source && isset( $tst_messages[ $tst_status ] ) ) : ?>
        <p class="tst-newsletter__message" role="status">
          <?php echo esc_html( $tst_messages[ $tst_status ] ); ?>
        </p>
      <?php endif; ?>
    </div>
    <form class="tst-newsletter__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
      <input type="hidden" name="action" value="tst_newsletter_subscribe">
      <input type="hidden" name="tst_newsletter_location" value="<?php echo esc_attr( $tst_location ); ?>">
      <?php wp_nonce_field( 'tst_newsletter_subscribe', 'tst_newsletter_nonce' ); ?>
      <label class="tst-newsletter__field">
        <span class="screen-reader-text">
          <?php esc_html_e( 'Địa chỉ email', 'tst-custom' ); ?>
        </span>
        <input
          class="tst-newsletter__input"
          type="email"
          name="tst_newsletter_email"
          placeholder="<?php echo esc_attr( $tst_block['placeholder'] ?? '' ); ?>"
          autocomplete="email"
          required
        >
      </label>
      <input
        class="tst-newsletter__honeypot"
        type="text"
        name="tst_newsletter_website"
        tabindex="-1"
        autocomplete="off"
        aria-hidden="true"
      >
      <button
        class="tst-newsletter__submit"
        type="submit"
        aria-label="<?php esc_attr_e( 'Đăng ký nhận bản tin', 'tst-custom' ); ?>"
      >
        <span aria-hidden="true">›</span>
      </button>
    </form>
  </div>
</section>
