<?php get_header(); ?>

	<section class="">

		<?php
		$args = array(
			'post_type' => array( 'product' /*, 'event'*/ ),
			// 'post_status' => array( 'publish', 'future' ),
			'post_parent' => 0,
			'numberposts' => 10,
			);

		$q = get_posts( $args );
		foreach ( $q as $post ) : setup_postdata($post);

			get_template_part( 'listing', $post->post_type );

		endforeach;
		?>

	</section>

<?php get_footer();
