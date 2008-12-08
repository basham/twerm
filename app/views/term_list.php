<?php

foreach( $terms as $term ) {
	$data['term'] = $term;
	$this->load->view('time_period_term', $data);
}

?>