<?php
/*
Plugin Name: File Gallery
Plugin URI: http://skyphe.org/code/wordpress/file-gallery/
Version: 1.6.2
Description: "File Gallery" extends WordPress' media (attachments) capabilities by adding a new gallery shortcode handler with templating support, a new interface for attachment handling when editing posts, and much more.
Author: Bruno "Aesqe" Babic
Author URI: http://skyphe.org

////////////////////////////////////////////////////////////////////////////

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
	
////////////////////////////////////////////////////////////////////////////

*/


/*
 * Translations
 */
load_plugin_textdomain('file-gallery', false, dirname(plugin_basename(__FILE__)) . "/languages");



/**
 * Setup the WordPress constants
 */
if ( !defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content' );
if ( !defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( !defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( !defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );



/**
 * Setup default File Gallery options
 */
$file_gallery_abspath     = WP_PLUGIN_DIR . "/" . basename(dirname(__FILE__));
$file_gallery_abspath     = str_replace("\\", "/", $file_gallery_abspath);
$file_gallery_abspath     = preg_replace("#/+#", "/", $file_gallery_abspath);
$file_gallery_crystal_url = get_bloginfo('wpurl') . "/" . WPINC . "/images/crystal";

define("FILE_GALLERY_URL", WP_PLUGIN_URL . "/" . basename(dirname(__FILE__)));
define("FILE_GALLERY_ABSPATH", $file_gallery_abspath);
define("FILE_GALLERY_DEFAULT_TEMPLATES", serialize( array("default", "file-gallery", "list") ) );
define("FILE_GALLERY_CRYSTAL_URL", apply_filters("file_gallery_crystal_url", $file_gallery_crystal_url));



/**
 * Support for other plugins
 *
 * Supported so far:
 * - WordPress Mobile Edition
 * - Media Tags
 */
function file_gallery_plugins_support()
{
	global $sitepress, $wp_taxonomies;
	
	$mobile = false;
	$fg_ss_dir = get_stylesheet_directory();
	$file_gallery_media_tag_name = "media_tag";
	$options = get_option("file_gallery");
	
	// WordPress Mobile Edition
	if( function_exists("cfmobi_check_mobile") && cfmobi_check_mobile() )
	{
		$mobile = true;
	
		if( "" != $options && isset($options['disable_shortcode_handler']) && true != $options['disable_shortcode_handler'] )
			add_filter('stylesheet_uri', 'file_gallery_mobile_css');
	}
	
	// Media Tags
	if( defined('MEDIA_TAGS_TAXONOMY') )
		$file_gallery_media_tag_name = MEDIA_TAGS_TAXONOMY;

	// theme dirs
	$file_gallery_theme_abspath = str_replace("\\", "/", $fg_ss_dir);
	$file_gallery_theme_abspath = preg_replace("#/+#", "/", $file_gallery_theme_abspath);
	$file_gallery_theme_templates_abspath = apply_filters("file_gallery_templates_folder_abspath", $file_gallery_theme_abspath . "/file-gallery-templates");
	$file_gallery_theme_templates_url	  = apply_filters("file_gallery_templates_folder_url", get_bloginfo("stylesheet_directory") . "/file-gallery-templates");
	
	define("FILE_GALLERY_MOBILE", $mobile);
	define("FILE_GALLERY_MEDIA_TAG_NAME", $file_gallery_media_tag_name);
	define("FILE_GALLERY_THEME_ABSPATH", $file_gallery_theme_abspath);
	define("FILE_GALLERY_THEME_TEMPLATES_ABSPATH", $file_gallery_theme_templates_abspath);
	define("FILE_GALLERY_THEME_TEMPLATES_URL", $file_gallery_theme_templates_url);
}
add_action("plugins_loaded", "file_gallery_plugins_support", 100);



/**
 * Registers default File Gallery options when plugin is activated
 */
function file_gallery_activate()
{
	file_gallery_plugins_support();
	
	$defaults = array(
		'folder' 					  => FILE_GALLERY_URL, 
		'abspath' 					  => FILE_GALLERY_ABSPATH, 
		'media_tag_name'			  => FILE_GALLERY_MEDIA_TAG_NAME,
		
		'in_excerpt' 				  => true, 
		'in_excerpt_replace_content'  => "<p><strong>(" . __("galleries are shown on full posts only", "file-gallery") . ")</strong></p>", 
		
		'default_image_size' 		  => 'thumbnail', 
		'default_linkto' 			  => 'attachment', 
		'default_external_url'		  => '', 
		'default_orderby' 			  => '', 
		'default_order' 			  => 'ASC', 
		'default_template' 			  => 'default', 
		'default_linkclass' 		  => '', 
		'default_imageclass' 		  => '', 
		'default_columns' 			  => 3, 
		'default_mimetype'			  => '',
		
		'single_default_image_size'   => 'thumbnail', 
		'single_default_linkto'		  => 'attachment', 
		'single_default_external_url' => '', 
		'single_default_linkclass' 	  => '', 
		'single_default_imageclass'   => '',
		'single_default_align'        => 'none',
		
		'insert_options_states'		  => '1,1',
		'display_insert_fieldsets'	  => true,
		
		'e_display_attachment_count'  => true,
		'e_display_media_tags'		  => true,
		'e_display_post_thumb'		  => true,
		
		'cache'						  => false,
		'cache_time'				  => 3600, // == 1 hour
		'cache_non_html_output'		  => false,
		
		'del_options_on_deactivate'   => false,

		'show_on_post_type_post'	  => true,
		'show_on_post_type_page'	  => true,
		
		'library_filter_duplicates'   => true,
		
		'auto_enqueued_scripts'		  => 'thickbox',
		
		'disable_shortcode_handler'	  => false
	);
	
	if( $options = get_option("file_gallery") )
		$defaults = shortcode_atts($defaults, $options);
	
	update_option("file_gallery", $defaults);
	
	file_gallery_clear_cache();
}
register_activation_hook( __FILE__, 'file_gallery_activate' );



/**
 * Some cleanup on deactivation
 */
function file_gallery_deactivate()
{
	file_gallery_clear_cache();
	
	$options = get_option("file_gallery");
	
	if( isset($options["del_options_on_deactivate"]) && true == $options["del_options_on_deactivate"] )
		delete_option("file_gallery");
}
register_deactivation_hook( __FILE__, 'file_gallery_deactivate' );



/**
 * Adds a link to plugin's settings page (shows up next to the 
 * deactivation link on the plugins management page)
 */
function file_gallery_add_settings_link( $links )
{ 
	array_unshift( $links, '<a href="options-media.php">' . __("Settings", "file-gallery") . '</a>' ); 
	
	return $links; 
}
add_filter("plugin_action_links_" . plugin_basename(__FILE__), 'file_gallery_add_settings_link' );



/**
 * Adds media_tags taxonomy so we can tag attachments
 */
function file_gallery_add_taxonomies()
{
	$args = array(
		"public"                => true,
		"query_var"             => str_replace("_", "-", FILE_GALLERY_MEDIA_TAG_NAME),
		"update_count_callback" => "file_gallery_update_media_tag_term_count",
		"label"                 => __("Media tags", "file-gallery"),
		"singular_label"        => __("Media tag", "file-gallery"),
		"rewrite"               => array(
									"slug" => str_replace("_", "-", FILE_GALLERY_MEDIA_TAG_NAME)
		),
		"labels"                => array(
									"singular_label" => __("Media tag", "file-gallery")
		)
	);
	
	register_taxonomy( FILE_GALLERY_MEDIA_TAG_NAME, "attachment", $args );
}
add_action("init", "file_gallery_add_taxonomies");



/**
 * A slightly modified copy of WordPress' _update_post_term_count function
 * 
 * Updates number of posts that use a certain media_tag
 */
function file_gallery_update_media_tag_term_count( $terms )
{
	global $wpdb;

	foreach ( (array) $terms as $term )
	{
		$count = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts 
						 WHERE $wpdb->posts.ID = $wpdb->term_relationships.object_id 
						 AND post_type = 'attachment' 
						 AND term_taxonomy_id = %d",
					$term )
		);
		
		do_action( 'edit_term_taxonomy', $term );
		
		$wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term ) );
		
		do_action( 'edited_term_taxonomy', $term );
	}
	
	// clear cache
	file_gallery_clear_cache("mediatags_all");
}



