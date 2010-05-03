<?php

/**
 * Returns current post's attachments
 */
function file_gallery_list_attachments(&$count_attachments, $post_id, $attachment_order, $checked_attachments)
{
	global $wpdb;
	
	$thumb_id = false;
	$attached_files = "";
	
	if( "" != $attachment_order && false !== strpos($attachment_order, ",") )
	{
		$attachment_ids = str_replace(",", "','", trim($attachment_order, ",") );
		
		$attachments = $wpdb->get_results("SELECT * FROM $wpdb->posts 
										   WHERE $wpdb->posts.post_parent = " . $post_id . "
										   AND $wpdb->posts.post_type = 'attachment' 
										   ORDER BY FIELD(ID,'" . $attachment_ids . "') ");
	}
	else
	{
		$attachments = get_children(
							array('post_parent' => $post_id, 
								  'post_type' => 'attachment', 
								  'order' => 'ASC', 
								  'orderby' => 'menu_order'
								  //'post_status' => 'inherit' // is this really needed?
								  //'post_mime_type' => 'image', // gotta add "images only" option, until then this is out
		));
	}

	if( $attachments )
	{
		$thumb_id = get_post_meta( $post_id, '_thumbnail_id', true );
		
		// start the list...
		$attached_files = '<ul class="ui-sortable" id="file_gallery_list">' . "\n";
		
		$count_attachments = count($attachments);
		
		foreach( $attachments as $attachment )
		{
			$classes         = array("sortableitem");
			$post_thumb_link = "set";
			
			$original_id = get_post_meta($attachment->ID, "_is_copy_of", true);
			$copies 	 = get_post_meta($attachment->ID, "_has_copies", true);
			
			if( "" != $original_id )
				$classes[] = "copy copy-of-" . $original_id;
			elseif( "" != $copies )
				$classes[] = "has_copies copies-" . implode("-", $copies);
			
			if( intval($thumb_id) === intval($attachment->ID) )
			{
				$classes[]       = "post_thumb";
				$post_thumb_link = "unset";
			}

			$attachment_thumb  = wp_get_attachment_thumb_url($attachment->ID);
			$attachment_width  = "70";
			$attachment_height = "70";
			
			$large = wp_get_attachment_image_src($attachment->ID, "large");
			
			$non_image = "";
			$checked   = "";
			
			if( in_array($attachment->ID, $checked_attachments) )
				$checked = ' checked="checked"';
			
			// if it's not an image...
			if( "" == $attachment_thumb )
			{
				$attachment_thumb = get_option('home') . "/wp-includes/images/crystal/" . file_gallery_get_file_type($attachment->post_mime_type) . ".png";
				$attachment_width = "";
				$attachment_height = "";
				$non_image = " non_image";
			}
			
			$attached_files .= '
			<li id="image-' . $attachment->ID . '" class="' . implode(" ", $classes) . '">
				
				<img src="' . $attachment_thumb . '" alt="' . $attachment->post_title . '" id="in-' . $attachment->ID . '" title="' . $attachment->post_title . '" width="' . $attachment_width . '" height="' . $attachment_height . '" class="fgtt' . $non_image . '" />';
				
				if( "" == $non_image ) :
					$attached_files .= '<a href="' . $large[0] . '" id="in-' . $attachment->ID . '-zoom" class="img_zoom colorbox">
						<img src="' . FILE_GALLERY_URL . '/images/famfamfam_silk/magnifier_zoom_in.png" alt="' . __("Zoom", "file-gallery") . '" title="' . __("Zoom", "file-gallery") . '" />
					</a>';
				endif;
				
				$attached_files .= '<a href="#" id="in-' . $attachment->ID . '-edit" class="img_edit">
					<img src="' . FILE_GALLERY_URL . '/images/famfamfam_silk/image_edit.png" alt="' . __("Edit", "file-gallery") . '" title="' . __("Edit", "file-gallery") . '" />
				</a>
				<input type="checkbox" id="att-chk-' . $attachment->ID . '" class="checker"' . $checked . ' />';
		
			if (current_user_can('edit_post', $attachment->ID)) :
				
				if( "" == $non_image ) :
				
					$attached_files .= '<a href="#" class="post_thumb_status" rel="' . $attachment->ID . '">
							<img src="' . FILE_GALLERY_URL . '/images/famfamfam_silk/bell_' . $post_thumb_link . '.png" alt="' . __(ucfirst($post_thumb_link) . " as post thumb", "file-gallery") . '" title="' . __(ucfirst($post_thumb_link) . " as featured image", "file-gallery") . '" />
						</a>';
					
					$attached_files .= '<div id="post_thumb_setter_' . $attachment->ID . '" class="post_thumb_setter">
							' . __("Really set as featured image?", "file-gallery") . ' 
							<a href="#" id="post_thumb_set[' . $attachment->ID . ']" class="post_thumb_set">' . __("Continue", "file-gallery") . '</a>
							' . __("or", "file-gallery") . '
							<a href="#" class="post_thumb_cancel" rel="' . $attachment->ID . '">' . __("Cancel", "file-gallery") . '</a>
						</div>';
					
					$attached_files .= '<div id="post_thumb_unsetter_' . $attachment->ID . '" class="post_thumb_unsetter">
							' . __("Really unset as featured image?", "file-gallery") . ' 
							<a href="#" id="post_thumb_unset[' . $attachment->ID . ']" class="post_thumb_unset">' . __("Continue", "file-gallery") . '</a>
							' . __("or", "file-gallery") . '
							<a href="#" class="post_thumb_cancel" rel="' . $attachment->ID . '">' . __("Cancel", "file-gallery") . '</a>
						</div>';
				
				endif;

				$attached_files .= '<a href="#" class="delete_or_detach_link" rel="' . $attachment->ID . '">
					<img src="' . FILE_GALLERY_URL . '/images/famfamfam_silk/delete.png" alt="' . __("Detach / Delete", "file-gallery") . '" title="' . __("Detach / Delete", "file-gallery") . '" />
				</a>
				<div id="detach_or_delete_'  . $attachment->ID . '" class="detach_or_delete">
					<br />';

				if (current_user_can('delete_post', $attachment->ID)) :
	
					$attached_files .= '<a href="#" class="do_single_delete" rel="' . $attachment->ID . '">' . __("Delete", "file-gallery") . '</a>
						<br />
						' . __("or", "file-gallery") . '
						<br />';
						
				endif;
					
					$attached_files .= '<a href="#" class="do_single_detach" rel="' . $attachment->ID . '">' . __("Detach", "file-gallery") . '</a>
				</div>
				<div id="detach_attachment_'  . $attachment->ID . '" class="detach_attachment">
					' . __("Really detach?", "file-gallery") . ' 
					<a href="#" id="detach[' . $attachment->ID . ']" class="detach">' . __("Continue", "file-gallery") . '</a>
					' . __("or", "file-gallery") . '
					<a href="#" class="detach_cancel" rel="' . $attachment->ID . '">' . __("Cancel", "file-gallery") . '</a>
				</div>';
				
				if (current_user_can('delete_post', $attachment->ID)) :
				
					$attached_files .= '<div id="del_attachment_' . $attachment->ID . '" class="del_attachment">
						' . __("Really delete?", "file-gallery") . ' 
						<a href="#" id="del[' . $attachment->ID . ']" class="delete">' . __("Continue", "file-gallery") . '</a>
						' . __("or", "file-gallery") . '
						<a href="#" class="delete_cancel" rel="' . $attachment->ID . '">' . __("Cancel", "file-gallery") . '</a>
					</div>';
					
				endif;
			
			endif;
			
			$attached_files .= '</li>
			' . "\n";
		}
		
		//end the list...
		$attached_files .= "</ul>\n";
	}

	return $attached_files;
}



/**
 * returns a list of media tags found in db
 */
function file_gallery_list_tags( $type = "html" )
{
	global $wpdb;
	
	$options = get_option("file_gallery");
	
	if( isset($options["cache"]) && true == $options["cache"] )
	{
		$transient = "filegallery_mediatags_" . $type;
		$cache     = get_transient($transient);
		
		if( $cache )
			return $cache;
	}

	$media_tags = $wpdb->get_results("SELECT * FROM $wpdb->terms 
									  LEFT JOIN $wpdb->term_taxonomy 
									  	ON ( $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id ) 
									  LEFT JOIN $wpdb->term_relationships 
									  	ON ( $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id ) 
									  WHERE $wpdb->term_taxonomy.taxonomy = '" . FILE_GALLERY_MEDIA_TAG_NAME . "'
									  ORDER BY `name` ASC");

	if( !empty($media_tags) )
	{
		if( "array" == $type || "json" == $type )
		{
			foreach( $media_tags as $tag )
			{
				$list[] = array(
									"term_id" => $tag->term_id,
									"name" => $tag->name,
									"slug" => $tag->slug,
									"term_group" => $tag->term_group,
									"term_taxonomy_id" => $tag->term_taxonomy_id,
									"taxonomy" => $tag->taxonomy,
									"description" => $tag->description,
									"parent" => $tag->parent,
									"count" => $tag->count,
									"object_id" => $tag->object_id,
									"term_order" => $tag->term_order
								);
			}
			
			if( "json" == $type )
				$list = "{" . json_encode($list) . "}";
		}
		elseif( "object" == $type )
		{
			$list = $media_tags;
		}
		else // html
		{
			foreach( $media_tags as $tag )
			{
				$list[] = '<a href="#" class="fg_insert_tag" name="' . $tag->slug . '">' . $tag->name . '</a>';
			}
		}
	}

	if( empty($list) )
		$list[] = implode("<br />", $media_tags);
	
	if( isset($options["cache"]) && true == $options["cache"] )
		set_transient($transient, $list, $options["cache_time"]);
	
	return $list;
}



/**
 * Displays the main form for inserting shortcodes / single images.
 * also handles attachments edit/delete/detach response
 * and displays atachment thumbnails on post edit screen in admin
 */
function file_gallery_main( $ajax = true )
{
	global $wpdb;
	
	check_ajax_referer('file-gallery');
	
	$post_id			  = $_POST['post_id'];
	$attachment_order	  = $_POST['attachment_order'];
	$files_or_tags		  = $_POST["files_or_tags"];
	$tags_from			  = $_POST["tags_from"];
	$action				  = $_POST['action'];
	$attachment_ids		  = $_POST['attachment_ids'];
	$attachment_data	  = $_POST['attachment_data'];
	$delete_what          = $_POST['delete_what'];
	$checked_attachments  = explode(",", $_POST['checked_attachments']);
	$copies				  = $_POST['copies'];
	$originals			  = $_POST['originals'];
	$fieldsets			  = $_POST['fieldsets'];
	
	$file_gallery_options = get_option('file_gallery');
	$states				  = explode(",", $file_gallery_options["insert_options_states"]);
	$output               = "&nbsp;";
	$count_attachments    = 0;
	$hide_form            = "";
	$sizes                = file_gallery_get_intermediate_image_sizes();
	
	$normals   		= explode(",", $normals);
	$copies    		= explode(",", $copies);
	$originals 		= explode(",", $originals);
	$attachment_ids = explode(",", $attachment_ids);
	
	if( empty_array($normals) )
		$normals = array();
	
	if( empty_array($copies) )
		$copies = array();
	
	if( empty_array($originals) )
		$originals = array();
	
	if( empty_array($attachment_ids) )
		$attachment_ids = array();
	
	if( "file_gallery_main_delete" == $action )
	{
		if( !empty($copies) && !empty($originals) )
		{
			$cpluso  = array_merge($copies, $originals);
			$normals = array_xor((array)$attachment_ids, $cpluso);
		}
		elseif( !empty($copies) )
		{
			$normals = array_xor((array)$attachment_ids, $copies);
		}
		elseif( !empty($originals) )
		{
			$normals = array_xor((array)$attachment_ids, $originals);
		}
		else
		{
			$normals = $attachment_ids;
		}
		
		// cancel our own 'wp_delete_attachment' filter
		define("FILE_GALLERY_SKIP_DELETE_CANCEL", true);
		
		foreach( $normals as $normal )
		{
			if( current_user_can('delete_post', $normal) )
			{
				wp_delete_attachment( $normal );
			
				$fully_deleted[] = $normal;
			}
		}
		
		foreach( $copies as $copy )
		{
			if( current_user_can('delete_post', $copy) )
			{
				file_gallery_delete_attachment( $copy );
				
				$partially_deleted[] = $copy;
			}
		}
		
		foreach( $originals as $original )
		{
			if( "all" == $delete_what && current_user_can('delete_post', $original) )
			{
				file_gallery_delete_all_attachment_copies( $original );
				wp_delete_attachment( $original );
				
				$fully_deleted[] = $original;
			}
			elseif( "data_only" == $delete_what && current_user_can('delete_post', $original) )
			{
				file_gallery_promote_first_attachment_copy( $original );
				file_gallery_delete_attachment( $original );
				
				$partially_deleted[] = $original;
			}
		}
		
		if( empty($fully_deleted) && empty($partially_deleted) )
			$output = __("No attachments were deleted (capabilities?)", "file-gallery");
		else
			$output = __("Attachment(s) deleted", "file-gallery");
	}
	elseif( "file_gallery_main_detach" == $action )
	{
		foreach( $attachment_ids as $attachment_id )
		{
			if( false === $wpdb->query( sprintf("UPDATE $wpdb->posts SET `post_parent`='0' WHERE `ID`='%d'", $attachment_id) ) )
				$detach_errors[] = $attachment_id;
		}

		if( empty($detach_errors) )
			$output = __("Attachment(s) detached", "file-gallery");
		else
			$output = __("Error detaching attachment(s)", "file-gallery");
	}
	elseif( "file_gallery_main_update" == $action )
	{
		$attachment_id = intval($_POST['attachment_id']);

		$attachment_data['ID'] 			 = $attachment_id;
		$attachment_data['post_title']   = $_POST['post_title'];
		$attachment_data['post_content'] = $_POST['post_content'];
		$attachment_data['post_excerpt'] = $_POST['post_excerpt'];
		$attachment_data['menu_order'] 	 = $_POST['menu_order'];
		
		// media_tag taxonomy - attachment tags
		$tax_input = "";
		$old_media_tags = "";
		
		$get_old_media_tags = wp_get_object_terms(intval($_POST['attachment_id']), FILE_GALLERY_MEDIA_TAG_NAME);
		
		if( !empty($get_old_media_tags) )
		{
			foreach( $get_old_media_tags as $mt )
			{
				$old_media_tags[] = $mt->name;
			}
			
			$old_media_tags = implode(", ", $old_media_tags);
		}
		
		if( "" != $_POST['tax_input'] && $old_media_tags != $_POST['tax_input'] )
		{
			$tax_input = preg_replace("#\s+#", " ", $_POST['tax_input']);
			$tax_input = preg_replace("#,+#", ",", $_POST['tax_input']);
			$tax_input = trim($tax_input, " ");
			$tax_input = trim($tax_input, ",");
			$tax_input = explode(", ", $tax_input);
			
			$media_tags_result = wp_set_object_terms( $attachment_id, $tax_input, FILE_GALLERY_MEDIA_TAG_NAME );
		}
		
		// check if there were any changes
		$old_attachment_data = get_object_vars( get_post($attachment_id) );
		
		if( $old_attachment_data['post_title']   != $attachment_data['post_title']   || 
			$old_attachment_data['post_content'] != $attachment_data['post_content'] || 
			$old_attachment_data['post_excerpt'] != $attachment_data['post_excerpt'] ||	
			$old_attachment_data['menu_order']   != $attachment_data['menu_order']   ||
			is_array($tax_input) )
		{
			if( 0 !== wp_update_post($attachment_data) )
				$output = __("Attachment data updated", "file-gallery");
			else
				$output = __("Error updating attachment data!", "file-gallery");
		}
		else
		{
			$output = __("No change.", "file-gallery");
		}
	}
	elseif( "file_gallery_set_post_thumb" == $action )
	{
		update_post_meta($post_id, "_thumbnail_id", $attachment_ids[0]);
		exit(_wp_post_thumbnail_html($attachment_ids[0]));
	}
	elseif( "file_gallery_unset_post_thumb" == $action )
	{
		exit();
	}

	include_once("main-form.php");

	exit();
}
add_action('wp_ajax_file_gallery_load',				'file_gallery_main');
add_action('wp_ajax_file_gallery_main_update',		'file_gallery_main');
add_action('wp_ajax_file_gallery_main_delete',		'file_gallery_main');
add_action('wp_ajax_file_gallery_main_detach',		'file_gallery_main');
add_action('wp_ajax_file_gallery_set_post_thumb',	'file_gallery_main');
add_action('wp_ajax_file_gallery_unset_post_thumb',	'file_gallery_main');
?>