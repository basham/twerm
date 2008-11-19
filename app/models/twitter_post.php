<?php

class Twitter_Post extends Model {
	
	public $twitter_post_id = 0;
	public $twitter_user_name = '';
	public $time_period_id = 0;
	public $content = '';
	public $published_datetime = '';
	
	private $_twitter_user = null;
	private $_time_period = null;
	
	function Twitter_Post() {
		parent::Model();
		$this->load->model('Twitter_User');
		$this->load->database();
	}
	
	// Returns new instance of Twitter_Post
	// Models are loaded as static, singleton objects,
	// making this an elagant workaround.
	public function newInstance() {
		return new Twitter_Post();
	}
	
	// Load Model data based on TwitterPostId
	public function load( $twitter_post_id = 0 ) {
		$query = $this->db->get_where('twitter_post', array('twitter_post_id' => $twitter_post_id));
		if ( $this->db->count_all_results() == 0 )
			return;
		$row = $query->row();
		$this->setModel( $twitter_post_id, $row->twitter_user_name, $row->time_period_id, $row->content, $row->published_datetime );
	}
	
	public function save() {
		$this->db->from('twitter_post')->where('twitter_post_id', $this->twitter_post_id);
		if ( $this->db->count_all_results() == 0 )
			$this->db->insert('twitter_post', $this);
		else
			$this->db->where('twitter_post_id', $this->twitter_post_id)->update('twitter_post', $this);
	}
	
	// Set Model data
	public function setModel( $twitter_post_id, $twitter_user_name, $time_period_id, $content, $published_datetime, $autoload = true ) {
		$this->twitter_post_id = $twitter_post_id;
		$this->twitter_user_name = $twitter_user_name;
		$this->time_period_id = $time_period_id;
		$this->content = $content;
		$d = new DateTime( $published_datetime );
		$this->published_datetime = $d->format('Y-m-d H:i:s');
		if ( $autoload )
			$this->loadTwitterUser();
	}

	// Get TwitterUser Model based on twitterScreenName
	// Force the Load of the TwitterUser if the twitterScreenName
	// was updated independently of the setModel() function.
	// Otherwise, you'll get old results.
	public function getTwitterUser( $force = false ) {
		if ( !$this->_twitter_user || $force )
			$this->loadTwitterUser();
		return $this->_twitter_user;
	}
	
	// Loads TwitterUser Model based on twitterScreenName
	public function loadTwitterUser() {
		$this->_twitter_user = $this->Twitter_User->newInstance();
		$this->_twitter_user->load( $this->twitter_user_name );
	}
	
	// Get TimePeriod Model based on timePeriodId
	public function getTimePeriod() {
	}
	
	// Permalink to the Twitter Post on the Twitter site
	public function getTwitterPostURL() {
		return $this->getTwitterUser()->getTwitterUserProfileURL().'/status/'.$this->twitter_post_id;
	}
	
	// Find and add links, hash tags, terms, etc. to twitter post content
	public function getProcessedContent() {
		return $this->content;
	}
	
}

?>