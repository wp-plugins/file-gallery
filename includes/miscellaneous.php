<?php

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
		echo __("Menu order saved successfully.", "file-gallery");
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

?>