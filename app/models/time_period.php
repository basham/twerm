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
		$this->setModel( $time_period_id, $row->time_period_date, $row->start_datetime, $row->end_datetime );
	}
	
	public function save() {
		$this->db->from('time_period')->where('time_period_id', $this->time_period_id);
		if ( $this->db->count_all_results() == 0 )
			$this->db->insert('time_period', $this);
		else
			$this->db->where('time_period_id', $this->time_period_id)->update('time_period', $this);
	}
	
	public function calculateTFIDF() {
		
		// Calculate the total number of Twitter Posts stored
		$totalTwitterPosts = $this->db->count_all('twitter_post');
		
		// Calculate the total number of Terms mentioned in the Time Period
		$query = $this->db->query('SELECT SUM(count) AS total_term_count FROM time_period_term WHERE time_period_id = ?', array('time_period_id' => $this->time_period_id));
		$totalTermCount = $query->row()->total_term_count;
		
		
		/*
		
		TODO:
		-----
		Is a Time Period a document or a Twitter Post?
		Document Frequency may be miscalculated due to the Time_Period_ID restriction
		
		*/
		
		// Calculate all Terms' Document Frequency, Total Frequency
		$query = $this->db->query('SELECT time_period_term.term AS term, time_period_term.count AS count, COUNT(*) AS document_frequency FROM twitter_post_term, time_period_term WHERE twitter_post_term.term = time_period_term.term && time_period_id = ? GROUP BY term', array($this->time_period_id));
		
		foreach( $query->result() as $row ) {
			
			// Calculate Term Frequency
			$tf = ( $row->count / $totalTermCount );
			
			// Calculate Inverse Document Frequency
			$idf = log( $totalTwitterPosts / $row->document_frequency );
			
			// Calculate Term Frequency-Inverse Document Frequency
			$tf_idf = $tf * $idf;

			// Update TF-IDF Score
			$this->db->update('time_period_term', array('tf_idf' => $tf_idf), array('time_period_id' => $this->time_period_id, 'term' => $row->term));

			echo $row->term . ' '. $row->count.' ' . $tf . ' ' . $idf . ' ' . $tf_idf . '<br/><br/>';
		}
			
		$this->calculateRanks();
	}
	
	private function calculateRanks() {
		
		// Stores ranks based on TF-IDF score
		$query = $this->db->query('SET @rowcount = 0');
		$query = $this->db->query('UPDATE time_period_term SET rank = @rowcount := @rowcount + 1 WHERE time_period_id = ? ORDER BY tf_idf DESC', array($this->time_period_id));
		
		
		/*
		POWER RANKS - PSEUDO
		
		-- CREATE TEMPORARY TABLE power_rankings SELECT term, rank, ( t2.rank - t1.rank ) AS power_rank FROM time_period_term JOIN time_period_term USING ( term ) WHERE t1.time_period_id = $t1_id && t2.time_period_id = $t2_id;
		
		-- UPDATE time_period_term SET power_rank = ( SELECT power_rank FROM power_rankings WHERE term = time_period_term.term );
		
		-- DROP TEMPORARY TABLE power_rankings;
		*/
	}
}

?>