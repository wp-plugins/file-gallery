<?php

/**
 * Collects template names from theme folder
 */
function file_gallery_get_templates()
{
	$options = get_option("file_gallery");
	
	if( isset($options["cache"]) && true == $options["cache"] )
	{		
		$transient = "filegallery_templates";
		$cache     = get_transient($transient);
		
		if( $cache )
			return $cache;
	}
	
	$file_gallery_templates = array();
	
	// check if file gallery templates folder exists within theme folder
	if( is_readable(FILE_GALLERY_THEME_TEMPLATES_ABSPATH) )
	{
		$opendir = opendir(FILE_GALLERY_THEME_TEMPLATES_ABSPATH);
		
		while( false !== ($files = readdir($opendir)) )
		{
			if( "." != $files && ".." != $files )
				$file_gallery_templates[] = $files;
		}
		
		closedir($opendir);
		
		$file_gallery_templates = array_unique($file_gallery_templates);
	}
	else
	{
		file_gallery_write_log( __("No templates found in theme folder (or in user supplied folder via filter). Provided location: ") . FILE_GALLERY_THEME_TEMPLATES_ABSPATH);
	}
	
	// check whether gallery.php and gallery.css exist within each template folder
	foreach( $file_gallery_templates as $key => $file_gallery_template )
	{
		$tf = FILE_GALLERY_THEME_TEMPLATES_ABSPATH . "/" . $file_gallery_template;
		
		if( !( is_readable($tf . "/gallery.php") && is_readable($tf . "/gallery.css") ) )
			unset($file_gallery_templates[$key]);
	}
	
	$default_templates = maybe_unserialize(FILE_GALLERY_DEFAULT_TEMPLATES);
	
	foreach( $default_templates as $df )
	{
		$file_gallery_templates[] = $df;
	}
	
	if( isset($options["cache"]) && true == $options["cache"] )
		set_transient($transient, $file_gallery_templates, $options["cache_time"]);
	
	return $file_gallery_templates;
}



/**
 * Injects CSS links via 'stylesheet_uri' filter, if mobile theme is active
 */
function file_gallery_mobile_css( $stylesheet_url )
{
	file_gallery_css_front( true );
	
	$mobiles = maybe_unserialize(FILE_GALLERY_MOBILE_STYLESHEETS);

	if( !empty($mobiles) )
	{
		array_push($mobiles, $stylesheet_url);
		$glue = '" type="text/css" media="screen" charset="utf-8" />' . "\n\t" . '<link rel="stylesheet" href="';
		return implode($glue, $mobiles);
	}
	
	return $stylesheet_url;	
}



/**
 * Enqueues stylesheets for each gallery template
 */
