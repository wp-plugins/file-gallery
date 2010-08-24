var file_gallery_gallery_img_clicked = false,
	file_gallery_tmp = 1;



/**
 * main to be extended
 */
function file_gallery()
{
};



jQuery(document).ready(function()
{	
	/**
	 * extending the main function
	 */
	jQuery.extend( file_gallery,
	{
		/**
		 * takes care of communication with tinyMCE
		 */
		tinymce : function()
		{			
			// get editor instance
			var ed = tinymce.EditorManager.get('content');
			
			if( ! ed )
			{
				setTimeout(function(){ file_gallery.tinymce(); }, 200);
				return false;
			}
			
			// trigger file_gallery.tinymce_gallery() if clicked-on image has a wpGallery class
			ed.onClick.add( function(tinymce_object, mouseEvent)
			{
				if( mouseEvent.target.className.match(/wpGallery/) )
				{
					file_gallery.tinymce_gallery( mouseEvent.target.title );
					file_gallery_gallery_img_clicked = true;
				}
				else
				{
					// uncheck all items and serialize()
					jQuery("#file_gallery_uncheck_all").trigger("click");
					file_gallery_gallery_img_clicked = false;
				}
			});

			// clear options on delete
			ed.onEvent.add(function(ed, e)
			{
				if( 46 === e.keyCode && 'keyup' == e.type && file_gallery_gallery_img_clicked )
				{					
					jQuery("#file_gallery_uncheck_all").trigger("click");
					file_gallery_gallery_img_clicked = false;
				}
			});
			
		},
		
		
		
		/**
		 * updates the contents of [gallery] shortcode
		 */
		tinymce_change_gallery_content : function( serial )
		{
			// skips setContent for webkit browsers if tinyMCE version is below 3.3.6
			if( (! jQuery.browser.webkit && ! jQuery.browser.safari) || (3 <= parseFloat(tinymce.majorVersion) && 3.6 <= parseFloat(tinymce.minorVersion)) )
			{
				var ed = tinymce.EditorManager.get('content'),
					new_content = serial.replace(/\[gallery([^\]]*)\]/g, function(a,b)
					{
						return "<img src='" + tinymce.baseURL + "/plugins/wpgallery/img/t.gif' class='wpGallery mceItem' title='gallery" + tinymce.DOM.encode(b) + "' id='file_gallery_tmp_" + file_gallery_tmp + "' />";
					});
				
				ed.selection.setContent(new_content);
				
				ed.selection.select(ed.getDoc().getElementById('file_gallery_tmp_' + file_gallery_tmp));
				tinyMCE.execCommand('mceFocus', false, 'content');
				
				file_gallery_tmp++;
			}
		},
		
		
		
		/**
		 * sets up the file gallery options when clicked on a gallery already
		 * inserted into visual editor
		 */
		tinymce_gallery : function( title )
		{
			var opt = title.replace("gallery ", "").replace(/"/g, "'"),
				attachment_ids = opt.match(/attachment_ids='([^']+)'/),
				attachment_includes = opt.match(/include='([^']+)'/),
				size = opt.match(/size='([^']+)'/),
				linkto = opt.match(/link='([^']+)'/),
				thelink = linkto ? linkto[1] : 'attachment',
				linkrel = opt.match(/rel='([^']+)'/),
				external_url = '',
				template = opt.match(/template='([^']+)'/),
				order = opt.match(/order='([^']+)'/),
				orderby = opt.match(/orderby='([^']+)'/),
				linkclass = opt.match(/linkclass='([^']+)'/),
				imageclass = opt.match(/imageclass='([^']+)'/),
				mimetype = opt.match(/mimetype='([^']+)'/),
				limit = opt.match(/limit='([^']+)'/),
				columns = opt.match(/columns='([^']+)'/),
				tags = opt.match(/tags='([^']+)'/),
				tags_from = opt.match(/tags_from='([^']+)'/);

			if( linkto && 'none' != thelink && 'file' != thelink && 'parent_post' != thelink )
			{
				external_url = decodeURIComponent(thelink);
				thelink = 'external_url';
			}
			
			jQuery("#file_gallery_size").val(size ? size[1] : 'thumbnail' );
			jQuery("#file_gallery_linkto").val( thelink );
			jQuery("#file_gallery_linkrel").val(linkrel ? linkrel[1] : 'true' );
			jQuery("#file_gallery_external_url").val( external_url );
			jQuery("#file_gallery_template").val(template ? template[1] : 'default' );
			jQuery("#file_gallery_order").val(order ? order[1] : 'ASC' );
			jQuery("#file_gallery_orderby").val(orderby ? orderby[1] : 'file gallery' );
			jQuery("#file_gallery_linkclass").val(linkclass ? linkclass[1] : '' );
			jQuery("#file_gallery_imageclass").val(imageclass ? imageclass[1] : '' );
			jQuery("#file_gallery_mimetype").val(mimetype ? mimetype[1] : '' );
			jQuery("#file_gallery_limit").val(limit ? limit[1] : '' );
			jQuery("#file_gallery_columns").val(columns ? columns[1] : '3' );
						
			if( tags )
			{
				jQuery("#fg_gallery_tags").val(tags[1]);
				jQuery("#files_or_tags").val("tags");
				file_gallery.files_or_tags( false );
				
				if( tags_from )
					jQuery("#fg_gallery_tags_from").attr("checked", false);
				else
					jQuery("#fg_gallery_tags_from").attr("checked", true);
				
				jQuery("#file_gallery_toggler").show();
			}
			else
			{
				jQuery("#files_or_tags").val("files");
				file_gallery.files_or_tags( false );
			}

			if( null !== attachment_ids )
				attachment_ids = attachment_ids[1].split(",");
			else if( null !== attachment_includes )
				attachment_ids = attachment_includes[1].split(",");
			else
				attachment_ids = 'all';
			
			if( 0 < jQuery('#file_gallery_list li').length )
			{
				jQuery("#file_gallery_uncheck_all").trigger("click_tinymce_gallery");
				
				jQuery('#fg_container .sortableitem .checker').map(function()
				{
					if( 'all' === attachment_ids )
						return this.checked = true;

					id = jQuery(this).attr("id").replace("att-chk-", "");
			
					if( -1 != attachment_ids.indexOf(id) )
						return this.checked = true;
				});
				
				file_gallery.serialize('tinymce_gallery');
			}
		},
		
		
		
		/*
		 * collapses selection if gallery placeholder is clicked
		 */
		tinymce_deselect : function()
		{
			if( false === file_gallery_gallery_img_clicked )
				return;

			var ed = tinymce.EditorManager.get('content');

			ed.selection.collapse(false);
			tinyMCE.execCommand('mceRepaint', false, 'content');
			tinyMCE.execCommand('mceFocus', false, 'content');
		},
		
		
		
		/*
		 * checks if all the attachments are, eh, checked...
		 */
		is_all_checked : function()
		{
			var all_checked = true;
			
			jQuery('#fg_container .sortableitem .checker').map(function()
			{
				if( ! this.checked )
				{
					all_checked = false;
					
					return;
				}
			});
			
			return all_checked;
		},
		
		
		
		/**
		 * loads main file gallery data via ajax
		 */
		start : function( response_message )
		{
			var tags_from = jQuery("#fg_gallery_tags_from").attr("checked"), 
				container = jQuery("#fg_container"), 
				fieldsets = jQuery("#file_gallery_fieldsets").val(),
				data = null;
			
			if( 'return_from_single_attachment' == response_message )
			{
				file_gallery.tinymce_deselect();
				
				response_message = null;
			}
			
			if( "undefined" == typeof(fieldsets) )
				fieldsets = "";
			
			if( true === tags_from || "undefined" == typeof( tags_from )|| "undefined" == tags_from )
				tags_from = "true";
			else
				tags_from = "false";

			data = {
					action				: "file_gallery_load",
					post_id 			: jQuery("#post_ID").val(),
					attachment_order 	: jQuery("#data_collector_full").val(),
					checked_attachments : jQuery("#data_collector_checked").val(),
					files_or_tags 		: jQuery("#files_or_tags").val(),
					tag_list 			: jQuery("#fg_gallery_tags").val(),
					tags_from 			: tags_from,
					fieldsets			: fieldsets,
					_ajax_nonce			: file_gallery_nonce
			};
			
			container
				.empty()
				.append('<p class="loading_image"><img src="' + file_gallery_url + '/images/ajax-loader.gif" alt="' + fgL10n["loading_attachments"] + '" /><br />' + fgL10n["loading_attachments"] + '<br /></p>')
				.css({ "height" : "auto" })
				.show();
			
			jQuery.post
			(
				ajaxurl, 
				data,
				function(response)
				{
					container.html(response);
					
					if( "undefined" != typeof( response_message ) && null !== response_message )
						jQuery('#file_gallery_response').html(response_message).show().fadeOut(7500);
					
					file_gallery.setup();
				},
				"html"
			);
		},
		
		
		
		/**
		 * some basic show / hide setup
		 */
		setup : function()
		{
			var container = jQuery("#fg_container"),
				files_or_tags = jQuery("#files_or_tags");
				
			if( 0 === container.length || 0 === files_or_tags.length  )
				return;
			
			num_attachments = jQuery("#fg_container #file_gallery_list li").length;
			
			container.css({"height" : "auto"});
			jQuery("#file_gallery_switch_to_tags").show();
			
			// hide elements if post has no attachments
			if( 0 === num_attachments )
			{
				jQuery("#file_gallery fieldset, #file_gallery_tag_attachment_switcher").hide();
				jQuery("#fg_info").html(fgL10n["no_attachments_upload"]).show();
				jQuery("#file_gallery_upload_files").show();
				container.css({"overflow":"hidden", "paddingBottom":"0"});
			}
			else
			{
				jQuery("#file_gallery fieldset, #file_gallery_tag_attachment_switcher").show();
				//jQuery("#fg_info").html(fgL10n["fg_info"]);
				container.css({"overflow":"auto"});
				jQuery("#file_gallery_upload_files").hide();
			}
			
			/* not taking non-attached files into consideration...
			if( (0 === num_attachments || 0 == jQuery("#file_gallery_list .post_thumb").length) && 0 < jQuery("#set-post-thumbnail").length && 0 < jQuery("#remove-post-thumbnail").length )
				WPRemoveThumbnail(post_thumb_nonce);
			*/
			
			// tags from current post only checkbox
			if( "false" == tags_from )
				jQuery("#fg_gallery_tags_from").attr("checked", false);
			else
				jQuery("#fg_gallery_tags_from").attr("checked", true);
			
			// clickable tags
			jQuery(".fg_insert_tag").each( function()
			{
				var ct = "," + jQuery("#fg_gallery_tags").val() + ",",
					ns = "," + jQuery(this).attr("name") + ",",
					nn = "," + jQuery(this).html() + ",";
				
				if ( "-1" != ct.search(ns) || "-1" != ct.search(nn) )
					jQuery(this).css({"color" : "#BBBBBB"});
			});
			
			// display tags or attachments
			if( "undefined" == typeof( files_or_tags.val() ) || "undefined" == files_or_tags.val() )
				files_or_tags.val("tags");
			
			// load files / tags respectively
			file_gallery.files_or_tags( true );

			file_gallery.do_plugins();
			
			file_gallery.serialize();
			
			file_gallery.tinymce();
			
			file_gallery.fieldset_toggle();
		},
		
		
		
		/**
		 * processes attachments data, builds the [gallery] shortcode
		 */
		serialize : function( internal_event )
		{
			var serial = "",
				size = "",
				linkto = "",
				linkrel = "",
				linkto_val = jQuery("#file_gallery_linkto").val(),
				external_url = jQuery("#file_gallery_external_url").val(),
				template = "",
				order = "",
				orderby = "",
				linkclass = "",
				imageclass = "",
				mimetype = "",
				limit = "",
				columns = "",
				tags = "",
				tags_from = "",
				ctlen = ""
				ct = "",
				ns = "",
				nn = "",
				copies = "",
				originals = "",
				file_gallery_order = "",
				file_gallery_orderby = "";

			if( 'undefined' == typeof(internal_event) )
				internal_event = 'normal';
			
			if( 'false' == jQuery("#file_gallery_linkrel").val() )
				linkrel = ' rel=false';
			
			if( "external_url" == linkto_val )
				linkto_val = encodeURIComponent(external_url);
				
			if( 0 < jQuery("#fg_gallery_tags").length )
			{
				if( "undefined" == typeof( jQuery("#fg_gallery_tags").val() ) || "undefined" == jQuery("#fg_gallery_tags").val() )
					jQuery("#fg_gallery_tags").val("");
				
				tags      = jQuery("#fg_gallery_tags").val();
				tags_from = jQuery("#fg_gallery_tags_from").attr("checked");
				
				tags = tags.replace(/\s+/g, " ").replace(/\s+,/g, ",").replace(/,+\s*/g, ",");
			
				ctlen = tags.length;
				
				if( "," == tags[0] )
					tags = tags.substring(1);
				
				if( "," == tags[ctlen-2] )
					tags = tags.substring(0, ctlen-1);
			
				jQuery("#fg_gallery_tags").val(tags);
				
				jQuery(".fg_insert_tag").each( function()
				{
					ct = "," + jQuery("#fg_gallery_tags").val() + ",";
					ns = "," + jQuery(this).attr("name") + ",";
					nn = "," + jQuery(this).html() + ",";
					
					if ( "-1" != ct.search(ns) || "-1" != ct.search(nn) )
						jQuery(this).css({"color" : "#BBBBBB"});
					else
						jQuery(this).css({"color" : "#21759B"});
				});
			}
			
			if( 0 < jQuery("#file_gallery_list li").length )
				serial = jQuery("#file_gallery_list").sortable("serialize");
			
			serial = serial.toString().replace(/image\[\]=/g, '').replace(/&/g, ',').replace(/,+/g, ',');
			jQuery("#data_collector_full").val(serial);
			
			// get checked items
			serial = file_gallery.map("checked", serial);
			jQuery("#data_collector_checked").val(serial);
			
			// get checked copies
			copies = file_gallery.map("copy", serial);
			jQuery("#file_gallery_copies").val(copies);
		
			// get checked originals
			originals = file_gallery.map("has_copies", serial);
			jQuery("#file_gallery_originals").val(originals);
			
			if( "" == jQuery("#file_gallery_originals").val() && "" == jQuery("#file_gallery_copies").val() )
				jQuery("#file_gallery_delete_what").val("all");
			
			file_gallery_order   = jQuery("#file_gallery_order");
			file_gallery_orderby = jQuery("#file_gallery_orderby");
			
			order = ' order="' + file_gallery_order.val() + '"';
				
			if( "default" != file_gallery_orderby.val() )
			{
				if( "rand" == file_gallery_orderby.val() )
				{
					file_gallery_order.hide();
					order = "";
				}
				else
				{
					file_gallery_order.css({"display" : "inline"});
				}
				
				orderby = ' orderby="' + file_gallery_orderby.val() + '"';
			}
			else
			{
				file_gallery_order.hide();
				order = "";
				orderby = "";
			}
			
			if( "external_url" == jQuery("#file_gallery_linkto").val() )
				jQuery("#file_gallery_external_url_label").show();
			else
				jQuery("#file_gallery_external_url_label").hide();
			
			if( "external_url" == jQuery("#file_gallery_single_linkto").val() )
				jQuery("#file_gallery_single_external_url_label").show();
			else
				jQuery("#file_gallery_single_external_url_label").hide();

			if( "none" == jQuery("#file_gallery_linkto").val() )
				jQuery("#file_gallery_linkclass_label").hide();
			else
				jQuery("#file_gallery_linkclass_label").show();
				
			if( "file" == jQuery("#file_gallery_linkto").val() || "external_url" == jQuery("#file_gallery_linkto").val())
				jQuery("#file_gallery_linkrel_label").show();
			else
				jQuery("#file_gallery_linkrel_label").hide();

			if( "none" == jQuery("#file_gallery_single_linkto").val() )
				jQuery("#file_gallery_single_linkclass_label").hide();
			else
				jQuery("#file_gallery_single_linkclass_label").show();
			
			if( tags_from )
				tags_from = "";
			else
				tags_from = ' tags_from="all"';
		
			if( "" != tags )
				serial = '[gallery tags="' + tags + '"' + tags_from;
			else if( "" != serial && false === file_gallery.is_all_checked() )
				serial = '[gallery include="' + serial + '"';
			else
				serial = '[gallery';
		
			if( "thumbnail" != jQuery("#file_gallery_size").val() )
				size = ' size="' + jQuery("#file_gallery_size").val() + '"';
			
			if( "attachment" != jQuery("#file_gallery_linkto").val() )
				linkto = ' link="' + linkto_val + '"';
		
			if( "default" != jQuery("#file_gallery_template").val() )
				template = ' template="' + jQuery("#file_gallery_template").val() + '"';
			
			if( "" != jQuery("#file_gallery_linkclass").val() && "none" != jQuery("#file_gallery_linkto").val() )
				linkclass = ' linkclass="' + jQuery("#file_gallery_linkclass").val() + '"';
			
			if( "" != jQuery("#file_gallery_imageclass").val() )
				imageclass = ' imageclass="' + jQuery("#file_gallery_imageclass").val() + '"';
			
			if( "" != jQuery("#file_gallery_mimetype").val() )
				mimetype = ' mimetype="' + jQuery("#file_gallery_mimetype").val() + '"';
				
			if( "" != jQuery("#file_gallery_limit").val() )
				limit = ' limit="' + jQuery("#file_gallery_limit").val() + '"';
			
			if( "" != jQuery("#file_gallery_columns").val() && "3" != jQuery("#file_gallery_columns").val() )
				columns = ' columns="' + jQuery("#file_gallery_columns").val() + '"';
			
			serial += size + linkto + linkclass + imageclass + mimetype + limit + order + orderby + template + columns + linkrel + "]\n";
			
			jQuery("#data_collector").val(serial);
			
			if( file_gallery_gallery_img_clicked && '' != tinymce.EditorManager.get('content').selection.getContent() && 'normal' == internal_event )
			{
				file_gallery.tinymce_change_gallery_content( serial );
			
				jQuery('#file_gallery_response').html("Gallery contents updated").show().fadeOut(1000);
			}
		},
		
		
		
		/**
		 * binds jquery plugins to objects
		 */
		do_plugins : function()
		{
			// set up draggable / sortable list of attachments
			jQuery("#file_gallery_list")
				.sortable(
				{
					placeholder : "ui-selected",
					tolerance   : "pointer",
					cursorAt    : "top",
					handle      : ".fgtt",
					items       : "li",
					opacity     : 0.6,
					start		: function(){ jQuery("#fg_container .fgtt").unbind("click.file_gallery") },
					update      : function(){ file_gallery.serialize(); }
				});
			
			// set up delete originals choice dialog
			jQuery("#delete_dialog")
				.dialog(
				{
					autoOpen  : false,
					closeText : fgL10n["close"],
					bgiframe  : true,
					resizable : false,
					width     : 600,
					modal     : true,
					draggable : false,
					close     : function(event, ui)
								{
									var id = jQuery("#delete_dialog").data("single_delete_id");
									jQuery("#detach_or_delete_" + id + ", #detach_attachment_" + id + ",#del_attachment_" + id).fadeOut(100);
								},
					buttons   :
					{
						"Cancel" : function()
						{
							var id = jQuery("#delete_dialog").data("single_delete_id");
							
							jQuery("#file_gallery_delete_what").val("data_only");
							jQuery("#detach_or_delete_" + id + ", #detach_attachment_" + id + ",#del_attachment_" + id).fadeOut(100);
							jQuery("#delete_dialog").removeData("single_delete_id");
							
							jQuery(this).dialog('close');
						},
						"Delete attachment data only" : function()
						{
							var message = false, id;
							
							if( jQuery(this).hasClass("single") )
							{
								id = jQuery("#delete_dialog").data("single_delete_id");
							}
							else
							{
								message = fgL10n["sure_to_delete"];
								id = jQuery('#data_collector_checked').val();
							}
							
							jQuery("#file_gallery_delete_what").val("data_only");
							file_gallery.delete_attachments( id, message );
							
							jQuery(this).dialog('close');
						},
						"Delete attachment data, its copies and the files" : function()
						{
							var message = false, id;
							
							if( jQuery(this).hasClass("single") )
							{
								id = jQuery("#delete_dialog").data("single_delete_id");
							}
							else
							{
								message = fgL10n["sure_to_delete"];
								id = jQuery('#data_collector_checked').val();
							}
							
							jQuery("#file_gallery_delete_what").val("all");
							file_gallery.delete_attachments( id, message );
							
							jQuery(this).dialog('close');
						}
					}
				});
				
			jQuery("#image_dialog")
				.dialog(
				{
					autoOpen  : false,
					closeText : fgL10n["close"],
					bgiframe  : true,
					resizable : false,
					position  : "center",
					modal     : true,
					draggable : false
				});
			
			jQuery("#file_gallery_copy_all_dialog")
				.dialog(
				{
					autoOpen  : false,
					closeText : fgL10n["close"],
					bgiframe  : true,
					resizable : false,
					position  : "center",
					width     : 500,
					modal     : true,
					draggable : false,
					buttons   :
					{
						"Cancel" : function()
						{
							jQuery(this).dialog('close');
						},
						"Continue" : function()
						{
							var from_id = jQuery("#file_gallery_copy_all_dialog input#file_gallery_copy_all_from").val();
								from_id = parseInt(from_id); 
							
							
							if( isNaN(from_id) || 0 === from_id )
							{
								alert("Supplied ID (" + from_id + ") is zero or not a number, please correct.");
								
								return false;
							}
							
							file_gallery.copy_all_attachments(from_id);
							
							jQuery(this).dialog('close');
						}
					}
				});
		},
		
		
		
		/**
		 * Displays the jQuery UI modal delete dialog
		 */
		delete_dialog : function( id, single )
		{
			var m = false,
				delete_dialog = jQuery("#delete_dialog"),
				o = jQuery("#file_gallery_originals").val();
			
			if( single )
				delete_dialog.addClass("single");
			else
				m = fgL10n["sure_to_delete"]
			
			if( ("" != o && "undefined" != o && "undefined" != typeof( o )) || jQuery("#image-" + id).hasClass("has_copies") ) //originals present in checked list
				delete_dialog.data("single_delete_id", id).dialog('open');
			else
				file_gallery.delete_attachments( id, m );
		},
		
		
		
		/**
		 * handles adding and removing of tags that will be used
		 * in gallery shortcode instead of attachment_ids,
		 * both when edited by hand and when a tag link is clicked
		 */
		add_remove_tags : function( tag )
		{
			var current_tags 	= jQuery("#fg_gallery_tags").val(),
				newtag_slug  	= jQuery(tag).attr("name"),
				newtag_name  	= jQuery(tag).html(),
				ct 			 	= "," + current_tags + ",",
				ns			 	= "," + newtag_slug  + ",",
				nn			 	= "," + newtag_name  + ",",
				ctlen			= 0;
			
			if( "-1" == ct.search(ns) && "-1" == ct.search(nn) )
			{
				jQuery(tag).css({"color" : "#BBBBBB"});
				
				if( "" != current_tags )
					newtag_slug = "," + newtag_slug;
				
				current_tags += newtag_slug;
			}
			else
			{
				jQuery(tag).css({"color" : "#21759B"});
		
				if( "-1" != ct.search(ns) )
					current_tags = ct.replace(ns, ",");
				else if( "-1" != ct.search(nn) )
					current_tags = ct.replace(nn, ",");
			}
			
			// clean up whitespace
			current_tags = current_tags.replace(/\s+/g, " ").replace(/\s+,/g, ",").replace(/,+\s*/g, ",");
		
			ctlen = current_tags.length;
			
			if( "," == current_tags[0] )
				current_tags = current_tags.substr(1);
			
			if( "," == current_tags[ctlen-2] )
				current_tags = current_tags.substr(0, ctlen-2);
			
			jQuery("#fg_gallery_tags").val(current_tags);
			
			file_gallery.serialize();
		},
		
		
		
		/**
		 * maps attachment data (checked, has copies, is a copy)
		 */
		map : function(what, data)
		{
			data = data.split(',');
			var dl = data.length;
			
			if( "checked" == what )
			{
				while( 0 < dl )
				{
					if( false === jQuery("#att-chk-" + data[dl-1]).attr('checked') )
						delete data[dl-1];
					
					dl--;
				}
			}
			else if( "copy" == what || "has_copies" == what )
			{
				while( 0 < dl )
				{
					if( false === jQuery("#image-" + data[dl-1]).hasClass(what) )
						delete data[dl-1];
					
					dl--;
				}
			}
			else
			{
				return false;
			}
			
			data = '"' + data.toString() + '"';
			
			return data.replace(/,+/g, ',').replace(/",/g, '').replace(/,"/g, '').replace(/"/g, '');
		},
		
		
		
		/**
		 * displays attachments thumbnails or the tag list
		 */
		files_or_tags : function( do_switch )
		{
			var files_or_tags = jQuery("#files_or_tags");
			
			if( do_switch )
			{
				if( "files" == files_or_tags.val() )
					files_or_tags.val("tags")
				else
					files_or_tags.val("files")
			}
			
			if( "files" == files_or_tags.val() || "undefined" == typeof( files_or_tags.val() ) || "undefined" == files_or_tags.val() )
			{
				jQuery("#file_gallery_switch_to_tags").attr("value", fgL10n["switch_to_tags"]);
				jQuery("#file_gallery_attachment_list").fadeIn();
				jQuery("#fg_gallery_tags_container, #file_gallery_tag_list").fadeOut();
				jQuery("#fg_gallery_tags").val('');
				files_or_tags.val("tags");
			}
			else if( "tags" == jQuery("#files_or_tags").val() )
			{
				jQuery("#file_gallery_switch_to_tags").attr("value", fgL10n["switch_to_files"]);
				jQuery("#file_gallery_attachment_list").fadeOut();
				jQuery("#fg_gallery_tags_container, #file_gallery_tag_list").fadeIn();
				files_or_tags.val("files");
			}
			
			if( 'undefined' == typeof(do_switch) || false === do_switch )
				file_gallery.serialize('files_or_tags');
		},
		
		
		
		/**
		 * saves attachment metadata
		 */
		save_attachment : function( attachment_data )
		{
				file_gallery_mode = "list";
			var newheight 		  = jQuery("#fg_container").height(),
				data;
			
			if( 460 > newheight )
				newheight = 460;
			
			data = {
					post_id 			: jQuery("#post_ID").val(),
					attachment_id 		: attachment_data.id, 
					action 				: "file_gallery_main_update",
					post_alt	   		: attachment_data.alt,
					post_title   		: attachment_data.title,
					post_content 		: attachment_data.content,
					post_excerpt 		: attachment_data.excerpt,
					tax_input	 		: attachment_data.tax_input,
					menu_order   		: attachment_data.menu_order,
					attachment_order 	: jQuery("#attachment_order").val(),
					checked_attachments : jQuery("#checked_attachments").val(),
					_ajax_nonce			: file_gallery_nonce
			};
			
			jQuery("#fg_container")
				.css({"height" : newheight })
				.html("<p class=\"loading_image\"><img src=\"" + file_gallery_url + "/images/ajax-loader.gif\" /><br />" + fgL10n["saving_attachment_data"] + "</p>");
			
			jQuery.post
			(
				ajaxurl, 
				data,
				function(response)
				{
					jQuery('#fg_container').html(response).css({"height" : "auto"});
					jQuery('#file_gallery_response').stop().fadeTo(0, 1).show().fadeOut(7500);
					
					file_gallery.setup();
				},
				"html"
			);
		},
		
		
		
		/**
		 * deletes checked attachments
		 */
		delete_attachments : function( attachment_ids, message )
		{
			var delete_what 	= jQuery("#file_gallery_delete_what"),
				delete_what_val = delete_what.val(),
				a,
				copies,
				originals,
				data,
				attachment_count = 1;
			
			if( "" == attachment_ids || "undefined" == typeof( attachment_ids ) || "" == delete_what_val || "undefined" == typeof( delete_what_val ) || "undefined" == delete_what_val )
				return false;
			
			if( "undefined" == typeof( message ) )
				message = false;
		
			if( (false !== message && confirm(message)) || false === message )
			{
				if( "-1" != attachment_ids.search(/,/) )
					attachment_count = attachment_ids.split(",").length;
				
				if( 1 < attachment_count )
					a = fgL10n["deleting_attachments"];
				else
					a = fgL10n["deleting_attachment"];
				
				if( 2 > attachment_count )
				{
					if( jQuery("#image-" + attachment_ids).hasClass("copy") )
						jQuery("#file_gallery_copies").val(attachment_ids);
					else if( jQuery("#image-" + attachment_ids).hasClass("has_copies") )
						jQuery("#file_gallery_originals").val(attachment_ids);
				}
				
				copies 	  = jQuery("#file_gallery_copies").val();
				originals = jQuery("#file_gallery_originals").val();
				
				if( "" == copies || "undefined" == copies || "undefined" == typeof( copies ))
					copies = "";
				
				if( "" == originals || "undefined" == originals || "undefined" == typeof( originals ))
					originals = "";
					
				jQuery("#fg_container")
					.css({"height" : jQuery("#fg_container").height()})
					.html("<p class=\"loading_image\"><img src=\"" + file_gallery_url + "/images/ajax-loader.gif\" /><br />" + a + "</p>");
				
				data = {
						post_id 			: jQuery("#post_ID").val(),
						action 				: "file_gallery_main_delete",
						attachment_ids 		: attachment_ids, 
						attachment_order 	: jQuery("#data_collector_full").val(),
						checked_attachments : jQuery("#data_collector_checked").val(),
						copies				: copies,
						originals			: originals,
						delete_what			: delete_what_val,
						_ajax_nonce			: file_gallery_nonce
				};
				
				jQuery.post
				(
					ajaxurl, 
					data,
					function(response)
					{
						jQuery('#fg_container').html(response).css({"height" : "auto"});
						jQuery('#file_gallery_response').stop().fadeTo(0, 1).css({"display" : "block"}).fadeOut(7500);
						
						file_gallery.setup();
					},
					"html"
				);
			}
			
			delete_what.val("data_only")
		},
		
		
		
		/**
		 * detaches checked attachments
		 */
		detach_attachments : function( attachment_ids, message )
		{
			if( "" == attachment_ids || "undefined" == typeof( attachment_ids ) )
				return false;
			
			if( "undefined" == typeof( message ) )
				message = false;
		
			if( (false !== message && confirm(message)) || false === message )
			{
				var attachment_count = 1,
					a = fgL10n["detaching_attachment"];
				
				if( "-1" != attachment_ids.search(/,/) )
					attachment_count = attachment_ids.split(",").length;
		
				if( 1 < attachment_count )
					a = fgL10n["detaching_attachments"];
		
				jQuery("#fg_container")
					.css({"height" : jQuery("#fg_container").height()})
					.html("<p class=\"loading_image\"><img src=\"" + file_gallery_url + "/images/ajax-loader.gif\" /><br />" + a + "</p>");
		
				data = {
						post_id 			: jQuery("#post_ID").val(),
						action 				: "file_gallery_main_detach",
						attachment_ids 		: attachment_ids, 
						attachment_order 	: jQuery("#data_collector_full").val(),
						checked_attachments : jQuery("#data_collector_checked").val(),
						_ajax_nonce			: file_gallery_nonce
				};
				
				jQuery.post
				(
					ajaxurl, 
					data,
					function(response)
					{
						jQuery('#fg_container')
							.html(response)
							.css({"height" : "auto"});
						
						jQuery('#file_gallery_response')
							.stop()
							.fadeTo(0, 1)
							.show()
							.fadeOut(7500);
						
						file_gallery.setup();
					},
					"html"
				);
			}
		},


		
		/**
		 * saves attachment order as menu_order
		 */ 
		save_menu_order : function()
		{
			var attachment_order = jQuery("#data_collector_full").val(),
				admin_url = ajaxurl.split("/admin-ajax.php").shift(),
				data;
		
			if( "undefined" == attachment_order || "" == attachment_order )
				return false;
			
			jQuery('#file_gallery_response').stop().fadeTo(0, 1).html('<img src="' + admin_url + '/images/loading.gif" width="16" height="16" alt="' + fgL10n["loading"] + '" id="fg_loading_on_bar" />').show();
			
			data = {
				action			 : "file_gallery_save_menu_order",
				post_id 		 : jQuery("#post_ID").val(),
				attachment_order : attachment_order,
				_ajax_nonce		 : file_gallery_nonce
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
		},
		
		
		
		/**
		 * sends single attachment to the editor
		 */
		send_single : function()
		{
			attachment_id = jQuery('#data_collector_checked').val();
		
			if( "" == attachment_id || "undefined" == typeof( attachment_id ) )
				return false;
			
			var data = {
				action		  : "file_gallery_send_single",
				attachment_id : attachment_id,
				size 		  : jQuery('#file_gallery_single_size').val(),
				linkto 		  : jQuery('#file_gallery_single_linkto').val(),
				external_url  : jQuery('#file_gallery_single_external_url').val(),
				linkclass 	  : jQuery('#file_gallery_single_linkclass').val(),
				imageclass 	  : jQuery('#file_gallery_single_imageclass').val(),
				align 	      : jQuery('#file_gallery_single_align').val(),
				post_id 	  : jQuery("#post_ID").val(),
				_ajax_nonce	  : file_gallery_nonce
			};
			
			jQuery.post
			(
				ajaxurl, 
				data,
				function(response)
				{
					send_to_editor(response);
				},
				"html"
			);
		},
		
		
		
		/**
		 * loads the attachment metadata edit page into fg_container
		 */
		edit : function( attachment_id )
		{
			if( "" == attachment_id || "undefined" == typeof( attachment_id ) )
				return false;
			
			file_gallery_mode = "edit";
			
			var data = {
				action				: "file_gallery_edit_attachment",
				post_id 			: jQuery("#post_ID").val(),
				attachment_id 		: attachment_id, 
				attachment_order 	: jQuery("#data_collector_full").val(),
				checked_attachments : jQuery("#data_collector_checked").val(),
				_ajax_nonce			: file_gallery_nonce
			};
			
			jQuery("#fg_container")
				.css({"height" : 505 })
				.html("<p class=\"loading_image\"><img src=\"" + file_gallery_url + "/images/ajax-loader.gif\" /><br />" + fgL10n["loading_attachment_data"] + "</p>");
			
			jQuery.post
			(
				ajaxurl, 
				data,
				function(response)
				{
					jQuery('#fg_container').html(response);
					
					file_gallery.tinymce_deselect();
				},
				"html"
			);
		},
		
		
		
		/**
		 * zooms the thumbnail (needs to be replaced with lightbox)
		 */
		zoom : function( element )
		{
			var image = new Image();
			image.src = jQuery(element).attr("href");
		
			jQuery("#image_dialog")
				.html('<p class="loading_image"><img src="' + file_gallery_url + '/images/ajax-loader.gif" alt="" />	</p>')
				.dialog( 'option', 'width',  'auto' )
				.dialog( 'option', 'height', 'auto' )
				.dialog("open");
			
			jQuery(image).bind("load", function()
			{
				var ih    = this.height,
					iw    = this.width,
					src   = this.src,
					ratio = iw/ih,
					ww    = jQuery(window).width(),
					wh    = jQuery(window).height();
				
				if( ih > (wh - 50) )
				{
					ih = wh - 50;
					iw = ratio * ih;
				}
				else if( iw > (ww - 50) )
				{
					iw = ww - 50;
					ih = ratio * iw;
				}
				
				jQuery("#image_dialog")
					.html('<img src="' + src + '" width="' + iw + '" height="' + ih + '" alt="" />')
					.dialog( 'option', 'width',  iw + 50 )
					.dialog( 'option', 'height', ih + 50 )
					.dialog( 'option', 'position', 'center');
			});
			
			return false;
		},
		
		
		
		fieldset_toggle : function( fieldset )
		{
			var	states;
			
			if( "undefined" == typeof( fieldset ) )
				return;
			
			jQuery("#" + fieldset).toggle();

			if( jQuery("#file_gallery_single_toggler").is(":visible") && jQuery("#file_gallery_toggler").is(":visible") )
				states = "1,1";
			else if( jQuery("#file_gallery_toggler").is(":visible") )
				states = "1,0";
			else if( jQuery("#file_gallery_single_toggler").is(":visible") )
				states = "0,1";
			else
				states = "0,0";
			
			var data = {
				action		: "file_gallery_save_toggle_state",
				states		: states,
				_ajax_nonce	: file_gallery_nonce
			};
			
			jQuery.post
			(
				ajaxurl, 
				data
			);
		},
		
		copy_all_attachments : function(from_id)
		{
			if( "" == from_id || "undefined" == typeof( from_id ) )
				return false;
			
			var admin_url = ajaxurl.split("/admin-ajax.php").shift();
			
			file_gallery_mode = "list";
			
			var data = {
				action				: "file_gallery_copy_all_attachments",
				to_id 				: jQuery("#post_ID").val(),
				from_id 		    : from_id, 
				_ajax_nonce			: file_gallery_nonce
			};
			
			jQuery('#file_gallery_response').stop().fadeTo(0, 1).html('<img src="' + admin_url + '/images/loading.gif" width="16" height="16" alt="' + fgL10n["loading"] + '" id="fg_loading_on_bar" />').show();
			
			jQuery.post
			(
				ajaxurl, 
				data,
				function(response)
				{
					file_gallery.start(response);
				},
				"html"
			);
		},
		
		
		
		/**
		 * set / unset image as post thumb
		 */
		set_post_thumb : function( attachment_ids, unset )
		{
			if( "" == attachment_ids || "undefined" == typeof( attachment_ids ) )
				return false;
			
			var action = "file_gallery_unset_post_thumb";
			
			if( false === unset )
				action = "file_gallery_set_post_thumb";
			
			var admin_url = ajaxurl.split("/admin-ajax.php").shift();

			jQuery('#file_gallery_response').stop().fadeTo(0, 1).html('<img src="' + admin_url + '/images/loading.gif" width="16" height="16" alt="' + fgL10n["loading"] + '" id="fg_loading_on_bar" />').show();

			jQuery("#image-" + attachment_ids).append('<img src="' + file_gallery_url + '/images/loading-big.gif" width="32" height="32" alt="' + fgL10n["loading"] + '" id="fg_loading_on_thumb" class="thumb_switch_load" />').children("#fg_loading_on_thumb").fadeIn(250);
			
			data = {
				action			: action,
				post_id			: jQuery("#post_ID").val(),
				attachment_ids	: attachment_ids,
				_ajax_nonce		: file_gallery_nonce
			};
			
			jQuery.post
			(
				ajaxurl, 
				data,
				function( new_thumb )
				{
					var src = jQuery("#image-" + attachment_ids + " .post_thumb_status img").attr("src"),
						response = fgL10n["post_thumb_set"],
						bell = null;
					
					jQuery("#fg_loading_on_thumb").fadeOut(250).remove();
					
					if( "file_gallery_set_post_thumb" == action )
					{
						jQuery(".sortableitem.post_thumb .post_thumb_status img")
							.attr("alt", fgL10n["set_as_featured"])
							.attr("src", src.replace(/bell_unset.png/, "bell_set.png"))
							.parent()
								.attr("title", fgL10n["set_as_featured"])
								.parent()
									.removeClass("post_thumb");
						
						jQuery("#image-" + attachment_ids + " .post_thumb_status img")
							.attr("src", src.replace(/bell_set.png/, "bell_unset.png"))
							.attr("alt", fgL10n["unset_as_featured"])
							.parent()
								.attr("title", fgL10n["unset_as_featured"]);
						
						jQuery("#image-" + attachment_ids).addClass("post_thumb");
						
						jQuery("#postimagediv .inside")
							.html(new_thumb);
					}
					else
					{						
						WPRemoveThumbnail(post_thumb_nonce);
						
						response = fgL10n["post_thumb_unset"];
						
						jQuery("#image-" + attachment_ids + " .post_thumb_status img")
							.attr("alt", fgL10n["set_as_featured"])
							.attr("src", src.replace(/bell_unset.png/, "bell_set.png"))
							.parent()
								.attr("title", fgL10n["set_as_featured"])
								.parent()
									.removeClass("post_thumb");
					}
					
					jQuery('#file_gallery_response').html(response).fadeOut(7500);
				}
			);
		}
	});


	/* wpml */
	
	if( jQuery("#icl_div").length > 0 )
	{
		var fg_icl_ori_id = jQuery("#icl_translation_of option:selected").val();
		
		if( "undefined" != typeof(fg_icl_ori_id) && "undefined" != fg_icl_ori_id )
		{
			jQuery("#icl_div .inside").append('<a href="#" id="file_gallery_copy_from_wmpl_original">Copy all attachments from the original post</a>');
			
			jQuery("#file_gallery_copy_from_wmpl_original").bind("click", function()
			{
				if( confirm(fgL10n["copy_all_from_original"]) )
					file_gallery.copy_all_attachments(fg_icl_ori_id);
				
				return false;
			});
		}
	}



	// show / hide additional gallery options depending on preselected values
	if( "default" != jQuery("#file_gallery_orderby").val() )
	{
		if( "rand" == jQuery("#file_gallery_orderby").val() )
		{
			jQuery("#file_gallery_order").css({"display" : "none"});
			order = "";
		}
		else
		{
			jQuery("#file_gallery_order").css({"display" : "inline"});
		}
		
		orderby = ' orderby="' + jQuery("#file_gallery_orderby").val() + '"';
	}
	else
	{
		jQuery("#file_gallery_order").css({"display" : "none"});
		order 	= "";
		orderby = "";
	}
	
	
	
	// start file gallery
	file_gallery.start();



	/* === BINDINGS === */
	
	jQuery("#file_gallery_linkclass, #file_gallery_imageclass, #file_gallery_mimetype, #file_gallery_limit, #file_gallery_external_url, #file_gallery_single_linkclass, #file_gallery_single_imageclass, #file_gallery_single_external_url, #fg_gallery_tags").live('keypress keyup', function(e)
	{
		// on enter
		if ( 13 === e.which || 13 === e.keyCode )
		{
			file_gallery.serialize();
			return false;
		}
	});
	
	jQuery("#fgae_post_alt, #fgae_post_title, #fgae_post_excerpt, #fgae_tax_input, #fgae_menu_order").live('keypress keyup', function(e)
	{
		if ( 13 === e.which || 13 === e.keyCode ) // on enter
		{
			jQuery("#file_gallery_edit_attachment_save").trigger("click");
			e.preventDefault();
			return false;
		}
		else if( 27 === e.which || 27 === e.keyCode ) // on esc
		{
			jQuery("#file_gallery_edit_attachment_cancel").trigger("click");
		}
	});

	jQuery("a.post_thumb_status").live("click", function()
	{
		var what = false;
		
		if( jQuery(this).parent().hasClass("post_thumb") )
			what = true;
		
		file_gallery.set_post_thumb(jQuery(this).attr("rel"), what);
		
		return false;
	});
		
	jQuery("#remove-post-thumbnail").attr("onclick", "").live("click.file_gallery", function()
	{		
		if( 0 < jQuery(".sortableitem.post_thumb").length )
		{
			var id = jQuery(".sortableitem.post_thumb").attr("id");
			
			file_gallery.set_post_thumb(id.split("-").pop(), true);
		}
		else
		{
			WPRemoveThumbnail(post_thumb_nonce);
		}
		
		return false;
	});
	
	jQuery("#file_gallery_copy_all_form").bind("submit", function(){ return false; });
	
	
	
	// copy all attachments from another post
	jQuery("#file_gallery_copy_all").live("click", function()
	{
		jQuery("#file_gallery_copy_all_dialog").dialog("open");
	});



	// toggle fieldsets
	jQuery("#file_gallery_hide_gallery_options").live("click", function()
	{
		file_gallery.fieldset_toggle("file_gallery_toggler");
		
		if( jQuery("#file_gallery_hide_gallery_options").hasClass("open") )
			jQuery("#file_gallery_hide_gallery_options").removeClass("open").addClass("closed");
		else
			jQuery("#file_gallery_hide_gallery_options").removeClass("closed").addClass("open");
	});
	jQuery("#file_gallery_hide_single_options").live("click", function()
	{
		file_gallery.fieldset_toggle("file_gallery_single_toggler");
		
		if( jQuery("#file_gallery_hide_single_options").hasClass("open") )
			jQuery("#file_gallery_hide_single_options").removeClass("open").addClass("closed");
		else
			jQuery("#file_gallery_hide_single_options").removeClass("closed").addClass("open");
	});



	/* attachment edit screen */
	
	// save attachment
	jQuery("#file_gallery_edit_attachment_save").live("click", function()
	{
		var attachment_data = {
								id         : jQuery('#fgae_attachment_id').val(),
								alt        : jQuery('#fgae_post_alt').val(),
								title      : jQuery('#fgae_post_title').val(),
								excerpt    : jQuery('#fgae_post_excerpt').val(),
								content    : jQuery('#fgae_post_content').val(),
								tax_input  : jQuery('#fgae_tax_input').val(),
								menu_order : jQuery('#fgae_menu_order').val()
							};
		
		file_gallery.save_attachment( attachment_data );
		
		return false;
	});
	
	// cancel changes
	jQuery("#file_gallery_edit_attachment_cancel").live("click", function()
	{
		file_gallery.start('return_from_single_attachment');
		
		return false;
	});



	/* thumbnails */
	
	// attachment thumbnail click
	jQuery("#fg_container .fgtt").live("click.file_gallery", function()
	{
		var c = "#att-chk-" + jQuery(this).parent("li:first").attr("id").replace("image-", "");
		
		jQuery(c).attr("checked", jQuery(c).attr("checked") ? false : true).change();
	});
	
	// attachment thumbnail double click
	jQuery("#fg_container .fgtt").live("dblclick", function()
	{
		file_gallery.edit( jQuery(this).parent("li:first").attr("id").replace("image-", "") );
	});
	
	// edit attachment button click
	jQuery("#fg_container .img_edit").live("click", function()
	{
		file_gallery.edit( jQuery(this).attr("id").replace('in-', '').replace('-edit', '') );
		
		return false;
	});


	
	// zoom attachment button click
	jQuery("#fg_container .img_zoom, .attachment_edit_thumb").live("click", function()
	{
		file_gallery.zoom( this );
		
		return false;
	});


	
	// delete or detach single attachment link click
	jQuery("#fg_container .delete_or_detach_link").live("click", function()
	{
		var id = jQuery(this).attr("rel"),
			a  = '#detach_or_delete_' + id,
			b  = '#detach_attachment_' + id,
			c  = '#del_attachment_' + id;

		if( jQuery(a).is(":hidden") && jQuery(b).is(":hidden") && jQuery(c).is(":hidden") )
			jQuery(a).fadeIn(100);
		else
			jQuery(a + ", " + b + ", " + c).fadeOut(100);
		
		return false;
	});
		
	// detach single attachment link click
	jQuery("#fg_container .do_single_detach").live("click", function()
	{
		var id = jQuery(this).attr("rel");
		
		jQuery('#detach_or_delete_' + id).fadeOut(250);
		jQuery('#detach_attachment_' + id).fadeIn(100);
		
		return false;
	});
		
	// delete single attachment link click
	jQuery("#fg_container .do_single_delete").live("click", function()
	{
		var id = jQuery(this).attr("rel");
		
		if( jQuery("#image-" + id).hasClass("has_copies") )
		{
			file_gallery.delete_dialog( id, true );
		}
		else
		{
			jQuery('#detach_or_delete_' + id).fadeOut(100);
			jQuery('#del_attachment_' + id).fadeIn(100);
		}

		return false;
	});	
		
	// delete single attachment link confirm
	jQuery("#fg_container .delete").live("click", function()
	{
		var id = jQuery(this).parent("div").attr("id").replace("del_attachment_", "");
		
		if( jQuery("#image-" + id).hasClass("copy") )
			jQuery("#file_gallery_delete_what").val("data_only");
		else
			jQuery("#file_gallery_delete_what").val("all");

		file_gallery.delete_dialog( id, true );
		
		return false;
	});
		
	// delete single attachment link confirm
	jQuery("#fg_container .detach").live("click", function()
	{
		 file_gallery.detach_attachments( jQuery(this).parent("div").attr("id").replace("detach_attachment_", ""), false );
		 
		 return false;
	});
	
	// delete / detach single attachment link cancel
	jQuery("#fg_container .delete_cancel, #fg_container .detach_cancel").live("click", function()
	{
		 jQuery(this)
			.parent("div")
				.fadeOut(250);
				
		 return false;
	});
	
	
		
	/* send gallery or single image(s) to editor */

	// send populated gallery shortcode to visual editor (send as gallery button click)
	jQuery("#file_gallery_send_gallery_legend").live("click", function()
	{
		var v = jQuery('#data_collector').val();
		
		if( "" == v || "undefined" == v )
			return false;
		
		send_to_editor( v );
	});
	
	// send single attachment to editor button click
	jQuery("#file_gallery_send_single_legend").live("click", function()
	{
		 file_gallery.send_single();
	});
		
		
		
	/* main menu buttons */

	// refresh attachments button click
	jQuery("#file_gallery_refresh").live("click", function()
	{
		 file_gallery.start();
	});
	
	// delete checked attachments button click
	jQuery("#file_gallery_delete_checked").live("click", function()
	{
		file_gallery.delete_dialog( jQuery('#data_collector_checked').val() );
	});
		
	// detach checked attachments button click
	jQuery("#file_gallery_detach_checked").live("click", function()
	{
		file_gallery.detach_attachments(jQuery('#data_collector_checked').val(), fgL10n["sure_to_detach"]);
	});
	
	// save attachments menu order button click
	jQuery("#file_gallery_save_menu_order").live("click", function()
	{
		file_gallery.save_menu_order();
	});
		
	// bind dropdown select boxes change to serialize attachments list
	jQuery("#file_gallery_size, #file_gallery_linkto, #file_gallery_orderby, #file_gallery_order, #file_gallery_template, #file_gallery_single_linkto, #fg_container .sortableitem .checker, #file_gallery_columns, #file_gallery_linkrel").live("change", function()
	{
		file_gallery.serialize();
	});
	
	// tags from current post only checkbox, switch to tags button
	jQuery("#fg_gallery_tags_from, #file_gallery_switch_to_tags").live("click", function()
	{
		file_gallery.serialize();
	});
	
	// blur binding for text inputs and dropdown selects
	jQuery("#fg_gallery_tags, #file_gallery_linkclass, #file_gallery_imageclass, #file_gallery_single_linkclass, #file_gallery_single_imageclass, #file_gallery_single_external_url, #file_gallery_external_url").live("blur", function()
	{
		file_gallery.serialize();
	});
		
	// whether to show tags or list of attachments
	jQuery("#file_gallery_switch_to_tags").live("click", function()
	{
		file_gallery.files_or_tags( false );
	});
		
	// clickable tag links
	jQuery(".fg_insert_tag").live("click", function()
	{
		file_gallery.add_remove_tags( this );
		
		return false;
	});
		
	// check all attachments button click
	jQuery("#file_gallery_check_all").live("click", function()
	{
		if( jQuery("#data_collector_checked").val() != jQuery("#data_collector_full").val() )
		{
			jQuery('#fg_container .sortableitem .checker').map(function()
			{
				return this.checked = true;
			});
			
			file_gallery.serialize();
		}
	});
		
	// uncheck all attachments button click
	jQuery("#file_gallery_uncheck_all").live("click", function(e)
	{
		if( "" != jQuery("#data_collector_checked").val() )
		{
			jQuery('#fg_container .sortableitem .checker').map(function()
			{
				return this.checked = false;
			});
		}
		
		file_gallery.serialize();
	});
	
	
	
	jQuery("#file_gallery_uncheck_all").live("click_tinymce_gallery", function(e)
	{
		if( "" != jQuery("#data_collector_checked").val() )
		{
			jQuery('#fg_container .sortableitem .checker').map(function()
			{
				return this.checked = false;
			});
		}
	});


	
	/**
	 * reload attachment list on thickbox close
	 */
	jQuery("#TB_window").live("unload", function(){ file_gallery.start(); });
	
	
	
});


	
// --------------------------------------------------------- //



/**
 * thanks to http://soledadpenades.com/2007/05/17/arrayindexof-in-internet-explorer/
 */
if( ! Array.indexOf )
{
	Array.prototype.indexOf = function(obj)
	{
		var l = this.length,
			i;
		
		for( i=0; i<l; i++ )
		{
			if( this[i] == obj )
				return i;
		}
		
		return -1;
	}
}