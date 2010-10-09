jQuery(document).ready(function()
{
	if( 0 < jQuery(".gallery.simple").length )
	{
		var file_gallery_doing_ajax = false;
		
		jQuery(".gallery.simple br.clear").remove();
		
		jQuery(".gallery.simple").map(function()
		{
			var file_gallery_simple_current_image = 1,
				id = "#" + jQuery(this).attr("id");
			
			jQuery(".gallery.simple .gallery-item").map(function()
			{
				if( 1 === file_gallery_simple_current_image )
				{
					var current_anchor = jQuery(id + " .gallery-item a:first"),
						current_image_src = current_anchor.attr("href"),
						current_caption = current_anchor.attr("title");

					jQuery(id).prepend('<div class="gallery_simple_thumbnails"></div>');
					jQuery(id).prepend('<div class="gallery_simple_current"></div>');
					jQuery(".gallery_simple_current").append('<img src="' + file_gallery_loading_img + '" width="16" height="16" alt="" class="file_gallery_simple_loading" style="display: none;" /><img src="' + current_image_src + '" class="gallery_simple_current_image" style="display: none;" /><div class="gallery_simple_current_image_caption"><p>' + current_caption + '</p></div>');
					jQuery(".gallery_simple_current_image_caption").css({"opacity":0}).fadeTo(500, 1);
					jQuery(".gallery_simple_current_image").css({"opacity":0}).fadeTo(500, 1);
				}
			
				jQuery(this).appendTo(".gallery_simple_thumbnails");

				file_gallery_simple_current_image++;
			});
		});
		
		jQuery(".gallery.simple .gallery-item a").live("click", function()
		{
			if( file_gallery_doing_ajax )
				return false;

			file_gallery_doing_ajax = true;
			
			var id = "#" + jQuery(this).parents(".gallery").attr("id"),
				new_src = jQuery(this).attr("href"),
				new_caption = jQuery(this).attr("title"),
				new_img = new Image();

			new_img.src = new_src;
			
			jQuery(".file_gallery_simple_loading").css({"opacity":0}).fadeTo(250, 1);
			
			jQuery(new_img).load(function()
			{
				jQuery(".file_gallery_simple_loading").fadeTo(250, 0);
				jQuery(id + " .gallery_simple_current_image_caption").fadeTo(250, 0);
				jQuery(id + " .gallery_simple_current_image").fadeTo(250, 0);
				setTimeout(
					function()
					{
						jQuery(id + " .gallery_simple_current_image_caption p").text(new_caption).parent().fadeTo(250, 1);
						jQuery(id + " .gallery_simple_current_image").attr("src", new_src).fadeTo(250, 1);
						file_gallery_doing_ajax = false;
					}
				, 250);});
			
			return false;
		});
	}
});