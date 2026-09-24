<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_template_part' ) ) {
    return;
}

get_header( 'shop' );
?>
<div class="tst-container tst-single-product">
  <?php while ( have_posts() ) : ?>
    <?php
    the_post();
    wc_get_template_part( 'content', 'single-product' );
    ?>
  <?php endwhile; ?>
</div>
<?php
$tst_newsletter_block = tst_home_newsletter_default_block();
$tst_contact_block = tst_home_contact_default_block();

foreach ( tst_get_home_blocks() as $tst_block ) {
    if ( ! is_array( $tst_block ) ) {
        continue;
    }

    if ( 'newsletter' === ( $tst_block['type'] ?? '' ) ) {
        $tst_newsletter_block = array_merge( $tst_newsletter_block, $tst_block );
    }

    if ( 'contact' === ( $tst_block['type'] ?? '' ) ) {
        $tst_contact_block = array_merge( $tst_contact_block, $tst_block );
    }
}

get_template_part(
    'template-parts/home/newsletter',
    null,
    array(
        'block'    => $tst_newsletter_block,
        'location' => 'product',
    )
);
get_template_part(
    'template-parts/home/contact-feedback',
    null,
    array( 'block' => $tst_contact_block )
);
?>
<?php get_footer( 'shop' ); ?>
