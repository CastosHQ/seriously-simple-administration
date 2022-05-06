<?php

namespace SSA\Controllers;


use SSA\Handlers\Notice_Handler;
use SSA\Handlers\SSP_Admin;

class Settings_Controller extends Abstract_Controller {

	/**
	 * Constructor function.
	 * @since   1.4.0
	 */
	public function init() {
		//add_action( 'admin_menu', array( $this, 'setup_development_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );
	}

	public function add_menu_item() {
		add_submenu_page(
			'edit.php?post_type=podcast',
			__( 'Administration', 'seriously-simple-podcasting' ),
			__( 'Administration', 'seriously-simple-podcasting' ),
			'manage_podcast',
			'admin',
			array( $this, 'setup_development_settings' )
		);
	}

	public function setup_development_settings() {

		$ssp_admin_podcast_environment = get_option( 'ssp_admin_podcast_environment', 'production' );

		echo '<div class="wrap ssa-settings">';
		echo '<h1>Admin settings</h1>';

		self::show_notice(
			'APP URL: ' . SSP_CASTOS_APP_URL . '<br>ENV: ' . ucwords( $ssp_admin_podcast_environment ),
			Notice_Handler::TYPE_WARNING, false
		);

		if ( isset( $_GET['ssa_admin_action'] ) ) {
			$admin_action = filter_var( $_GET['ssa_admin_action'], FILTER_SANITIZE_STRING );

			switch ( $admin_action ) {
				case 'reset_all':
					SSP_Admin::ssa_reset_episodes();
					SSP_Admin::ssa_reset_import();
					SSP_Admin::ssa_reset_account_details();
					Notice_Handler::show_notice( 'Database settings reset!' );
					break;
				case 'reset_import':
					SSP_Admin::ssa_reset_import();
					Notice_Handler::show_notice( 'Import settings reset!' );
					break;
				case 'get_safe_podcast_json':
					SSP_Admin::ssa_get_safe_podcast_json();
					break;
				case 'get_podcast_data_csv':
					SSP_Admin::ssa_get_podcast_data_csv();
					break;
				case 'get_series_data':
					SSP_Admin::ssa_get_series_data();
					break;
				case 'set_ssp_podcast_environment':
					SSP_Admin::ssa_set_podcast_environment();
					break;
				case 'get_episode_ids_by_series':
					SSP_Admin::ssa_get_episode_ids_by_series();
					break;
				case 'delete_castos_post_meta':
					SSP_Admin::delete_castos_post_meta();
					break;
				case 'get_podpress_json':
					SSP_Admin::ssa_get_podpress_json();
					break;
				case 'get_episode_data_with_castos_ids':
					SSP_Admin::ssa_get_episode_data_with_castos_ids();
					break;
				case 'ssa_export_missed_episodes':
					SSP_Admin::export_missed_episodes();
					break;
			}
		}

		$log_path = SSP_PLUGIN_PATH . 'log' . DIRECTORY_SEPARATOR . 'ssp.log.' . date( 'd-m-y' ) . '.txt';
		$log_url  = SSP_PLUGIN_URL . 'log' . DIRECTORY_SEPARATOR . 'ssp.log.' . date( 'd-m-y' ) . '.txt';
		if ( is_file( $log_path ) ) {
			echo '<p><a class="button" href="' . esc_url( $log_url ) . '">Download current log file</a></p>';
		}

		if ( 'production' === $ssp_admin_podcast_environment ) {
			$set_ssp_podcast_environment_url = add_query_arg( array(
				'ssa_admin_action' => 'set_ssp_podcast_environment',
				'environment'      => 'staging',
			) );
			echo '<p><a class="button" href="' . esc_url( $set_ssp_podcast_environment_url ) . '">Set podcast environment to staging</a></p>';
		}

		if ( 'staging' === $ssp_admin_podcast_environment ) {
			$set_ssp_podcast_environment_url = add_query_arg( array(
				'ssa_admin_action' => 'set_ssp_podcast_environment',
				'environment'      => 'production',
			) );
			echo '<p><a class="button" href="' . esc_url( $set_ssp_podcast_environment_url ) . '">Set podcast environment to production</a></p>';
		}

		foreach ( $this->get_admin_actions() as $action => $title ) {
			$action_url = add_query_arg( 'ssa_admin_action', $action );
			echo '<p><a class="button" href="' . esc_url( $action_url ) . '">' . $title . '</a></p>';
		}

		echo '</div>';

		$this->activate_action_warning();
	}

	protected function activate_action_warning() {
		?>
        <script>
            jQuery(document).ready(function ($) {
                $('.ssa-settings').find('.button').click(function (e) {
                    if (!confirm('Are you sure?')) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                });
            });
        </script>
		<?php
	}

	/**
	 * @param string $notice
	 * @param string $type
	 * @param bool $is_dismissible
	 *
	 * @return void
	 */
	public function show_notice( $notice, $type = 'success', $is_dismissible = true ) {
		Notice_Handler::show_notice( $notice, $type, $is_dismissible );
	}

	public function get_admin_actions() {
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
			'ssa_export_missed_episodes'       => 'Export missed episodes',
		);
	}
}
