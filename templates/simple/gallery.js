jQuery(document).ready(function()
{
	var file_gallery_simple_gallery_counter = 1;
	
	if( 0 < jQuery(".gallery.simple").length )
	{
		var file_gallery_doing_ajax = false;
		
		// remove the clearing element
		jQuery(".gallery.simple br.clear").remove();
		
		// go through each gallery...
		jQuery(".gallery.simple").each(function()
		{
			var file_gallery_simple_current_image = 1,
				id = "#" + jQuery(this).attr("id");
			
			// and each item in gallery
			jQuery(id + " .gallery-item").each(function()
			{
				// if it's the first run...
				if( 1 === file_gallery_simple_current_image )
				{
					var current_anchor = jQuery(this).find("a:first-child"),
						current_image_src = decodeURIComponent(current_anchor.attr("name")),
						current_image_href = current_anchor.attr("href"),
						current_caption = current_anchor.attr("title");

					// add two containers, one for the thumbnails on the right and the other one for the bigger image on the left
					jQuery(id).prepend('<div class="gallery_simple_thumbnails"></div>').prepend('<div class="gallery_simple_current"></div>');
					
					// append the linked bigger image and its caption on the left
					jQuery(id + " .gallery_simple_current")
						.append('<img src="' + file_gallery_loading_img + '" width="16" height="16" alt="" class="file_gallery_simple_loading" style="display: none; border: none;" /><a href="' + current_image_href + '" title="' + current_caption + '"><img src="' + current_image_src + '" class="gallery_simple_current_image colorbox-' + file_gallery_simple_gallery_counter + '" style="display: none;" /></a><div class="gallery_simple_current_image_caption"><p>' + current_caption + '</p></div>');
					
					if( current_anchor.hasClass("colorbox") )
						jQuery(id + " .gallery_simple_current a").addClass("cboxElement");
					
					// and fade in the image and its caption
					jQuery(id + " .gallery_simple_current_image_caption").css({"opacity":0}).fadeTo(500, 1);
					jQuery(id + " .gallery_simple_current_image").css({"opacity":0}).fadeTo(500, 1);
				}

				// move all gallery items into the thumbnails container
				jQuery(this).appendTo(id + " .gallery_simple_thumbnails");
				// advance .gallery-item counter
				file_gallery_simple_current_image++;
			});
			
			// advance gallery counter
			file_gallery_simple_gallery_counter++;
		});
		
		// bind a function to each thumbnail link to replace the bigger image on the left
		jQuery(".gallery.simple .gallery-item a").live("click", function()
		{
			if( file_gallery_doing_ajax )
				return false;
			
			// ajax (not technically, but hey:)) in progress
			file_gallery_doing_ajax = true;
			
			var id = "#" + jQuery(this).parents(".gallery").attr("id"),
				new_src = decodeURIComponent(jQuery(this).attr("name")),
				new_href = jQuery(this).attr("href"),
				new_caption = jQuery(this).attr("title"),
				new_img = new Image();

			new_img.src = new_src;
			
			// fade in the loading animation while fading out the old image and its caption
			jQuery(id + " .file_gallery_simple_loading").css({"opacity":0}).fadeTo(250, 1);
			jQuery(id + " .gallery_simple_current_image_caption").fadeTo(250, 0);
			jQuery(id + " .gallery_simple_current_image").fadeTo(250, 0);
			
			// when the new bigger image is loaded...
			jQuery(new_img).load(function()
			{
				// fade out the loading animation
				jQuery(id + " .file_gallery_simple_loading").fadeTo(250, 0);
				
				// wait for the loading animation to fade out and then...
				setTimeout(
					function()
					{
						// replace caption text and fade it in
						jQuery(id + " .gallery_simple_current_image_caption p").text(new_caption).parent().fadeTo(250, 1);
						// replace bigger image source and fade it in, then replace link location and image caption
						jQuery(id + " .gallery_simple_current_image").attr("src", new_src).fadeTo(250, 1).parents("a").attr("href", new_href).attr("title", new_caption);
						// ajax no longer in process
						file_gallery_doing_ajax = false;
					}
				, 250);});
			
			return false;
		});
		// remove colorbox class from thumbnail links...
		jQuery(".gallery_simple_thumbnails a").removeClass("cboxElement");
		// and bind colorbox to the bigger image
		jQuery("a.cboxElement").colorbox();
	}
});