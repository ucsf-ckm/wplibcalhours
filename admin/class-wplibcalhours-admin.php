<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/ucsf-ckm/wplibcalhours
 * @since      1.0.0
 *
 * @package    WpLibCalHours
 * @subpackage WpLibCalHours/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WpLibCalHours
 * @subpackage WpLibCalHours/admin
 * @author     Stefan Topfstedt <stefan.topfstedt@ucsf.edu>
 */
class WpLibCalHours_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WpLibCalHours_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WpLibCalHours_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/wplibcalhours-admin.css',
			array(),
			$this->version,
			'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WpLibCalHours_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WpLibCalHours_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/wplibcalhours-admin.js',
			array( 'jquery' ),
			$this->version,
			false );

	}

	/**
	 * Add an options page under the Settings submenu
	 *
	 * @since  1.0.0
	 */
	public function add_options_page() {
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'LibCal Hours Settings', 'wplibcalhours' ),
			__( 'LibCal Hours', 'wplibcalhours' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_options_page' )
		);
	}

	/**
	 * Render the options page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_options_page() {
		include_once 'partials/wplibcalhours-admin-display.php';
	}

	/**
	 * Register all related settings of this plugin
	 *
	 * @since  1.0.0
	 */
	public function register_setting() {
		$section_name = $this->plugin_name . '_general';
		add_settings_section(
			$section_name,
			null,
			null,
			$this->plugin_name
		);

		$option_name = $this->plugin_name . '_api_url';
		add_settings_field(
			$option_name,
			__( 'LibCal API endpoint (URL)', 'wplibcalhours' ),
			array( $this, $option_name . '_cb' ),
			$this->plugin_name,
			$section_name,
			array( 'label_for' => $option_name )
		);
		register_setting( $this->plugin_name, $option_name, 'sanitize_text_field' );

		$option_name = $this->plugin_name . '_ignore_cache';
		add_settings_field(
			$option_name,
			__( 'Ignore Cache', 'wplibcalhours' ),
			array( $this, $option_name . '_cb' ),
			$this->plugin_name,
			$section_name,
			array( 'label_for' => $option_name )
		);
		register_setting( $this->plugin_name, $option_name, 'intval' );
	}

	/**
	 * Render the LibCal API endpoint input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function wplibcalhours_api_url_cb() {
		$option_name = $this->plugin_name . '_api_url';
		$url         = get_option( $option_name );
		echo '<input type="url" class="regular-text" name="' . $option_name . '" id="' . $option_name . '" value="' . esc_attr( $url ) . '"> ';
	}

	/**
	 * Render the "Ignore Cache" checkbox field for this plugin.
	 *
	 * @since  1.0.0
	 */
	public function wplibcalhours_ignore_cache_cb() {
		$option_name  = $this->plugin_name . '_ignore_cache';
		$ignore_cache = get_option( $option_name );
		echo '<input type="checkbox" name="' . $option_name . '" value="1" ' . checked( '1', $ignore_cache, false ) . '/>';
	}

	/**
	 * Filter-callback function that adds links to the list of links displayed on the plugins page.
	 *
	 * @param array $actions array List of existing links.
	 * @param string $foo
	 *
	 * @return array The updated list of links.
	 *
	 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
	 *
	 * @since 1.0.0
	 */
	public function add_action_links( $actions, $foo ) {
		$settings = '<a href="' . esc_attr( get_admin_url( null,
				'options-general.php?page=wplibcalhours' ) ) . '">' . __( 'Settings', 'General' ) . '</a>';
		array_unshift( $actions, $settings );

		return $actions;
	}

	/**
	 * Update option callback on the "Ignore Cache" setting.
	 * Clears out any LibCal data that may be in the transient cache if this option's value changes.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/update_option_option/
	 *
	 * @since 1.0.0
	 */
	public function update_option_ignore_cache() {
		delete_transient( WpLibCalHours::CACHE_KEY );
	}
}
