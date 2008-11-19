<?php

class Term_Controller extends Controller {
	
	function Term_Controller() {
		parent::Controller();
		$this->load->library('template');
	}
	
	function index() {
		$this->lookup();
		//$this->template->write('title', 'Twerm');
		//$this->template->render();
	}
	
	function lookup( $term = '' ) {
		
		$this->load->model('Twitter_Post');
		
		$data['term'] = $term;
		
		$head_data['title'] = array('Term', $term);
		
		$a = array();
		
		$b = $this->Twitter_Post->newInstance();
		$b->setModel(994382904, 'chrisbasham', 1, "Played some levels on the World of Goo http://tr.im/vjo Mac demo. I like the WiiWare version better. It plays better w/ Wiimote than mouse.", 'Nov 6, 2008');
		$a[] = $b;

		$c = $this->Twitter_Post->newInstance();
		$c->setModel(994418430, 'kevinrose', 1, 'wow, friend just brought one over, making coffee w/it now: <a href="http://www.pomegranatephone.com/" rel="nofollow" target="_blank">http://www.pomegranatephone...</a>', 'Nov 7, 2008');
		$a[] = $c;
		
		$d = $this->Twitter_Post->newInstance();
		$d->setModel(994418430, 'gladus', 1, 'this is a triump', 'Oct 31, 2008');
		$a[] = $d;
		
		$e = $this->Twitter_Post->newInstance();
		$e->setModel(994418430, 'cakeisalie', 1, 'this is a triump of huge success', 'Oct 20, 2008');
		$a[] = $e;
		
		$twitter_post_data['twitter_posts'] = $a;
		
		//$this->template->write_view('head', 'head', $head_data)
		//$this->load->view('term_history', $data);
		$this->template->write('title', 'Twerm | Term | '.$term);
		//
		// 	$this->template->write_view('content', 'term_history', $data);
		$this->template->write_view('content', 'timeline', $twitter_post_data);
		$this->template->render();
	}

}

?>