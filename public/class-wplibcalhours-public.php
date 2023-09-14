<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/ucsf-ckm/wplibcalhours
 * @since      1.0.0
 *
 * @package    WpLibCalHours
 * @subpackage WpLibCalHours/public
 */

use JetBrains\PhpStorm\Pure;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WpLibCalHours
 * @subpackage WpLibCalHours/public
 * @author     Stefan Topfstedt <stefan.topfstedt@ucsf.edu>
 * @author     Dean Farrell <lfarrell@email.unc.edu>
 */
class WpLibCalHours_Public {
    /**
     * The default number of weeks to display.
     * @access public
     * @var int DEFAULT_NUM_WEEKS The default number of weeks to display.
     */
    const DEFAULT_NUM_WEEKS = 5;

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
     * @var WpLibCalHours_Client $client The LibCal API client.
     */
    private WpLibCalHours_Client $client;

    /**
     * Initialize the class and set its properties.
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     * @param      WpLibCalHours_Client $client The LibCal API client.
     */
    public function __construct($plugin_name, $version, WpLibCalHours_Client $client) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        $this->client      = $client;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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

        wp_enqueue_style($this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/wplibcalhours-public.css',
            array(),
            $this->version,
            'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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

        wp_enqueue_script($this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/wplibcalhours-public.js',
            array('jquery'),
            $this->version,
            false);

        wp_enqueue_script($this->plugin_name . '-grid',
            plugin_dir_url(__FILE__) . 'js/grid.js',
            [],
            $this->version,
            ['in_footer' => true, 'strategy'  => 'defer']);
    }

    /**
     * Registers all short codes at once.
     */
    public function register_shortcodes() {
        add_shortcode('wplibcalhours', [$this, 'wplibcalhours_sc']);
    }

