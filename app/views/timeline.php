<?php

foreach( $twitter_posts as $twitter_post ) {

	echo '<div class="twitter-post">';

	$user = $twitter_post->getTwitterUser();

	echo '<a href="'.$user->getTwitterUserProfileURL().'"><img class="profile-img" src="'.$user->twitter_profile_image_url.'"/></a>';

	echo '<p>';
	
	echo '<strong><a href="'.$user->getTwitterUserProfileURL().'">'.$user->twitter_user_name.'</a></strong>';

	echo '<span class="entry-content">'.$twitter_post->getProcessedContent().'</span>';

	echo '<span class="entry-date"><a href="'.$twitter_post->getTwitterPostURL().'">'.$twitter_post->published_datetime.'</a></span>';
	
	echo '</p>';
	
	echo '<br class="clear"/>';
	
	echo '</div>';

}

?>