<?php

function file_gallery_media_tags_get_taxonomy_slug()
{
	global $wpdb, $mediatags, $file_gallery;

	if( defined('FILE_GALLERY_MEDIA_TAG_NAME') )
		return FILE_GALLERY_MEDIA_TAG_NAME;

	file_gallery_do_settings();
	
	$options = get_option('file_gallery');
	$default_slug = $file_gallery->settings['media_tag_taxonomy_slug']['default'];
	
	// Media Tags plugin
	if( is_a($mediatags, 'MediaTags') && defined('MEDIA_TAGS_TAXONOMY') )
		$slug = MEDIA_TAGS_TAXONOMY;

	define('FILE_GALLERY_MEDIA_TAG_NAME', $slug);
}

function file_gallery_media_tags_update_taxonomy_slug( $old_slug = '', $new_slug = '' )
{
	global $wpdb;

	if( empty($old_slug) || empty($new_slug) )
		return -1;

	// $sql = "UPDATE $wpdb->term_taxonomy SET taxonomy = '$new_slug' WHERE taxonomy = '$old_slug'";
	
	if( 0 <= $wpdb->update($wpdb->term_taxonomy, array('taxonomy' => $new_slug), array('taxonomy' => $old_slug) ) )
		return true;
	
	return false;
}

?>