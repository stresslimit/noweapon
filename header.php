<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="utf-8">
	<title>no weapon<?php wp_title() ?></title>
	<link rel="icon" href="/favicon.png" type="image/x-icon">
	<link rel="shortcut icon" href="/favicon.png" type="image/x-icon">

	<?php
		wp_head();

		$t = array( 'thurst', 'thrust', 'thirst');
		shuffle($t);
		$t = array_pop($t);
	?>

	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<script src="<?php bloginfo('template_url') ?>/js/selectivizr.js"></script>
	<![endif]-->

</head>

<body <?php body_class() ?>>

<div class="container">

	<header>
		<p class="meta">		
			<a href="/catalog/cart" class="right">your cart</a>
			<?php echo ucfirst($t); ?> for a better future.
		</p>

		<a href="<?php echo home_url(); ?>">
			<img src="<?php echo get_bloginfo('template_url').'/images/logo'.( (is_front_page()) ? '_big' : '').'.png'; ?>" alt="<?php wp_title(''); ?>" title="No weapon records &amp; stuff" />
		</a>

		<ul class="menu">
			<?php wp_nav_menu( array( 'container'=>'', 'menu'=>'menu', 'items_wrap' => '%3$s' ) ) ?>
		</ul>

	</header>

	<div id="content">
