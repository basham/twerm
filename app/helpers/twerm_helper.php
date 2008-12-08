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
	
	function twerm_date( $year, $month, $day ) {
		// TODO: Exception handling doesn't work
		try {
			$dt = new DateTime( $year.'-'.$month.'-'.$day );
			$date = $dt->format('Y-m-d');
		} catch( Exception $e ) {
			echo 'your date sucks';
			$date = '';
		}
		return $date;
	}
}


/* End of file twerm_helper.php */
/* Location: ./app/helpers/twerm_helper.php */