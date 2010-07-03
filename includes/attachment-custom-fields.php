<?php


/**
 * Adds a button to the edit/insert attachment form
 * to link the attachment to the parent post
 * in addition to itself, the actual file, or nothing
 */
function file_gallery_attachment_fields_to_edit( $form_fields, $attachment )
{
	global $wpdb;

	$form_fields["url"]["html"] = str_replace( __("Post URL"), __("Attachment URL", "file-gallery"), $form_fields["url"]["html"]);
	$form_fields["url"]["html"] .= "<button type='button' class='button urlparent' title='" . get_permalink( $wpdb->get_var( $wpdb->prepare("SELECT `post_parent` FROM $wpdb->posts WHERE `ID`='%d'", $attachment->ID) ) ) . "'>Parent Post URL</button>";
	
	return $form_fields;
}
add_filter("attachment_fields_to_edit", "file_gallery_attachment_fields_to_edit", 10, 2);



function file_gallery_attachment_fields_to_save( $fields )
{
	$args = func_get_args();
	
	print_r($_POST);
	print_r($args);

	return $fields;
}
// add_filter("attachment_fields_to_save", "file_gallery_attachment_fields_to_save", 10, 2);



function attachment_custom_meta_box()
{
	$args = func_get_args();
	$attachment_id = $args[1]->ID;
?>
<div id="postcustomstuff" class="attachmentcustomstuff">
	<div id="ajax-response"></div>
	<input type="hidden" id="post_ID" name="post_ID" value="<?php echo $attachment_id; ?>" />
	<?php
		$metadata = has_meta($attachment_id);
		list_meta($metadata);
		meta_form();
	?>
	
	<p><?php _e('Custom fields can be used to add extra metadata to a post that you can <a href="http://codex.wordpress.org/Using_Custom_Fields" target="_blank">use in your theme</a>.'); ?></p>
</div>
<?php
}
// add_filter('media_meta', 'attachment_custom_meta_box', 10, 2);

?>