<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Twerm {

	public $CI;
	
	function Twerm() {
		$this->CI =& get_instance();
		$this->CI->load->database();
		$this->CI->load->model('Twitter_Post');
		$this->CI->load->model('Twitter_User');
		$this->CI->load->model('Time_Period');
		$this->CI->load->model('Time_Period_Term');
	}

	public function getTwitterUser( $twitter_user_name ) {
		
		$tu = $this->CI->Twitter_User->newInstance();
		$tu->load( $twitter_user_name );
		
		return $tu;
	}

	public function getTimePeriodTerms( $timePeriod ) {

		$a = array();

		$query = $this->CI->db->query('SELECT * FROM time_period_term WHERE count > 1 AND time_period_id = ? ORDER BY rank ASC', array($timePeriod->time_period_id));
		
		foreach( $query->result() as $row ) {
			
			// Creates Time Period Term
			$tpt = $this->CI->Time_Period_Term->newInstance();
			$tpt->setModelByObject( $row );
			$tpt->setTimePeriod( $timePeriod );

			$a[] = $tpt;
		}

		return $a;
	}
	
	public function getTimePeriodTwitterPosts( $timePeriod ) {
		
		$a = array();
		
		$query = $this->CI->db->query('SELECT * FROM time_period, twitter_post, twitter_user WHERE DATE_FORMAT(twitter_post.published_datetime, "%Y-%m-%d") BETWEEN DATE_FORMAT(time_period.start_date, "%Y-%m-%d") AND DATE_FORMAT(time_period.end_date, "%Y-%m-%d") AND twitter_post.twitter_user_name = twitter_user.twitter_user_name AND time_period.time_period_id = ?', array($timePeriod->time_period_id));
		
		foreach( $query->result() as $row ) {

			// Creates Twitter User
			$tu = $this->CI->Twitter_User->newInstance();
			$tu->setModelByObject( $row );
			
			// Creates Twitter Post
			$tp = $this->CI->Twitter_Post->newInstance();
			$tp->setModelByObject( $row );
			$tp->setTwitterUser( $tu );

			$a[] = $tp;
		}
		
		return $a;
	}
	
	public function getTimePeriodTermTwitterPosts( $timePeriod, $term ) {
		
		$a = array();
		
		$query = $this->CI->db->query('SELECT * FROM time_period, twitter_post, twitter_user, twitter_post_term WHERE DATE_FORMAT(twitter_post.published_datetime, "%Y-%m-%d") BETWEEN DATE_FORMAT(time_period.start_date, "%Y-%m-%d") AND DATE_FORMAT(time_period.end_date, "%Y-%m-%d") AND twitter_post.twitter_user_name = twitter_user.twitter_user_name AND time_period.time_period_id = ? AND twitter_post.twitter_post_id = twitter_post_term.twitter_post_id AND twitter_post_term.term = ? ORDER BY twitter_post.published_datetime DESC', array($timePeriod->time_period_id, $term));
		
		foreach( $query->result() as $row ) {

			// Creates Twitter User
			$tu = $this->CI->Twitter_User->newInstance();
			$tu->setModelByObject( $row );
			
			// Creates Twitter Post
			$tp = $this->CI->Twitter_Post->newInstance();
			$tp->setModelByObject( $row );
			$tp->setTwitterUser( $tu );

			$a[] = $tp;
		}
		
		return $a;
	}
	
	public function getTermHistory( $term ) {
		
		$a = array();
		
		$query = $this->CI->db->query('SELECT * FROM time_period, time_period_term WHERE time_period.time_period_id = time_period_term.time_period_id AND time_period_term.term = ? ORDER BY time_period.start_date DESC', array($term));
		
		foreach( $query->result() as $row ) {
			
			// Creates Time Period
			$tp = $this->CI->Time_Period->newInstance();
			$tp->setModelByObject( $row );
			
			// Creates Time Period Term
			$tpt = $this->CI->Time_Period_Term->newInstance();
			$tpt->setModelByObject( $row );
			$tpt->setTimePeriod( $tp );

			$a[] = $tpt;
		}
		
		return $a;
	}
	
}

?>