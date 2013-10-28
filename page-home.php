<?php get_header(); ?>


	<section class="first-block">

		<?php

		while (have_posts()) : the_post();
			the_content();
		endwhile;

		?>

		<h1 class="supersize">records</h1>
		
		<div>

		<?php
		$args = array(
			'post_type' => array( 'product' /*, 'event'*/ ),
			'post_parent' => 0,
			'numberposts' => 10,
			);

		$p = get_posts( $args );
		foreach ( $p as $post ) : setup_postdata($post);

			get_template_part( 'listing', $post->post_type );

		endforeach;
		?>
		
		</div>
		
	</section>


	<section class="second-block">

		<h1 class="supersize">thoughts</h1>
		
		<div>

        <?php

		$p = get_posts( array(
			'numberposts' => 3,
			));
		foreach ( $p as $post ) : setup_postdata($post);

			get_template_part( 'listing', $post->post_type );

		endforeach;
		
		?>
		
		<div class="more clear">
			<a href="<?php bloginfo( 'url' ) ?>#more">&rarr;&nbsp;more</a>
		</div>

		</div>
		
	</section>



<?php get_footer();
