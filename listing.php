<?php global $i; $i++ ?>
<?php global $more; $more = false; ?>

		<article <?php post_class( 'listing result-'.$i ) ?><?php if ( $i==4 ) echo ' id="more"'; ?>>

			<div class="images">
				<a href="<?php the_permalink() ?>"><?php nw_thumbnail( ( $i==1 ? 'large' : 'medium' ) ) ?></a>
			</div>

			<h2><a href="<?php the_permalink() ?>"><?php the_title() ?></a> /</h2>
			<?php if ( $i==1 ) : 
				add_filter( 'the_content', 'remove_images' );
				function remove_images( $c ) {
					return preg_replace( '!<img[^>]*>!', '', $c );
				}
				the_content( '&rarr;&nbsp;more', false, '' );
			else : ?>
			<p><?php echo get_the_excerpt() ?> <a href="<?php the_permalink() ?>">&rarr;&nbsp;more</a></p>
			<?php endif; ?>

		</article>

