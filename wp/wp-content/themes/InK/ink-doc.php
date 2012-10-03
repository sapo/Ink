<?php
/*
Template Name: InK Documentation
*/
?>


<?php get_header(); ?>
<section>
	<h1><?php wp_title() ?></h1>
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<?php the_content()?>
	<?php endwhile; else: ?>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	<?php endif; ?>
</section>
<?php get_footer(); ?>