jQuery(document).ready(function()
{
	function file_gallery_clear_cache_manual()
	{
		var admin_url = ajaxurl.split("/admin-ajax.php").shift(),
			data;
		
		jQuery('#file_gallery_response').stop().fadeTo(0, 1).html('<img src="' + admin_url + '/images/loading.gif" width="16" height="16" alt="loading" id="fg_loading_on_bar" />').show();
		
		data = {
			action			 : "file_gallery_clear_cache_manual",
			_ajax_nonce		 : file_gallery_clear_cache_nonce
		};
		
		jQuery.post
		(
			ajaxurl, 
			data,
			function(response)
			{
				jQuery('#file_gallery_response').html(response).fadeOut(7500);
			},
			"html"
		);
	}
	
	jQuery("#file_gallery_clear_cache_manual").live("click", function()
	{
		file_gallery_clear_cache_manual();
		
		return false;
	});
});