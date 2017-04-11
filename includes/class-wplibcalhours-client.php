<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WpLibCalHours
 * @subpackage WpLibCalHours/includes
 */

/**
 * LibCal Hours client.
 *
 * This class defines all code necessary to retrieve hours data from LibCal API.
 *
 * @since      1.0.0
 * @package    WpLibCalHours
 * @subpackage WpLibCalHours/includes
 * @author     Stefan Topfstedt <stefan.topfstedt@ucsf.edu>
 */
class WpLibCalHours_Client {

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
	 * Returns the hours from a given location.
	 *
	 * @param string $location The name of the location.
	 * @param boolean $ignore_cache Set to TRUE to bypass cache.
	 *
	 * @return array|WP_Error
	 *
	 * @since    1.0.0
	 */
	public function getHours( $location, $ignore_cache = false ) {
		$data          = false;
		$transient_key = $this->plugin_name . '_data';
		if ( ! $ignore_cache ) {
			$data = get_transient( $transient_key );
		}
		if ( false === $data ) {
			$data = $this->fetchHoursFromAPI();
			if ( is_wp_error( $data ) ) {
				return $data;
			}
			if ( ! $ignore_cache ) {
				$transient_timeout = 60 * 4; // 4 hours
				set_transient( $transient_key, $data, $transient_timeout );
			}
		}

		return $this->extractTimetableForLocation( $location, $data );
	}

	protected function fetchHoursFromAPI() {
		$url = get_option( $this->plugin_name . '_api_url' );
		if ( '' === trim( $url ) ) {
			return new WP_Error( $this->plugin_name . '_missing_api_url',
				__( 'No LibCal API endpoint has been configured.', 'wplibcalhours' )
			);
		}
		$response = wp_remote_get( $url );
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$payload = wp_remote_retrieve_body( $response );
		if ( is_wp_error( $payload ) ) {
			return $payload;
		}

		$data = json_decode( $payload, true );
		if ( false === $data ) {
			return new WP_Error( $this->plugin_name . '_invalid_json',
				__( 'Unable to JSON-decode the payload retrieved from API endpoint', 'wplibcalhours' ),
				array( $url, $payload, json_last_error() )
			);
		}

		return $data;
	}

	/**
	 * Extracts the hours for a given location from the given data set.
	 *
	 * @param string $location The name of the location.
	 * @param array $data The entire location/hours data set
	 *
	 * @return array|WP_Error The hours for the given location, or an error if none could be found.
	 *
	 * @since 1.0.0
	 */
	protected function extractTimetableForLocation( $location, array $data ) {
		$locations_data = $data['locations'];
		$location_data  = array_values( array_filter( $locations_data,
			function ( $location_data ) use ( $location ) {
				return ( $location_data['name'] === $location );
			} ) );
		if ( empty( $location_data ) ) {
			return new WP_Error( $this->plugin_name . '_unknown_location',
				__( 'The requested location does not exist in LibCal data.', 'wplibcalhours' ),
				array( $location, $data )
			);
		}
		$location_data = $location_data[0];

		return $location_data;
	}
}
