<?php


include_once('inc/stresspress.php');
// include_once('inc/types.php');
// include_once('inc/helpers.php');
include_once('inc/woocommerce.php');



/*
 Main init & fields
====================*/
add_action( 'init', 'sld_init' );
function sld_init() {

	set_post_thumbnail_size( 90, 90, true );
    add_image_size( 'medium', 220, 220, true );
	add_image_size( 'large', 660, 9999 );
	add_image_size( 'fullsize', 830, 9999 );

	if ( !is_admin() && !is_login_page() ) {

		// scripts
		wp_deregister_script( 'l10n' );

		// register types
		// $args = array( 'supports' => array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt' ) );
		// sld_register_post_type( 'record', $args );
		// sld_register_post_type( 'event' );

		if ( !is_admin() && !is_login_page() ) {
			wp_enqueue_style( 'style', get_bloginfo('stylesheet_url') );
			wp_enqueue_script( 'noweaponjs', get_bloginfo('template_url').'/js/noweapon.js', array('jquery'), '666' );
		}
	}
}

add_action( 'admin_init', 'nw_custom_fields' );
function nw_custom_fields() {
	if ( !function_exists( 'x_add_metadata_field' ) ) return;

	// posts
	x_add_metadata_group( 'x_custom', 'post', array( 'label' => 'extra fields' ) );
	x_add_metadata_field( 'nw_vimeo_code', 'post', array( 'group' => 'x_custom', 'label' => 'Vimeo code' ));
	x_add_metadata_field( 'nw_youtube_code', 'post', array( 'group' => 'x_custom', 'label' => 'Youtube code' ));

	// events
	// x_add_metadata_group( 'x_event_box', 'event', $args = array( 'label' => 'extra fields' ) );
	// x_add_metadata_field( 'nw_event_date', 'event', array( 'group' => 'x_event_box', 'label' => 'Event Date', 'display_column' => true ));

	// records
	// x_add_metadata_group( 'x_record_box', 'record', $args = array( 'label' => 'extra fields' ) );
	// x_add_metadata_field( 'nw_catalog_no', 'record', array( 'group' => 'x_record_box', 'label' => 'Catalog #', 'display_column' => true ));
	// x_add_metadata_field( 'paypal-button-code', 'record', array( 'group' => 'x_record_box', 'label' => 'PayPayl button code' ));

}



/*
 Random helpers
====================*/
add_filter( 'bloginfo_url', 'sld_blogurl' );
function sld_blogurl( $url ) {
	return get_option( 'show_on_front' ) == 'page' ? get_permalink( get_option('page_for_posts' ) ) : $url;
}

add_filter( 'the_content', 'disable_jump' );
function disable_jump($content) {
	$pattern = "/\#more-\d+\" class=\"more-link\"/";
	$replacement = "\" class=\"more-link\"";
	$content = preg_replace($pattern, $replacement, $content);
	return $content;
}
add_filter( 'excerpt_length', 'nw_excerpt_length', 999 );
function nw_excerpt_length( $length ) {
	return 20;
}



/*
 Image helpers
====================*/
function nw_get_thumbnail( $size = 'post-thumbnail', $only_video = false ) {
	global $post;
	$vimeo = get_post_meta( $post->ID, 'nw_vimeo_code', true );
	$youtube = get_post_meta( $post->ID, 'nw_youtube_code', true );
	$image_sizes = nw_image_sizes();
	$width = $image_sizes[$size]['width'];
	$height = round( $width * 9/16 );
	if ( !empty( $vimeo )  &&  $size != 'post-thumbnail'  &&  $size != 'medium' ) {
		return '<iframe src="http://player.vimeo.com/video/'. $vimeo .'" frameborder="0" width="'. $width .'" height="'. $height .'"></iframe>';
	} elseif ( !empty( $youtube )  &&  $size != 'post-thumbnail'  &&  $size != 'medium' ) {
		return '<iframe src="http://www.youtube.com/embed/'. $youtube .'" frameborder="0" width="'. $width .'" height="'. $height .'"></iframe>';
	} elseif ( empty($only_video) ) {
		return get_the_post_thumbnail( $post->ID, $size );
	}
}
function nw_thumbnail( $size = 'post-thumbnail', $only_video = false ) {
	echo nw_get_thumbnail( $size, $only_video );
}

function nw_image_sizes() {
	// $image_sizes = ; // Standard sizes
	foreach ( array('thumbnail', 'medium', 'large') as $size ) {
		$image_sizes[ $size ]['width']	= intval( get_option( "{$size}_size_w") );
		$image_sizes[ $size ]['height'] = intval( get_option( "{$size}_size_h") );
		// Crop false per default if not set
		$image_sizes[ $size ]['crop']	= get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
	}
	global $_wp_additional_image_sizes;
	if ( !empty( $_wp_additional_image_sizes ) )
		$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );

	return apply_filters( 'intermediate_image_sizes', $image_sizes );
}


// function nw_home( $query ) {
// 	if ( $query->is_home() /*&& $wp_the_query === $query*/ ) {
// 		$query->set( 'post_type', array( 'record', 'event' ) );
// 		$query->set( 'post_status', array( 'publish', 'future' ) );
// 	} else if ( $query->is_singular() ) {
// 		$query->set( 'post_status', array( 'publish', 'future' ) );
// 	}
// }
// add_action( 'pre_get_posts', 'nw_home' );



// function nw_cart_button() {
// 	return '<form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----"><input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_viewcart_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!"><img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"></form>';
// }

