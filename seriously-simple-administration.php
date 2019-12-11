<?php
/*
 * Plugin Name: Seriously Simple Administration
 * Version: 1.2.0
 * Plugin URI: http://jonathanbossenger.com/
 * Description: Basic admin for Seriously Simple Podcasting/Hosting
 * Author: Jonathan Bossenger
 * Author URI: http://jonathanbossenger.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: seriously-simple-admin
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Jonathan Bossenger
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SSP_DEBUG', true );

/**
 * If environment setting has changed
 */
if ( isset( $_GET['ssa_admin_action'] ) ) {
	$admin_action = filter_var( $_GET['ssa_admin_action'], FILTER_SANITIZE_STRING );
	if ( 'set_ssp_podcast_environment' === $admin_action ) {
		ssa_set_podcast_environment();
	}
}

/**
 * Set up environment
 */
$ssp_admin_podcast_environment = get_option( 'ssp_admin_podcast_environment', 'production' );

if ( 'staging' === $ssp_admin_podcast_environment ) {
	if ( ! defined( 'SSP_CASTOS_APP_URL' ) ) {
		define( 'SSP_CASTOS_APP_URL', 'http://app.seriouslysimplehosting.com/' );
	}
	if ( ! defined( 'SSP_CASTOS_EPISODES_URL' ) ) {
		define( 'SSP_CASTOS_EPISODES_URL', 'https://s3.amazonaws.com/seriouslysimplestaging/' );
	}
}

if ( 'local' === $ssp_admin_podcast_environment ) {
	if ( ! defined( 'SSP_CASTOS_APP_URL' ) ) {
		define( 'SSP_CASTOS_APP_URL', 'http://192.168.10.10/' );
	}
	if ( ! defined( 'SSP_CASTOS_EPISODES_URL' ) ) {
		define( 'SSP_CASTOS_EPISODES_URL', 'https://s3.amazonaws.com/seriouslysimplestaging/' );
	}
}

/**
 * Add menu item
 */
add_action( 'admin_menu', 'ssa_add_menu_item' );
if ( ! function_exists( 'ssa_add_menu_item' ) ) {
	function ssa_add_menu_item() {
		add_submenu_page(
			'edit.php?post_type=podcast',
			__( 'Administration', 'seriously-simple-podcasting' ),
			__( 'Administration', 'seriously-simple-podcasting' ),
			'manage_podcast',
			'admin',
			'ssa_setup_development_settings'
		);
	}
}

/**
 * Setup settings callback
 */
