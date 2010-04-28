<?php
	$starttag = "div";
	$cleartag = "\n<br class=\"clear\" />\n";
?>
<dl class="gallery-item<?php echo $endcol; ?>">
	<dt class="gallery-icon">
	<?php if( "" != $link ) : ?>
		<a href="<?php echo $link; ?>" title="<?php echo $title; ?>"<?php if("" != $link_class) : ?> class="<?php echo $link_class; ?>"<?php endif; ?>>
	<?php endif; ?>
			<img src="<?php echo $thumb_link; ?>" width="<?php echo $thumb_width; ?>" height="<?php echo $thumb_height; ?>" alt="<?php echo $title; ?>" class="attachment-<?php echo $size; ?><?php echo $image_class; ?>" />
	<?php if( "" != $link ) : ?>
		</a>
	<?php endif; ?>
	</dt>
	<?php if( "" != $caption ) : ?>
		<dd class="gallery-caption"><?php echo $caption; ?></dd>
	<?php endif; ?>
</dl>
