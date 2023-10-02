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
 * @package    WpLibCalHours
 * @subpackage WpLibCalHours/includes
 * @author     Stefan Topfstedt <stefan.topfstedt@ucsf.edu>
 */
class WpLibCalHours_Client {

    /**
     * The ID of this plugin.
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private string $plugin_name;

    /**
     * The version of this plugin.
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private string $version;

    /**
     * The API endpoint URL.
     * @access protected
     * @var string $url The API endpoint.
     */
    protected string $url;

    /**
     * Initialize the class and set its properties.
     *
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     * @param      string $url The API endpoint.
     */
    public function __construct(string $plugin_name, string $version, string $url) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        $this->url         = $url;
    }

    /**
     * Returns the hours from a given location.
     *
     * @param string $location The name of the location.
     * @param boolean $ignore_cache Set to TRUE to bypass cache.
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getHours(string $location, bool $ignore_cache = false): array
    {
        $data = $this->getRawData($ignore_cache);
        return $this->extractTimetableForLocation($location, $data);
    }

    /**
     * Returns the raw data as consumed from the LibCal API.
     *
     * @param bool $ignore_cache Set to TRUE to bypass cache.
     *
     * @return bool|array
     *
     * @throws Exception
     */
    public function getRawData(bool $ignore_cache = false): bool|array
    {
        $data = false;
        $transient_key = $this->plugin_name . '_data';
        if(!$ignore_cache) {
            $data = get_transient($transient_key);
        }
        if(!$data) {
            $data = $this->fetchHoursFromAPI();

            if(!$ignore_cache) {
                $transient_timeout = 60 * 4; // 4 hours
                set_transient($transient_key, $data, $transient_timeout);
            }
        }

        return $data;
    }

    /**
     * Fetches hours-data from the LibCal Hours API.
     *
     * @return array The retrieved data.
     *
     * @throws Exception
     */
    protected function fetchHoursFromAPI(): array
    {
        if(trim($this->url) == '') {
            throw new \Exception(__('No LibCal API endpoint has been configured.', 'wplibcalhours'));
        }

        $response = wp_remote_get($this->url);
        if(is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        $payload = wp_remote_retrieve_body($response);
        $data    = json_decode($payload, true);
        if(! is_array($data)) {
            throw new \Exception(__('Unable to JSON-decode the payload retrieved from API endpoint', 'wplibcalhours'));
        }

        return $data;
    }

    /**
     * Extracts the hours for a given location from the given data set.
     *
     * @param string $location The name of the location.
     * @param array $data The entire location/hours data set.
     *
     * @return array The hours for the given location.
     *
     * @throws Exception
     */
    protected function extractTimetableForLocation(string $location, array $data): array
    {
        $locations_data = $data['locations'];
        $location_data  = array_values(array_filter($locations_data,
            function($location_data) use($location) {
                return($location_data['name'] == $location);
            }));
        if(empty($location_data)) {
            throw new \Exception(__('The requested location does not exist in LibCal data.', 'wplibcalhours'));
        }
        $location_data = $location_data[0];

        if (!array_key_exists('weeks', $location_data)) {
            throw new \Exception(__('No weeks found for location', 'wplibcalhours'));
        }

        return $location_data;
    }
}
