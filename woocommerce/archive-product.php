<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'woocommerce_product_loop' ) ) {
    return;
}

$tst_object = get_queried_object();
$tst_archive_url = is_tax() && $tst_object instanceof WP_Term
    ? get_term_link( $tst_object )
    : get_post_type_archive_link( 'product' );
$tst_archive_url = is_wp_error( $tst_archive_url ) || ! $tst_archive_url
    ? home_url( '/' )
    : $tst_archive_url;
$tst_brand_taxonomy = tst_collection_brand_taxonomy();
$tst_filter_groups = array(
    'tst_color' => array( 'taxonomy' => 'pa_color', 'label' => __( 'Màu sắc', 'tst-custom' ) ),
    'tst_size'  => array( 'taxonomy' => 'pa_size', 'label' => __( 'Size', 'tst-custom' ) ),
    'tst_brand' => array( 'taxonomy' => $tst_brand_taxonomy, 'label' => __( 'Thương hiệu', 'tst-custom' ) ),
);
$tst_highest_product = function_exists( 'wc_get_products' )
    ? wc_get_products( array( 'limit' => 1, 'orderby' => 'price', 'order' => 'DESC', 'status' => 'publish' ) )
    : array();
$tst_highest_price = $tst_highest_product ? (float) $tst_highest_product[0]->get_price() : 0;
$tst_min_price = isset( $_GET['min_price'] ) && is_scalar( $_GET['min_price'] )
    ? max( 0, (float) wc_format_decimal( wp_unslash( $_GET['min_price'] ) ) )
    : 0;
$tst_max_price = isset( $_GET['max_price'] ) && is_scalar( $_GET['max_price'] )
    ? max( 0, (float) wc_format_decimal( wp_unslash( $_GET['max_price'] ) ) )
    : 0;
$tst_price_ceiling = max( 1, $tst_highest_price, $tst_max_price, $tst_min_price );
$tst_active_filters = array();
$tst_query_args = array();

if ( isset( $_GET['orderby'] ) && is_string( $_GET['orderby'] ) ) {
    $tst_query_args['orderby'] = sanitize_key( wp_unslash( $_GET['orderby'] ) );
}

if ( $tst_min_price > 0 ) {
    $tst_query_args['min_price'] = $tst_min_price;
    $tst_active_filters[] = array(
        'key'   => 'min_price',
        'label' => sprintf( __( 'Từ %s', 'tst-custom' ), wc_price( $tst_min_price ) ),
    );
}

if ( $tst_max_price > 0 && $tst_max_price < $tst_price_ceiling ) {
    $tst_query_args['max_price'] = $tst_max_price;
    $tst_active_filters[] = array(
        'key'   => 'max_price',
        'label' => sprintf( __( 'Đến %s', 'tst-custom' ), wc_price( $tst_max_price ) ),
    );
}

foreach ( $tst_filter_groups as $tst_key => $tst_group ) {
    $tst_slug = tst_collection_filter_value( $tst_key );
    $tst_taxonomy = $tst_group['taxonomy'];

    if ( ! $tst_slug || ! $tst_taxonomy || ! taxonomy_exists( $tst_taxonomy ) ) {
        continue;
    }

    $tst_selected_term = get_term_by( 'slug', $tst_slug, $tst_taxonomy );

    if ( ! $tst_selected_term ) {
        continue;
    }

    $tst_query_args[ $tst_key ] = $tst_slug;
    $tst_active_filters[] = array(
        'key'   => $tst_key,
        'label' => $tst_selected_term->name,
    );
}

$tst_clear_url = isset( $tst_query_args['orderby'] )
    ? add_query_arg( 'orderby', $tst_query_args['orderby'], $tst_archive_url )
    : $tst_archive_url;
$tst_total_products = isset( $GLOBALS['wp_query'] ) && $GLOBALS['wp_query'] instanceof WP_Query
    ? (int) $GLOBALS['wp_query']->found_posts
    : 0;

