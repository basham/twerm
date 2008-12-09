<?php

foreach( $time_periods as $tp ) {
	
	echo '<p class="twitter-term">';

	echo '<a href="'.$tp->getURL().'">'.$tp->getDate().'</a>';

	echo '</p>';
	
}

?>