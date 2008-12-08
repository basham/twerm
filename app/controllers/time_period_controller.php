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

		$tp = $this->Time_Period->newInstance();
		$tp->loadByDate( twerm_date($year, $month, $day) );
		
		$data = array();
		$data['terms'] = $tp->getTerms();
		
		$this->template->write('title', twerm_title( array( $tp->start_date ) ));
		$this->template->write_view('content', 'term_list', $data);
		$this->template->render();
	}
	
	function day_period_timeline( $year, $month, $day ) {

		$tp = $this->Time_Period->newInstance();
		$tp->loadByDate( twerm_date($year, $month, $day) );
		
		$data = array();
		$data['twitter_posts'] = $tp->getTwitterPosts();
		
		$this->template->write('title', twerm_title( array( $tp->start_date, 'Timeline' ) ));
		$this->template->write_view('content', 'timeline', $data);
		$this->template->render();
	}

}

?>