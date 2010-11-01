<?php

/**
 * Modified WP function to support !%mimetype% syntax
 *
 * Convert MIME types into SQL.
 *
 * @since 1.6.5
 *
 * @param string|array $post_mime_types List of mime types or comma separated string of mime types.
 * @param string $table_alias Optional. Specify a table alias, if needed.
 * @return string The SQL AND clause for mime searching.
 */
function file_gallery_wp_post_mime_type_where($post_mime_types, $table_alias = '') {
	$where = '';
	$wildcards = array('', '%', '%/%');
	if ( is_string($post_mime_types) )
		$post_mime_types = array_map('trim', explode(',', $post_mime_types));
	foreach ( (array) $post_mime_types as $mime_type ) {
		$mime_type = preg_replace('/\s/', '', $mime_type);
		$slashpos = strpos($mime_type, '/');
		if ( false !== $slashpos ) {
			$mime_group = preg_replace('/[^-*.a-zA-Z0-9]/', '', substr($mime_type, 0, $slashpos));
			$mime_subgroup = preg_replace('/[^-*.+a-zA-Z0-9]/', '', substr($mime_type, $slashpos + 1));
			if ( empty($mime_subgroup) )
				$mime_subgroup = '*';
			else
				$mime_subgroup = str_replace('/', '', $mime_subgroup);
			$mime_pattern = "$mime_group/$mime_subgroup";
		} else {
			$mime_pattern = preg_replace('/[^-*.a-zA-Z0-9]/', '', $mime_type);
			if ( false === strpos($mime_pattern, '*') )
				$mime_pattern .= '/*';
		}

		$mime_pattern = preg_replace('/\*+/', '%', $mime_pattern);

		if ( in_array( $mime_type, $wildcards ) )
			return '';

		if ( false !== strpos($mime_pattern, '%') )
			$wheres[] = empty($table_alias) ? "post_mime_type LIKE '$mime_pattern'" : "$table_alias.post_mime_type LIKE '$mime_pattern'";
		else
			$wheres[] = empty($table_alias) ? "post_mime_type = '$mime_pattern'" : "$table_alias.post_mime_type = '$mime_pattern'";
	}
	if ( !empty($wheres) )
		$where = ' AND (' . join(' OR ', $wheres) . ') ';
	return $where;
}




/**
 * does exactly what its name says :)
 *
 * thanks to merlinyoda at dorproject dot net
 * http://php.net/manual/en/function.array-diff.php
 */
if( ! function_exists("array_xor") )
{
	function array_xor( $array_a, $array_b )
	{
		$union_array = array_merge($array_a, $array_b);
		$intersect_array = array_intersect($array_a, $array_b);
		return array_diff($union_array, $intersect_array);
	}
}



/**
 * checks if an array is just full of empty strings
 *
 * http://bytes.com/topic/php/answers/456092-slick-way-check-if-array-contains-empty-elements
 */
if( ! function_exists("empty_array") )
{
	function empty_array( $array = array() )
	{
		foreach ($array as $a)
		{
			if ( 0 < strlen($a) )
				return false;
		}
		
		return true;
	}
}

?>