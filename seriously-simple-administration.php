<?php
/*
 * Plugin Name: Seriously Simple Administration
 * Version: 1.4.1
 * Plugin URI: https://www.castos.com/
 * Description: Basic admin for Seriously Simple Podcasting
 * Author: Castos
 * Author URI: https://castos.com/
 * Requires at least: 5.0
 * Tested up to: 5.9
 *
 * Text Domain: seriously-simple-admin
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Jonathan Bossenger, Sergio Zakharchenko
 * @since 1.0.0
 */

use SSA\SSA;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SSA_VERSION', '1.4.1' );
define( 'SSA_PLUGIN_FILE', __FILE__ );
define( 'SSA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SSA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

require_once 'autoloader.php';

SSA::instance();
