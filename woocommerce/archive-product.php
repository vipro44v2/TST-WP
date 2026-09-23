<?php
defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
?>
<div class="tst-container tst-content">
  <header class="tst-archive-header">
    <h1><?php woocommerce_page_title(); ?></h1>
    <?php do_action( 'woocommerce_archive_description' ); ?>
  </header>

  <div class="tst-collection-layout">
    <aside class="tst-collection__filters">
      <h2><?php esc_html_e( 'Filter products', 'tst-custom' ); ?></h2>
      <?php do_action( 'woocommerce_sidebar' ); ?>
    </aside>

    <section>
      <div class="tst-sortbar">
        <?php do_action( 'woocommerce_before_shop_loop' ); ?>
      </div>

      <div class="tst-product-grid">
        <?php if ( woocommerce_product_loop() ) : ?>
          <?php while ( have_posts() ) : ?>
            <?php
            the_post();
            wc_get_template_part( 'content', 'product' );
            ?>
          <?php endwhile; ?>
        <?php else : ?>
          <?php do_action( 'woocommerce_no_products_found' ); ?>
        <?php endif; ?>
      </div>

      <?php do_action( 'woocommerce_after_shop_loop' ); ?>
    </section>
  </div>
</div>
<?php get_footer( 'shop' ); ?>
