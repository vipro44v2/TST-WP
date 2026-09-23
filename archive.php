<?php get_header(); ?>
<div class="tst-container tst-content">
  <h1><?php the_archive_title(); ?></h1>

  <?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : ?>
      <?php the_post(); ?>
      <article>
        <h2>
          <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>
        <?php the_excerpt(); ?>
      </article>
    <?php endwhile; ?>
    <?php the_posts_pagination(); ?>
  <?php endif; ?>
</div>
<?php get_footer(); ?>