/**
 * Adds media tags submenu
 */
function file_gallery_media_submenu()
{
    add_submenu_page('upload.php', __("Media tags", "file-gallery"), __("Media tags", "file-gallery"), 8, 'edit-tags.php?taxonomy=' . FILE_GALLERY_MEDIA_TAG_NAME);
}
add_action('admin_menu', 'file_gallery_media_submenu');



/**
 * Gets intermediate image sizes
 */
function file_gallery_get_intermediate_image_sizes()
{
	$sizes = array();

	if( function_exists("get_intermediate_image_sizes") )
		$sizes = get_intermediate_image_sizes();

	$additional_intermediate_sizes = apply_filters("intermediate_image_sizes", $sizes);
	
	array_unshift($additional_intermediate_sizes, "thumbnail", "medium", "large", "full");
	
	return array_unique($additional_intermediate_sizes);
}



/**
 * Media library extensions
 */
function file_gallery_add_library_query_vars( $input )
{
	global $wpdb, $pagenow;

	$options = get_option("file_gallery");

	// affect the query only if we're on a certain page
	if( "media-upload.php" == $pagenow && "library" == $_GET["tab"] && is_numeric($_GET['post_id']) )
	{
		if( "current" == $_GET['exclude'] )
			$input .= " AND `post_parent` != " . intval($_GET["post_id"]) . " ";

		if( true == $options["library_filter_duplicates"] )
			$input .= " AND $wpdb->posts.ID NOT IN ( SELECT ID FROM $wpdb->posts AS ps INNER JOIN $wpdb->postmeta AS pm ON pm.post_id = ps.ID WHERE pm.meta_key = '_is_copy_of' ) ";
	}
	elseif( "upload.php" == $pagenow && true == $options["library_filter_duplicates"] )
	{
		$input .= " AND $wpdb->posts.ID NOT IN ( SELECT ID FROM $wpdb->posts AS ps INNER JOIN $wpdb->postmeta AS pm ON pm.post_id = ps.ID WHERE pm.meta_key = '_is_copy_of' ) ";
	}

	return $input;
}
add_filter('posts_where', 'file_gallery_add_library_query_vars');



