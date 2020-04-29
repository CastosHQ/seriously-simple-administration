<?php
/*
 * Plugin Name: Seriously Simple Administration
 * Version: 1.2.8
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

			$log_path = SSP_PLUGIN_PATH . 'log' . DIRECTORY_SEPARATOR . 'ssp.log.' . date( 'd-m-y' ) . '.txt';
			$log_url  = SSP_PLUGIN_URL . 'log' . DIRECTORY_SEPARATOR . 'ssp.log.' . date( 'd-m-y' ) . '.txt';
			if ( is_file( $log_path ) ) {
				echo '<p><a href="' . esc_url( $log_url ) . '">Download current log file</a></p>';
			}
		}
		echo '</div>';
	}
}
