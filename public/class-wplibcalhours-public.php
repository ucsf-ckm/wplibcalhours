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

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WpLibCalHours
 * @subpackage WpLibCalHours/public
 * @author     Stefan Topfstedt <stefan.topfstedt@ucsf.edu>
 */
class WpLibCalHours_Public {

	/**
	 * The default number of weeks to display.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var int DEFAULT_NUM_WEEKS The default number of weeks to display.
	 */
	const DEFAULT_NUM_WEEKS = 3;

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
	 * @var WpLibCalHours_Client $client The LibCal API client.
	 */
	private $client;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 * @param      WpLibCalHours_Client $client The LibCal API client.
	 */
	public function __construct( $plugin_name, $version, WpLibCalHours_Client $client ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->client      = $client;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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
			plugin_dir_url( __FILE__ ) . 'css/wplibcalhours-public.css',
			array(),
			$this->version,
			'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
			plugin_dir_url( __FILE__ ) . 'js/wplibcalhours-public.js',
			array( 'jquery' ),
			$this->version,
			false );

	}

	/**
	 * Registers all short codes at once.
	 *
	 * @since 1.0.0
	 */
	public function register_shortcodes() {

		add_shortcode( 'wplibcalhours', array( $this, 'wplibcalhours_sc' ) );
	}

	/**
	 * Processes the wplibcalhours shortcode.
	 *
	 * @param array $attrs The attributes from the shortcode.
	 *
	 * @return string The shortcode output.
	 *
	 * @since 1.0.0
	 *
	 */
	public function wplibcalhours_sc( $attrs = array() ) {

		$attrs     = array_change_key_case( $attrs, CASE_LOWER );
		$attrs     = shortcode_atts( [
			'location'  => '',
			'num_weeks' => self::DEFAULT_NUM_WEEKS
		], $attrs );
		$num_weeks = (int) $attrs['num_weeks'];
		if ( $num_weeks < 1 || $num_weeks > self::DEFAULT_NUM_WEEKS ) {
			$num_weeks = self::DEFAULT_NUM_WEEKS;
		}
		$num_days = $num_weeks * 7;

		$ignore_cache = (boolean) get_option( 'wplibcalhours_ignore_cache' );
		$data         = [];
		try {
			$data = $this->client->getHours( $attrs['location'], $ignore_cache );
			$data = $this->extract_hours( $data['weeks'] );
		} catch ( \Exception $e ) {
			error_log( $e->getMessage() );
		}

		// calculate the start date (this should either be today, or the Monday of this week).
		$now = current_time( 'timestamp' );

		$today      = date_create()->setTimestamp( $now );
		$start_date = clone $today;

		$days = array();
		for ( $i = 0; $i < $num_days; $i ++ ) {
			$date = clone $start_date;
			$date->add( new \DateInterval( "P${i}D" ) );
			$key = $date->format( 'Y-m-d' );
			$day = array( 'date' => $date, 'text' => '' );
			if ( array_key_exists( $key, $data ) ) {
				$day['text'] = $data[ $key ];
			} else {
				$day['text'] = __( 'n/a', 'wplibcalhours' );
			}
			$day['is_today'] = $key === $today->format( 'Y-m-d' );
			$days[]          = $day;
		}

		$o = '<table class="wplibcalhours">';
		$o .= '<thead><tr><th colspan="3">' . __( 'Hours', 'wplibcalhours' ) . '</th></tr></thead>';
		$o .= '<tbody>';
		for ( $i = 0, $n = count( $days ); $i < $n; $i ++ ) {
			$day = $days[ $i ];
			if ( $i && ! ( $i % 7 ) ) {
				$o .= '</tbody><tbody class="hidden">';
			}
			/* @var \DateTime $date */
			$date = $day['date'];
			$o    .= '<tr' . ( $day['is_today'] ? ' class="today" ' : '' ) . '><td>' . $date->format( 'l' ) . '</td>';
			$o    .= '<td>' . $date->format( 'M j' ) . '</td>';
			$o    .= '<td>' . $day['text'] . '</td></tr>';
		}
		$o .= '</tbody>';

		if ( 1 < $num_weeks ) {
			$o .= '<tfoot><tr><td colspan="3">';
			$o .= '<a class="prev hidden">&laquo; ' . __( 'previous', 'wplibcalhours' ) . '</a>';
			$o .= '<a class="next">' . __( 'next', 'wplibcalhours' ) . ' &raquo;</a>';
			$o .= '</td></tr></tfoot>';
		}
		$o .= '</table>';

		return $o;
	}

	/**
	 * Extracts opening hours from a given list of opening hours as returned from the API.
	 *
	 * @param array $weeks_raw_data An array of nested arrays, each one containing the opening hours for an entire week.
	 *
	 * @return array The list of opening hours, keyed off by their date ('YYYY-MM-DD').
	 *
	 * @throws \Exception
	 *
	 * @since 1.0.0
	 */
	protected function extract_hours( array $weeks_raw_data ) {
		if ( empty( $weeks_raw_data ) ) {
			throw new \Exception( __( 'Retrieved data is empty.', 'wplibcalhours' ) );
		}

		$all_days_raw = array();
		foreach ( $weeks_raw_data as $week_raw ) {
			$all_days_raw = array_merge( $all_days_raw, array_values( $week_raw ) );
		}

		$days = array();
		foreach ( $all_days_raw as $day_raw ) {
			if ( array_key_exists( 'times', $day_raw ) && array_key_exists( 'date', $day_raw ) ) {
				$text = __( 'n/a', 'wplibcalhours' );
				switch ( $day_raw['times']['status'] ) {
					case '24hours':
						$text = __( '24 hours', 'wplibcalhours' );
						break;
					case 'closed':
						$text = __( 'closed', 'wplibcalhours' );
						break;
					default:
						if ( array_key_exists( 'hours', $day_raw['times'] )
						     && ! empty( $day_raw['times']['hours'] )
						     && array_key_exists( 'from', $day_raw['times']['hours'][0] )
						     && array_key_exists( 'to', $day_raw['times']['hours'][0] )
						) {
							$text = $day_raw['times']['hours'][0]['from'] . ' - ' . $day_raw['times']['hours'][0]['to'];
						}
				}
				$days[ $day_raw['date'] ] = $text;
			}
		}

		return $days;
	}

	/**
	 * Sends a JSON-formatted response of all library location timetables.
	 *
	 * @since 1.1.0
	 */
	public function api() {
		$ignore_cache = (boolean) get_option( 'wplibcalhours_ignore_cache' );
		$data         = [];
		try {
			$data = $this->client->getRawData( $ignore_cache );
		} catch ( \Exception $e ) {
			error_log( $e->getMessage() );
		}
		wp_send_json( $data );
	}
}
