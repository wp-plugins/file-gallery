<?php

/**
 * returns descriptive document type
 * used for icons in attachment list
 * both in backend and frontend
 *
 * needs more options and maybe a different approach...
 */
function file_gallery_get_file_type($mime)
{
	if( false !== strpos($mime, "text") || 
		false !== strpos($mime, "xhtml"))
	{
		return "text";
	}
	elseif( false !== strpos($mime, "excel") )
	{
		return "spreadsheet";
	}
	elseif( false !== strpos($mime, "powerpoint") )
	{
		return "interactive";
	}
	elseif( false !== strpos($mime, "code") )
	{
		return "code";
	}
	elseif( false !== strpos($mime, "octet-stream") )
	{
		return "interactive";
	}
	elseif( false !== strpos($mime, "audio") )
	{
		return "audio";
	}
	elseif( false !== strpos($mime, "video") )
	{
		return "video";
	}
	elseif( false !== strpos($mime, "stuffit") || 
			 false !== strpos($mime, "compressed") || 
			 false !== strpos($mime, "x-tar") ||
			 false !== strpos($mime, "zip"))
	{
		return "archive";
	}
	elseif( false !== strpos($mime, "application") )
	{
		return "document";
	}
	else
	{
		return "default";
	}
}

?>