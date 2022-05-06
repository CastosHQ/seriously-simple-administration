<?php

namespace SSA\Controllers;


use SSA\SSP_Admin;

class Env_Controller extends Abstract_Controller {

	/**
	 * Constructor function.
	 * @since   1.4.0
	 */
	public function init() {
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
				SSP_Admin::ssa_set_podcast_environment();
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
	}
}
