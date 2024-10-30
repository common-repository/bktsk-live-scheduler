<?php
/**
 * @package bktsk-yt-live-scheduler
 * @author SASAGAWA Kiyoshi
 * @license GPL-2.0+
 */

class BktskYtSchedulerAdminPage {

	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		// This page will be under "Settings"
		add_options_page(
			__( 'YouTube Live Scheduler Settings', 'bktsk-live-scheduler' ),
			__( 'YT Live Settings', 'bktsk-live-scheduler' ),
			'administrator',
			'bktsk-live-scheduleradmin',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		// Set class property
		$this->options = get_option( 'bktsk_yt_scheduler_options' );
		?>
		<div class="wrap">
			<h2><?php _e( 'YouTube Live Scheduler Settings', 'bktsk-live-scheduler' ); ?></h2>
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'bktsk-yt-scheduler-group' );
				do_settings_sections( 'bktsk-yt-scheduler-admin' );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting(
			'bktsk-yt-scheduler-group', // Option group
			'bktsk_yt_scheduler_options', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		// add sction for slugs
		add_settings_section(
			'bktsk-yt-scheduler-slugs', // ID
			__( 'Slugs', 'bktsk-live-scheduler' ), // Title
			array( $this, 'print_slugs_section_info' ), // Callback
			'bktsk-yt-scheduler-admin' // Page
		);

		// add field for post type slug
		add_settings_field(
			'posttype_slug', // ID
			__( 'Live post slug', 'bktsk-live-scheduler' ), // Title
			array( $this, 'posttype_slug_callback' ), // Callback
			'bktsk-yt-scheduler-admin', // Page
			'bktsk-yt-scheduler-slugs' // Section
		);

		// add field for taxonomy slug
		add_settings_field(
			'taxonomy_slug',
			__( 'Live category slug', 'bktsk-live-scheduler' ),
			array( $this, 'taxonomy_slug_callback' ),
			'bktsk-yt-scheduler-admin',
			'bktsk-yt-scheduler-slugs'
		);

		// add field for iCalendar URL slug
		add_settings_field(
			'ical_slug',
			__( 'Live iCalendar slug', 'bktsk-live-scheduler' ),
			array( $this, 'ical_slug_callback' ),
			'bktsk-yt-scheduler-admin',
			'bktsk-yt-scheduler-slugs'
		);

		// add section for iCalendar title/description
		add_settings_section(
			'bktsk-yt-scheduler-ical-info', // ID
			__( 'iCalendar Info', 'bktsk-live-scheduler' ), // Title
			array( $this, 'print_icalinfo_section_info' ), // Callback
			'bktsk-yt-scheduler-admin' // Page
		);

		// add field for name of iCalendar
		add_settings_field(
			'ical_title', // ID
			__( 'iCalendar title', 'bktsk-live-scheduler' ), // Title
			array( $this, 'title_info_callback' ), // Callback
			'bktsk-yt-scheduler-admin', // Page
			'bktsk-yt-scheduler-ical-info' // Section
		);

		// add field for description of iCalendar
		add_settings_field(
			'ical_desc',
			__( 'iCalendar Description', 'bktsk-live-scheduler' ),
			array( $this, 'desc_info_callback' ),
			'bktsk-yt-scheduler-admin',
			'bktsk-yt-scheduler-ical-info'
		);

		// add section for iCalendar tags
		add_settings_section(
			'bktsk-yt-scheduler-ical-tags', // ID
			__( 'iCalendar Tags', 'bktsk-live-scheduler' ), // Title
			array( $this, 'print_icaltags_section_info' ), // Callback
			'bktsk-yt-scheduler-admin' // Page
		);

		// add field for tag of canceled events
		add_settings_field(
			'canceled_tag', // ID
			__( 'Canceled', 'bktsk-live-scheduler' ), // Title
			array( $this, 'canceled_tag_callback' ), // Callback
			'bktsk-yt-scheduler-admin', // Page
			'bktsk-yt-scheduler-ical-tags' // Section
		);

		// add field for tag of time not fixed events
		add_settings_field(
			'notfixed_tag',
			__( 'Time not fixed', 'bktsk-live-scheduler' ),
			array( $this, 'notfixed_tag_callback' ),
			'bktsk-yt-scheduler-admin',
			'bktsk-yt-scheduler-ical-tags'
		);

		// add field for tag of day off events
		add_settings_field(
			'dayoff_tag',
			__( 'Day off', 'bktsk-live-scheduler' ),
			array( $this, 'dayoff_tag_callback' ),
			'bktsk-yt-scheduler-admin',
			'bktsk-yt-scheduler-ical-tags'
		);

		// add section for calendar start day
		add_settings_section(
			'bktsk-yt-scheduler-cal-disp', // ID
			__( 'Calendar display settings', 'bktsk-live-scheduler' ), // Title
			array( $this, 'print_cal_disp_section_info' ), // Callback
			'bktsk-yt-scheduler-admin' // Page
		);

		// add field for tag of canceled events
		add_settings_field(
			'wod_start', // ID
			__( 'Week start', 'bktsk-live-scheduler' ), // Title
			array( $this, 'calendar_start_callback' ), // Callback
			'bktsk-yt-scheduler-admin', // Page
			'bktsk-yt-scheduler-cal-disp' // Section
		);

		// add field for calendar header format
		add_settings_field(
			'cal_month_format', // ID
			__( 'Calendar header month format', 'bktsk-live-scheduler' ), // Title
			array( $this, 'calendar_header_month_callback' ), // Callback
			'bktsk-yt-scheduler-admin', // Page
			'bktsk-yt-scheduler-cal-disp' // Section
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		$new_input = array();
		if ( isset( $input['posttype_slug'] ) ) {
			$new_input['posttype_slug'] = urlencode( $input['posttype_slug'] );
		}

		if ( isset( $input['taxonomy_slug'] ) ) {
			$new_input['taxonomy_slug'] = urlencode( $input['taxonomy_slug'] );
		}

		if ( isset( $input['ical_slug'] ) ) {
			$new_input['ical_slug'] = urlencode( $input['ical_slug'] );
		}

		if ( isset( $input['ical_title'] ) ) {
			$new_input['ical_title'] = $input['ical_title'];
		}

		if ( isset( $input['ical_desc'] ) ) {
			$new_input['ical_desc'] = $input['ical_desc'];
		}

		if ( isset( $input['canceled_tag'] ) ) {
			$new_input['canceled_tag'] = $input['canceled_tag'];
		}

		if ( isset( $input['notfixed_tag'] ) ) {
			$new_input['notfixed_tag'] = $input['notfixed_tag'];
		}

		if ( isset( $input['dayoff_tag'] ) ) {
			$new_input['dayoff_tag'] = $input['dayoff_tag'];
		}

		if ( isset( $input['wod_start'] ) ) {
			$new_input['wod_start'] = $input['wod_start'];
		}

		if ( isset( $input['cal_month_format'] ) ) {
			if ( ! empty( $input['cal_month_format'] ) ) {
				$new_input['cal_month_format'] = $input['cal_month_format'];
			} else {
				$new_input['cal_month_format'] = 'Y-m';
			}
		}
		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_slugs_section_info() {
		_e( 'Fields of this section will be used for URL slugs. All fields will be urlencoded.', 'bktsk-live-scheduler' );
		echo '<br>';
		_e( 'After changing this section, permalink update is strongly recommended', 'bktsk-live-scheduler' );
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function posttype_slug_callback() {
		printf(
			'<input type="text" id="posttype_slug" name="bktsk_yt_scheduler_options[posttype_slug]" value="%s" placeholder="live_schedule">',
			isset( $this->options['posttype_slug'] ) ? esc_attr( $this->options['posttype_slug'] ) : ''
		);
		echo '<div class="bktsk-yt-notes">';
		_e( 'This will be used for post type slug of the lives. "live_schedule" is the default. (When this field is empty.)', 'bktsk-live-scheduler' );
		echo '</div>';
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function taxonomy_slug_callback() {
		printf(
			'<input type="text" id="taxonomy_slug" name="bktsk_yt_scheduler_options[taxonomy_slug]" value="%s" placeholder="live_category">',
			isset( $this->options['taxonomy_slug'] ) ? esc_attr( $this->options['taxonomy_slug'] ) : ''
		);
		echo '<div class="bktsk-yt-notes">';
		_e( 'This will be used for taxonomy slug of the lives. "live_category" is the default. (When this field is empty.)', 'bktsk-live-scheduler' );
		echo '</div>';
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function ical_slug_callback() {
		printf(
			'<input type="text" id="ical_slug" name="bktsk_yt_scheduler_options[ical_slug]" value="%s" placeholder="bktsk_yt_live">',
			isset( $this->options['ical_slug'] ) ? esc_attr( $this->options['ical_slug'] ) : ''
		);
		echo '<div class="bktsk-yt-notes">';
		_e( 'This will be used for iCalendar URL slug. "bktsk_yt_live" is the default. (When this field is empty.)', 'bktsk-live-scheduler' );
		echo '</div>';
	}

	/**
	 * Print the Section text
	 */
	public function print_icalinfo_section_info() {
		_e( 'Fields of this section will be used on the iCalendar.', 'bktsk-live-scheduler' );
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function title_info_callback() {
		printf(
			'<input type="text" id="ical_title" name="bktsk_yt_scheduler_options[ical_title]" value="%s">',
			isset( $this->options['ical_title'] ) ? esc_attr( $this->options['ical_title'] ) : ''
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function desc_info_callback() {
		printf(
			'<input type="text" id="ical_desc" name="bktsk_yt_scheduler_options[ical_desc]" value="%s">',
			isset( $this->options['ical_desc'] ) ? esc_attr( $this->options['ical_desc'] ) : ''
		);
	}

	/**
	 * Print the Section text
	 */
	public function print_icaltags_section_info() {
		_e( 'Fields of this section will be used just before title (VEVENT/SUMMARY) on the iCalendar.', 'bktsk-live-scheduler' );
		echo '<br>';
		_e( 'When none given, nothing will be added.', 'bktsk-live-scheduler' );
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function canceled_tag_callback() {
		printf(
			'<input type="text" id="canceled_tag" name="bktsk_yt_scheduler_options[canceled_tag]" value="%s">',
			isset( $this->options['canceled_tag'] ) ? esc_attr( $this->options['canceled_tag'] ) : ''
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function notfixed_tag_callback() {
		printf(
			'<input type="text" id="notfixed_tag" name="bktsk_yt_scheduler_options[notfixed_tag]" value="%s">',
			isset( $this->options['notfixed_tag'] ) ? esc_attr( $this->options['notfixed_tag'] ) : ''
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function dayoff_tag_callback() {
		printf(
			'<input type="text" id="dayoff_tag" name="bktsk_yt_scheduler_options[dayoff_tag]" value="%s">',
			isset( $this->options['dayoff_tag'] ) ? esc_attr( $this->options['dayoff_tag'] ) : ''
		);
	}

	/**
	 * Print the Section text
	 */
	public function print_cal_disp_section_info() {
		_e( 'Fields of this section will be used for displaying calendar using the shortcode', 'bktsk-live-scheduler' );
		echo '<br>';
		_e( 'The shortcode is [bktsk-live-calendar] .', 'bktsk-live-scheduler' );
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function calendar_start_callback() {
		print( '<select id="wod_start" name="bktsk_yt_scheduler_options[wod_start]">' );
		printf(
			'<option value="0"%s>' . __( 'Sunday', 'bktsk-live-scheduler' ) . '</option>',
			0 == $this->options['wod_start'] ? ' selected' : ''
		);
		printf(
			'<option value="1"%s>' . __( 'Monday', 'bktsk-live-scheduler' ) . '</option>',
			1 == $this->options['wod_start'] ? ' selected' : ''
		);
		print( '</select>' );
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function calendar_header_month_callback() {
		printf(
			'<input type="text" id="cal_month_format" name="bktsk_yt_scheduler_options[cal_month_format]" value="%s">',
			isset( $this->options['cal_month_format'] ) ? esc_attr( $this->options['cal_month_format'] ) : ''
		);
		echo '<div class="bktsk-yt-notes">';
		_e( 'This will be used for the header of calendar displayed by shortcode. The format is as same as WordPress date format.', 'bktsk-live-scheduler' );
		echo '<br>';
		_e( 'If none given, this will the format will be <code>Y-m</code>.', 'bktsk-live-scheduler' );
		echo '</div>';
	}
}

if ( is_admin() ) {
	$bktsk_yt_scheduler_settings_page = new BktskYtSchedulerAdminPage();
}
