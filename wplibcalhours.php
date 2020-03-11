<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/ucsf-ckm/wplibcalhours
 * @since             1.0.0
 * @package           WpLibCalHours
 *
 * @wordpress-plugin
 * Plugin Name:       LibCal Hours for WordPress
 * Plugin URI:        https://github.com/ucsf-ckm/wplibcalhours
 * Description:       Embed LibCal hours for a given location into contents via short-code.
 * Version:           1.2.0
 * Author:            Stefan Topfstedt
 * Author URI:        https://github.com/stopfstedt
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       wplibcalhours
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/ucsf-ckm/wplibcalhours
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wplibcalhours-deactivator.php
 */
function deactivate_wplibcalhours() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wplibcalhours-deactivator.php';
	WpLibCalHours_Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_wplibcalhours' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wplibcalhours.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wplibcalhours() {

	$plugin = new WpLibCalHours();
	$plugin->run();

}

run_wplibcalhours();