    /**
     * Processes the wplibcalhours shortcode.
     *
     * @param array $attrs The attributes from the shortcode.
     *
     * @return string The shortcode output.
     */
    public function wplibcalhours_sc(array $attrs = []): string
    {
        $attrs = array_change_key_case($attrs, CASE_LOWER);
        /*
         * display_type options 'table', 'grid', 'block
         */
        $attrs = shortcode_atts([
            'location'  => '',
            'display_type' => 'grid',
            'today_only' => false,
            'num_weeks' => self::DEFAULT_NUM_WEEKS
        ], $attrs);

        $num_weeks = (int) $attrs['num_weeks'];

        $today_only = $attrs['today_only'] == 'true';
        if ($today_only) {
            $num_weeks = 1;
        }
        if ($num_weeks < 1 || $num_weeks > self::DEFAULT_NUM_WEEKS) {
            $num_weeks = self::DEFAULT_NUM_WEEKS;
        }

        $num_days = $num_weeks * 7;

        $ignore_cache = (boolean) get_option('wplibcalhours_ignore_cache');
        $location = strip_tags($attrs['location']);

        $data = [];

        try {
            $data = $this->client->getHours($location, $ignore_cache);
            $data = $this->extract_hours($data['weeks']);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        $days = $this->setDays($num_days, $data);
        ob_start();
        if ($attrs['display_type'] == 'table') {
            require_once 'partials/table.php';
        } else {
            require_once 'partials/grid.php';
        }
        return ob_get_clean();
    }

    /**
     * Formats the date information returned from LibCal
     * @param $num_days
     * @param $data
     * @return array
     */
    private function setDays($num_days, $data): array
    {
        // calculate the start date (this should either be today, or the Monday of this week).
        $now = current_time('timestamp');
        $today = date_create()->setTimestamp($now);
        $start_date = clone $today;

        $days = [];
        for ($i = 0; $i < $num_days; $i ++) {
            $date = clone $start_date;
            $date->add(new \DateInterval("P${i}D"));
            $key = $date->format('Y-m-d');

            $day = array('date' => $date);
            $day['text'] = (array_key_exists($key, $data)) ? $data[$key] : __('n/a', 'wplibcalhours');
            $day['is_today'] = $key === $today->format('Y-m-d');
            $days[] = $day;
        }

        return $days;
    }
    /**
     * Extracts opening hours from a given list of opening hours as returned from the API.
     *
     * @param array $weeks_raw_data An array of nested arrays, each one containing the opening hours for an entire week.
     *
     * @return array The list of opening hours, keyed off by their date ('YYYY-MM-DD').
     *
     * @throws Exception
     */
    protected function extract_hours(array $weeks_raw_data): array
    {
        if (empty($weeks_raw_data)) {
            throw new \Exception(__('Retrieved data is empty.', 'wplibcalhours'));
        }

        $all_days_raw = [];
        foreach ($weeks_raw_data as $week_raw) {
            $all_days_raw = array_merge($all_days_raw, array_values($week_raw));
        }

        $days = [];
        foreach ($all_days_raw as $day_raw) {
            if (array_key_exists('times', $day_raw) && array_key_exists('date', $day_raw)) {
                $text = __('n/a', 'wplibcalhours');
                switch ($day_raw['times']['status']) {
                    case '24hours':
                        $text = __('24 hours', 'wplibcalhours');
                        break;
                    case 'closed':
                        $text = __('closed', 'wplibcalhours');
                        break;
                    default:
                        if (array_key_exists('hours', $day_raw['times'])  && ! empty($day_raw['times']['hours'])) {
                            $hours_text = [];
                            foreach($day_raw['times']['hours'] as $hours) {
                                if (array_key_exists('from', $hours) && array_key_exists('to', $hours)) {
                                    $hours_text[] = $hours['from'] . ' - ' . $hours['to'];
                                }
                            }
                            $text = implode('<br>', $hours_text);
                        }
                }

                $days[$day_raw['date']] = ['hours' => $text, 'status' => $this->currentlyOpen($day_raw['times'])];
            }
        }

        return $days;
    }

    /**
     * Sends a JSON-formatted response of all library location timetables.
     */
    public function api() {
        $ignore_cache = (boolean) get_option('wplibcalhours_ignore_cache');
        $timestamp    = current_time('timestamp');
        $now          = date('Y-m-d', $timestamp);
        try {
            $data  = $this->client->getRawData($ignore_cache);
            $rhett = [];
            foreach ($data['locations'] as $location) {
                $rhett[ $location['name'] ] = [];
                $timetable                  = $this->extract_hours($location['weeks']);
                foreach ($timetable as $date => $hours) {
                    if (strcmp($now, $date) > 0) {
                        continue;
                    }
                    $rhett[ $location['name'] ][] = [
                        'day'  => date('D', strtotime($date)),
                        'date' => date('M j', strtotime($date)),
                        'text' => $hours,
                    ];

                }

            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        wp_send_json($rhett);
    }

    /**
     * UNC additions
     */

    /**
     * Set open status
     * @param $day_info
     * @return string
     */
    public function currentlyOpen($day_info): string
    {
        if (array_key_exists('currently_open', $day_info) && $day_info['currently_open'] == 1) {
            return 'open';
        }
        return 'closed';
    }

    /**
     * @param $day
     * @return mixed
     */
    public function hoursText($day): mixed
    {
        return is_array($day['text']) ? $day['text']['hours'] : $day['text'];
    }

    /**
     * Formats open until time
     * @param $day
     * @return mixed|string
     */
    #[Pure] public function openUntil($day): mixed
    {
        $hours_text = $this->hoursText($day);
        $hours = preg_split('/-/', $hours_text);
        if (sizeof($hours) == 1) {
            return $hours[0];
        }
        return 'Until ' . trim($hours[1]);
    }

    public function setToday($today): string
    {
        return ($today) ? 'class="today"' : '';
    }

    public function weekBlocks($days): array
    {
        return array_chunk($days, 7);
    }

    public function setClassList($stacked): string
    {
        return ($stacked) ? 'hours-list-view-stacked' : '';
    }

    public function getBaseLocation($location): string
    {
        $location_parts = preg_split('/\s+/', $location);
        return strtolower(trim($location_parts[0]));
    }
}
