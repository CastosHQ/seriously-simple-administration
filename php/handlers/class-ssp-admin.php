<?php

namespace SSA\Handlers;

use WP_Query;

class SSP_Admin {

	/**
	 * Export podcasts which were skipped for some reason
	 * */
	public static function ssa_export_all_episodes() {
		Castos_Exporter::export_all_episodes_with_file_id( false );
	}

	/**
	 * Export podcasts which were skipped for some reason
	 * */
	public static function ssa_schedule_export_all_episodes() {
		Castos_Exporter::export_all_episodes_with_file_id();
	}

	/**
	 * Export podcasts which were skipped for some reason
	 * */
	public static function export_missed_episodes() {
		$limit = filter_input( INPUT_GET, 'limit' );
		$limit = $limit ?: 10;
		Castos_Exporter::export_missed_episodes_with_file_id( $limit );
	}

	/**
	 * Delete all Castos Episode IDS
	 */
	public static function ssa_reset_episodes() {
		global $wpdb;
		$postmeta = $wpdb->prefix . 'postmeta';

		$sql = "DELETE FROM `$postmeta` WHERE `meta_key` = 'podmotor_episode_id'";
		$wpdb->query( $sql );
		$wpdb->flush();
	}

	/*
	 * Reset Import options
	 */
	public static function ssa_reset_import() {
		delete_option( 'ss_podcasting_podmotor_import_podcasts' );
		delete_option( 'ss_podcasting_podmotor_queue_id' );
	}

	/**
	 * Reset API account details
	 */
	public static function ssa_reset_account_details() {
		delete_option( 'ss_podcasting_podmotor_account_email' );
		delete_option( 'ss_podcasting_podmotor_account_api_token' );
		delete_option( 'ss_podcasting_podmotor_account_id' );
	}

