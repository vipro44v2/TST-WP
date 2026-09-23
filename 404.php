<?php get_header(); ?>
<div class="tst-container tst-content">
  <h1><?php esc_html_e( 'Page not found', 'tst-custom' ); ?></h1>
  <a class="tst-button" href="<?php echo esc_url( home_url( '/' ) ); ?>">
    <?php esc_html_e( 'Back home', 'tst-custom' ); ?>
  </a>
</div>
<?php get_footer(); ?>
