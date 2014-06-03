<?php get_header(); ?>

	<section>

		<?php if (have_posts()) : $first = true; while (have_posts()) : the_post();

			get_template_part( 'listing', $post->post_type );
      if ( $first ) : $first = false; ?>
      <div>
      <? endif;

		endwhile; endif; ?>
      </div>


		<div class="more clear">

			<?php global $page; ?>

			<?php /* if ( $page == 1 ) : ?><a href="<?php bloginfo( 'url' ) ?>/page/2">&rarr;&nbsp;more</a><?php endif; */ ?>

			<?php previous_posts_link( 'less&nbsp;&larr;' ) ?>

			<?php next_posts_link( '&rarr;&nbsp;more' ) ?>

		</div>

	</section>

<?php get_footer();
