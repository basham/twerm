<?php

class Time_Period_Controller extends Controller {
	
	function Time_Period_Controller() {
		parent::Controller();
		$this->load->helper('twerm_helper');
		$this->load->library('template');
		$this->load->library('twerm');
		$this->load->model('Time_Period_Term');
		$this->load->model('Time_Period');
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
		$tp->loadByDate( $year.$month.$day );
		
		$data = array();
		$data['terms'] = $this->twerm->getTimePeriodTerms( $tp );
		
		$this->template->write('title', twerm_title( array( $tp->getDate() ) ));
		$this->template->write('content', '<p><a href="'.$tp->getTimelineURL().'"><strong>Timeline</strong></a></p>');
		$this->template->write_view('content', 'term_list', $data);
		$this->template->render();
	}
	
	function day_period_timeline( $year, $month, $day ) {

		$tp = $this->Time_Period->newInstance();
		$tp->loadByDate( $year.$month.$day );
		
		$data = array();
		$data['twitter_posts'] = $this->twerm->getTimePeriodTwitterPosts( $tp );
		
		$this->template->write('title', twerm_title( array( $tp->getDate(), 'Timeline' ) ));
		$this->template->write('content', '<p><a href="'.$tp->getURL().'"><strong>Terms</strong></a></p>');
		$this->template->write_view('content', 'timeline', $data);
		$this->template->render();
	}

	function day_period_term( $year, $month, $day, $term ) {

		$term = strtolower($term);

		$tp = $this->Time_Period->newInstance();
		$tp->loadByDate( $year.$month.$day );
		
		$tpt = $this->Time_Period_Term->newInstance();
		$tpt->term = $term;
		$tpt->setTimePeriod($tp);
		
		$data = array();
		$data['twitter_posts'] = $this->twerm->getTimePeriodTermTwitterPosts( $tp, $term );
		
		$this->template->write('title', twerm_title( array( $tp->getDate(), $term ) ));
		$this->template->write('content', '<p><a href="'.$tp->getURL().'"><strong>Terms</strong></a> / <a href="'.$tp->getTimelineURL().'"><strong>Timeline</strong></a> / <a href="'.$tpt->getHistoryURL().'"><strong>History</strong></a></p>');
		$this->template->write_view('content', 'timeline', $data);
		$this->template->render();
	}
	
	function term( $term ) {

		$term = strtolower($term);
		
		$data = array();
		$data['time_period_terms'] = $this->twerm->getTermHistory( $term );
		
		$this->template->write('title', twerm_title( array( $term ) ));
		$this->template->write_view('content', 'term_history', $data);
		$this->template->render();
	}
	
}

?>