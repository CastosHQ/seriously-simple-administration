<?php
/*
 * Plugin Name: Seriously Simple Administration
 * Version: 1.3.0
 * Plugin URI: http://jonathanbossenger.com/
 * Description: Basic admin for Seriously Simple Podcasting/Hosting
 * Author: Jonathan Bossenger, Sergey Zakharchenko
 * Author URI: http://jonathanbossenger.com/, https://github.com/zahardev
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

use SSA\SSA;

require_once 'class-castos-exporter.php';
require_once 'ssa-admin-functions.php';
require_once 'ssa-custom-function.php';

/**
 * Turn on script debugging, and the SSP debug logging
 */
if ( ! defined( 'SCRIPT_DEBUG' ) ) {
	define( 'SCRIPT_DEBUG', true );
}
if ( ! defined( 'SSP_DEBUG' ) ) {
	define( 'SSP_DEBUG', true );
}

/**
 * If environment setting has changed
 */
if ( isset( $_GET['ssa_admin_action'] ) ) {
	$admin_action = filter_var( $_GET['ssa_admin_action'], FILTER_SANITIZE_STRING );
	if ( 'set_ssp_podcast_environment' === $admin_action ) {
		SSA::ssa_set_podcast_environment();
	}
}

/**
 * Set up environment
 */
$ssp_admin_podcast_environment = get_option( 'ssp_admin_podcast_environment', 'production' );

if ( 'staging' === $ssp_admin_podcast_environment ) {
	if ( ! defined( 'SSP_CASTOS_APP_URL' ) ) {
		define( 'SSP_CASTOS_APP_URL', 'https://app.seriouslysimplehosting.com/' );
	}
	if ( ! defined( 'SSP_CASTOS_EPISODES_URL' ) ) {
		define( 'SSP_CASTOS_EPISODES_URL', 'https://s3.amazonaws.com/seriouslysimplestaging/' );
	}
}

if ( 'local' === $ssp_admin_podcast_environment ) {
	if ( ! defined( 'SSP_CASTOS_APP_URL' ) ) {
		define( 'SSP_CASTOS_APP_URL', 'https://castos.test/' );
	}
	if ( ! defined( 'SSP_CASTOS_EPISODES_URL' ) ) {
		define( 'SSP_CASTOS_EPISODES_URL', 'https://s3.amazonaws.com/seriouslysimplestaging/' );
	}
}

/**
 * On plugin deactivation, clean up the log directory
 */
register_deactivation_hook( __FILE__, 'ssa_deactivation' );
if ( ! function_exists( 'ssa_deactivation' ) ) {
	function ssa_deactivation() {
		$log_dir_path = SSP_PLUGIN_PATH . 'log' . DIRECTORY_SEPARATOR;
		if ( is_dir( $log_dir_path ) ) {
			array_map( 'unlink', glob( "$log_dir_path/*.*" ) );
		}
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
					SSA::ssa_reset_episodes();
					SSA::ssa_reset_import();
					SSA::ssa_reset_account_details();
					echo '<p>Database settings reset.</p>';
					break;
				case 'reset_import':
					SSA::ssa_reset_import();
					echo '<p>Import setting reset.</p>';
					break;
				case 'get_safe_podcast_json':
					SSA::ssa_get_safe_podcast_json();
					break;
				case 'get_podcast_data_csv':
					SSA::ssa_get_podcast_data_csv();
					break;
				case 'get_series_data':
					SSA::ssa_get_series_data();
					break;
				case 'set_ssp_podcast_environment':
					SSA::ssa_set_podcast_environment();
					break;
				case 'get_episode_ids_by_series':
					SSA::ssa_get_episode_ids_by_series();
					break;
				case 'delete_castos_post_meta':
					SSA::delete_castos_post_meta();
					break;
				case 'get_podpress_json':
					SSA::ssa_get_podpress_json();
					break;
				case 'get_episode_data_with_castos_ids':
					SSA::ssa_get_episode_data_with_castos_ids();
					break;
				case 'ssa_export_missed_episodes':
					SSA::export_missed_episodes();
					break;
				case 'ssa_custom_function':
					ssa_custom_function();
					break;
			}
		}

		$log_path = SSP_PLUGIN_PATH . 'log' . DIRECTORY_SEPARATOR . 'ssp.log.' . date( 'd-m-y' ) . '.txt';
		$log_url  = SSP_PLUGIN_URL . 'log' . DIRECTORY_SEPARATOR . 'ssp.log.' . date( 'd-m-y' ) . '.txt';
		if ( is_file( $log_path ) ) {
			echo '<p><a href="' . esc_url( $log_url ) . '">Download current log file</a></p>';
		}

		if ( 'production' === $ssp_admin_podcast_environment ) {
			$set_ssp_podcast_environment_url = add_query_arg( array(
				'ssa_admin_action' => 'set_ssp_podcast_environment',
				'environment'      => 'staging',
			) );
			echo '<p><a href="' . esc_url( $set_ssp_podcast_environment_url ) . '">Set podcast environment to staging</a></p>';
		}

		if ( 'staging' === $ssp_admin_podcast_environment ) {
			$set_ssp_podcast_environment_url = add_query_arg( array(
				'ssa_admin_action' => 'set_ssp_podcast_environment',
				'environment'      => 'production',
			) );
			echo '<p><a href="' . esc_url( $set_ssp_podcast_environment_url ) . '">Set podcast environment to production</a></p>';
		}

		foreach ( ssa_get_admin_actions() as $action => $title ) {
			$action_url = add_query_arg( 'ssa_admin_action', $action );
			echo '<p><a href="' . esc_url( $action_url ) . '">' . $title . '</a></p>';
		}

		echo '</div>';
	}
}

if ( ! function_exists( 'ssa_get_admin_actions' ) ) {
	function ssa_get_admin_actions() {
		return array(
			'reset_all'                        => 'Reset all database settings',
			'reset_import'                     => 'Reset importer',
			'get_safe_podcast_json'            => 'Get all podcast JSON data without content',
			'get_podcast_data_csv'             => 'Get all podcast data in CSV',
			'get_series_data'                  => 'Get all series data',
			'get_episode_ids_by_series'        => 'Get Episode IDs by Series',
			'delete_castos_post_meta'          => 'Delete Episode Postmeta',
			'get_podpress_json'                => 'Get PodPress Data',
			'get_episode_data_with_castos_ids' => 'Get Episodes with Castos IDS',
			'ssa_custom_function'              => 'Run Custom Function',
			'ssa_export_missed_episodes'       => 'Export missed episodes',
		);
	}
}
