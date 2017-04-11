<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/ucsf-ckm/wplibcalhours
 * @since      1.0.0
 *
 * @package    WpLibCalHours
 * @subpackage WpLibCalHours/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WpLibCalHours
 * @subpackage WpLibCalHours/includes
 * @author     Stefan Topfstedt <stefan.topfstedt@ucsf.edu>
 */
class WpLibCalHours {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string PLUGIN_NAME The string used to uniquely identify this plugin.
	 */
	const PLUGIN_NAME = 'wplibcalhours';

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WpLibCalHours_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The LibCal API client.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WpLibCalHours_Client $client The LibCal API client.
	 */
	protected $client;

	/**
	 * The base name of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_basename The base name of this plugin.
	 */
	protected $plugin_basename;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . self::PLUGIN_NAME . '.php' );
		$this->version         = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WpLibCalHours_Loader. Orchestrates the hooks of the plugin.
	 * - WpLibCalHours_i18n. Defines internationalization functionality.
	 * - WpLibCalHours_Admin. Defines all hooks for the admin area.
	 * - WpLibCalHours_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wplibcalhours-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wplibcalhours-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wplibcalhours-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wplibcalhours-public.php';

		/**
		 * The API client class.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wplibcalhours-client.php';

		$this->loader = new WpLibCalHours_Loader();
		$this->client = new WpLibCalHours_Client( self::PLUGIN_NAME, $this->version );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WpLibCalHours_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WpLibCalHours_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WpLibCalHours_Admin( self::PLUGIN_NAME, $this->get_version() );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_setting' );
		$this->loader->add_action( 'update_option_' . self::PLUGIN_NAME . '_ignore_cache', $plugin_admin, 'update_option_ignore_cache', null, 0 );
		$this->loader->add_filter( 'plugin_action_links_' . $this->plugin_basename, $plugin_admin, 'add_action_links' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WpLibCalHours_Public( self::PLUGIN_NAME,
			$this->get_version(),
			$this->get_client() );

		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WpLibCalHours_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Returns the LibCal API client.
	 *
	 * @since     1.0.0
	 * @return    WpLibCalHours_Client    The LibCal API client.
	 */
	public function get_client() {
		return $this->client;
	}
}
