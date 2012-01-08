<?php

function file_gallery_admin_head( $class )
{
	if( isset($_GET['file_gallery']) || false !== strpos($_SERVER['HTTP_REFERER'], 'file_gallery=true') )
	{
		?>
		<style type="text/css">
			html,
			#media-upload
			{
				background: transparent !important;
			}
			
			#media-upload
			{
				min-width: 0;
			}
			
			#media-items
			{
				margin-top: 15px;
				width: 99%;
			}
			
			#media-upload-header,
			.savebutton,
			h3.media-title
			{
				display: none !important;
			}
			
			.drag-drop #drag-drop-area
			{
				background: #FFF;
				text-align: center;
			}
			
			.media-upload-form
			{
				margin: 0;
			}
		</style>
		<?php
	}
}
add_action( 'admin_head', 'file_gallery_admin_head' );

function file_gallery_post_upload_ui()
{
	if( isset($_GET['file_gallery']) )
	{
		?>
		<script type="text/javascript">
			var topWin = window.dialogArguments || opener || parent || top, file_gallery_plupload;
			
			jQuery(document).ready(function()
			{
				uploader.bind("FilesAdded", function(up, files) {
					jQuery(".drag-drop").slideUp(300);
				});
				
				uploader.bind("UploadComplete", function(up, files) {
					topWin.file_gallery.init( "efreshed" );
				});
			});
		</script>
		<?php
	}
}
add_action( 'post-upload-ui', 'file_gallery_post_upload_ui' );

?>