	/**
	 * Get a list of episodes in JSON format, without the content
	 */
	public static function ssa_get_safe_podcast_json() {
		$podcast_post_types = ssp_post_types( true );
		$args               = array(
			'post_type'      => $podcast_post_types,
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'orderby'        => 'post_date',
			'order'          => 'DESC',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => 'audio_file',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => 'audio_file',
					'value'   => '',
					'compare' => '!=',
				)
			),
		);
		$podcast_query      = new WP_Query( $args );
		$podcasts           = $podcast_query->get_posts();

		$podcast_data = array();
		foreach ( $podcasts as $podcast ) {
			$podcast_data[ $podcast->ID ] = array(
				'post_id'      => $podcast->ID,
				'post_title'   => $podcast->post_title,
				'post_content' => '',
				'post_date'    => $podcast->post_date,
				'audio_file'   => get_post_meta( $podcast->ID, 'audio_file', true ),
			);
		}
		echo '<textarea cols="200" rows="25">';
		print_r( wp_json_encode( $podcast_data, JSON_PRETTY_PRINT ) );
		echo '</textarea>';
	}

	/**
	 * Get a list of episodes, in CSV format
	 */
	public static function ssa_get_podcast_data_csv() {
		$podcast_post_types = ssp_post_types( true );
		$args               = array(
			'post_type'      => $podcast_post_types,
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'orderby'        => 'post_date',
			'order'          => 'DESC',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => 'audio_file',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => 'audio_file',
					'value'   => '',
					'compare' => '!=',
				),
			),
		);
		$podcast_query      = new WP_Query( $args );
		$podcasts           = $podcast_query->get_posts();

		$podcast_file_data = array();
		foreach ( $podcasts as $podcast ) {
			$audio_file = get_post_meta( $podcast->ID, 'audio_file', true );
			if ( empty( $audio_file ) ) {
				continue;
			}
			$podcast_file_data[ $podcast->ID ] = [
				'post_id'    => $podcast->ID,
				'post_title' => $podcast->post_title,
				'post_date'  => $podcast->post_date,
				'audio_file' => get_post_meta( $podcast->ID, 'audio_file', true ),
				'enclosure'  => get_post_meta( $podcast->ID, 'enclosure', true ),
				'episode_id' => $podcast->episode_id,
			];
		}

		$csv = '';
		foreach ( $podcast_file_data as $podcast_file_data_item ) {
			$csv .= implode( ';', $podcast_file_data_item ) . PHP_EOL;
		}

		echo '<textarea cols="200" rows="25">';
		print_r( $csv );
		echo '</textarea>';
	}

	/**
	 * Get a list of series data, in CSV format
	 */
	public static function ssa_get_series_data() {
		$terms = get_terms(array(
			'taxonomy' => 'series',
			'hide_empty' => false,
		));
		echo '<textarea cols="200" rows="25">';
		print_r( wp_json_encode( $terms, JSON_PRETTY_PRINT ) );
		echo '</textarea>';
	}

	/**
	 * Get the episodes that belong to each series
	 */
	public static function ssa_get_episode_ids_by_series() {
		global $wpdb;
		$term_relationships = $wpdb->prefix . 'term_relationships';
		$term_taxonomy      = $wpdb->prefix . 'term_taxonomy';

		$sql     = "SELECT term_taxonomy.term_id AS series_id, term_relationships.object_id AS post_id
			FROM $term_relationships AS term_relationships
			LEFT join $term_taxonomy AS term_taxonomy
			ON term_relationships.term_taxonomy_id = term_taxonomy.term_taxonomy_id
			WHERE term_taxonomy.taxonomy = 'series' ORDER BY series_id";
		$results = $wpdb->get_results( $sql, ARRAY_A );

		$podcast_ids_by_series = array();
		foreach ( $results as $result ) {
			if ( ! empty( $podcast_ids_by_series[ $result['series_id'] ] ) ) {
				$podcast_ids_by_series[ $result['series_id'] ] .= ',';
			}
			$podcast_ids_by_series[ $result['series_id'] ] .= $result['post_id'];
		}
		echo '<textarea cols="200" rows="25">';
		print_r( wp_json_encode( $podcast_ids_by_series, JSON_PRETTY_PRINT ) );
		echo '</textarea>';
	}

	/**
	 * Change from production to staging
	 */
	public static function ssa_set_podcast_environment() {
		$environment = filter_var( $_GET['environment'], FILTER_SANITIZE_STRING );
		update_option( 'ssp_admin_podcast_environment', $environment );
	}

	/**
	 * Delete the Castos episode and file ids
	 */
	public static function delete_castos_post_meta() {
		$podcast_post_types = ssp_post_types( true );
		$args               = array(
			'post_type'      => $podcast_post_types,
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'orderby'        => 'post_date',
			'order'          => 'DESC',
		);
		$podcast_query      = new WP_Query( $args );
		$podcasts           = $podcast_query->get_posts();

		foreach ( $podcasts as $podcast ) {
			delete_post_meta( $podcast->ID, 'podmotor_episode_id' );
			delete_post_meta( $podcast->ID, 'podmotor_file_id' );
		}
	}

	/**
	 * Get the podPress episode data
	 */
	public static function ssa_get_podpress_json() {
		$args          = array(
			'post_type'      => 'post',
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'orderby'        => 'post_date',
			'order'          => 'DESC',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => '_podPressMedia',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => '_podPressMedia',
					'value'   => '',
					'compare' => '!=',
				)
			),
		);
		$podcast_query = new WP_Query( $args );
		$podcasts      = $podcast_query->get_posts();

		$podcast_data = array();
		foreach ( $podcasts as $podcast ) {
			$pod_press_media    = get_post_meta( $podcast->ID, '_podPressMedia' )[0][0];
			$pod_press_specific = get_post_meta( $podcast->ID, '_podPressPostSpecific' )[0];
			$podcast_data[ $podcast->ID ] = array(
				'post_id'      => $podcast->ID,
				'post_title'   => $podcast->post_title,
				'post_date'    => $podcast->post_date,
			);
			foreach ( $pod_press_media as $label => $value ) {
				$podcast_data[ $podcast->ID ][ $label ] = $value;
			}
			foreach ( $pod_press_specific as $label => $value ) {
				$podcast_data[ $podcast->ID ][ $label ] = $value;
			}
		}

		$csv = '';
		$labels = array();
		foreach ( $podcast_data as $key => $podcast_data_item ) {
			$labels[] = $key;
			$csv .= implode( ';', $podcast_data_item ) . PHP_EOL;
		}
		$csv = implode( ';', $labels ) . PHP_EOL . $csv;

		echo '<textarea cols="200" rows="25">';
		print_r( $csv );
		echo '</textarea>';
	}

	/**
	 * Get all episodes, including Castos episode and file ids
	 */
	public static function ssa_get_episode_data_with_castos_ids() {

		$podcast_post_types = ssp_post_types( true );
		$args               = array(
			'post_type'      => $podcast_post_types,
			'posts_per_page' => - 1,
			'post_status'    => 'any',
			'orderby'        => 'post_date',
			'order'          => 'DESC',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => 'audio_file',
					'compare' => 'EXISTS',
				),
				array(
					'key'     => 'audio_file',
					'value'   => '',
					'compare' => '!=',
				),
			),
		);
		$podcast_query      = new WP_Query( $args );
		$podcasts           = $podcast_query->get_posts();

		$podcast_data = array();
		foreach ( $podcasts as $podcast ) {
			$audio_file                   = get_post_meta( $podcast->ID, 'audio_file', true );
			$episode_id                   = get_post_meta( $podcast->ID, 'podmotor_episode_id', true );
			$file_id                      = get_post_meta( $podcast->ID, 'podmotor_file_id', true );
			$podcast_data[] = array(
				$podcast->ID,
				$podcast->post_title,
				$audio_file,
				$episode_id,
				$file_id,
			);
		}

		$csv    = '';
		$labels = array(
			'id',
			'post_title',
			'audio_file',
			'podmotor_episode_id',
			'podmotor_file_id'
		);
		foreach ( $podcast_data as $podcast_data_item ) {
			$csv      .= implode( ';', $podcast_data_item ) . PHP_EOL;
		}
		$csv = implode( ';', $labels ) . PHP_EOL . $csv;

		echo '<textarea cols="200" rows="25">';
		print_r( $csv );
		echo '</textarea>';

	}
}
