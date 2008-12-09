<?php

class Cron extends Controller {
	
	function Cron() {
		parent::Controller();
		$this->load->library('template');
	}
	
	function index() {
		$this->user();
	}
	
	function user( $username = 'kevinrose', $date = '2008-11-15' ) {
		
		set_time_limit(10);
		
		$this->load->model('Twitter_Post');
		$this->load->model('Twitter_User');
		
		$date = new DateTime( $date );
		$end = $date->format('Y-m-d');
		$date->modify('-1 day');
		$start = $date->format('Y-m-d');
		
		$json_url = 'http://search.twitter.com/search.json?';
		
		try {
			$json = file_get_contents( $json_url.'from='.$username.'&since='.$end.'&until='.$end );
		}
		catch( Exception $e ) {
			return;
		}
		
		$tweets = json_decode( $json, TRUE );

		//print_r($tweets['results'][0]);
		
		$a = array();

		foreach( $tweets['results'] as $tweet ) {
			
			// Saves User to DB
			$tu = $this->Twitter_User->newInstance();
			$tu->setModel( $tweet['from_user'], $tweet['profile_image_url'] );
			$tu->save();
			
			// Saves Tweet to DB
			$tp = $this->Twitter_Post->newInstance();
			$tp->setModel( $tweet['id'], $tweet['from_user'], $tweet['text'], $tweet['created_at']);
			$tp->save();

			$a[] = $tp;
			$this->c += 1;
		}
		
		$twitter_post_data['twitter_posts'] = $a;
		echo $this->c.' '.count($a).' '.$username.'<br/>';



		//$this->template->write('title', 'Tweets | '.$username);
		//$this->template->write_view('content', 'timeline', $twitter_post_data);
		//$this->template->render();
	}
	
	function loop( $date = '2008-11-15' ) {
		
		echo '<p><strong>'.$date.'</strong></p>';
		
		$this->load->model('Time_Period');
		$this->load->database();
		
		$query = $this->db->get('twitter_user');
		
		foreach( $query->result() as $row )
			$this->user( $row->twitter_user_name, $date );
			
		$t = $this->Time_Period->newInstance();
		$t->setModel(0, $date, $date);
		$t->save();
		$t->loadByDate($date);

		$t->calculateTFIDF();
	}
private $c = 0;
	public $users = array('BarackObama', 'kevinrose', 'leolaporte', 'cnnbrk', 'alexalbrecht', 'JasonCalacanis', 'Scobleizer', 'Veronica', 'THErealDVORAK', 'ricksanchezcnn', 'TechCrunch', 'hotdogsladies', 'twitter', 'macrumors', 'hodgman', 'patricknorton', 'timoreilly', 'TheOnion', 'Ihnatko', 'alexlindsay', 'twitlive', 'photomatt', 'kevinrose', 'zeldman', 'nytimes', 'majornelson', 'davewiner', 'gruber', 'chrispirillo', 'guykawasaki', 'ijustine', 'ev', 'wilw', 'nickbasham', 'garyvee', 'problogger', 'feliciaday', 'jowyang', 'ambermacarthur', 'om', 'jeffcannata', 'sarahlane', 'buckhollywood', 'leahculver', 'nick', 'Aubs');
	
	function loopy() {
		$this->load->model('Twitter_Post');
		$r = $this->Twitter_Post->getAllTwitterPosts();
		foreach( $r as $row )
			$this->tf( $row->twitter_post_id );
	}
	
	function tf( $twitter_post_id = 986853782 ) {
		$this->load->model('Twitter_Post');
		$t = $this->Twitter_Post->newInstance();
		$t->load($twitter_post_id);
		$t->calculateTermFrequency();
	}
	
	function tfidf($id = 1) {
		$this->load->model('Time_Period');
		$t = $this->Time_Period->newInstance();
		$t->load($id);
		$t->calculateTFIDF();
	}
	
	function month( $m = 11, $y = 2008 ) {
		
		$this->load->helper('date');
		$this->load->model('Time_Period');
		//days_in_month($m, $y)
		for( $d = 1; $d <= 14; $d++ ) {
			$dt = new DateTime( $y.'-'.$m.'-'.$d );
			$date = $dt->format('Y-m-d');
			//$t = $this->Time_Period->newInstance();
			//$t->setModel(0, $date, $date);
			//$t->save();
			echo '<strong>'.$date.'</strong><br/>';
			
			$this->loop($date);
			//$t->calculateTFIDF();
			echo '<br/>';
		}
	}
	
	function loopinsertusers() {
		foreach( $this->users as $user )
			$this->insertuser( $user );
	}
	
	function insertuser( $user ) {
		
		$this->load->model('Twitter_User');
		
		$tu = $this->Twitter_User->newInstance();
		$tu->setModel( $user, '' );
		$tu->save();
	}
	
	function next() {
		$this->load->database();
		$query = $this->db->query('SELECT ADDDATE(start_date, 1) AS next_date FROM `time_period` ORDER BY start_date DESC LIMIT 1');
		if ( $query->num_rows() == 0 )
			$date = "2008-09-01";
		else
			$date = $query->row()->next_date;
		if ( $date >= "2008-12-08" )
			return;
		$this->loop( $date );
	}
	
}

?>