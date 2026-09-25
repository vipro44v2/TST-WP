<?php
/**
 * Customer login and registration forms.
 *
 * @package TST_Custom
 * @version 9.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tst_can_register = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
$tst_register_posted = isset( $_POST['register'] );
$tst_initial_tab = $tst_can_register && $tst_register_posted ? 'register' : 'login';
$tst_username = isset( $_POST['username'] ) && is_string( $_POST['username'] )
    ? sanitize_text_field( wp_unslash( $_POST['username'] ) )
    : '';
$tst_email = isset( $_POST['email'] ) && is_string( $_POST['email'] )
    ? sanitize_email( wp_unslash( $_POST['email'] ) )
    : '';
$tst_phone = isset( $_POST['tst_register_phone'] ) && is_string( $_POST['tst_register_phone'] )
    ? sanitize_text_field( wp_unslash( $_POST['tst_register_phone'] ) )
    : '';

do_action( 'woocommerce_before_customer_login_form' );
?>
<div class="tst-account" data-tst-account>
  <div class="tst-account__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Tài khoản', 'tst-custom' ); ?>">
    <button
      class="tst-account__tab"
      id="tst-login-tab"
      type="button"
      role="tab"
      aria-controls="tst-login-panel"
      aria-selected="<?php echo esc_attr( 'login' === $tst_initial_tab ? 'true' : 'false' ); ?>"
      data-tst-account-tab="login"
    ><?php esc_html_e( 'Đăng nhập', 'tst-custom' ); ?></button>
    <?php if ( $tst_can_register ) : ?>
      <button
        class="tst-account__tab"
        id="tst-register-tab"
        type="button"
        role="tab"
        aria-controls="tst-register-panel"
        aria-selected="<?php echo esc_attr( 'register' === $tst_initial_tab ? 'true' : 'false' ); ?>"
        data-tst-account-tab="register"
      ><?php esc_html_e( 'Đăng ký', 'tst-custom' ); ?></button>
    <?php endif; ?>
  </div>

  <section
    class="tst-account__panel"
    id="tst-login-panel"
    role="tabpanel"
    aria-labelledby="tst-login-tab"
    data-tst-account-panel="login"
    <?php echo 'login' !== $tst_initial_tab ? 'hidden' : ''; ?>
  >
    <form class="woocommerce-form woocommerce-form-login login tst-account__form" method="post" novalidate>
      <?php do_action( 'woocommerce_login_form_start' ); ?>

      <p class="woocommerce-form-row form-row form-row-wide">
        <label for="username"><?php esc_html_e( 'Tên tài khoản hoặc địa chỉ email', 'tst-custom' ); ?> <span class="required" aria-hidden="true">*</span></label>
        <input class="woocommerce-Input input-text" type="text" name="username" id="username" autocomplete="username" value="<?php echo esc_attr( $tst_username ); ?>" required>
      </p>

      <p class="woocommerce-form-row form-row form-row-wide">
        <label for="password"><?php esc_html_e( 'Mật khẩu', 'tst-custom' ); ?> <span class="required" aria-hidden="true">*</span></label>
        <input class="woocommerce-Input input-text" type="password" name="password" id="password" autocomplete="current-password" required>
      </p>

      <?php do_action( 'woocommerce_login_form' ); ?>

      <label class="woocommerce-form__label woocommerce-form-login__rememberme">
        <input class="woocommerce-form__input-checkbox" name="rememberme" type="checkbox" value="forever">
        <span><?php esc_html_e( 'Ghi nhớ mật khẩu', 'tst-custom' ); ?></span>
      </label>

      <p class="form-row">
        <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
        <button class="woocommerce-button button woocommerce-form-login__submit" type="submit" name="login" value="1"><?php esc_html_e( 'Đăng nhập', 'tst-custom' ); ?></button>
      </p>

      <p class="woocommerce-LostPassword lost_password">
        <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Quên mật khẩu?', 'tst-custom' ); ?></a>
      </p>

      <?php do_action( 'woocommerce_login_form_end' ); ?>
    </form>
  </section>

  <?php if ( $tst_can_register ) : ?>
    <section
      class="tst-account__panel"
      id="tst-register-panel"
      role="tabpanel"
      aria-labelledby="tst-register-tab"
      data-tst-account-panel="register"
      <?php echo 'register' !== $tst_initial_tab ? 'hidden' : ''; ?>
    >
      <form class="woocommerce-form woocommerce-form-register register tst-account__form" method="post" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
        <?php do_action( 'woocommerce_register_form_start' ); ?>

        <p class="woocommerce-form-row form-row form-row-wide">
          <label for="tst_register_phone"><?php esc_html_e( 'Số điện thoại', 'tst-custom' ); ?> <span class="required" aria-hidden="true">*</span></label>
          <input class="woocommerce-Input input-text" type="tel" name="tst_register_phone" id="tst_register_phone" autocomplete="tel" value="<?php echo esc_attr( $tst_phone ); ?>" required>
        </p>

        <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
          <p class="woocommerce-form-row form-row form-row-wide">
            <label for="reg_username"><?php esc_html_e( 'Tên tài khoản', 'tst-custom' ); ?> <span class="required" aria-hidden="true">*</span></label>
            <input class="woocommerce-Input input-text" type="text" name="username" id="reg_username" autocomplete="username" value="<?php echo esc_attr( $tst_username ); ?>" required>
          </p>
        <?php endif; ?>

        <p class="woocommerce-form-row form-row form-row-wide">
          <label for="reg_email"><?php esc_html_e( 'Địa chỉ email', 'tst-custom' ); ?> <span class="required" aria-hidden="true">*</span></label>
          <input class="woocommerce-Input input-text" type="email" name="email" id="reg_email" autocomplete="email" value="<?php echo esc_attr( $tst_email ); ?>" required>
        </p>

        <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
          <p class="woocommerce-form-row form-row form-row-wide">
            <label for="reg_password"><?php esc_html_e( 'Mật khẩu', 'tst-custom' ); ?> <span class="required" aria-hidden="true">*</span></label>
            <input class="woocommerce-Input input-text" type="password" name="password" id="reg_password" autocomplete="new-password" required>
          </p>
        <?php else : ?>
          <p class="tst-account__hint"><?php esc_html_e( 'Một liên kết đặt mật khẩu sẽ được gửi đến địa chỉ email của bạn.', 'tst-custom' ); ?></p>
        <?php endif; ?>

        <?php do_action( 'woocommerce_register_form' ); ?>

        <p class="form-row">
          <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
          <button class="woocommerce-button button woocommerce-form-register__submit" type="submit" name="register" value="1"><?php esc_html_e( 'Đăng ký', 'tst-custom' ); ?></button>
        </p>

        <?php do_action( 'woocommerce_register_form_end' ); ?>
      </form>
    </section>
  <?php endif; ?>
</div>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
