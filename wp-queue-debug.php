<?php
/**
 * Template code for a WordPress plugin.
 *
 * @package wp-queue-debug
 */

/*
-------------------------------------------------------------------------------
	Plugin Name: WP Queue Debug
	Plugin URI:
	Description: Add-on plugin for debuging the WP-Queue Library.
	Text Domain: wp-queue-debug
	Author: sfgarza
	Author URI:
	Contributors:
	License: GPLv3 or later
	License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
	Version: 1.0.1
------------------------------------------------------------------------------
*/

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Include dependencies */
include_once( 'includes.php' );

/** Instantiate the plugin. */
$template_plugin = WP_Queue_Debug::get_instance();

/**
 * WP_Queue_Debug class.
 *
 * @package wp-queue-debug
 * @todo Change class name to be unique to your plugin.
 **/
class WP_Queue_Debug {

	/**
	 * Plugin Basename.
	 *
	 * IE: wordpress-plugin-template/plugin-template.php
	 *
	 * @var [String]
	 */
	public static $plugin_base_name;

	/**
	 * Path to current plugin directory.
	 *
	 * @var [String]
	 */
	public static $plugin_base_dir;

	/**
	 * Path to plugin base file.
	 *
	 * @var [String]
	 */
	public static $plugin_file;

	/**
	 * Plugin Constructor.
	 */
	private function __construct() {
		/* Define Constants */
		static::$plugin_base_name = plugin_basename( __FILE__ );
		static::$plugin_base_dir = plugin_dir_path( __FILE__ );
		static::$plugin_file = __FILE__;

		$this->init();
	}

	/**
	 * Singleton instantiator.
	 *
	 * @return WP_Queue_Debug Single instance of plugin class.
	 */
	public static function get_instance() {
		static $instance;

		if ( ! isset( $instance ) ) {
			$instance = new WP_Queue_Debug();
		}

		return $instance;
	}

	/**
	 * Initialize Plugin.
	 */
	private function init() {
		/* Language Support */
		load_plugin_textdomain( 'wp-queue-debug', false, dirname( static::$plugin_base_name ) . '/languages' );

		/* Plugin Activation/De-Activation. */
		register_activation_hook( static::$plugin_file, array( $this, 'activate' ) );
		register_deactivation_hook( static::$plugin_file, array( $this, 'deactivate' ) );
		register_activation_hook( static::$plugin_file, 'wp_queue_install_tables' );

		/** Enqueue css and js files */
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

		/* Add link to settings in plugins admin page */
		add_filter( 'plugin_action_links_' . static::$plugin_base_name , array( $this, 'plugin_links' ) );
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
	}

	/**
	 * Enqueue Scripts and styles for Backend.
	 */
	public function admin_scripts() {
		// Any JS or CSS needed to display on admin pages should be enqueued here.
	}

	/**
	 * Enqueue Scripts and styles for Frontend.
	 */
	public function frontend_scripts() {
		wp_register_style( 'wp-queue-debug-css', plugins_url( 'assets/css/main.css', static::$plugin_file ) );
		wp_enqueue_style( 'wp-queue-debug-css' );

		wp_enqueue_script( 'wp-queue-debug-js',plugins_url( 'assets/js/plugin.min.js', static::$plugin_file ), array( 'jquery' ), null, true );
	}

	/**
	 * Method that executes on plugin activation.
	 */
	public function activate() {
		add_action( 'plugins_loaded', 'flush_rewrite_rules' );
	}

	/**
	 * Method that executes on plugin de-activation.
	 */
	public function deactivate() {
		add_action( 'plugins_loaded', 'flush_rewrite_rules' );
	}

	/**
	 * Add Tools link on plugin page.
	 *
	 * @param  [Array] $links : Array of links on plugin page.
	 * @return [Array]        : Array of links on plugin page.
	 */
	public function plugin_links( $links ) {
		$settings_link = '<a href="options-general.php?page=wp-queue-debug">Settings</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	public function plugins_loaded(){
		wp_queue()->cron();
	}
}
