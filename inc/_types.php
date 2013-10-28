<?php

if ( function_exists( 'sld_register_post_type' ) ) {

	$args = array(
		'labels' => array(
			'name' => __('Records'),
			'singular_name' => __('Record')
		),
		'publicly_queryable' => null,
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 5,
		// 'menu_icon'=>get_bloginfo('template_url').'/images/icons/test.gif',
		'public' => true,
		'rewrite' => array(
			'with_front'=>false
		),
		'query_var' => true,
		'supports' => array(
			'title',
			'editor',
			'comments',
			'revisions',
			'trackbacks',
			'author',
			'excerpt',
			'page-attributes',
			'thumbnail',
			'custom-fields'
			),
		'register_meta_box_cb' => null,
		'taxonomies' => array(),
		'show_ui' => true
		);
		sld_register_post_type( 'Records', $args, 'records' );



	$args = array(
		'labels' => array(
			'name' => __('Events'),
			'singular_name' => __('Event')
		),
		'publicly_queryable' => null,
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 5,
		// 'menu_icon'=>get_bloginfo('template_url').'/images/icons/test.gif',
		'public' => true,
		'rewrite' => array(
			'with_front'=>false
		),
		'query_var' => true,
		'supports' => array(
			'title',
			'editor',
			'comments',
			'revisions',
			'trackbacks',
			'author',
			'excerpt',
			'page-attributes',
			'thumbnail',
			'custom-fields'
		),
		'register_meta_box_cb' => null,
		'taxonomies' => array(),
		'show_ui' => true
	);
	sld_register_post_type( 'Events', $args, 'events' );

}
