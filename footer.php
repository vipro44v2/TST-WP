<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
</main>
<footer class="tst-footer">
  <div class="tst-container">
    <div class="tst-footer__grid">
      <div><?php bloginfo( 'name' ); ?></div>
      <?php
        wp_nav_menu(
            array(
                'theme_location' => 'footer-1',
                'fallback_cb'    => false,
                'container'      => false,
            )
        );
        wp_nav_menu(
            array(
                'theme_location' => 'footer-2',
                'fallback_cb'    => false,
                'container'      => false,
            )
        );
        if ( is_active_sidebar( 'footer' ) ) {
            dynamic_sidebar( 'footer' );
        }
        ?>
    </div>
    <p>
      &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
      <?php bloginfo( 'name' ); ?>
    </p>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
