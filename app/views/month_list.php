<?php

$this->load->helper('twerm_helper');

foreach( $months as $month ) {
	
	echo '<p class="twitter-term">';

	echo '<a href="'.$year.'/'.$month.'">'.twerm_month($year.$month.'01').'</a>';

	echo '</p>';
	
}

?>