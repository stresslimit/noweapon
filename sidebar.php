<?php /**/ ?><div id="sidebar">
	
<? //if(is_home()) : ?>
	
	

<? //else : ?>
	
	<h3><a href="/">NW</a></h3>
	
	
	<h2>new releases</h2>
	<?
	// dynamic_sidebar('sidebar');
	$args = array(
		'post_type' => 'wpsc-product',
	);
	$products = get_posts($args);

	foreach($products as $post) : setup_postdata($post);
	?>
		<p><a href="<? the_permalink() ?>"><? the_title() ?></a></p>
	<?
	endforeach;
	?>

<? //endif; ?>

</div>
