<?php
/**
 * Generic place to put custom customer specific functionality
 */
function ssa_custom_function() {
	$episode_ids = array();
	foreach ( $episode_ids as $episode ) {
		update_post_meta( $episode['ID'], 'podmotor_episode_id', $episode['podmotor_episode_id'] );
		update_post_meta( $episode['ID'], 'podmotor_file_id', $episode['podmotor_file_id'] );
		echo 'Episode ID ' . $episode['ID'] . ' updated</br>';
	}
	echo '<p>Complete</p>';
}