function file_gallery_css_front( $mobile = false )
{
	global $wp_query;

	$options = get_option("file_gallery");

	// if option to show galleries in excerpts is set to false
	if( !is_single() && "1" != $options["in_excerpt"] && false == $mobile )
		return;
	
	$gallery_matches = 0;
	$missing = array();
	$mobiles = array();
	
	// check for gallery shortcode in all posts
	if( !empty($wp_query->posts) )
	{
		foreach( $wp_query->posts as $post )
		{
			$m = preg_match_all("#\[gallery([^\]]*)\]#is", $post->post_content, $g);

			// if there's a match...
			if( false !== $m && 0 < $m )
			{
				$gallery_matches += $m; // ...add the number of matches to global count...
				$galleries = $g[0];   // ...and add the match to galleries array
			}
		}
	}
	
	// no matches...
	if( 0 === $gallery_matches )
		return;

	if( ! $mobile )
		wp_enqueue_style( "file_gallery_columns", FILE_GALLERY_URL . "/templates/columns.css" );
	else
		$mobiles[] = FILE_GALLERY_URL . "/templates/columns.css";
	
	// automaticaly enqueue predefined scripts and styles
	$aqs = explode(",", $options["auto_enqueued_scripts"]);
	$aqs = array_filter($aqs, "trim");
	$aq_linkclasses = array();

	// collect template names
	foreach($galleries as $gallery)
	{
		$tm = preg_match("#\stemplate=(['\"])([^'\"]+)\\1#is", $gallery, $gm);

		if( isset($gm[2]) )
			$templates[] = $gm[2];

		$gcm = preg_match("#\slinkclass=(['\"])([^'\"]+)\\1#is", $gallery, $gcg);
		$glm = preg_match("#\slink=(['\"])([^'\"]+)\\1#is", $gallery, $glg);
		
		if( isset($gcg[2]) && "" != $gcg[2] && isset($glg[2]) && "file" == $glg[2] )
		{
			$glc = explode(" ", $gcg[2]);

			foreach( $glc as $glcs )
			{
				$glcs = trim($glcs);
				
				if( false !== strpos( implode(" ", $aqs), $glcs) )
					$aq_linkclasses[] = $glcs;
			}
		}
	}

	$aq_linkclasses = apply_filters("file_gallery_lightbox_classes", array_unique($aq_linkclasses));
	
	// auto enqueue scripts
	if( ! empty($aq_linkclasses) )
	{
		define("FILE_GALLERY_LIGHTBOX_CLASSES", serialize($aq_linkclasses));

		file_gallery_print_scripts( true );
	}
	
	if( empty($templates) )
	{
		// enqueue only the default stylesheet if no template names are found
		if( ! $mobile )
			wp_enqueue_style( "file_gallery_default", FILE_GALLERY_URL . "/templates/default/gallery.css" );
		else
			$mobiles[] = FILE_GALLERY_URL . "/templates/default/gallery.css";
	}
	else
	{
		if( count($templates) < count($galleries) )
			$templates[] = "default";

		// eliminate duplicate entries
		$templates = array_unique($templates);
		
		foreach($templates as $template)
		{
			// check if file exists and enqueue it if it does
			if( is_readable(FILE_GALLERY_THEME_TEMPLATES_ABSPATH . "/" . $template . "/gallery.css") )
			{
				if( ! $mobile )
					wp_enqueue_style( "file_gallery_" . str_replace(" ", "-", $template), FILE_GALLERY_THEME_TEMPLATES_URL . "/" . str_replace(" ", "%20", $template) . "/gallery.css" );
				else
					$mobiles[] = FILE_GALLERY_THEME_TEMPLATES_URL . "/" . str_replace(" ", "%20", $template) . "/gallery.css";
			}
			elseif( is_readable(FILE_GALLERY_ABSPATH . "/templates/" . $template . "/gallery.css") )
			{
				if( ! $mobile )
					wp_enqueue_style( "file_gallery_" . $template, FILE_GALLERY_URL . "/templates/" . $template . "/gallery.css" );
				else
					$mobiles[] = FILE_GALLERY_URL . "/templates/" . $template . "/gallery.css";
			}
			else
			{
				$missing[] = $template;
				echo "<!-- " . __("file does not exist:", "file-gallery") . " " . $template . "/gallery.css - " . __("using default style", "file-gallery")  . "-->\n";
			}
		}
	}
	
	if( $mobile )
		define("FILE_GALLERY_MOBILE_STYLESHEETS", serialize($mobiles));
}
add_action('wp_print_styles', 'file_gallery_css_front');


/**
 * prints scripts and styles for autoqueue linkclasses
 */
function file_gallery_print_scripts( $styles = false )
{
	if( defined("FILE_GALLERY_LIGHTBOX_CLASSES") )
	{
		$linkclasses = maybe_unserialize(FILE_GALLERY_LIGHTBOX_CLASSES);

		if( !empty($linkclasses) )
		{
			foreach( $linkclasses as $lc )
			{
				if( $styles )
				{
					wp_enqueue_style( $lc );
				}
				else
				{
					if( "thickbox" == $lc )
					{
echo "\n" . 
'<script type="text/javascript">
	var tb_pathToImage = "' . includes_url() . 'js/thickbox/loadingAnimation.gif";
	var tb_closeImage  = "' . includes_url() . 'js/thickbox/tb-close.png";
</script>'
. "\n";
					}
					
					wp_enqueue_script( $lc );
				}
			}
		}
	}
}
add_action('wp_print_scripts', 'file_gallery_print_scripts');



/**
 * Main shortcode function
 */
