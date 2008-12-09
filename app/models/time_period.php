<?php

class Time_Period extends Model {
	
	public $time_period_id = 0;
	public $start_date = '';
	public $end_date = '';
	public $recalculate_flag = 0;
	
	function Time_Period() {
		parent::Model();
		$this->load->database();
		$this->load->helper('twerm_helper');
	}
	
	public function newInstance() {
		return new Time_Period();
	}
	
	// Set Model data
	public function setModel( $time_period_id, $start_date, $end_date, $recalculate_flag = 0 ) {
		$this->time_period_id = $time_period_id;
		$this->start_date = $start_date;
		$this->end_date = $end_date;
		$this->recalculate_flag = $recalculate_flag;
	}
	
	public function setModelByObject( $obj ) {
		$this->setModel( $obj->time_period_id, $obj->start_date, $obj->end_date, $obj->recalculate_flag );
	}
	
	// Load Model data based on twitter_screen_name
	public function load( $time_period_id ) {
		$query = $this->db->get_where('time_period', array('time_period_id' => $time_period_id));
		return $this->_load( $query );
	}
	
	public function loadByDate( $date ) {
		$query = $this->db->get_where('time_period', array('start_date' => $date));
		return $this->_load( $query );
	}
	
	private function _load( $queryResults ) {
		if ( $queryResults->num_rows() == 0 )
			return false;
		$this->setModelByObject( $queryResults->row() );
		return true;
	}
	
	public function save() {
		$this->db->from('time_period')->where('time_period_id', $this->time_period_id);
		if ( $this->db->count_all_results() == 0 ) {
			$query = $this->db->get_where('time_period', array('start_date' => $this->start_date, 'end_date' => $this->end_date));
			if ( $query->num_rows() == 0 )
				$this->db->insert('time_period', $this);
			else
				$this->time_period_id = $query->row()->time_period_id;
		} else
			$this->db->update('time_period', $this, array('time_period_id' => $this->time_period_id));
	}
	
	public function getDate() {
		return twerm_date( $this->start_date );
	}
	
	public function getURL() {
		return '/'.twerm_date( $this->start_date, '/' );
	}

	public function getMonth() {
		return twerm_month( $this->start_date );
	}
	
	public function getMonthURL() {
		return '/'.twerm_month( $this->start_date, '/' );
	}
	public function getTermsURL() {
		return $this->getURL().'/terms';
	}	
	
	public function getTimelineURL() {
		return $this->getURL().'/timeline';
	}
	
	public function getPreviousTimePeriodId() {

		// 
		// Statically or manually group Time Periods?
		// e.g. Single day, single week, single month, single year
		// 
		// Divide by zero?
//	SELECT *, time_period_id, (DATEDIFF(end_date, start_date) / 5) AS time_span_match FROM `time_period` WHERE time_period_id != 1 AND end_date <= "2008-10-31" HAVING time_span_match BETWEEN .8 AND 1.2 ORDER BY time_span_match DESC

		$query = $this->db->query('SELECT time_period_id FROM time_period WHERE end_date <= ? AND DATEDIFF(end_date, start_date) = ? ORDER BY start_date DESC LIMIT 1', array($this->end_date, 0));
		
		$row = $query->row();
		
		return $row->time_period_id;
	}
	
	public function calculateTFIDF() {
		
		// Remove any previous term calculations for this Time Period
		$this->db->delete('time_period_term', array('time_period_id' => $this->time_period_id));
		
		// Populate Term Frequency for a Time Period range
		$query = $this->db->query('INSERT INTO time_period_term (time_period_id, term, count) SELECT time_period.time_period_id, twitter_post_term.term, SUM(twitter_post_term.count) FROM twitter_post_term, twitter_post, time_period WHERE twitter_post_term.twitter_post_id = twitter_post.twitter_post_id AND DATE_FORMAT(twitter_post.published_datetime, "%Y-%m-%d") BETWEEN DATE_FORMAT(time_period.start_date, "%Y-%m-%d") AND DATE_FORMAT(time_period.end_date, "%Y-%m-%d") AND time_period.time_period_id = ? GROUP BY twitter_post_term.term', array($this->time_period_id));
		
		// Calculate the total number of Terms mentioned in the Time Period
		$query = $this->db->query('SELECT SUM(count) AS total_term_count FROM time_period_term WHERE time_period_id = ?', array('time_period_id' => $this->time_period_id));
		$totalTermCount = $query->row()->total_term_count;
		
		// Calculate the total number of Time Periods
		$totalTimePeriods = $this->db->count_all('time_period');
		
		
		// Delete temporary Document Frequency table
		$this->db->query('DROP TEMPORARY TABLE IF EXISTS temp_df');
		
		// Delete temporary Term table
		$this->db->query('DROP TEMPORARY TABLE IF EXISTS temp_terms');
		
		
		// Calculate all Terms' Document Frequency
		$this->db->query('CREATE TEMPORARY TABLE temp_df SELECT term, COUNT(*) AS document_frequency FROM time_period_term GROUP BY term');

		// Merge Document Frequency calculations back with a list of Terms in the Time Period
		$this->db->query('CREATE TEMPORARY TABLE temp_terms SELECT time_period_term.term AS term, time_period_term.count AS count, temp_df.document_frequency AS document_frequency FROM time_period_term, temp_df WHERE time_period_term.term = temp_df.term AND time_period_id = ?', array($this->time_period_id));
		
		// Delete temporary Document Frequency table
		$this->db->query('DROP TEMPORARY TABLE IF EXISTS temp_df');
		
		// Calculate and store TF-IDF score for each Term in the Time Period
		$this->db->query('UPDATE time_period_term, temp_terms SET time_period_term.tf_idf = ( ( temp_terms.count / ? ) * LOG( ? / temp_terms.document_frequency ) ) WHERE time_period_term.term = temp_terms.term AND time_period_term.time_period_id = ?', array($totalTermCount, $totalTimePeriods, $this->time_period_id));

		// Delete temporary Term table
		$this->db->query('DROP TEMPORARY TABLE IF EXISTS temp_terms');
		
		// Calculate and store ranks based on TF-IDF score
		$this->db->query('SET @rowcount = 0');
		$this->db->query('UPDATE time_period_term SET rank = @rowcount := @rowcount + 1 WHERE time_period_id = ? ORDER BY tf_idf DESC', array($this->time_period_id));

		/*
		POWER RANKS - PSEUDO
		
		-- CREATE TEMPORARY TABLE power_rankings SELECT term, rank, ( t2.rank - t1.rank ) AS power_rank FROM time_period_term JOIN time_period_term USING ( term ) WHERE t1.time_period_id = $t1_id && t2.time_period_id = $t2_id;
		
		-- UPDATE time_period_term SET power_rank = ( SELECT power_rank FROM power_rankings WHERE term = time_period_term.term );
		
		-- DROP TEMPORARY TABLE power_rankings;
		*/
		
		/*
		TWITTER_USER RANKS - NECCESSARY?
		count TFIDF for each term a user used
		*/
		
		$this->recalculate_flag = 0;
		$this->save();
	}
}

?>