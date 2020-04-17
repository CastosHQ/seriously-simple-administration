<?php
/**
 * Generic place to put custom customer specific functionality
 */
function ssa_custom_function() {
	$post                = get_post( 628 );
	$podmotor_file_id    = get_post_meta( $post->ID, 'podmotor_file_id', true );
	$podmotor_episode_id = get_post_meta( $post->ID, 'podmotor_episode_id', true );
	$series_id           = ssp_get_episode_series_id( $post->ID );
	$post_body           = array(
		'post_id'        => $post->ID,
		'post_title'     => $post->post_title,
		'post_content'   => $post->post_content,
		'keywords'       => get_keywords_for_episode( $post->ID ),
		'series_number'  => get_post_meta( $post->ID, 'itunes_season_number', true ),
		'episode_number' => get_post_meta( $post->ID, 'itunes_episode_number', true ),
		'episode_type'   => get_post_meta( $post->ID, 'itunes_episode_type', true ),
		'post_date'      => $post->post_date,
		'file_id'        => $podmotor_file_id,
		'series_id'      => $series_id,
	);
	if ( ! empty( $podmotor_episode_id ) ) {
		$post_body['id'] = $podmotor_episode_id;
	}
	echo '<textarea cols="200" rows="25">';
	echo print_r( wp_json_encode( $post_body, JSON_PRETTY_PRINT ), true );
	echo '</textarea>';
}
