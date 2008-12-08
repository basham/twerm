<?php

class Twitter_Post extends Model {
	
	public $twitter_post_id = 0;
	public $twitter_user_name = '';
	public $time_period_id = 0;
	public $content = '';
	public $published_datetime = '';
	
	private $_twitter_user = null;
	private $_time_period = null;
	private $_uniqueTerms = array();
	
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
	
	public function setModelByObject( $obj ) {
		$this->setModel( $obj->twitter_post_id, $obj->twitter_user_name, $obj->time_period_id, $obj->content, $obj->published_datetime );
	}
	
	// Load Model data based on TwitterPostId
	public function load( $twitter_post_id = 0 ) {
		$query = $this->db->get_where('twitter_post', array('twitter_post_id' => $twitter_post_id));
		if ( $query->num_rows() == 0 )
			return;
		$this->setModelByObject( $query->row() );
	}
	
	// Loads TwitterUser Model based on twitterScreenName
	public function loadTwitterUser() {
		$this->_twitter_user = $this->Twitter_User->newInstance();
		$this->_twitter_user->load( $this->twitter_user_name );
	}
	
	public function setTwitterUser( $twitterUser ) {
		$this->_twitter_user = $twitterUser;
	}
	
	public function save() {
		$this->db->from('twitter_post')->where('twitter_post_id', $this->twitter_post_id);
		if ( $this->db->count_all_results() == 0 )
			$this->db->insert('twitter_post', $this);
		else
			$this->db->where('twitter_post_id', $this->twitter_post_id)->update('twitter_post', $this);
		$this->calculateTermFrequency();
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
	
	public function getAllTwitterPosts() {
		$query = $this->db->select('twitter_post_id')->get('twitter_post');
		return $query->result();
	}
	
	public function calculateTermFrequency() {

		// Remove any previous term calculations for this Twitter Post
		$this->db->delete('twitter_post_term', array('twitter_post_id' => $this->twitter_post_id));
		
		$this->_uniqueTerms = array();
		
		// Deliminate words by any space characters, decode HTML entities
		$splitArray =  split( '[[:space:]]' , htmlspecialchars_decode($this->content) );

		// Find word frequency in the array
		foreach( $splitArray as $word ) {
			
			/*
			TODO: Allow words that are not purely non-alpha: l33t sp3@c4 G4
			*/
			
			// Ignore referers ( @leolaporte ), URLs ( http://www.google.com )
			if ( preg_match( '/(^@|:\/\/)/', $word, $matches ) > 0 )
				continue;
			
			// Replace any non-alphanumeric/# characters at the beginning or end of a word, lowercase
			$word = ereg_replace( '(^[^a-zA-Z0-9#]+)|([^a-zA-Z0-9]+$)', '', strtolower($word) );
			
			// Ingore a word with more than one internal non-alphanumeric character or a word of no length
			if ( preg_match( '/([^a-zA-Z]{2,}|[0-9]+)/', $word, $matches ) > 0 || strlen($word) == 0 )
				continue;
				
			// Store word frequency
			if ( array_key_exists( $word, $this->_uniqueTerms ) )
				$this->_uniqueTerms[$word] += 1;
			else
				$this->_uniqueTerms[$word] = 1;
		}
		/*
		echo '<p>'.$this->content.'</p>';
		echo '<p>';
		print_r($splitArray);
		echo '</p><p>';
		print_r($this->_uniqueTerms);
		echo '</p>';
		*/
		foreach( $this->_uniqueTerms as $term => $count )
			$this->saveTermFrequency( $term, $count );
		
		$d = new DateTime( $this->published_datetime );
		$date = $d->format('Y-m-d');
		
		$this->db->update('time_period', array('recalculate_flag' => 1), array('start_date >=' => $date, 'end_date <=' => $date));
	}
	
	private function saveTermFrequency( $term, $count ) {

		// Insert Term if its not already stored
		$query = $this->db->from('twitter_term')->where('term', $term);
		if ( $this->db->count_all_results() == 0 )
			$this->db->insert('twitter_term', array('term' => $term));
	
		// Insert or update Twitter_Post_Term
		//$query = $this->db->get_where('twitter_post_term', array('twitter_post_id' => $this->twitter_post_id, 'term' => $term));
	//	if ( $query->num_rows() == 0 )
		$this->db->insert('twitter_post_term', array('twitter_post_id' => $this->twitter_post_id, 'term' => $term, 'count' => $count));
		//else
		//	$this->db->update('twitter_post_term', array('count' => $count), array('twitter_post_id' => $this->twitter_post_id, 'term' => $term));
	}
	
}

?>