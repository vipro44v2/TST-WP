<?php get_header(); ?>
<div class="tst-container tst-content">
  <?php while ( have_posts() ) : ?>
    <?php the_post(); ?>
    <article>
      <h1><?php the_title(); ?></h1>
      <?php the_content(); ?>
    </article>
  <?php endwhile; ?>
</div>
<?php get_footer(); ?>
