<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('twerm_title')) {
	
	function twerm_title( $values ) {
		$title = 'Twerm';
		foreach( $values as $value )
			$title .= ' / '.$value;
		return $title;
	}
}


/* End of file twerm_helper.php */
/* Location: ./app/helpers/twerm_helper.php */