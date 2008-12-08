<?php

class Twitter_User extends Model {
	
	public $twitter_user_name = '';
	public $twitter_profile_image_url = '';
	
	function Twitter_User() {
		parent::Model();
		$this->load->database();
	}
	
	public function newInstance() {
		return new Twitter_User();
	}
	
	// Set Model data
	public function setModel( $twitter_user_name, $twitter_profile_image_url ) {
		$this->twitter_user_name = $twitter_user_name;
		$this->twitter_profile_image_url = $twitter_profile_image_url;
	}
	
	public function setModelByObject( $obj ) {
		$this->setModel( $obj->twitter_user_name, $obj->twitter_profile_image_url );
	}
	
	// Load Model data based on twitter_screen_name
	public function load( $twitter_user_name = '' ) {
		$query = $this->db->get_where('twitter_user', array('twitter_user_name' => $twitter_user_name));
		if ( $query->num_rows() == 0 )
			return;
		$row = $query->row();
		$this->setModel( $twitter_user_name, $row->twitter_profile_image_url );
	}
	
	public function save() {
		$this->db->from('twitter_user')->where('twitter_user_name', $this->twitter_user_name);
		if ( $this->db->count_all_results() == 0 )
			$this->db->insert('twitter_user', $this);
		else
			$this->db->where('twitter_user_name', $this->twitter_user_name)->update('twitter_user', $this);
	}
	
	// Permalink to the Twitter User on the Twitter site
	public function getTwitterUserProfileURL() {
		return 'http://twitter.com/'.$this->twitter_user_name;
	}
	
}

?>