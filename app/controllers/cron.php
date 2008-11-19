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
		}
		
		$twitter_post_data['twitter_posts'] = $a;

		$this->template->write('title', 'Tweets | '.$username);
		$this->template->write_view('content', 'timeline', $twitter_post_data);
		$this->template->render();
	}

}

?>