if ( ! function_exists( 'ssa_setup_development_settings' ) ) {
	function ssa_setup_development_settings() {
		
		$ssp_admin_podcast_environment = get_option( 'ssp_admin_podcast_environment', 'production' );
		
		echo '<div class="wrap">';
		echo '<h1>Admin settings</h1>';
		
		echo '<p>' . SSP_CASTOS_APP_URL . '</p>';
		
		echo '<p>' . ucwords( $ssp_admin_podcast_environment ) . '</p>';
		
		if ( isset( $_GET['ssa_admin_action'] ) ) {
			
			$admin_action = filter_var( $_GET['ssa_admin_action'], FILTER_SANITIZE_STRING );
			
			switch ( $admin_action ) {
				case 'reset_all':
					ssa_reset_episodes();
					ssa_reset_import();
					ssa_reset_account_details();
					echo '<p>Database settings reset.</p>';
					break;
				case 'reset_import':
					ssa_reset_import();
					echo '<p>Import setting reset.</p>';
					break;
				/*
				case 'get_local_podcast_files':
					ssa_get_local_podcast_files();
					break;
				case 'get_podcast_json':
					ssa_get_podcast_json();
					break;
				case 'get_safe_podcast_json':
					ssa_get_safe_podcast_json();
					break;
				case 'get_podcast_files':
					ssa_get_podcast_files();
					break;
				case 'get_podcast_credentials':
					ssa_get_podcast_credentials();
					break;
				case 'get_safe_podcast_json_via_query':
					ssa_get_safe_podcast_json_via_query();
					break;
				case 'get_podcast_ids':
					ssa_get_podcast_ids();
					break;
				case 'get_podcast_ids_csv':
					ssa_get_podcast_ids_csv();
					break;
				case 'get_series_data':
					ssa_get_series_data();
					break;
				case 'set_ssp_podcast_environment':
					ssa_set_podcast_environment();
					break;
				case 'get_episode_ids_by_series':
					ssa_get_episode_ids_by_series();
					break;
				case 'ssa_custom_function':
					ssa_custom_function();
					break;
				*/
			}
		}
		
		$reset_all_settings_url = add_query_arg( 'ssa_admin_action', 'reset_all' );
		echo '<p><a href="' . esc_url( $reset_all_settings_url ) . '">Reset all database settings</a></p>';
		
		//ss_podcasting_podmotor_import_podcasts
		
		$reset_import_podcasts_url = add_query_arg( 'ssa_admin_action', 'reset_import' );
		echo '<p><a href="' . esc_url( $reset_import_podcasts_url ) . '">Reset importer</a></p>';
		
		$log_path = SSP_PLUGIN_PATH . 'log' . DIRECTORY_SEPARATOR . 'ssp.log.' . date( 'd-m-y' ) . '.txt';
		$log_url  = SSP_PLUGIN_URL . 'log' . DIRECTORY_SEPARATOR . 'ssp.log.' . date( 'd-m-y' ) . '.txt';
		if ( is_file( $log_path ) ) {
			echo '<p><a href="' . esc_url( $log_url ) . '">Download current log file</a></p>';
		}
		
		/*
		$list_podcast_file_urls_url = add_query_arg( 'ssa_admin_action', 'get_local_podcast_files' );
		echo '<p><a href="' . esc_url( $list_podcast_file_urls_url ) . '">Get all local podcast files</a></p>';
		
		$list_podcast_json_url = add_query_arg( 'ssa_admin_action', 'get_podcast_json' );
		echo '<p><a href="' . esc_url( $list_podcast_json_url ) . '">Get all podcast JSON data</a></p>';
		
		$list_podcast_json_url = add_query_arg( 'ssa_admin_action', 'get_safe_podcast_json' );
		echo '<p><a href="' . esc_url( $list_podcast_json_url ) . '">Get all podcast JSON data without content</a></p>';
		
		$list_podcast_file_urls_url = add_query_arg( 'ssa_admin_action', 'get_podcast_files' );
		echo '<p><a href="' . esc_url( $list_podcast_file_urls_url ) . '">Get all podcast files</a></p>';
		
		$list_podcast_credentials_url = add_query_arg( 'ssa_admin_action', 'get_podcast_credentials' );
		echo '<p><a href="' . esc_url( $list_podcast_credentials_url ) . '">Get podcast credentials</a></p>';
		
		$list_podcast_json_via_query_url = add_query_arg( 'ssa_admin_action', 'get_safe_podcast_json_via_query' );
		echo '<p><a href="' . esc_url( $list_podcast_json_via_query_url ) . '">Get all podcast JSON data without content (custom query)</a></p>';
		
		$list_podcast_ids_url = add_query_arg( 'ssa_admin_action', 'get_podcast_ids' );
		echo '<p><a href="' . esc_url( $list_podcast_ids_url ) . '">Get all podcast ids</a></p>';
		
		$list_podcast_ids_url = add_query_arg( 'ssa_admin_action', 'get_podcast_ids_csv' );
		echo '<p><a href="' . esc_url( $list_podcast_ids_url ) . '">Get all podcast ids in CSV</a></p>';
		
		$list_series_url = add_query_arg( 'ssa_admin_action', 'get_series_data' );
		echo '<p><a href="' . esc_url( $list_series_url ) . '">Get all series data</a></p>';
		
		$list_series_url = add_query_arg( 'ssa_admin_action', 'get_episode_ids_by_series' );
		echo '<p><a href="' . esc_url( $list_series_url ) . '">Get Episode IDs by Series</a></p>';
		
		$action_url = add_query_arg( 'ssa_admin_action', 'ssa_custom_function' );
		echo '<p><a href="' . esc_url( $action_url ) . '">Run Custom Function</a></p>';
		
		if ( 'production' === $ssp_admin_podcast_environment ) {
			$set_ssp_podcast_environment_url = add_query_arg( array(
				'ssa_admin_action' => 'set_ssp_podcast_environment',
				'environment'  => 'staging',
			) );
			echo '<p><a href="' . esc_url( $set_ssp_podcast_environment_url ) . '">Set podcast environment to staging</a></p>';
		}
		
		if ( 'staging' === $ssp_admin_podcast_environment ) {
			$set_ssp_podcast_environment_url = add_query_arg( array(
				'ssa_admin_action' => 'set_ssp_podcast_environment',
				'environment'  => 'production',
			) );
			echo '<p><a href="' . esc_url( $set_ssp_podcast_environment_url ) . '">Set podcast environment to production</a></p>';
		}
		*/
		
		echo '</div>';
	}
}

