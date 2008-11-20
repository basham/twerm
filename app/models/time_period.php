<?php

class Time_Period extends Model {
	
	public $time_period_id = 0;
	public $time_period_date = '';
	public $start_datetime = '';
	public $end_datetime = '';
	
	function Time_Period() {
		parent::Model();
		$this->load->database();
	}
	
	public function newInstance() {
		return new Time_Period();
	}
	
	// Set Model data
	public function setModel( $time_period_id, $time_period_date, $start_datetime, $end_datetime ) {
		$this->time_period_id = $time_period_id;
		$this->time_period_date = $time_period_date;
		$this->start_datetime = $start_datetime;
		$this->end_datetime = $end_datetime;
	}
	
	// Load Model data based on twitter_screen_name
	public function load( $time_period_id ) {
		$query = $this->db->get_where('time_period', array('time_period_id' => $time_period_id));
		if ( $query->num_rows() == 0 )
			return;
		$row = $query->row();
		$this->setModel( $twitter_user_name, $row->twitter_profile_image_url );
	}
	
	public function save() {
		$this->db->from('time_period')->where('time_period_id', $this->time_period_id);
		if ( $this->db->count_all_results() == 0 )
			$this->db->insert('time_period', $this);
		else
			$this->db->where('time_period_id', $this->time_period_id)->update('time_period', $this);
	}
	
}

?>