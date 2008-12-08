<?php

class Time_Period_Term extends Model {
	
	public $time_period_id = 0;
	public $term = '';
	public $count = 0;
	public $rank = 0;
	public $power_rank = 0;
	
	private $_time_period = null;
	
	function Time_Period_Term() {
		parent::Model();
		$this->load->database();
		$this->load->helper('twerm_helper');
	}
	
	// Returns new instance of Time_Period_Term
	// Models are loaded as static, singleton objects,
	// making this an elagant workaround.
	public function newInstance() {
		return new Time_Period_Term();
	}
	
	// Set Model data
	public function setModel( $time_period_id, $term, $count, $rank, $power_rank ) {
		$this->time_period_id = $time_period_id;
		$this->term = $term;
		$this->count = $count;
		$this->rank = $rank;
		$this->power_rank = $power_rank;
	}
	
	public function setModelByObject( $obj ) {
		$this->setModel( $obj->time_period_id, $obj->term, $obj->count, $obj->rank, $obj->power_rank );
	}
	
	public function setTimePeriod( $timePeriod ) {
		$this->_time_period = $timePeriod;
	}
	
	public function getTimePeriod() {
		return $this->_time_period;
	}
	
	// Load Model data based on TwitterPostId
	public function load( $time_period_id, $term ) {
		$query = $this->db->get_where('time_period_term', array('time_period_id' => $time_period_id, 'term' => $term));
		if ( $query->num_rows() == 0 )
			return;
		$this->setModelByObject( $query->row() );
	}
	
	public function getURL() {
		$date = twerm_date( $this->getTimePeriod()->start_date, '/' );
		$url = '/'.$date.'/term/'.$this->term;
		return $url;
	}
	
	public function getTwitterPosts() {
		
		$a = array();
		
		$query = $this->db->query('SELECT * FROM time_period, twitter_post, twitter_user, twitter_post_term WHERE DATE_FORMAT(twitter_post.published_datetime, "%Y-%m-%d") BETWEEN DATE_FORMAT(time_period.start_date, "%Y-%m-%d") AND DATE_FORMAT(time_period.end_date, "%Y-%m-%d") AND twitter_post.twitter_user_name = twitter_user.twitter_user_name AND time_period.time_period_id = ? AND twitter_post.twitter_post_id = twitter_post_term.twitter_post_id AND twitter_post_term.term = ? ORDER BY twitter_post.published_datetime DESC', array($this->time_period_id, $this->term));
		
		foreach( $query->result() as $row ) {

			// Creates Twitter User
			$tu = $this->Twitter_User->newInstance();
			$tu->setModelByObject( $row );
			
			// Creates Twitter Post
			$tp = $this->Twitter_Post->newInstance();
			$tp->setModelByObject( $row );
			$tp->setTwitterUser( $tu );

			$a[] = $tp;
		}
		
		return $a;
	}
	
}

?>