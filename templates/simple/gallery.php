<?php
	$starttag = "div";
	$cleartag = "\n<br class='clear' />\n";
	$js_dependencies = array("jquery");
	$linkto = "file";
	
	if( 1 === $file_gallery_this_template_counter )
		echo '<script type="text/javascript">var file_gallery_loading_img = "' . FILE_GALLERY_URL . '/images/loading.gif"</script>';
?>
<dl class="gallery-item<?php echo $endcol; ?>">
	<dt class="gallery-icon">
	<?php if( "" != $link ) : ?>
		<a href="<?php echo $link; ?>" title="<?php echo $caption; ?>"<?php if("" != $link_class) : ?> class="<?php echo $link_class; ?>"<?php endif; ?><?php if("" != $rel) : ?> rel="<?php echo $rel; ?>"<?php endif; ?>>
	<?php endif; ?>
			<img src="<?php echo $thumb_link; ?>" width="<?php echo $thumb_width; ?>" height="<?php echo $thumb_height; ?>" title="<?php echo $title; ?>" class="attachment-<?php echo $size ?><?php if( "" != $image_class ){ echo " " . $image_class;} ?>" alt="<?php if( $thumb_alt ){ echo $thumb_alt; }else{ echo $title; }?><?php ?>" />
	<?php if( "" != $link ) : ?>
		</a>
	<?php endif; ?>
	</dt>
</dl>