function file_gallery_shortcode( $attr )
{
	global $wp, $wpdb, $post;
		
	if( !isset($wp->file_gallery_gallery_id) )
		$wp->file_gallery_gallery_id = 1;
	else
		$wp->file_gallery_gallery_id++;
	
	$options = get_option("file_gallery");

	if( isset($options["cache"]) && true == $options["cache"] )
	{
		if( "html" == $attr["output_type"] || ( isset($options["cache_non_html_output"]) && true == $options["cache_non_html_output"] ) )
		{
			$transient = 'filegallery_' . md5( $post->ID . "_" . serialize($attr) );
			$cache     = get_transient($transient);
			
			if( $cache )
				return $cache;
		}
	}

	// if option to show galleries in excerpts is set to false...
	// ...replace [gallery] with user selected text
	if( !is_single() && "1" != $options["in_excerpt"] )
		return $options["in_excerpt_replace_content"];
	
	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if( isset($attr['orderby']) )
	{
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		
		if ( !$attr['orderby'] )
			unset($attr['orderby']);
	}
	
	// extract the defaults...
	extract(
		shortcode_atts(
			array(
				/* default values: */
				'order'      => 'ASC',
				'orderby'    => '',
				'id'         => $post->ID,
				'columns'    => 3,
				'size'       => 'thumbnail',
				'link'		 => 'attachment',
				
				/* added by file gallery: */
				'template'	 => 'default',
				'attachment_ids' => '',
				'linkclass' => '',
				'imageclass' => '',
				'tags' => '',
				'tags_from' => 'current',
				'output_type' => 'html',
				'output_params' => true // needed when outputting html
				//'limit' => '', //not implemented yet
				//'content_type' => '', //not implemented yet
			)
	, $attr));
	
	$linkto = $link;
	
	// start with tags because they negate attachment_ids
	if( "" != $tags )
	{
		$tags = str_replace(",", "','", $tags);
		
		$fg_my_query = 
			   sprintf("SELECT * FROM $wpdb->posts 
						LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
						LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
						LEFT JOIN $wpdb->terms ON($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id)
						WHERE $wpdb->posts.post_type = 'attachment' 
						AND $wpdb->term_taxonomy.taxonomy='" . FILE_GALLERY_MEDIA_TAG_NAME . "'
						AND ($wpdb->terms.name IN ('%s') OR $wpdb->terms.slug IN ('%s'))",
						$tags, $tags);
		
		if( "current" == $tags_from )
			$fg_my_query .= sprintf(" AND $wpdb->posts.post_parent = '%d' ", $id);
		
		if( "" != $orderby )
		{
			if( "rand" == $orderby )
			{
				$orderby = "RAND()";
				$order = "";
			}
			
			$fg_my_query .= sprintf(" ORDER BY %s %s", $orderby, $order); // beats array shuffle only if LIMIT isn't set
		}
		
		$attachments = $wpdb->get_results( $fg_my_query );
	}
	elseif( "" != $attachment_ids )
	{
		$attachment_ids = trim($attachment_ids, ",");
		$attachment_ids = explode(",", $attachment_ids);
		$sql_limit      = count($attachment_ids);

		if( "rand" == $orderby )
			shuffle($attachment_ids);
			
		$attachment_ids = implode(",", $attachment_ids);

		if( "" == $orderby || "rand" == $orderby )
		{
			$orderby = sprintf("FIELD(ID,'%s')", str_replace(",", "','", $attachment_ids));
			$order   = "";
		}
		elseif( "title" == $orderby )
		{
			$orderby = "$wpdb->posts.post_title";
		}
		
		$query = sprintf("SELECT * FROM $wpdb->posts 
						  WHERE $wpdb->posts.ID IN (%s) 
						  	AND $wpdb->posts.post_type = 'attachment' 
						  ORDER BY %s %s 
						  LIMIT %d", 
					$attachment_ids, $orderby, $order, $sql_limit);
		
		$attachments = $wpdb->get_results($query);
	}
	else
	{
		// default orderby without attachment_ids is set here
		if( "" == $orderby )
			$orderby = "menu_order ID";
		
		$attachments = get_children(
							array('post_parent' => $id, 
								  'post_type' => 'attachment', 
								  'order' => $order, 
								  'orderby' => $orderby//,
								  // 'post_status' => 'inherit', // is this really needed?
								  // 'post_mime_type' => 'image' // gotta add "these filetypes only" option...
		));
	}

	if( empty($attachments) )
		return '';

	// feed
	if ( is_feed() )
	{
		$output = "\n";

		foreach ( $attachments as $id => $attachment )
		{
			$output .= wp_get_attachment_link($id, $size, true) . "\n";
		}
		
		return $output;
	}
	
	if( "file-gallery" != $template && "default" != $template && "list" != $template )
		$template_file = FILE_GALLERY_THEME_TEMPLATES_ABSPATH . '/' . $template . '/gallery.php';
	else
		$template_file = FILE_GALLERY_ABSPATH . '/templates/' . $template . '/gallery.php';
	
	// check if template exists and replace with default if it does not
	if( !is_readable($template_file) )
	{
		$template_file = FILE_GALLERY_ABSPATH . '/templates/default/gallery.php';
		$template      = "default";
	}
	
	$i = 0;
	$unique_ids = array();
	$gallery_items = "";
	
	if( "object" == $output_type || "array" == $output_type )
		$gallery_items = array();
	
	$aqlc = array();
	
	if( defined("FILE_GALLERY_LIGHTBOX_CLASSES") )
		$aqlc = maybe_unserialize(FILE_GALLERY_LIGHTBOX_CLASSES);
	
	// create output
	foreach($attachments as $attachment)
	{
		/*
		//filter out duplicates?
		if( !in_array($attachment->ID, $unique_ids) )
			$unique_ids[] = $attachment->ID;
		else
			continue;
		*/
		
		$param = array('image_class' => " " . $imageclass,
					   'link_class' => " " . $linkclass,
					   'rel' => "",
					   'title'  => "",
					   'cation'  => "",
					   'description'  => "",
					   'thumb_alt' => ""
					   );

		$attachment_file = get_attached_file($attachment->ID);
		$attachment_is_image = file_gallery_file_is_displayable_image($attachment_file);
		$endcol = "";
		$x = "";
		
		if( $output_params )
		{
			$plcai = array_intersect($aqlc, explode(" ", trim($linkclass)));
			
			if( !empty($plcai) && "file" == $linkto )
			{
				if( $attachment_is_image )
				{
					$param['rel']         = apply_filters("file_gallery_lightbox_linkrel", $plcai[0] . "[" .  $wp->file_gallery_gallery_id . "]", $wp->file_gallery_gallery_id);
					$param['link_class']  = apply_filters("file_gallery_lightbox_linkclass", $param['link_class'], $wp->file_gallery_gallery_id);
					$param['image_class'] = apply_filters("file_gallery_lightbox_imageclass", $param['image_class'], $param['link_class'], $wp->file_gallery_gallery_id);
				}
				else
				{
					$param['link_class'] = str_replace( trim(implode(" ", $plcai)), "", trim($linkclass));
				}
			}
			
			switch( $linkto )
			{
				case "parent_post" :
					$param['link'] = get_permalink( $wpdb->get_var("SELECT post_parent FROM $wpdb->posts WHERE ID = '" . $attachment->ID . "'") );
					break;
				case "file" :
					$param['link'] = wp_get_attachment_url($attachment->ID);
					break;
				case "attachment" :
					$param['link'] = get_attachment_link($attachment->ID);
					break;
				case "none" :
				default :
					$param['link'] = "";
					break;
			}
						
			$param['title'] 		= $attachment->post_title;
			$param['caption'] 		= $attachment->post_excerpt;
			$param['description'] 	= $attachment->post_content;
			
			if( $attachment_is_image )
			{
				$thumb_src             = wp_get_attachment_image_src($attachment->ID, $size);
				$param['thumb_link']   = $thumb_src[0];
				$param['thumb_width']  = 0 == $thumb_src[1] ? file_gallery_get_image_size($param['thumb_link']) : $thumb_src[1];
				$param['thumb_height'] = 0 == $thumb_src[2] ? file_gallery_get_image_size($param['thumb_link'], true) : $thumb_src[2];
			}
			else
			{
				$param['thumb_link']   = get_bloginfo('wpurl') . "/" . WPINC . "/images/crystal/" . file_gallery_get_file_type($attachment->post_mime_type) . ".png";
				$param['thumb_width']  = "46";
				$param['thumb_height'] = "60";
			}

			if( $thumb_alt = get_post_meta($attachment->ID, "_wp_attachment_image_alt", true) )
				$param['thumb_alt'] = $thumb_alt;
		}
		
		$param = array_map("trim", $param);
		
		if( "object" == $output_type )
		{
			if( $output_params )
				$attachment->params = (object) $param;
			
			$gallery_items[] = $attachment;
		}
		elseif( "array" == $output_type || "json" == $output_type)
		{
			if( $output_params )
				$attachment->params = $param;
			
			$gallery_items[] = get_object_vars($attachment);
		}
		else
		{
			// add the column break class and append a line break...
			if ( $columns > 0 && ++$i % $columns == 0 )
				$endcol = " gallery-endcol";
			
			// parse template
			ob_start();
			
				extract( $param );
				include($template_file);
				$x = ob_get_contents();
				
			ob_end_clean();
			
			if ( $columns > 0 && $i % $columns == 0 )
				$x .= $cleartag;
			
			$gallery_items .= $x;
		}
	}

	// handle data types
	if( "object" == $output_type || "array" == $output_type )
	{
		$output = $gallery_items;
	}
	elseif( "json" == $output_type )
	{
		$output = "{" . json_encode($gallery_items) . "};";
	}
	else
	{
		$cols = "";
		
		if( 0 < $columns && "" != $columns )
			$cols = " columns_" . $columns;
		
		$trans_append = "\n<!-- file gallery output cached on " . date("Y.m.d @ H:i:s", time()) . "-->\n";
		
		$output = "<" . $starttag . " class=\"gallery " . str_replace(" ", "-", $template) . $cols . "\">\n" . $gallery_items . "\n</" . $starttag . ">";
	}
	
	if( isset($options["cache"]) && true == $options["cache"] )
	{
		if( "html" == $output_type )
			set_transient($transient, $output . $trans_append, $options["cache_time"]); // with a comment appended to the end of cached output
		elseif( isset($options["cache_non_html_output"]) && true == $options["cache_non_html_output"] )
			set_transient($transient, $output, $options["cache_time"]);
	}
	
	return $output;
}
add_shortcode('gallery', 'file_gallery_shortcode');

?>