function ssa_reset_episodes() {
	global $wpdb;
	$postmeta = $wpdb->prefix . 'postmeta';
	
	// clear out ssh episode id.
	$sql = "DELETE FROM `$postmeta` WHERE `meta_key` = 'podmotor_episode_id'";
	$wpdb->query( $sql );
	$wpdb->flush();
}

function ssa_reset_import() {
	delete_option( 'ss_podcasting_podmotor_import_podcasts' );
	delete_option( 'ss_podcasting_podmotor_queue_id' );
}

function ssa_reset_account_details() {
	delete_option( 'ss_podcasting_podmotor_account_email' );
	delete_option( 'ss_podcasting_podmotor_account_api_token' );
	delete_option( 'ss_podcasting_podmotor_account_id' );
}

function ssa_get_local_podcast_files() {
	$podcast_post_types = ssp_post_types( true );
	$args               = array(
		'post_type'      => $podcast_post_types,
		'posts_per_page' => - 1,
		'post_status'    => 'any',
		'meta_query'     => array(
			array(
				'key'     => 'audio_file',
				'compare' => 'EXISTS',
			),
			array(
				'relation' => 'OR',
				array(
					'key'     => 'podmotor_episode_id',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => 'podmotor_episode_id',
					'value'   => '0',
					'compare' => '=',
				),
			),
		),
	);
	$podcast_query      = new WP_Query( $args );
	$podcasts           = $podcast_query->get_posts();
	
	$podcast_file_data = array();
	foreach ( $podcasts as $podcast ) {
		$podcast_file_data[ $podcast->ID ] = array(
			'post_id'    => $podcast->ID,
			'post_title' => $podcast->post_title,
			'post_date'  => $podcast->post_date,
			'audio_file' => get_post_meta( $podcast->ID, 'audio_file', true ),
		);
	}
	
	echo '<div style="background: #fff; border: 1px solid #ccc; padding: 10px;"><pre>';
	print_r( $podcast_file_data );
	echo '</pre></div>';
}

function ssa_get_podcast_json() {
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
			'post_content' => $podcast->post_content,
			'post_date'    => $podcast->post_date,
			'audio_file'   => get_post_meta( $podcast->ID, 'audio_file', true ),
		);
	}
	echo '<textarea cols="200" rows="25">';
	print_r( wp_json_encode( $podcast_data, JSON_PRETTY_PRINT ) );
	echo '</textarea>';
}

