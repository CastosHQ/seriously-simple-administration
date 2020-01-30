<?php

/**
 * Generic place to put custom customer specific functionality
 */
function ssa_custom_function() {
	$episodes = array();

	foreach ( $episodes as $episode ) {
		update_post_meta( $episode['post_id'], 'audio_file', $episode['file_path'] );
		echo '<p> Updated post ' . $episode['post_id'] . ' with audio file URL ' . $episode['file_path'] . '</p>';  //phpcs:ignore
	}
}
