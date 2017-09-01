<?php
/*
 * Plugin Name: Seriously Simple Administration
 * Version: 1.0.3
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

/** Staging
if ( ! defined( 'SSP_PODMOTOR_APP_URL' ) ) {
	define( 'SSP_PODMOTOR_APP_URL', 'https://staging.seriouslysimplepodcasting.com/' );
}
if ( ! defined( 'SSP_PODMOTOR_EPISODES_URL' ) ) {
	define( 'SSP_PODMOTOR_EPISODES_URL', 'https://s3.amazonaws.com/seriouslysimplestaging/' );
}
 */
/** Jonathan Local Development
if ( ! defined( 'SSP_PODMOTOR_APP_URL' ) ) {
	define( 'SSP_PODMOTOR_APP_URL', 'http://192.168.10.10/' );
}
if ( ! defined( 'SSP_PODMOTOR_EPISODES_URL' ) ) {
	define( 'SSP_PODMOTOR_EPISODES_URL', 'https://s3.amazonaws.com/seriouslysimplestaging/' );
}
 */

// main plugin code.
if ( ! function_exists( 'ssa_setup_administration' ) ) {
	function ssa_setup_administration() {
		
		ssa_setup_logging_directory();
		
	}
}

/**
 * Checks if logging directory exists and creates it if not */
if ( ! function_exists( 'ssa_setup_logging_directory' ) ) {
	function ssa_setup_logging_directory() {
		if ( ! is_dir( SSP_LOG_DIR_PATH ) ) {
			if ( ! wp_mkdir_p( SSP_LOG_DIR_PATH ) ) {
				wp_die( 'An error occurred attempting to create the SSP logging directory' );
			}
		}
	}
}
add_action( 'admin_init', 'ssa_setup_administration' );

/**
 * Add menu item
 */
if ( ! function_exists( 'ssa_add_menu_item' ) ) {
	function ssa_add_menu_item() {
		add_submenu_page( 'edit.php?post_type=podcast', __( 'Administration', 'seriously-simple-podcasting' ), __( 'Administration', 'seriously-simple-podcasting' ), 'manage_podcast', 'admin', 'ssa_reset_development_settings' );
	}
}
add_action( 'admin_menu', 'ssa_add_menu_item' );


/**
 * Reset settings callback
 */
if ( ! function_exists( 'ssa_reset_development_settings' ) ) {
	function ssa_reset_development_settings() {
		echo '<div class="wrap">';
		echo '<h1>Admin settings</h1>';
		
		echo '<p>'.SSP_PODMOTOR_APP_URL.'</p>';
		
		if ( isset( $_GET['admin_action'] ) ) {
			$admin_action = filter_var( $_GET['admin_action'], FILTER_SANITIZE_STRING );
			
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
				case 'get_podcast_files':
					ssa_get_podcast_files();
					break;
				
			}
		}
		
		$reset_all_settings_url = add_query_arg( 'admin_action', 'reset_all' );
		echo '<p><a href="' . esc_url( $reset_all_settings_url ) . '">Reset all database settings</a></p>';
		
		//ss_podcasting_podmotor_import_podcasts
		
		$reset_import_podcasts_url = add_query_arg( 'admin_action', 'reset_import' );
		echo '<p><a href="' . esc_url( $reset_import_podcasts_url ) . '">Reset importer</a></p>';
		
		if ( is_file( SSP_LOG_PATH ) ) {
			$log_url = SSP_LOG_URL;
			echo '<p><a href="' . esc_url( $log_url ) . '">Download current log file</a></p>';
		}
		
		$list_podcast_file_urls_url = add_query_arg( 'admin_action', 'get_podcast_files' );
		echo '<p><a href="' . esc_url( $list_podcast_file_urls_url ) . '">Get all podcast files</a></p>';
		
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

function ssa_reset_account_details(){
	delete_option( 'ss_podcasting_podmotor_account_email' );
	delete_option( 'ss_podcasting_podmotor_account_api_token' );
	delete_option( 'ss_podcasting_podmotor_account_id' );
}

function ssa_get_podcast_files(){
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
	$podcast_query = new WP_Query( $args );
	$podcasts = $podcast_query->get_posts();
	
	$podcast_file_data = array();
	foreach ( $podcasts as $podcast ) {
		$podcast_file_data[ $podcast->ID ] = array(
			'post_id'      => $podcast->ID,
			'post_title'   => $podcast->post_title,
			'post_content' => $podcast->post_content,
			'post_date'    => $podcast->post_date,
			'audio_file'   => get_post_meta( $podcast->ID, 'audio_file', true ),
		);
	}
	
	echo '<div style="background: #fff; border: 1px solid #ccc; padding: 10px;"><pre>';
	print_r( $podcast_file_data );
	echo '</pre></div>';
}