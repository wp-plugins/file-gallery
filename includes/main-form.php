<?php
/**
 * html form in which all the attachments are 
 * displayed on edit post screen in admin
 */
?>
<div id="file_gallery_response"><?php echo $output; ?></div>

<form id="file_gallery_form" action="<?php echo FILE_GALLERY_URL . "/file-gallery.php"?>" method="post">

	<input type="hidden" name="data_collector"           id="data_collector"           value="" style="width: 90%" />
	<input type="hidden" name="data_collector_checked"   id="data_collector_checked"   value="<?php echo $checked_attachments; ?>" style="width: 90%" />
	<input type="hidden" name="data_collector_full"      id="data_collector_full"      value="" style="width: 90%" />
	<input type="hidden" name="file_gallery_delete_what" id="file_gallery_delete_what" value="<?php echo $delete_what; ?>" style="width: 90%" />
	<input type="hidden" name="file_gallery_copies"      id="file_gallery_copies"      value="" style="width: 90%" />
	<input type="hidden" name="file_gallery_originals"   id="file_gallery_originals"   value="" style="width: 90%" />
	
	<div id="fg_buttons"<?php if( 0 == intval($file_gallery_options["display_insert_fieldsets"]) ){ echo ' class="alt"'; }?>>
		<input type="button" value="<?php _e("Refresh attachments", "file-gallery"); ?>" title="<?php _e("Refresh attachments", "file-gallery"); ?>" class="button" id="file_gallery_refresh" />
		<input type="button" value="<?php _e("Check all", "file-gallery"); ?>" title="<?php _e("Check all", "file-gallery"); ?>" class="button" id="file_gallery_check_all" />
		<input type="button" value="<?php _e("Uncheck all", "file-gallery"); ?>" title="<?php _e("Uncheck all", "file-gallery"); ?>" class="button" id="file_gallery_uncheck_all" />
		<input type="button" value="<?php _e("Save as menu order", "file-gallery"); ?>" title="<?php _e("Save as menu order", "file-gallery"); ?>" class="button" id="file_gallery_save_menu_order" />
		<input type="button" value="<?php _e("Copy all attachments from another post", "file-gallery"); ?>" title="<?php _e("Copy all attachments from another post", "file-gallery"); ?>" class="button" id="file_gallery_copy_all" />
		<input type="button" value="<?php _e("Delete all checked", "file-gallery"); ?>" title="<?php _e("Delete all checked", "file-gallery"); ?>" class="button" id="file_gallery_delete_checked" />
		<input type="button" value="<?php _e("Detach all checked", "file-gallery"); ?>" title="<?php _e("Detach all checked", "file-gallery"); ?>" class="button" id="file_gallery_detach_checked" />
	</div>

	<p id="fg_info">
		<?php if( "1" == strval($file_gallery_options["display_insert_fieldsets"]) ) : ?>
		<?php _e("Insert checked attachments into post as", "file-gallery"); ?>:
		<?php endif; ?>
	</p>
	
	<?php if( 1 == intval($file_gallery_options["display_insert_fieldsets"]) ) : ?>
	
	<fieldset id="file_gallery_gallery_options">
	
		<legend class="button-primary" id="file_gallery_send_gallery_legend"><?php _e("a gallery", "file-gallery"); ?>:</legend>
		<input type="button" id="file_gallery_hide_gallery_options" class="<?php if( "0" == strval($states[0]) ){ echo 'closed'; }else{ echo 'open'; } ?>" title="<?php _e("show/hide this fieldset", "file-gallery"); ?>" />

		<div id="file_gallery_toggler"<?php if( "0" == strval($states[0]) ){ echo ' style="display: none;"'; } ?>>
		
			<p>
				<label for="file_gallery_size"><?php _e("size", "file-gallery"); ?>:</label>
				<select name="file_gallery_size" id="file_gallery_size">
					<?php foreach( $sizes as $size ) : ?>
					<option value="<?php echo $size; ?>"<?php if( $size == $file_gallery_options["default_image_size"]){ ?> selected="selected"<?php } ?>><?php echo $size; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		
			<p>
				<label for="file_gallery_linkto"><?php _e("link to", "file-gallery"); ?>:</label>
				<select name="file_gallery_linkto" id="file_gallery_linkto">
					<option value="none"<?php if( "none" == $file_gallery_options["default_linkto"]){ ?> selected="selected"<?php } ?>><?php _e("nothing (do not link)", "file-gallery"); ?></option>
					<option value="file"<?php if( "file" == $file_gallery_options["default_linkto"]){ ?> selected="selected"<?php } ?>><?php _e("file", "file-gallery"); ?></option>
					<option value="attachment"<?php if( "attachment" == $file_gallery_options["default_linkto"]){ ?> selected="selected"<?php } ?>><?php _e("attachment page", "file-gallery"); ?></option>
				</select>
			</p>
			
			<p id="file_gallery_linkclass_label">
				<label for="file_gallery_linkclass"><?php _e("link class", "file-gallery"); ?>:</label>
				<input type="text" name="file_gallery_linkclass" id="file_gallery_linkclass" value="<?php echo $file_gallery_options["default_linkclass"]; ?>" />
			</p>
		
			<p>
				<label for="file_gallery_orderby"><?php _e("order", "file-gallery"); ?>:</label>
				<select name="file_gallery_orderby" id="file_gallery_orderby">
					<option value="default"<?php if( "default" == $file_gallery_options["default_orderby"]){ ?> selected="selected"<?php } ?>><?php _e("file gallery", "file-gallery"); ?></option>
					<option value="rand"<?php if( "rand" == $file_gallery_options["default_orderby"]){ ?> selected="selected"<?php } ?>><?php _e("random", "file-gallery"); ?></option>
					<option value="menu_order"<?php if( "menu_order" == $file_gallery_options["default_orderby"]){ ?> selected="selected"<?php } ?>><?php _e("menu order", "file-gallery"); ?></option>
					<option value="title"<?php if( "title" == $file_gallery_options["default_orderby"]){ ?> selected="selected"<?php } ?>><?php _e("title", "file-gallery"); ?></option>
					<option value="ID"<?php if( "ID" == $file_gallery_options["default_orderby"]){ ?> selected="selected"<?php } ?>><?php _e("date / time", "file-gallery"); ?></option>
				</select>
				<select name="file_gallery_order" id="file_gallery_order">
					<option value="ASC"<?php if( "ASC" == $file_gallery_options["default_order"]){ ?> selected="selected"<?php } ?>><?php _e("ASC", "file-gallery"); ?></option>
					<option value="DESC"<?php if( "DESC" == $file_gallery_options["default_order"]){ ?> selected="selected"<?php } ?>><?php _e("DESC", "file-gallery"); ?></option>
				</select>
			</p>
		
			<p>
				<label for="file_gallery_template"><?php _e("template", "file-gallery"); ?>:</label>
				<select name="file_gallery_template" id="file_gallery_template">
					<?php
						$file_gallery_templates = file_gallery_get_templates();
				
						foreach( $file_gallery_templates as $template_name )
						{
							$templates_dropdown .= "<option value=\"" . $template_name . "\"";
							
							if( $file_gallery_options["default_template"] == $template_name )
								$templates_dropdown .= ' selected="selected"';
							
							$templates_dropdown .=">" . $template_name . "</option>\n";
						}
						
						echo $templates_dropdown;
					?>
				</select>
			</p>
	
			<p>
				<label for="file_gallery_imageclass"><?php _e("image class", "file-gallery"); ?>:</label>
				<input type="text" name="file_gallery_imageclass" id="file_gallery_imageclass" value="<?php echo $file_gallery_options["default_imageclass"]; ?>" />
			</p>
			
			<br />
			
			<input type="button" id="file_gallery_send_gallery" value="<?php _e("a gallery", "file-gallery"); ?>" class="button-primary" />&nbsp;
			
			<br class="clear" />
			
			<p id="fg_gallery_tags_container">
				<label for="fg_gallery_tags"><?php _e("Media tags", "file-gallery");?>:</label>
				<input type="text" id="fg_gallery_tags" name="fg_gallery_tags" value="<?php echo $_POST["tag_list"]; ?>" />
	
				<label for="fg_gallery_tags_from"><?php _e("current post's attachments only?", "file-gallery"); ?></label>
				<input type="checkbox" id="fg_gallery_tags_from" name="fg_gallery_tags_from" checked="checked" />
			</p>
			
			<!--<input type="button" onclick="file_gallery_preview_template(jQuery('#file_gallery_template').val()); return false;" value="&uArr;" title="preview template" class="button" />-->
			
		</div>
		
	</fieldset>

	<fieldset id="file_gallery_single_options">
	
		<legend class="button-primary" id="file_gallery_send_single_legend"><?php _e("single files", "file-gallery"); ?>:</legend>
		<input type="button" id="file_gallery_hide_single_options" class="<?php if( "0" == strval($states[1]) ){ echo 'closed'; }else{ echo 'open'; } ?>" title="<?php _e("show/hide this fieldset", "file-gallery"); ?>" />

		<div id="file_gallery_single_toggler"<?php if( "0" == strval($states[1]) ){ echo ' style="display: none;"'; } ?>>
			<p>
				<label for="file_gallery_single_size"><?php _e("size", "file-gallery"); ?>:</label>
				<select name="file_gallery_single_size" id="file_gallery_single_size">
					<?php foreach( $sizes as $size ) : ?>
					<option value="<?php echo $size; ?>"<?php if( $size == $file_gallery_options["single_default_image_size"]){ ?> selected="selected"<?php } ?>><?php echo $size; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			
			<p>
				<label for="file_gallery_single_linkto"><?php _e("link to", "file-gallery"); ?>:</label>
				<select name="file_gallery_single_linkto" id="file_gallery_single_linkto">
					<option value="none"<?php if( "none" == $file_gallery_options["single_default_linkto"]){ ?> selected="selected"<?php } ?>><?php _e("nothing (do not link)", "file-gallery"); ?></option>
					<option value="file"<?php if( "file" == $file_gallery_options["single_default_linkto"]){ ?> selected="selected"<?php } ?>><?php _e("file", "file-gallery"); ?></option>
					<option value="attachment"<?php if( "attachment" == $file_gallery_options["single_default_linkto"]){ ?> selected="selected"<?php } ?>><?php _e("attachment page", "file-gallery"); ?></option>
				</select>
			</p>
			
			<p id="file_gallery_single_linkclass_label">
				<label for="file_gallery_single_linkclass"><?php _e("link class", "file-gallery"); ?>:</label>
				<input type="text" name="file_gallery_single_linkclass" id="file_gallery_single_linkclass" value="<?php echo $file_gallery_options["single_default_linkclass"]; ?>" />
			</p>
			
			<p>
				<label for="file_gallery_single_imageclass"><?php _e("image class", "file-gallery"); ?>:</label>
				<input type="text" name="file_gallery_single_imageclass" id="file_gallery_single_imageclass" value="<?php echo $file_gallery_options["single_default_imageclass"]; ?>" />
			</p>
			
			<br />
			
			<input type="button" id="file_gallery_send_single" value="<?php _e("single files", "file-gallery"); ?>" class="button-primary" />&nbsp;
		</div>
		
	</fieldset>
	
	<fieldset id="file_gallery_tag_attachment_switcher">
	
		<input type="button" id="file_gallery_switch_to_tags" value="<?php _e("Switch to tags", "file-gallery"); ?>" class="button" />
		<input type="hidden" id="files_or_tags" value="<?php echo $files_or_tags; ?>" />
	
	</fieldset>
	
	<?php endif; ?>

	<div id="file_gallery_attachment_list">
		<?php echo file_gallery_list_attachments($count_attachments, $post_id, $attachment_order, $checked_attachments); ?>
	</div>
	
	<div id="file_gallery_tag_list">
		<p id="fg_media_tag_list"><?php echo implode(" ", array_unique(file_gallery_list_tags())); ?></p>
	</div>
	
</form>

<?php

// prints number of attachments
$print_attachment_count = __("File Gallery &mdash; %d attachment.", "file-gallery");

if( 0 == $count_attachments || $count_attachments > 1 )
	$print_attachment_count = __("File Gallery &mdash; %d attachments.", "file-gallery");

echo '<script type="text/javascript">
		jQuery("#file_gallery .hndle").html("<span>' . sprintf($print_attachment_count, $count_attachments) . '</span>");
	  </script>';
?>