/**
 * Media library extensions
 */
function file_gallery_js_attach_to_post()
{
	global $pagenow, $current_screen, $wp_version;
	
	if( "media-upload.php" == $pagenow && "library" == $_GET["tab"] )
	{
		echo "<script type=\"text/javascript\">
				fgL10n = new Array();
				fgL10n['attach_all_checked_copy']	= '" . __("Attach all checked items to current post", "file-gallery") . "';
				fgL10n['exclude_current']			= '" . __("Exclude current post\'s attachments", "file-gallery") . "';
				fgL10n['include_current']			= '" . __("Include current post\'s attachments", "file-gallery") . "';
				
				var file_gallery_attach_nonce = '" . wp_create_nonce( 'file-gallery-attach' ) . "';
			  </script>
			  <style type='text/css'>
			  	#library-form .media-item.child-of-" . $_GET["post_id"] . "
				{
					background-color: #FFE;
				}
			  </style>\n";
	}
	elseif( "edit-tags.php" == $pagenow && FILE_GALLERY_MEDIA_TAG_NAME == $_GET["taxonomy"] && 3 > floatval($wp_version) )
	{
		echo "<script type=\"text/javascript\">
				jQuery(document).ready(function()
				{
					jQuery('h2').html('" . __("Media tags", "file-gallery") . "');
				});
			  </script>\n";
	}
	elseif( "options-media.php" == $pagenow )
	{
		echo "<script type=\"text/javascript\">	var file_gallery_clear_cache_nonce = '" . wp_create_nonce( 'file-gallery-clear_cache' ) . "'; </script>\n";
	}
}
add_action("admin_head", "file_gallery_js_attach_to_post");



/**
 * Adds js to admin area
 */
