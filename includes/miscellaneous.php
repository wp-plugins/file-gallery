<?php

/**
 * @since 1.6.5.4
 */
function file_gallery_get_default_options( $invert = false )
{
	if( false === $invert )
	{
		return array(
			'folder' 					  => FILE_GALLERY_URL, 
			'abspath' 					  => FILE_GALLERY_ABSPATH, 
			'media_tag_name'			  => FILE_GALLERY_MEDIA_TAG_NAME,
			
			'in_excerpt' 				  => 1, 
			'in_excerpt_replace_content'  => '<p><strong>(' . __('galleries are shown on full posts only', 'file-gallery') . ')</strong></p>', 
			
			'default_image_size' 		  => 'thumbnail', 
			'default_linkto' 			  => 'attachment', 
			'default_linked_image_size'   => 'full',
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
			
			'insert_options_state'		  => 1,
			'insert_single_options_state' => 1,
			'acf_state'					  => 1,
			'display_gallery_fieldset'	  => 1,
			'display_single_fieldset'	  => 1,
			'display_acf'				  => 1,
			'insert_gallery_button'		  => 1,
			'insert_single_button'		  => 1,
			
			'e_display_attachment_count'  => 1,
			'e_display_media_tags'		  => 1,
			'e_display_post_thumb'		  => 1,
			
			'cache'						  => 0,
			'cache_time'				  => 3600, // == 1 hour
			'cache_non_html_output'		  => 0,
			
			'del_options_on_deactivate'   => 0,
	
			'show_on_post_type_post'	  => 1,
			'show_on_post_type_page'	  => 1,
			
			'library_filter_duplicates'   => 1,
			
			'auto_enqueued_scripts'		  => 'thickbox',
			
			'disable_shortcode_handler'	  => 0,
			
			'default_metabox_image_size'  => 'thumbnail',
			'default_metabox_image_width' => 75
		);
	}
	
	return array(		
		'in_excerpt' 				  => 0, 

		'insert_options_state'		  => 0,
		'insert_single_options_state' => 0,
		'acf_state'					  => 0,
		'display_gallery_fieldset'	  => 0,
		'display_single_fieldset'	  => 0,
		'display_acf'				  => 0,
		'insert_gallery_button'		  => 0,
		'insert_single_button'		  => 0,
		
		'e_display_attachment_count'  => 0,
		'e_display_media_tags'		  => 0,
		'e_display_post_thumb'		  => 0,
		
		'cache'						  => 0,
		'cache_non_html_output'		  => 0,
		
		'del_options_on_deactivate'   => 0,

		'show_on_post_type_post'	  => 0,
		'show_on_post_type_page'	  => 0,
		
		'library_filter_duplicates'   => 0,
		
		'disable_shortcode_handler'	  => 0
	);
}


/**
 * Makes sure that plugin options do not disappear just
 * because we're lazy (using checkboxes instead of radio buttons) :D
 *
 * @since 1.6.5.4
 */
function file_gallery_save_media_settings( $options )
{
	$defaults = file_gallery_get_default_options( true );
	$defaults = file_gallery_parse_args( $options, $defaults); // $defaults = shortcode_atts( $defaults, $options );
	$defaults['folder']  = FILE_GALLERY_URL;
	$defaults['abspath'] = FILE_GALLERY_ABSPATH;
	
	return $defaults;
}


/**
 * Parses plugin options
 *
 * @since 1.6.5.2
 */
function file_gallery_parse_args( $args, $defaults )
{
	foreach( $defaults as $key => $val )
	{
		// if key isn't set, it's a new option - add
		if( ! isset($args[$key]) )
			$args[$key] = $val;
		// if a key's value is empty, but should be a false - make it rather a zero
		elseif( '' == $args[$key] && (0 === $val || 1 === $val) )
			$args[$key] = 0;
	}
	
	return $args;
}


/**
 * Taken from WordPress 3.1-beta1
 */
if( ! function_exists('_wp_link_page') )
{
	/**
	 * Helper function for wp_link_pages().
	 *
	 * @since 3.1.0
	 * @access private
	 *
	 * @param int $i Page number.
	 * @return string Link.
	 */
	function _wp_link_page( $i ) {
		global $post, $wp_rewrite;
	
		if ( 1 == $i ) {
			$url = get_permalink();
		} else {
			if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
				$url = add_query_arg( 'page', $i, get_permalink() );
			elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') == $post->ID )
				$url = trailingslashit(get_permalink()) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
			else
				$url = trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged');
		}
	
		return '<a href="' . esc_url( $url ) . '">';
	}
}


/**
 * Modified WP function to support !%mimetype% syntax // not yet, actually
 *
 * Convert MIME types into SQL.
 *
 * @since 1.6.5
 *
 * @param string|array $post_mime_types List of mime types or comma separated string of mime types.
 * @param string $table_alias Optional. Specify a table alias, if needed.
 * @return string The SQL AND clause for mime searching.
 */
