<?php

foreach( $twitter_posts as $twitter_post ) {
	$data['twitter_post'] = $twitter_post;
	$this->load->view('twitter_post', $data);
}

?>