function file_gallery_js_admin()
{
	global $pagenow, $current_screen, $wp_version, $post_ID;
	
	$nonce = wp_create_nonce('file-gallery');
	$clear_cache_nonce = wp_create_nonce('file-gallery-clear_cache');
	
	if(
	   "post.php" == $pagenow || "post-new.php" == $pagenow || 
	   "page.php" == $pagenow || "page-new.php" == $pagenow || 
	   "post" == $current_screen->base && isset($current_screen->post_type)
	  )
	{
		echo   '<script type="text/javascript">
					//internationalization
					var fgL10n							= new Array();
					fgL10n["switch_to_tags"] 			= "' . __("Switch to tags", "file-gallery") . '";
					fgL10n["switch_to_files"] 			= "' . __("Switch to list of attachments", "file-gallery") . '";
					fgL10n["fg_info"] 					= "' . __("Insert checked attachments into post as", "file-gallery") . ':";
					fgL10n["no_attachments_upload"] 	= "' . __("No files are currently attached to this post.", "file-gallery") . '";
					fgL10n["sure_to_delete"] 			= "' . __("Are you sure that you want to delete these attachments? Press [OK] to delete or [Cancel] to abort.", "file-gallery") . '";
					fgL10n["saving_attachment_data"] 	= "' . __("saving attachment data...", "file-gallery") . '";
					fgL10n["loading_attachment_data"]	= "' . __("loading attachment data...", "file-gallery") . '";
					fgL10n["deleting_attachment"] 		= "' . __("deleting attachment...", "file-gallery") . '";
					fgL10n["deleting_attachments"] 		= "' . __("deleting attachments...", "file-gallery") . '";
					fgL10n["loading"] 					= "' . __("loading...", "file-gallery") . '";
					fgL10n["detaching_attachment"]		= "' . __("detaching attachment", "file-gallery") . '";
					fgL10n["detaching_attachments"]		= "' . __("detaching attachments", "file-gallery") . '";
					fgL10n["sure_to_detach"]			= "' . __("Are you sure that you want to detach these attachments? Press [OK] to detach or [Cancel] to abort.", "file-gallery") . '";
					fgL10n["close"]						= "' . __("close", "file-gallery") . '";
					fgL10n["loading_attachments"]		= "' . __("loading attachments", "file-gallery") . '";
					fgL10n["post_thumb_set"]			= "' . __("Featured image set successfully", "file-gallery") . '";
					fgL10n["post_thumb_unset"]			= "' . __("Featured image removed", "file-gallery") . '";
					fgL10n["copy_all_from_original"]	= "' . __("Copy all attachments from the original post?", "file-gallery") . '";
					
					fgL10n["set_as_featured"]			= "' . __("Set as featured image", "file-gallery") . '";
					fgL10n["unset_as_featured"]			= "' . __("Unset as featured image", "file-gallery") . '";
				
					var file_gallery_url 	 = "' . FILE_GALLERY_URL . '",
						file_gallery_nonce   = "' . $nonce . '",
						file_gallery_clear_cache_nonce = "' . $clear_cache_nonce . '",
						file_gallery_mode    = "list",
						current_item_dragged = "",
						thumb_w 			 = '  . get_option("thumbnail_size_w") . ',
						thumb_h 			 = '  . get_option("thumbnail_size_h") . ',
						num_attachments 	 = 1,
						wp_version 			 = '  . (float)$wp_version . ',
						tags_from 			 = true,
						list_attachments 	 = "",
						post_thumb_nonce     = "' . wp_create_nonce( "set_post_thumbnail-" . $post_ID ) . '";
				</script>';

		wp_enqueue_script( "file-gallery-main",  FILE_GALLERY_URL . "/js/file-gallery.js", array("jquery", "jquery-ui-core", "jquery-ui-draggable", "jquery-ui-sortable", "jquery-ui-dialog"), false, true );
		
		// clear cache js
		wp_enqueue_script( "file-gallery-clear_cache",  FILE_GALLERY_URL . "/js/file-gallery-clear_cache.js" );
	}
	elseif( "media-upload.php" == $pagenow && "library" == $_GET["tab"] )
	{
		// media library extensions
		wp_enqueue_script( "file-gallery-attach",  FILE_GALLERY_URL . "/js/file-gallery-attach.js" );
	}
	elseif( "media.php" == $pagenow)
	{
		//
	}
	elseif( "options-media.php" == $pagenow )
	{
		// clear cache js
		wp_enqueue_script( "file-gallery-clear_cache",  FILE_GALLERY_URL . "/js/file-gallery-clear_cache.js" );
	}
}
add_action('admin_print_scripts', 'file_gallery_js_admin');