function file_gallery_wp_post_mime_type_where($post_mime_types, $table_alias = '') {
	$where = '';
	$wildcards = array('', '%', '%/%');
	if ( is_string($post_mime_types) )
		$post_mime_types = array_map('trim', explode(',', $post_mime_types));
	foreach ( (array) $post_mime_types as $mime_type ) {
		$mime_type = preg_replace('/\s/', '', $mime_type);
		$slashpos = strpos($mime_type, '/');
		if ( false !== $slashpos ) {
			$mime_group = preg_replace('/[^-*.a-zA-Z0-9]/', '', substr($mime_type, 0, $slashpos));
			$mime_subgroup = preg_replace('/[^-*.+a-zA-Z0-9]/', '', substr($mime_type, $slashpos + 1));
			if ( empty($mime_subgroup) )
				$mime_subgroup = '*';
			else
				$mime_subgroup = str_replace('/', '', $mime_subgroup);
			$mime_pattern = "$mime_group/$mime_subgroup";
		} else {
			$mime_pattern = preg_replace('/[^-*.a-zA-Z0-9]/', '', $mime_type);
			if ( false === strpos($mime_pattern, '*') )
				$mime_pattern .= '/*';
		}

		$mime_pattern = preg_replace('/\*+/', '%', $mime_pattern);

		if ( in_array( $mime_type, $wildcards ) )
			return '';

		if ( false !== strpos($mime_pattern, '%') )
			$wheres[] = empty($table_alias) ? "post_mime_type LIKE '$mime_pattern'" : "$table_alias.post_mime_type LIKE '$mime_pattern'";
		else
			$wheres[] = empty($table_alias) ? "post_mime_type = '$mime_pattern'" : "$table_alias.post_mime_type = '$mime_pattern'";
	}
	if ( !empty($wheres) )
		$where = ' AND (' . join(' OR ', $wheres) . ') ';
	return $where;
}


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
 * copy of the standard WordPress function found in admin
 *
 * @since 1.5.2
 */
function file_gallery_file_is_displayable_image( $path )
{
	$path = preg_replace(array("#\\\#", "#/+#"), array("/", "/"), $path);		
	$info = @getimagesize($path);

	if ( empty($info) )
		$result = false;
	elseif ( !in_array($info[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG)) )    // only gif, jpeg and png images can reliably be displayed
		$result = false;
	else
		$result = true;
	
	return apply_filters('file_is_displayable_image', $result, $path);
}


/**
 * saves attachment order using "menu_order" field
 *
 * @since 1.0
 */
function file_gallery_save_menu()
{
	global $wpdb;
	
	$updates = '';
	
	check_ajax_referer('file-gallery');

	$order = explode(',', $_POST['attachment_order']);

	foreach($order as $mo => $ID)
	{
		$updates .= sprintf(" WHEN '%d' THEN '%d' ", $ID, $mo);
	}
	
	if( false !== $wpdb->query("UPDATE $wpdb->posts SET `menu_order` = CASE `ID` " . $updates . " ELSE `menu_order` END") )
	{
		echo __('Attachment order saved successfully.', 'file-gallery');
	}
	else
	{
		$error = __('Database error! Function: file_gallery_save_menu', 'file-gallery');
		file_gallery_write_log( $error );
		echo $error;
	}
	
	exit();
}
add_action('wp_ajax_file_gallery_save_menu_order', 'file_gallery_save_menu');


/**
 * saves state of gallery and single file insertion options
 *
 * @since 1.5
 */
function file_gallery_save_toggle_state()
{
	check_ajax_referer('file-gallery');
	
	$options = get_option('file_gallery');
	$opt = 'insert_options_state';
	
	switch( $_POST['action'] )
	{
		case 'file_gallery_save_single_toggle_state' :
			$opt = 'insert_single_options_state';
			break;
		case 'file_gallery_save_acf_toggle_state' :
			$opt = 'acf_state';
			break;
		default : 
			break;
	}
	
	$options[$opt] = (int) $_POST['state'];
	
	update_option('file_gallery', $options);
	
	exit();
}
add_action('wp_ajax_file_gallery_save_toggle_state', 'file_gallery_save_toggle_state');
add_action('wp_ajax_file_gallery_save_single_toggle_state', 'file_gallery_save_toggle_state');
add_action('wp_ajax_file_gallery_save_acf_toggle_state', 'file_gallery_save_toggle_state');


/**
 * Writes errors, notices, etc, to the log file
 * Limited to 100 kB
 */
function file_gallery_write_log( $data = "" )
{
	$data = date("Y-m-d@H:i:s") . "\n" . str_replace("<br />", "\n", $data) . "\n";
	$filename = str_replace("\\", "/", WP_CONTENT_DIR) . "/file_gallery_log.txt";
	
	if( @file_exists($filename) )
		$data .= @implode("", @file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) . "\n";
	
	$file = @fopen($filename, "w+t");

	if( false !== $file )
	{		
		@fwrite($file, $data);
		
		if( 102400 < (filesize($filename) + strlen($data)) )
			@ftruncate($file, 102400);
	}
	
	@fclose($file);
}

?>