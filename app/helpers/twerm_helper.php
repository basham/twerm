<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('twerm_title')) {
	
	function twerm_title( $values ) {
		$title = 'Twerm';
		foreach( $values as $value )
			$title .= ' / '.$value;
		return $title;
	}
	
}

if ( ! function_exists('twerm_date')) {
	
	function twerm_date( $date, $delimiter = '-' ) {
		$dt = new DateTime( $date );
		$d = $dt->format('Y'.$delimiter.'m'.$delimiter.'d');
		return $d;
	}
	
}


/* End of file twerm_helper.php */
/* Location: ./app/helpers/twerm_helper.php */