/**
 * Adds css to admin area
 */
function file_gallery_css_admin()
{
	global $pagenow, $current_screen;
	
	if(
	   "post.php" 			== $pagenow || 
	   "post-new.php" 		== $pagenow || 
	   "page.php" 			== $pagenow || 
	   "page-new.php" 		== $pagenow ||
	   "media.php" 			== $pagenow || 
	   "media-upload.php" 	== $pagenow || 
	   "edit.php"			== $pagenow || 
	   ( isset($current_screen->post_type) && "post" == $current_screen->base )
	  )
	{
		wp_enqueue_style( "file_gallery_admin", apply_filters("file_gallery_admin_css_location", FILE_GALLERY_URL . "/css/file-gallery.css") );
		
		if( "rtl" == get_bloginfo("text_direction") )
			wp_enqueue_style( "file_gallery_admin_rtl", apply_filters("file_gallery_admin_rtl_css_location", FILE_GALLERY_URL . "/css/file-gallery-rtl.css") );
	}
}
add_action('admin_print_styles', 'file_gallery_css_admin');



/**
 * Gets image dimensions, width by default
 */
function file_gallery_get_image_size($link, $height = false)
{
	$link = trim($link);
	
	if( "" != $link )
	{
		$server_name = preg_match("#http://([^/]+)[/]?(.*)#", get_bloginfo('url'), $matches);
		$server_name = "http://" . $matches[1];
		
		if( false === strpos($link, $server_name) )
		{
			$size = getimagesize($link);
			
			if( $height )
				return $size[1];

			return $size[0];
		}		
	}
	
	return "";
}



/**
 * Edit post/page meta box content
 */
function file_gallery_content()
{	
	echo '<div id="fg_container">&nbsp;<noscript>' . __("File Gallery requires Javascript to function. Please enable it in your browser.", "file-gallery") . '</noscript></div>
			
			<div id="image_dialog"></div>
			
			<div id="delete_dialog" title="' . __("Delete attachment dialog", "file-gallery") . '">
				<p><strong>' . __("Warning: one of the attachments you've chosen to delete has copies.", "file-gallery") . '</strong></p>
				<p>' . __("How do you wish to proceed?", "file-gallery") . '</p>
				<p><a href="' . FILE_GALLERY_URL . '/help/index.html#deleting_originals" target="_blank">' . __("Click here if you have no idea what this dialog means", "file-gallery") . '</a> (opens File Gallery help in new browser window)</p>
			</div>
			
			<div id="file_gallery_copy_all_dialog" title="' . __("Copy all attachments from another post", "file-gallery") . '">
				<form action="" id="file_gallery_copy_all_form">
					<div id="file_gallery_copy_all_wrap">
						<label for="file_gallery_copy_all_from">' . __("Post ID:", "file-gallery") . '</label>
						<input type="text" id="file_gallery_copy_all_from" value="" />
					</div>
				</form>
			</div>
		';
}



/**
 * Creates meta boxes on post editing screen
 */
function file_gallery()
{
	$options = get_option("file_gallery");
	
	if( function_exists("get_post_types") )
	{
		$types = get_post_types();
		
		foreach( $types as $type )
		{
			if( ! in_array( $type, array("nav_menu_item", "revision", "attachment") ) && 
				isset($options["show_on_post_type_" . $type]) && true == $options["show_on_post_type_" . $type]
			)
				add_meta_box( 'file_gallery', __( 'File gallery', 'file-gallery' ), 'file_gallery_content', $type );
		}
	}
	else // pre 2.9
	{
		add_meta_box( 'file_gallery', __( 'File gallery', 'file-gallery' ), 'file_gallery_content', 'post' );
		add_meta_box( 'file_gallery', __( 'File gallery', 'file-gallery' ), 'file_gallery_content', 'page' );
	}
}
add_action('admin_menu', 'file_gallery');



/**
 * Outputs attachment count in the proper column
 */
