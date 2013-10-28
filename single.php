<?php get_header(); ?>

	<section>
	
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<article <?php post_class() ?>>
			
			<?php the_post_thumbnail( 'fullsize' ) ?>

			<h1 id="_"><?php echo str_replace( '/', '/<br>', get_the_title( $post->ID ) ) ?></h1>

			<h4>by <?php the_author(); ?></h4>
		
			<?php the_content() ?>
			<?php nw_thumbnail( 'fullsize', 'only video' ) ?>			

		</article>


		<?php endwhile; endif; ?>



		<h2 class="more_stuff">Check out more stuff:</h2>

		<div>

		<?php 
		global $i; $i=2; // make sure that all post listings are small, not the big top one like on the blog index page
		$p = get_posts( array( 'exclude' => $post->ID ) );
		foreach ( $p as $post ) : setup_postdata( $post );

			get_template_part( 'listing', $post->post_type );
			
		endforeach;
		?>

		</div>

		<?php echo posts_nav_link(); ?>

	</section>

<?php get_footer();
