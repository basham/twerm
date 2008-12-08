<?php

class Time_Period_Controller extends Controller {
	
	function Time_Period_Controller() {
		parent::Controller();
		$this->load->library('template');
		$this->load->model('Time_Period');
		$this->load->helper('twerm_helper');
	}

	function year_period( $year ) {
		// List all Months for the year
		// Summize top terms?
	}
	
	function month_period( $year, $month ) {
		// List all Days for the month
		// Summize top terms?
	}
	
	function day_period( $year, $month, $day ) {
		
		// TODO: Exception handling doesn't work
		try {
			$dt = new DateTime( $year.'-'.$month.'-'.$day );
			$date = $dt->format('Y-m-d');
		} catch( Exception $e ) {
			echo 'your date sucks';
			$date = '';
		}

		$tp = $this->Time_Period->newInstance();
		$tp->loadByDate($date);
		
		$data = array();
		
		$data['twitter_posts'] = $tp->getTwitterPosts();
		
		$this->template->write('title', twerm_title( array( $tp->start_date ) ));
		$this->template->write_view('content', 'timeline', $data);
		$this->template->render();
	}

}

?>