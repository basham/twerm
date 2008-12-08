<?php

foreach( $terms as $term ) {
	
	echo '<p class="twitter-term">';

	echo '<strong>#'.$term->rank.'</strong> ';
	echo '<a href="'.$term->getURL().'"><em>'.$term->term.'</em></a>';

	echo '</p>';
	
}

?>