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
	// remove_menu_page('edit-comments.php'); // Comments
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

// grab all post meta into $post object [could be resource intensive]
function setup_postmeta_all( $post=false ) {
	global $wpdb;
	// make sure we have a proper $post object, so we can use in templates without args, 
	// or in special cases where we want to pass the object manually
	if ( !$post ) global $post;
	$sql = "
		SELECT `meta_key`, `meta_value`
		FROM `$wpdb->postmeta`
		WHERE `post_id` = $post->ID
	";
	$wpdb->query($sql);
	foreach($wpdb->last_result as $k => $v) {
		if ( isset ( $post->{$v->meta_key} ) ) {
			if ( !is_array($post->{$v->meta_key}) ) {
				$post->{$v->meta_key} = array( $post->{$v->meta_key} );
			}
			$post->{$v->meta_key}[] = $v->meta_value;
		} else
		$post->{$v->meta_key} = $v->meta_value;
	};
	return $post;
}

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


/*-----------------------------------
   Stresslimit admin branding
-------------------------------------*/

function sld_admin_styles() { ?>
		<style>
		/* nice client logo for wp login screen	*/
		#login h1 a { background:url('<?php echo get_bloginfo( 'template_url' ) ?>/images/noweapon-logo-admin.png') 50% top no-repeat; height:180px; background-size:auto; }
		.login form { position:relative; }
		/* nice stresslimit kut-korners	*/
		.login form:after { content:"."; text-indent:-999em; display:block; position:absolute; right:-8px; bottom:-10px; width:26px; height:74px; 
	/*	background:url('<?php bloginfo('template_url') ?>/images/stresscorner-login.png') right bottom no-repeat;*/
		background:url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAABKCAIAAAAqptNuAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAj5JREFUeNqsmNuOwjAMREla4P9/EyHxgBDXwg54GZkmaRInfkCrhR7Zk7FjcK/Xa1Ufu93Oe7/dbvE6DANenXP4vzewkMHj8RjHEX+7T/AtC+56vb6f9F5w+i0L7nK5oECymCBejThU6r7RlN3z+ZymqRsOqenqZu9W487ns5wpVWs62dvtJpXKyTIsvoNq0A7H2qdYVEoWO6EJx2bQNRpx9/udFgmFq8OBhW51KsLPeJtwHXDSW6FwFt+hzJRwluzgXpoj9HA1TlukQ3YYmev1OpTM4js0Fqa5HOtCaqU4DqUFx9XhpNI+2UE4PeNSwhXhoBq0o0WWP+xLKuUhdCg2dFyq0iKc9FZ0ulXjdG+lGqsCh0r18tCqXcpxKajPbkrct7LCZXBwLyUrYWVw+sIvES6fXXS1sYyA6RO6H5qKXd6ULDg9fltPlsJFN6U6HOwG09FurcVyKBX6I4PLbkp1uOymVIEr2ZQqcFJpTxwdl9qUSnEcSrV5xXEylBglA30JV7gpleK4YmYv/DxOfwsxpDbHzYRrLZYWMUy6eHbRC98yApCaXqZXpvA6NT3gWo8C6wiE0yyDfJ4Wkd4yG/gHdzqdZHkwnGYEp1NrSdBzZEYvmlqoQ16HwwFzabPZDJ+QQWK08X6/F7uhW2erjaXJ5Ac3PCn3Q+tRIIRouwkjuFAymfLGk+UQZ+HmYlHi6L/RWOn7wePxKNnNZomN6NBevLQ6ZIe7hj9kNgr3fly+JrWD/iHo1lW/6Iz7E2AAoVgTmcr1bUIAAAAASUVORK5CYII=') right bottom no-repeat;
		 }
		/* stresslimit logo on dashboard */
		#icon-index { margin:8px 8px 0 8px; background:transparent url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAeCAYAAABNChwpAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABYpJREFUeNrEV21MW2UUfu7t7XdLYWVAo/KhOJCNbcwPICIL6gSXEedijPtlov4yMSYuRo0mJsaPxLjEGT8WE0n8scVkmuEmEiWwycRkYnBLjIL7sTFYgTKgpe1tSz+u57wts3y0BWLmm7ztve173/Oc5zznnPdKvW4v0sZeTdM+TiQSmpixGOLxOOgG9HtqiXbjevn38uulg58Tn0hAQlSTpHBCiyrLVh2gWYGbN1Q57cZF8yHc3LGEgQdolmZdzhxKgCzLkHU6SJIs7jlEFDLEKWR8TX+sGUE6gFaacjbjBrMZer0eETUEv9eLUCiIBGlEp+hhsdlhy8sT4MKhEIGJrgnIIoBimi0ZV9FGVtp8cnwMg30/4u8Lv2PWM4VwWGVVCaNmqxUlpWXY2bQb2xubBNgIAZFygFDEDpAaMtJPG1isNpzv6caJo5/ANzsNvcEAHTEhUwh4/xhtEQ6p8LjduPjLAKrq7sbBFw/BWVQMNRDICiJFufYIfehWW2CxWHFhoB8d771FHgXhcBYKug0Go/CcRcB6MJjMsDkcYg4PDeKzN17F9Qk3jCYTkk5mBrApk/plWSfi3H38S0hkxGSxpASnIayqQpMKMcG1IkSesh5YjHqjEVcvDWOo/4xgKpcGmP7K1USn0yuYGLsMz/hVirFFbM6FJkYCa9l/ALuaW4RzqhrA0E9nMfD9aWG8etc9aG5/HBXVNQgFg4KlTCwwgEcz0c8Uh/wBMhgXceeRSMRFTJv2taO6rg6qT4VE63Y0NqP0jkoK0WbU1DeQPiSobFzTcjKwO3PmaTCarRRjma6pgEo66HQKYtEoOt59G3fW7kCBswj5mwvhdLkI1GMwkWADPi+CZFyS5dxpSN44M9VvLixOVwnySXgzU1MwmRUBitlwX7mM0ZFhwYZCoWKxOQqLUFm7HfUPt6KssooywA8tBwCGGMuUfgzA7ihA/YOtCHjnkj/LcpIZUr09P1/UB84A0iWp/hr6T53EkVdewsAPXbA68nIykJOjSDiEPU8eRGPbPio+HlK7X1Q5jbSQZE4SoFgvekpNe36ByJRjh9/HCBUsriGapm0cAMebx7OvvYmnX34dZVU1MBjNJMwYKTxANM8nQcWT4uRMMXO6kvD7Tn4t7mVZWlMvyBAJCQuRiPCwuX0/7m/bC8+1McxMT2HGPYlp9zimqESPjvwl0pOLUpyMmihtJ0evYJ5CxynM4dwQAKRoNlB+sx9cgG4pr0DxrbdBq0t6xtSf+64Txz86LIoVBQU6KmIR6hXcD6xUOeMZpCZnt61BIcU7NjnhnZ1B5xdH8c7zz+DXM70i3ZhaLvPsYZ7T+W/OS1qyw3CvkKWspVjJ5rlCbXb2ugffdnyOwbO9mPNMUutVcOzIB7j0x0XcftdW4f0crRno7oLCLFGoJKGdBRSWuKhFOygk8Y1pQCZj3M16vvmKnRKNSKN8i8djOHeqEz93nRZFKrawIFKRwyQUTyBUvx9b762HzZ4Hv98Had1ZIMQXRvmWKjxHGcAbBH0+QaeOhGalrmeiMwAbtpKXbJyfYQBzdFYoq6qmfvGEELC0xhPRSgw0ucs17GmjkmzBiU8/pOY0Sqcio6iGgu6UUU7DKAHm6G+7rwFPvXAIFrudng9m1UHOLODH+Pi1k045ZVuqSYA9+PO385idmKBWrSaFmjqSucrLUNvQhG31jclWfsN4Fif7JryjtEkpUkTxhonUITP9vYDvmWaD0YRIJCQaTpgaDue8ngCYbDZRlul4QqkaFDpZehJa8V4Aei/w5a4DaQUpSmKLLkTEtZXExZOBc6dkgBGqEfy9uH69p+I1j0WGVnszWu+Q8T8PeaMs/EdD4RMGnxq8S2Qv1JI2oaWl0fJrLEsxbeVPmdfO/yPAAMsHmr95i8U2AAAAAElFTkSuQmCC') 0 0 no-repeat; }
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

// admin footer branding
function sld_admin_footer_brand() {
  	return date("Y") .' <a href="http://stresslimitdesign.com">stresslimitdesign</a> <a href="http://creativecommons.org/licenses/by-nc-sa/3.0/">cc</a>';
} 
add_filter('admin_footer_text', 'sld_admin_footer_brand');