function ssa_get_safe_podcast_json() {
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

function ssa_get_podcast_files() {
	$podcast_post_types = ssp_post_types( true );
	$args               = [
		'post_type'      => $podcast_post_types,
		'posts_per_page' => - 1,
		'post_status'    => 'any',
		'orderby'        => 'post_date',
		'order'          => 'DESC',
		'meta_query'     => [
			'relation' => 'OR',
			[
				'key'     => 'audio_file',
				'compare' => 'EXISTS',
			],
			[
				'key'     => 'audio_file',
				'value'   => '',
				'compare' => '!=',
			],
		],
	];
	$podcast_query      = new WP_Query( $args );
	$podcasts           = $podcast_query->get_posts();
	
	$podcast_file_data = [];
	foreach ( $podcasts as $podcast ) {
		$podcast_file_data[ $podcast->ID ] = [
			'post_id'    => $podcast->ID,
			'post_title' => $podcast->post_title,
			'post_date'  => $podcast->post_date,
			'audio_file' => get_post_meta( $podcast->ID, 'audio_file', true ),
		];
	}
	
	echo '<div style="background: #fff; border: 1px solid #ccc; padding: 10px;"><pre>';
	print_r( $podcast_file_data );
	echo '</pre></div>';
}

function ssa_get_podcast_credentials() {
	$podmotor_account_id    = get_option( 'ss_podcasting_podmotor_account_id', '' );
	$podmotor_account_email = get_option( 'ss_podcasting_podmotor_account_email', '' );
	$podmotor_array         = ssp_podmotor_decrypt_config( $podmotor_account_id, $podmotor_account_email );
	
	echo '<div style="background: #fff; border: 1px solid #ccc; padding: 10px;"><pre>';
	print_r( array( $podmotor_account_id, $podmotor_account_email, $podmotor_array ) );
	echo '</pre></div>';
}

function ssa_get_safe_podcast_json_via_query() {
	
	global $wpdb;
	$posts    = $wpdb->prefix . 'posts';
	$postmeta = $wpdb->prefix . 'postmeta';
	
	$sql     = "SELECT posts.ID, posts.post_title, posts.post_date, postmeta.meta_value as audio_file
			FROM $posts as posts
			LEFT JOIN $postmeta as postmeta
			ON posts.ID = postmeta.post_id
			WHERE posts.post_type = 'podcast'
			AND postmeta.meta_key = 'audio_file'
			AND postmeta.meta_value != ''
			ORDER BY posts.post_date DESC";
	$results = $wpdb->get_results( $sql, ARRAY_A );
	
	$podcast_data = array();
	foreach ( $results as $result ) {
		$podcast_data[ $result['ID'] ] = array(
			'post_id'      => $result['ID'],
			'post_title'   => $result['post_title'],
			'post_content' => '',
			'post_date'    => $result['post_date'],
			'audio_file'   => $result['audio_file'],
		);
	}
	
	echo '<textarea cols="200" rows="25">';
	print_r( wp_json_encode( $podcast_data, JSON_PRETTY_PRINT ) );
	echo '</textarea>';
}

function ssa_get_podcast_ids() {
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
		$podcast_file_data[ $podcast->ID ] = [
			'post_id'    => $podcast->ID,
			'post_title' => $podcast->post_title,
			'post_date'  => $podcast->post_date,
			'audio_file' => get_post_meta( $podcast->ID, 'audio_file', true ),
			'enclosure'  => get_post_meta( $podcast->ID, 'enclosure', true ),
		];
	}
	
	echo '<textarea cols="200" rows="25">';
	print_r( wp_json_encode( $podcast_file_data, JSON_PRETTY_PRINT ) );
	echo '</textarea>';
}

function ssa_get_podcast_ids_csv() {
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
		];
	}
	
	$csv = '';
	foreach ( $podcast_file_data as $podcast_file_data_item ) {
		$csv .= implode( ',', $podcast_file_data_item ) . PHP_EOL;
	}
	
	echo '<textarea cols="200" rows="25">';
	print_r( $csv );
	echo '</textarea>';
}

function ssa_get_series_data() {
	$terms = get_terms(array(
		'taxonomy' => 'series',
		'hide_empty' => false,
	));
	echo '<textarea cols="200" rows="25">';
	print_r( wp_json_encode( $terms, JSON_PRETTY_PRINT ) );
	echo '</textarea>';
}

function ssa_get_episode_ids_by_series() {
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


function ssa_set_podcast_environment() {
	$environment = filter_var( $_GET['environment'], FILTER_SANITIZE_STRING );
	update_option( 'ssp_admin_podcast_environment', $environment );
}

require_once 'ssa-custom-function.php';
