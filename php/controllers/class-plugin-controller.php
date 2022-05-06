<?php

namespace SSA\Controllers;


class Plugin_Controller extends Abstract_Controller {

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.4.0
	 */
	public function init() {
		/**
		 * On plugin deactivation, clean up the log directory
		 */
		register_deactivation_hook( SSA_PLUGIN_FILE, array( $this, 'ssa_deactivation' ) );
	}

	/**
	 * Deactivation actions.
	 */
	public function ssa_deactivation() {
		if ( ! defined( 'SSP_PLUGIN_PATH' ) ) {
			return;
		}

		$log_dir_path = SSP_PLUGIN_PATH . 'log' . DIRECTORY_SEPARATOR;
		if ( is_dir( $log_dir_path ) ) {
			array_map( 'unlink', glob( "$log_dir_path/*.*" ) );
		}
	}
}