get_header( 'shop' );
?>
<div class="tst-container tst-collection">
  <nav class="tst-collection__breadcrumb" aria-label="<?php esc_attr_e( 'Đường dẫn', 'tst-custom' ); ?>">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'tst-custom' ); ?></a>
    <span aria-hidden="true">/</span>
    <?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
        <?php esc_html_e( 'Collections', 'tst-custom' ); ?>
      </a>
      <?php if ( is_tax() ) : ?><span aria-hidden="true">/</span><?php endif; ?>
    <?php endif; ?>
    <?php if ( is_tax() ) : ?>
      <span aria-current="page"><?php echo esc_html( single_term_title( '', false ) ); ?></span>
    <?php endif; ?>
  </nav>

  <header class="tst-collection__header">
    <h1><?php echo esc_html( woocommerce_page_title( false ) ); ?></h1>
    <?php do_action( 'woocommerce_archive_description' ); ?>
  </header>

  <div class="tst-collection__layout">
    <aside class="tst-collection__filters" aria-label="<?php esc_attr_e( 'Bộ lọc sản phẩm', 'tst-custom' ); ?>">
      <div class="tst-collection__active">
        <div class="tst-collection__active-heading">
          <h2>
            <?php
            echo esc_html(
                sprintf( __( 'BỘ LỌC (%d)', 'tst-custom' ), count( $tst_active_filters ) )
            );
            ?>
          </h2>
          <?php if ( $tst_active_filters ) : ?>
            <a href="<?php echo esc_url( $tst_clear_url ); ?>">
              <?php esc_html_e( 'Xóa Hết', 'tst-custom' ); ?>
            </a>
          <?php endif; ?>
        </div>
        <?php if ( $tst_active_filters ) : ?>
          <div class="tst-collection__active-chips">
            <?php foreach ( $tst_active_filters as $tst_filter ) : ?>
              <?php
              $tst_remove_args = $tst_query_args;
              unset( $tst_remove_args[ $tst_filter['key'] ] );
              $tst_remove_url = add_query_arg( $tst_remove_args, $tst_archive_url );
              ?>
              <a
                class="tst-collection__chip"
                href="<?php echo esc_url( $tst_remove_url ); ?>"
                aria-label="<?php echo esc_attr( sprintf( __( 'Bỏ lọc %s', 'tst-custom' ), wp_strip_all_tags( $tst_filter['label'] ) ) ); ?>"
              >
                <span><?php echo wp_kses_post( $tst_filter['label'] ); ?></span>
                <span aria-hidden="true">×</span>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <form method="get" action="<?php echo esc_url( $tst_archive_url ); ?>" data-tst-collection-filter>
        <?php if ( isset( $_GET['orderby'] ) && is_string( $_GET['orderby'] ) ) : ?>
          <input type="hidden" name="orderby" value="<?php echo esc_attr( sanitize_key( wp_unslash( $_GET['orderby'] ) ) ); ?>">
        <?php endif; ?>

        <details class="tst-collection__filter" open>
          <summary><?php esc_html_e( 'Giá', 'tst-custom' ); ?></summary>
          <div class="tst-collection__price-range" data-tst-price-range>
            <input type="range" min="0" max="<?php echo esc_attr( $tst_price_ceiling ); ?>" value="<?php echo esc_attr( $tst_min_price ); ?>" aria-label="<?php esc_attr_e( 'Giá thấp nhất', 'tst-custom' ); ?>" data-tst-price-min-range>
            <input type="range" min="0" max="<?php echo esc_attr( $tst_price_ceiling ); ?>" value="<?php echo esc_attr( $tst_max_price ?: $tst_price_ceiling ); ?>" aria-label="<?php esc_attr_e( 'Giá cao nhất', 'tst-custom' ); ?>" data-tst-price-max-range>
          </div>
          <div class="tst-collection__price-inputs">
            <label>
              <span class="screen-reader-text"><?php esc_html_e( 'Giá thấp nhất', 'tst-custom' ); ?></span>
              <input type="number" name="min_price" min="0" max="<?php echo esc_attr( $tst_price_ceiling ); ?>" value="<?php echo esc_attr( $tst_min_price ); ?>" data-tst-price-min>
              <span>₫</span>
            </label>
            <label>
              <span class="screen-reader-text"><?php esc_html_e( 'Giá cao nhất', 'tst-custom' ); ?></span>
              <input type="number" name="max_price" min="0" value="<?php echo esc_attr( $tst_max_price ?: $tst_price_ceiling ); ?>" data-tst-price-max>
              <span>₫</span>
            </label>
          </div>
        </details>

        <?php foreach ( $tst_filter_groups as $tst_key => $tst_group ) : ?>
          <?php
          $tst_taxonomy = $tst_group['taxonomy'];
          $tst_terms = $tst_taxonomy && taxonomy_exists( $tst_taxonomy )
              ? get_terms( array( 'taxonomy' => $tst_taxonomy, 'hide_empty' => true ) )
              : array();

          if ( is_wp_error( $tst_terms ) || ! $tst_terms ) {
              continue;
          }
          ?>
          <details class="tst-collection__filter" open>
          <summary>
            <?php echo esc_html( $tst_group['label'] ); ?>
            <?php if ( isset( $tst_query_args[ $tst_key ] ) ) : ?>
              <span>(1)</span>
            <?php endif; ?>
          </summary>
            <div class="<?php echo 'tst_color' === $tst_key ? 'tst-collection__colors' : 'tst-collection__choices'; ?>">
              <?php foreach ( $tst_terms as $tst_term ) : ?>
                <?php if ( 'tst_color' === $tst_key ) : ?>
                  <?php $tst_hex = sanitize_hex_color( get_term_meta( $tst_term->term_id, 'tst_color_swatch', true ) ); ?>
                  <label class="tst-collection__color" title="<?php echo esc_attr( $tst_term->name ); ?>">
                    <input type="checkbox" name="tst_color" value="<?php echo esc_attr( $tst_term->slug ); ?>" <?php checked( tst_collection_filter_value( 'tst_color' ), $tst_term->slug ); ?>>
                    <span style="background-color: <?php echo esc_attr( $tst_hex ?: '#d9d9d9' ); ?>;"></span>
                    <span class="screen-reader-text"><?php echo esc_html( $tst_term->name ); ?></span>
                  </label>
                <?php else : ?>
                  <label>
                    <input type="checkbox" name="<?php echo esc_attr( $tst_key ); ?>" value="<?php echo esc_attr( $tst_term->slug ); ?>" <?php checked( tst_collection_filter_value( $tst_key ), $tst_term->slug ); ?>>
                    <span><?php echo esc_html( $tst_term->name ); ?></span>
                  </label>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          </details>
        <?php endforeach; ?>

      </form>
    </aside>

    <section class="tst-collection__products" aria-label="<?php esc_attr_e( 'Sản phẩm', 'tst-custom' ); ?>">
      <div class="tst-collection__sortbar">
        <span><?php esc_html_e( 'Sắp xếp theo', 'tst-custom' ); ?></span>
        <?php if ( function_exists( 'woocommerce_catalog_ordering' ) ) : ?>
          <?php woocommerce_catalog_ordering(); ?>
        <?php endif; ?>
        <span class="tst-collection__result-count">
          <?php echo esc_html( sprintf( __( '%d sản phẩm', 'tst-custom' ), $tst_total_products ) ); ?>
        </span>
      </div>

      <?php if ( woocommerce_product_loop() ) : ?>
        <div class="tst-collection__grid">
          <?php while ( have_posts() ) : ?>
            <?php
            the_post();
            tst_get_product_card(
                wc_get_product( get_the_ID() ),
                array( 'show_swatches' => true, 'show_sizes' => true )
            );
            ?>
          <?php endwhile; ?>
        </div>
      <?php else : ?>
        <?php do_action( 'woocommerce_no_products_found' ); ?>
      <?php endif; ?>

      <?php do_action( 'woocommerce_after_shop_loop' ); ?>
    </section>
  </div>
</div>
<?php get_footer( 'shop' ); ?>
