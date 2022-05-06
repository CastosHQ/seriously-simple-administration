<?php

namespace SSA;

use SSA\Controllers\Env_Controller;
use SSA\Controllers\Menu_Controller;
use SSA\Controllers\Plugin_Controller;
use SSA\Controllers\Settings_Controller;
use SSA\Handlers\Controllers_Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Endpoint to run all the app controllers.
 * */
class SSA {

	/**
	 * The single instance of SSP_Transcripts.
	 * @var    object
	 * @access  private
	 * @since    1.0.0
	 */
	private static $_instance = null;

	/**
	 * @var     Controllers_Handler
	 * @access  public
	 * @since   1.1.0
	 */
	public $controllers_handler;


	/**
	 * Constructor function.
	 *
	 * @since   1.0.0
	 */
	protected function __construct() {
		$this->init_controllers();
	}

	/**
	 * Initialize all the controllers.
	 *
	 * @return void
	 *
	 * @see Env_Controller
	 * @see Menu_Controller
	 * @see Plugin_Controller
	 * @see Settings_Controller
	 */
	protected function init_controllers() {
		$controllers = array(
			'assets'       => 'SSA\Controllers\Env_Controller',
			'fields'       => 'SSA\Controllers\Plugin_Controller',
			'settings'     => 'SSA\Controllers\Settings_Controller',
		);

		$this->controllers_handler = new Controllers_Handler( $controllers );
	}


	/**
	 * Main SSP_Transcripts Instance
	 *
	 * Ensures only one instance of SSP_Transcripts is loaded or can be loaded.
	 *
	 * @return SSA|null instance
	 * @since 1.0.0
	 * @static
	 */
	public static function instance() {
		if ( ! defined( 'SSP_TRANSCRIPTS_PLUGIN_FILE' ) || ! defined( 'SSP_TRANSCRIPTS_VERSION' ) ) {
			return null;
		}

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}
