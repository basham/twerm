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
	
	function twerm_date( $date, $delimiter = null ) {
		$dt = new DateTime( $date );
		$format = $delimiter != null ? 'Y'.$delimiter.'m'.$delimiter.'d' : 'F j, Y';
		$d = $dt->format( $format );
		return $d;
	}
	
}

if ( ! function_exists('twerm_month')) {
	
	function twerm_month( $date, $delimiter = null ) {
		$dt = new DateTime( $date );
		$format = $delimiter != null ? 'Y'.$delimiter.'m' : 'F Y';
		$d = $dt->format( $format );
		return $d;
	}
	
}


/* End of file twerm_helper.php */
/* Location: ./app/helpers/twerm_helper.php */