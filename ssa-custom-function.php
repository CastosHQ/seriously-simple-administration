<?php

function ssa_get_custom_posts() {
	$posts = [
		[
			626,
			'episode-missing',
			130715,
			'Episode does not exist for the Post',
			'file-missing',
			147309,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR078-Kathryn-Bloxham.mp3'
		],
		[
			624,
			'episode-missing',
			130317,
			'Episode does not exist for the Post',
			'file-missing',
			146825,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR077-Dominic-Collins.mp3'
		],
		[
			622,
			'episode-missing',
			129598,
			'Episode does not exist for the Post',
			'file-missing',
			145548,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR076-Jordan-Lawver.mp3'
		],
		[
			620,
			'episode-missing',
			129160,
			'Episode does not exist for the Post',
			'file-missing',
			145547,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR075-Greg-Demchak.mp3'
		],
		[
			617,
			'episode-missing',
			128521,
			'Episode does not exist for the Post',
			'file-missing',
			144833,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR074-Dave-Sime.mp3'
		],
		[
			615,
			'episode-missing',
			127963,
			'Episode does not exist for the Post',
			'file-missing',
			144142,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR73-James-Watson-Justin-Parry.mp3'
		],
		[
			607,
			'episode-missing',
			126912,
			'Episode does not exist for the Post',
			'file-missing',
			142971,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR072-Christophe-Mallet.mp3'
		],
		[
			604,
			'episode-missing',
			126379,
			'Episode does not exist for the Post',
			'file-missing',
			142392,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR071-Yan-Simard.mp3'
		],
		[
			602,
			'episode-missing',
			125480,
			'Episode does not exist for the Post',
			'file-missing',
			141435,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR070-Nick-Cherukuri.mp3'
		],
		[
			600,
			'episode-missing',
			125025,
			'Episode does not exist for the Post',
			'file-missing',
			140919,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR069-Mike-Campbell.mp3'
		],
		[
			595,
			'episode-missing',
			124552,
			'Episode does not exist for the Post',
			'file-missing',
			140402,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR068-Teppei-Tsutsui.mp3'
		],
		[
			593,
			'episode-missing',
			124056,
			'Episode does not exist for the Post',
			'file-missing',
			139822,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR067-Sandro-Tavares.mp3'
		],
		[
			591,
			'episode-missing',
			123673,
			'Episode does not exist for the Post',
			'file-missing',
			139366,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR066-Jon-Cheney.mp3'
		],
		[
			588,
			'episode-missing',
			123150,
			'Episode does not exist for the Post',
			'file-missing',
			138792,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR065-Kai-Liang.mp3'
		],
		[
			586,
			'episode-missing',
			122644,
			'Episode does not exist for the Post',
			'file-missing',
			138222,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR064-Lou-Pushelberg.mp3'
		],
		[
			584,
			'episode-missing',
			122242,
			'Episode does not exist for the Post',
			'file-missing',
			137757,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR063-Benjamin-De-Wit.mp3'
		],
		[
			582,
			'episode-missing',
			121919,
			'Episode does not exist for the Post',
			'file-missing',
			137387,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR062-Michael-Shabun.mp3'
		],
		[
			579,
			'episode-missing',
			121261,
			'Episode does not exist for the Post',
			'file-missing',
			136660,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR061-Michael-Mansouri.mp3'
		],
		[
			577,
			'episode-missing',
			120840,
			'Episode does not exist for the Post',
			'file-missing',
			136175,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR060-Paul-Travers.mp3'
		],
		[
			574,
			'episode-missing',
			120483,
			'Episode does not exist for the Post',
			'file-missing',
			135758,
			'File does not exist for the Post',
			'https://episodes.castos.com/xrforbusiness/XR059-Shachar-Vice-Weis.mp3'
		],
	];

	return $posts;
}

/**
 * Generic place to put custom customer specific functionality
 */
function ssa_custom_function() {
	return;
	set_time_limit( 0 );
	$posts = ssa_get_custom_posts();

	foreach ( $posts as $post ) {
		$post_ID        = $post[0];
		$episode_status = $post[1];
		$file_status    = $post[4];
		$file_path      = $post[7];

		$castos_handler = new \SeriouslySimplePodcasting\Handlers\Castos_Handler();

		if ( 'file-missing' === $file_status ) {
			// create file record and update postmeta
			$response = $castos_handler->upload_podmotor_storage_file_data_to_podmotor( $file_path );
			if ( 'success' === $response['status'] ) {
				update_post_meta( $post_ID, 'podmotor_file_id', $response['file_id'] );
				echo '<p> Updated podmotor_file_id to ' . $response['file_id'] . ' for post ' . $post_ID . ' with audio file URL ' . $file_path . '</p>';  //phpcs:ignore
			}
		}

		if ( 'episode-missing' === $episode_status ) {
			// store the episode record and update postmeta
			$post = get_post( $post_ID );
			// clear old podmotor_episode_id
			delete_post_meta( $post_ID, 'podmotor_episode_id' );
			$response = $castos_handler->upload_podcast_to_podmotor( $post );
			if ( 'success' === $response['status'] ) {
				update_post_meta( $post_ID, 'podmotor_episode_id', $response['episode_id'] );
				echo '<p> Updated podmotor_episode_id to ' . $response['episode_id'] . ' for post ' . $post_ID . '</p>';  //phpcs:ignore
			}
		}
	}
}