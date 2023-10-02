<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/ucsf-ckm/wplibcalhours
 * @since      1.0.0
 *
 * @package    WpLibCalHours
 * @subpackage WpLibCalHours/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 * @package    WpLibCalHours
 * @subpackage WpLibCalHours/includes
 * @author     Stefan Topfstedt <stefan.topfstedt@ucsf.edu>
 */
class WpLibCalHours_i18n {
    /**
     * Load the plugin text domain for translation.
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'wplibcalhours',
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );
    }
}
