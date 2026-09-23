<?php
$limit = max( 2, absint( tst_get_setting( 'tabs_products_per_tab', 8 ) ) );
$groups = array(
    'new'  => wc_get_products(
        array(
            'status'  => 'publish',
            'limit'   => $limit,
            'orderby' => 'date',
            'order'   => 'DESC',
        )
    ),
    'best' => wc_get_products(
        array(
            'status'  => 'publish',
            'limit'   => $limit,
            'orderby' => 'popularity',
            'order'   => 'DESC',
        )
    ),
);
?>
<section class="tst-product-tabs tst-section" data-tst-product-tabs>
  <div class="tst-container">
    <div class="tst-product-tabs__header">
      <div class="tst-product-tabs__nav" role="tablist">
        <button
          class="tst-product-tabs__tab is-active"
          role="tab"
          aria-selected="true"
          data-tst-tab="new"
        >
          <?php echo esc_html( tst_get_setting( 'tabs_new_label', 'Sản phẩm mới' ) ); ?>
        </button>
        <button
          class="tst-product-tabs__tab"
          role="tab"
          aria-selected="false"
          data-tst-tab="best"
        >
          <?php echo esc_html( tst_get_setting( 'tabs_best_label', 'Best Seller' ) ); ?>
        </button>
      </div>
      <a
        class="tst-product-tabs__more"
        href="<?php echo esc_url( tst_get_setting( 'tabs_more_url', wc_get_page_permalink( 'shop' ) ) ); ?>"
      >Xem thêm →</a>
    </div>

    <?php foreach ( $groups as $key => $products ) : ?>
      <div
        class="tst-product-tabs__panel"
        role="tabpanel"
        data-tst-panel="<?php echo esc_attr( $key ); ?>"
        <?php echo 'new' === $key ? '' : 'hidden'; ?>
      >
        <div class="tst-product-tabs__slider">
          <button
            class="tst-product-tabs__arrow tst-product-tabs__arrow--prev"
            type="button"
            aria-label="Previous products"
          >‹</button>
          <div class="tst-product-tabs__track">
            <?php foreach ( $products as $product ) : ?>
              <?php tst_get_product_card( $product ); ?>
            <?php endforeach; ?>
          </div>
          <button
            class="tst-product-tabs__arrow tst-product-tabs__arrow--next"
            type="button"
            aria-label="Next products"
          >›</button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
