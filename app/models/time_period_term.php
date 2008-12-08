<?php

class Time_Period_Term extends Model {
	
	public $time_period_id = 0;
	public $term = '';
	public $count = 0;
	public $rank = 0;
	public $power_rank = 0;
	
	function Time_Period_Term() {
		parent::Model();
		$this->load->database();
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
	
	// Load Model data based on TwitterPostId
	public function load( $time_period_id, $term ) {
		$query = $this->db->get_where('time_period_term', array('time_period_id' => $time_period_id, 'term' => $term));
		if ( $query->num_rows() == 0 )
			return;
		$this->setModelByObject( $query->row() );
	}
	
}

?>