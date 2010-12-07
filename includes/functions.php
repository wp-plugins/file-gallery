<?php

/**
 * Taken from WordPress 3.1-beta1
 */
if( ! function_exists('_wp_link_page') )
{
	/**
	 * Helper function for wp_link_pages().
	 *
	 * @since 3.1.0
	 * @access private
	 *
	 * @param int $i Page number.
	 * @return string Link.
	 */
	function _wp_link_page( $i ) {
		global $post, $wp_rewrite;
	
		if ( 1 == $i ) {
			$url = get_permalink();
		} else {
			if ( '' == get_option('permalink_structure') || in_array($post->post_status, array('draft', 'pending')) )
				$url = add_query_arg( 'page', $i, get_permalink() );
			elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') == $post->ID )
				$url = trailingslashit(get_permalink()) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
			else
				$url = trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged');
		}
	
		return '<a href="' . esc_url( $url ) . '">';
	}
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