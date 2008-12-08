<?php

class Time_Period_Controller extends Controller {
	
	function Time_Period_Controller() {
		parent::Controller();
		$this->load->library('template');
		$this->load->model('Time_Period');
		$this->load->model('Time_Period_Term');
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
		$tp->loadByDate( twerm_date($year.$month.$day) );
		
		$data = array();
		$data['terms'] = $tp->getTerms();
		
		$date = twerm_date( $tp->start_date, '/' );
		$timeline_url = '/'.$date.'/timeline';
		
		$this->template->write('title', twerm_title( array( $tp->start_date ) ));
		$this->template->write('content', '<p><a href="'.$timeline_url.'"><strong>Timeline</strong></a></p>');
		$this->template->write_view('content', 'term_list', $data);
		$this->template->render();
	}
	
	function day_period_timeline( $year, $month, $day ) {

		$tp = $this->Time_Period->newInstance();
		$tp->loadByDate( twerm_date($year.$month.$day) );
		
		$data = array();
		$data['twitter_posts'] = $tp->getTwitterPosts();

		$date = twerm_date( $tp->start_date, '/' );
		$terms_url = '/'.$date;
		
		$this->template->write('title', twerm_title( array( $tp->start_date, 'Timeline' ) ));
		$this->template->write('content', '<p><a href="'.$terms_url.'"><strong>Terms</strong></a></p>');
		$this->template->write_view('content', 'timeline', $data);
		$this->template->render();
	}

	function day_period_term( $year, $month, $day, $term ) {

		$term = strtolower($term);

		$tp = $this->Time_Period->newInstance();
		$tp->loadByDate( twerm_date($year.$month.$day) );
		
		$t = $this->Time_Period_Term->newInstance();
		$t->load( $tp->time_period_id, $term );
		
		$data = array();
		$data['twitter_posts'] = $t->getTwitterPosts();
		
		$date = twerm_date( $tp->start_date, '/' );
		$timeline_url = '/'.$date.'/timeline';
		$terms_url = '/'.$date;
		
		$this->template->write('title', twerm_title( array( $tp->start_date, $term ) ));
		$this->template->write('content', '<p><a href="'.$terms_url.'"><strong>Terms</strong></a> / <a href="'.$timeline_url.'"><strong>Timeline</strong></a></p>');
		$this->template->write_view('content', 'timeline', $data);
		$this->template->render();
	}
	
}

?>