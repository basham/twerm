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
		
		$json = file_get_contents( $json_url.'from='.$username.'&since='.$end.'&until='.$end );
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
			$tp->setModel( $tweet['id'], $tweet['from_user'], 1, $tweet['text'], $tweet['created_at']);
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
		foreach( $this->users as $u )
			$this->user( $u, $date );
	}
private $c = 0;
	public $users = array('BarackObama', 'kevinrose', 'leolaporte', 'cnnbrk', 'alexalbrecht', 'JasonCalacanis', 'Scobleizer', 'Veronica', 'THErealDVORAK', 'ricksanchezcnn', 'TechCrunch', 'hotdogsladies');
	
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
	
	function tfidf() {
		$this->load->model('Time_Period');
		$t = $this->Time_Period->newInstance();
		$t->load(1);
		$t->calculateTFIDF();
	}
	
	function month( $m = 11, $y = 2008 ) {
		
		$this->load->helper('date');
		$this->load->model('Time_Period');
		
		for( $d = 1; $d <= days_in_month($m, $y); $d++ ) {
			$dt = new DateTime( $y.'-'.$m.'-'.$d );
			$date = $dt->format('Y-m-d');
			$t = $this->Time_Period->newInstance();
			$t->setModel(0, $date, $date);
			$t->save();
			echo '<strong>'.$date.'</strong><br/>';
			
			$this->loop($date);
			$t->calculateTFIDF();
			echo '<br/>';
		}
	}
	
}

?>