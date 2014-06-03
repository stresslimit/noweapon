<?php
/**
 * @author Stresslimit [@stresslimit, @jkudish]
 * this file gives a bunch of core functionality extensions for 
 * use in setting up a new Stresslimit WordPress site. Feel free 
 * to comment/uncomment/add stuff as needed.
 */

/*-----------------------------------
   Enable/Disable WordPress stuff
-------------------------------------*/

// remove junk from head
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'parent_post_rel_link', 10, 0);

// we normally want these to be active
// remove_action('wp_head', 'feed_links', 2);
// remove_action('wp_head', 'index_rel_link');
// remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);

// add post thumbnail support + custom menu support
add_theme_support('post-thumbnails');
register_nav_menus();

// Limit post revisions: this should go in wp-config
// define('WP_POST_REVISIONS', 5);

// disable file editor
define('DISALLOW_FILE_EDIT',true);

// remove unwanted core dashboard widgets
add_action('wp_dashboard_setup', 'sld_rm_dashboard_widgets');
function sld_rm_dashboard_widgets() {
	global $wp_meta_boxes;
	// unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);         // right now [content, discussion, theme, etc]
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);              // plugins
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);       // incoming links
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);                // wordpress blog
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);              // other wordpress news
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);            // quickpress
	// unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);          // drafts
	// unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);      // comments
}

// who uses Links ? goodbye 2005...
add_action('admin_menu', 'sld_manage_menu_items', 99);
function sld_manage_menu_items() {
	// we can do this based on permissions too if we want
	if( !current_user_can( 'administrator' ) ) {
	}
	remove_menu_page('link-manager.php'); // Links
	// remove_menu_page('edit.php'); // Posts
	remove_menu_page('upload.php'); // Media
  remove_menu_page('edit-comments.php'); // Comments
	// remove_menu_page('edit.php?post_type=page'); // Pages
	// remove_menu_page('plugins.php'); // Plugins
	// remove_menu_page('themes.php'); // Appearance
	// remove_menu_page('users.php'); // Users
	// remove_menu_page('tools.php'); // Tools
	// remove_menu_page('options-general.php'); // Settings
}

// remove unwanted metaboxes
// these are managed through Screen Options, but in case you want to disable them 
// entirely, here they are. Disabled for now, so post edit screen is per default.
// add_action('admin_head', 'sld_rm_post_custom_fields');
function sld_rm_post_custom_fields() {
	// pages
	remove_meta_box( 'postcustom' , 'page' , 'normal' );
	remove_meta_box( 'commentstatusdiv' , 'page' , 'normal' );
	remove_meta_box( 'commentsdiv' , 'page' , 'normal' );
	remove_meta_box( 'authordiv' , 'page' , 'normal' );

	// posts
	remove_meta_box( 'postcustom' , 'post' , 'normal' );
	remove_meta_box( 'postexcerpt' , 'post' , 'normal' );
	remove_meta_box( 'trackbacksdiv' , 'post' , 'normal' );
}

// Do not show the Editorial Calendar for yourcustomtype
// if ( is_admin() ) {
// 	add_filter('edcal_show_calendar_yourcustomtype', '__return_false');
// }



/*-----------------------------------
	Misc
-------------------------------------*/

// make cleaner better permalink urls
function sld_url_cleaner_clean($slug) {
	// make sure to replace spaces with dashes
	$slug = str_replace( ' ', '-', $slug);

	// remove everything except letters, numbers and -
	$pattern = '~([^a-z0-9\-])~i';
	$replacement = '';
	$slug = preg_replace($pattern, $replacement, $slug);

	// when more than one - , replace it with one only
	$pattern = '~\-\-+~';
	$replacement = '-';
	$slug = preg_replace($pattern, $replacement, $slug);

	return $slug;
}
add_filter('editable_slug', 'sld_url_cleaner_clean');

// add conditional for login page
function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

// add post type class to body admin 
function sld_admin_body_class( $classes ) {
	global $wpdb, $post;
	$post_type = get_post_type( $post->ID );
	if ( is_admin() ) {
		$classes .= 'type-' . $post_type;
	}
	return $classes;
}
add_filter( 'admin_body_class', 'sld_admin_body_class' );


// get either the featured image or first image in the post
function sld_get_post_thumbnail( $postid, $size='thumbnail' ) {
	if ( has_post_thumbnail( $postid ) ) {
		return get_the_post_thumbnail( $postid, $size );
	} else {
		// echo 'has no thumbnail';
		$post = get_post( $postid );
		if ( preg_match_all('/<img.+class=[\'"].*wp-image-([\d]*).?[\'"].*>/i', $post->post_content, $matches) ) {
			// var_dump($matches);
			$img_id = @$matches[1][0];
			return wp_get_attachment_image( $img_id, $size );
		} else if ( preg_match_all('/<img.+src=[\'"]([^\'"])[\'"].*>/i', $post->post_content, $matches) ) {
			// get sizes dimensions from wp
			$img = @$matches[1][0];
			return '<img src="'.$img.'">';
		}

	}
}

function sld_post_thumbnail( $postid, $size='thumbnail' ) {
	echo sld_get_post_thumbnail( $postid, $size );
}

// Remove width & height from img element
// From CSS Tricks: http://css-tricks.com/snippets/wordpress/remove-width-and-height-attributes-from-inserted-images/
add_filter( 'post_thumbnail_html', 'remove_width_attribute', 10 );
add_filter( 'image_send_to_editor', 'remove_width_attribute', 10 );
function remove_width_attribute( $html ) {
   $html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
   return $html;
}

/*-----------------------------------
   Stresslimit admin branding
-------------------------------------*/

function sld_admin_styles() { ?>
  <style>
  /* nice client logo for wp login screen	*/
  #login h1 a { background:url('<?php echo get_bloginfo( 'template_url' ) ?>/images/noweapon-logo-admin.png') 50% top no-repeat; width:auto; height:180px; background-size:auto; }
  .login form { position:relative; }
  </style>
  <?php
}
add_action('login_head', 'sld_admin_styles');
add_action('admin_head', 'sld_admin_styles');

// login logo link url
function login_header_url() {
	return home_url();
}
add_filter( 'login_headerurl', 'login_header_url' );


