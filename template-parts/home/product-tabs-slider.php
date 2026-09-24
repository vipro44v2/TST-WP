<?php
if ( ! defined( 'ABSPATH' ) || ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_products' ) ) {
    return;
}

$tst_block = is_array( $args['block'] ?? null ) ? $args['block'] : array();
$tst_id = sanitize_html_class( $tst_block['id'] ?? 'default' );
$tst_limit = tst_home_clamp( $tst_block['products_per_tab'] ?? 8, 2, 24 );
$tst_shop_url = function_exists( 'wc_get_page_permalink' )
    ? wc_get_page_permalink( 'shop' )
    : home_url( '/' );
$tst_shop_url = $tst_shop_url ?: home_url( '/' );
$tst_more_url = $tst_block['more_url'] ?? '';
$tst_style = sprintf(
    '--tst-tabs-desktop:%d;--tst-tabs-tablet:%d;--tst-tabs-mobile:%d;'
    . '--tst-tabs-image-bg:%s;--tst-tabs-radius:%dpx;--tst-tabs-top:%dpx;--tst-tabs-bottom:%dpx;',
    tst_home_clamp( $tst_block['desktop_columns'] ?? 4, 1, 6 ),
    tst_home_clamp( $tst_block['tablet_columns'] ?? 3, 1, 4 ),
    tst_home_clamp( $tst_block['mobile_columns'] ?? 2, 1, 2 ),
    sanitize_hex_color( $tst_block['image_background'] ?? '' ) ?: '#f5f5f5',
    tst_home_clamp( $tst_block['card_radius'] ?? 0, 0, 50 ),
    tst_home_clamp( $tst_block['top_spacing'] ?? 80, 0, 200 ),
    tst_home_clamp( $tst_block['bottom_spacing'] ?? 80, 0, 200 )
);
$tst_groups = array();
$tst_collection_ids = is_array( $tst_block['collection_ids'] ?? null )
    ? $tst_block['collection_ids']
    : array();
$tst_collections = array();

foreach ( array_slice( array_unique( array_map( 'absint', $tst_collection_ids ) ), 0, 6 ) as $tst_collection_id ) {
    if ( $tst_collection_id === absint( get_option( 'default_product_cat', 0 ) ) ) {
        continue;
    }

    $tst_term = get_term( absint( $tst_collection_id ), 'product_cat' );

    if ( $tst_term && ! is_wp_error( $tst_term ) && 'uncategorized' !== $tst_term->slug ) {
        $tst_collections[] = $tst_term;
    }
}

if ( ! $tst_collections ) {
    $tst_collections = tst_home_product_collections( true );

    if ( ! $tst_collections ) {
        $tst_collections = tst_home_product_collections();
    }

    $tst_collections = array_slice( $tst_collections, 0, 2 );
}

foreach ( $tst_collections as $tst_term ) {
    $tst_term_url = get_term_link( $tst_term );
    $tst_collection_limit = ! isset( $tst_block['collection_show_all'] ) || $tst_block['collection_show_all']
        ? 24
        : $tst_limit;
    $tst_groups[ 'cat-' . $tst_term->term_id ] = array(
        'label'    => $tst_term->name,
        'url'      => is_wp_error( $tst_term_url ) ? $tst_shop_url : $tst_term_url,
        'products' => wc_get_products(
            array(
                'status'   => 'publish',
                'limit'    => $tst_collection_limit,
                'category' => array( $tst_term->slug ),
                'orderby'  => 'date',
                'order'    => 'DESC',
            )
        ),
    );
}

