<?php
/**
 * @package bktsk-yt-live-scheduler
 * @author SASAGAWA Kiyoshi
 * @license GPL-2.0+
 */

// add url route for ics

add_filter( 'query_vars', 'bktsk_yt_scheduler_ics_query_vars' );
add_action( 'init', 'bktsk_yt_scheduler_ics_urls' );

function bktsk_yt_scheduler_ics_query_vars( $vars ) {
	$vars[] = 'bktsk_yt_live';
	return $vars;
}

function bktsk_yt_scheduler_ics_urls() {
	$bktsk_yt_live_options   = get_option( 'bktsk_yt_scheduler_options' );
	$bktsk_yt_live_ical_slug = $bktsk_yt_live_options['ical_slug'];

	if ( empty( $bktsk_yt_live_ical_slug ) ) {
		$bktsk_yt_live_ical_slug = 'bktsk_yt_live';
	}

	add_rewrite_rule(
		'^' . $bktsk_yt_live_ical_slug . '/?',
		'index.php?bktsk_yt_live=true',
		'top'
	);
}

// add response for ics

add_action( 'parse_request', 'bktsk_yt_scheduler_ics_requests' );

function bktsk_yt_scheduler_ics_requests( $wp ) {
	$valid_actions = array( true );

	if (
	! empty( $wp->query_vars['bktsk_yt_live'] ) &&
	in_array( $wp->query_vars['bktsk_yt_live'], $valid_actions )
	) {

		$bktsk_yt_live_options    = get_option( 'bktsk_yt_scheduler_options' );
		$bktsk_yt_live_ical_title = $bktsk_yt_live_options['ical_title'];
		$bktsk_yt_live_ical_desc  = $bktsk_yt_live_options['ical_desc'];
		if ( isset( $bktsk_yt_live_ical_title ) ) {
			$bktsk_yt_live_ical_name = $bktsk_yt_live_ical_title;
		} else {
			$bktst_yt_live_ical_name = 'iCalendar';
		}

		header( 'Content-Type: text/calendar; charset=UTF-8' );
		$bktsk_yt_live_calendar = <<<EOF
BEGIN:VCALENDAR
CALSCALE:GREGORIAN
PRODID:-//$bktsk_yt_live_ical_name//BKTSK YouTube Live Scheduler for WordPress//EN
VERSION:2.0
X-WR-CALNAME:$bktsk_yt_live_ical_title
X-WR-CALDESC:$bktsk_yt_live_ical_desc
EOF;

		$bktsk_yt_live_calendar .= "\n" . bktsk_yt_live_make_events_ics();

		$bktsk_yt_live_calendar .= <<<EOF

END:VCALENDAR
EOF;

		$bktsk_tmp = preg_replace( "/\r\n|\r|\n/", "\r\n", $bktsk_yt_live_calendar );
		echo preg_replace( "/\r\n\r\n/", "\r\n", $bktsk_tmp );
		exit();
	}

}