function file_gallery_posts_custom_column($column_name, $post_id)
{
	global $wpdb;
	
	$options = get_option("file_gallery");

	if( "attachment_count" == $column_name && isset($options["e_display_attachment_count"]) && true == $options["e_display_attachment_count"] )
	{
		$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT() FROM $wpdb->posts WHERE post_type='attachment' AND post_parent=%d", $post_id) );
		
		echo apply_filters('file_gallery_post_attachment_count', $count, $post_id);
	}
	elseif( "post_thumb" == $column_name && isset($options["e_display_post_thumb"]) && true == $options["e_display_post_thumb"] )
	{
		if( $thumb_id = get_post_meta( $post_id, '_thumbnail_id', true ) )
		{
			$thumb_src = wp_get_attachment_image_src( $thumb_id, "thumbnail", false, $attr );
			$content   = '<img src="' . $thumb_src[0] .'" alt="Post thumb" />';
			
			echo apply_filters('file_gallery_post_thumb_content', $content, $post_id, $thumb_id);
		}
		else
		{
			echo apply_filters('file_gallery_no_post_thumb_content', '-', $post_id);
		}
	}
}
add_action( 'manage_posts_custom_column', 'file_gallery_posts_custom_column', 100, 2 );
add_action( 'manage_pages_custom_column', 'file_gallery_posts_custom_column', 100, 2 );



/**
 * Adds attachment count column to the post and page edit screens
 */
function file_gallery_posts_columns( $columns )
{
	$options = get_option("file_gallery");
	
	if( isset($options["e_display_attachment_count"]) && true == $options["e_display_attachment_count"] )
		$columns['attachment_count'] = __('No. of attachments', 'file-gallery');
		
	if( isset($options["e_display_post_thumb"]) && true == $options["e_display_post_thumb"] )
		$columns = array('post_thumb' => __('Post thumb', 'file-gallery')) + $columns; // $columns['post_thumb'] = 'Post thumb';
	
	return $columns;
}
add_filter( 'manage_posts_columns', 'file_gallery_posts_columns' );
add_filter( 'manage_pages_columns', 'file_gallery_posts_columns' );



/**
 * Outputs attachment media tags in the proper column
 */
function file_gallery_media_custom_column($column_name, $post_id)
{
	global $wpdb;
	
	$options = get_option("file_gallery");
	
	if( "media_tags" == $column_name && isset($options["e_display_media_tags"]) && true == $options["e_display_media_tags"])
	{
		if( isset($options["cache"]) && true == $options["cache"] )
		{
			$transient = "fileglry_mt_" . md5($post_id);
			$cache     = get_transient($transient);
			
			if( $cache )
			{
				echo $cache;
				
				return;
			}
		}
		
		$l = "?taxonomy=" . FILE_GALLERY_MEDIA_TAG_NAME . "&amp;term=";
		$out = "No Media Tags";
		
		$q = "SELECT `name`, `slug` 
			  FROM $wpdb->terms
			  LEFT JOIN $wpdb->term_taxonomy ON ( $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id ) 
			  LEFT JOIN $wpdb->term_relationships ON ( $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id ) 
			  WHERE `taxonomy` = '" . FILE_GALLERY_MEDIA_TAG_NAME . "'
			  AND `object_id` = %d
			  ORDER BY `name` ASC";

		$r = $wpdb->get_results( $wpdb->prepare($q, $post_id) );
		
		if( $r )
		{
			$out = array();
			
			foreach( $r as $tag )
			{
				$out[] = '<a href="' . $l . $tag->slug . '">' . $tag->name . '</a>';
			}
			
			$out = implode(", ", $out);
		}
		
		if( isset($options["cache"]) && true == $options["cache"] )
			set_transient($transient, $out, $options["cache_time"]);
		
		echo $out;
	}
}
add_action( 'manage_media_custom_column', 'file_gallery_media_custom_column', 100, 2 );



/**
 * Adds media tags column to attachments
 */
function file_gallery_media_columns( $columns )
{
	$columns['media_tags'] = __('Media tags', "file-gallery");
	
	return $columns;
}
add_filter( 'manage_media_columns', 'file_gallery_media_columns' );



/**
 * Includes
 */
include_once("includes/attachments.php");
include_once("includes/attachment-custom-fields.php");
include_once("includes/media-settings.php");
include_once("includes/miscellaneous.php");
include_once("includes/mime-types.php");
include_once("includes/lightboxes-support.php");
include_once("includes/templating.php");
include_once("includes/main.php");
include_once("includes/functions.php");
include_once("includes/cache.php");

?>