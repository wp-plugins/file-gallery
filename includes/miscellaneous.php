<?php

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
	
	check_ajax_referer('file-gallery');

	$order = explode(",", $_POST["attachment_order"]);

	foreach($order as $mo => $ID)
	{
		$updates .= sprintf(" WHEN '%d' THEN '%d' ", $ID, $mo);
	}

	$r = $wpdb->query( "UPDATE $wpdb->posts SET `menu_order` = CASE `ID` " . $updates . " ELSE `menu_order` END" );
	
	if( false !== $r )
	{
		echo __("Attachment order saved successfully.", "file-gallery");
	}
	else
	{
		$error = __("Database error! Function: file_gallery_save_menu", "file-gallery");
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
	
	$options = get_option("file_gallery");
	$options["insert_options_states"] = $_POST["states"];
	
	update_option("file_gallery", $options);
	
	exit();
}
add_action('wp_ajax_file_gallery_save_toggle_state', 'file_gallery_save_toggle_state');



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