if ( ! $tst_groups ) {
    $tst_groups = array(
        'new'  => array(
            'label'    => $tst_block['new_label'] ?? __( 'Sản phẩm mới', 'tst-custom' ),
            'url'      => $tst_shop_url,
            'products' => wc_get_products(
                array(
                    'status'  => 'publish',
                    'limit'   => $tst_limit,
                    'orderby' => 'date',
                    'order'   => 'DESC',
                )
            ),
        ),
        'best' => array(
            'label'    => $tst_block['best_label'] ?? __( 'Best Seller', 'tst-custom' ),
            'url'      => $tst_shop_url,
            'products' => wc_get_products(
                array(
                    'status'  => 'publish',
                    'limit'   => $tst_limit,
                    'orderby' => 'popularity',
                    'order'   => 'DESC',
                )
            ),
        ),
    );
}

$tst_first_key = array_key_first( $tst_groups );
$tst_first_group = $tst_groups[ $tst_first_key ];
$tst_initial_more_url = $tst_more_url ?: $tst_first_group['url'];
?>
<section class="tst-product-tabs tst-section" data-tst-product-tabs style="<?php echo esc_attr( $tst_style ); ?>">
  <div class="tst-container">
    <?php if ( ! empty( $tst_block['title'] ) ) : ?>
      <h2><?php echo esc_html( $tst_block['title'] ); ?></h2>
    <?php endif; ?>
    <div class="tst-product-tabs__header">
      <div
        class="tst-product-tabs__nav"
        role="tablist"
        aria-label="<?php esc_attr_e( 'Product groups', 'tst-custom' ); ?>"
      >
        <?php foreach ( $tst_groups as $tst_key => $tst_group ) : ?>
          <button
            id="tst-tab-<?php echo esc_attr( $tst_key . '-' . $tst_id ); ?>"
            class="tst-product-tabs__tab<?php echo $tst_first_key === $tst_key ? ' is-active' : ''; ?>"
            type="button"
            role="tab"
            tabindex="<?php echo $tst_first_key === $tst_key ? '0' : '-1'; ?>"
            aria-selected="<?php echo $tst_first_key === $tst_key ? 'true' : 'false'; ?>"
            aria-controls="tst-panel-<?php echo esc_attr( $tst_key . '-' . $tst_id ); ?>"
            data-tst-tab="<?php echo esc_attr( $tst_key ); ?>"
            data-tst-more-url="<?php echo esc_url( $tst_more_url ?: $tst_group['url'] ); ?>"
          ><?php echo esc_html( $tst_group['label'] ); ?></button>
        <?php endforeach; ?>
      </div>
      <?php if ( ! empty( $tst_block['more_enabled'] ) ) : ?>
        <a class="tst-product-tabs__more" href="<?php echo esc_url( $tst_initial_more_url ); ?>">
          <?php echo esc_html( $tst_block['more_text'] ?? __( 'Xem thêm', 'tst-custom' ) ); ?> →
        </a>
      <?php endif; ?>
    </div>

    <?php foreach ( $tst_groups as $tst_key => $tst_group ) : ?>
      <div
        id="tst-panel-<?php echo esc_attr( $tst_key . '-' . $tst_id ); ?>"
        class="tst-product-tabs__panel"
        role="tabpanel"
        aria-labelledby="tst-tab-<?php echo esc_attr( $tst_key . '-' . $tst_id ); ?>"
        tabindex="0"
        data-tst-panel="<?php echo esc_attr( $tst_key ); ?>"
        <?php echo $tst_first_key === $tst_key ? '' : 'hidden'; ?>
      >
        <div class="tst-product-tabs__slider">
          <button
            class="tst-product-tabs__arrow tst-product-tabs__arrow--prev"
            type="button"
            aria-label="<?php esc_attr_e( 'Previous products', 'tst-custom' ); ?>"
          >‹</button>
          <div class="tst-product-tabs__track">
            <?php foreach ( $tst_group['products'] as $tst_product ) : ?>
              <?php tst_get_product_card( $tst_product, $tst_block ); ?>
            <?php endforeach; ?>
          </div>
          <button
            class="tst-product-tabs__arrow tst-product-tabs__arrow--next"
            type="button"
            aria-label="<?php esc_attr_e( 'Next products', 'tst-custom' ); ?>"
          >›</button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
