<?php
/**
 * @package bktsk-yt-live-scheduler
 * @author SASAGAWA Kiyoshi
 * @license GPL-2.0+
 */

// get posts using WP_Query
function bktsk_yt_live_get_posts( $args = array() ) {
	$args['post_type'] = 'bktskytlive';

	if ( ! isset( $args['order'] ) ) {
		$args['order'] = 'ASC';
	}
	if ( ! isset( $args['meta_key'] ) ) {
		$args['meta_key'] = 'bktsk_yt_live_frontpage_start';
	}
	if ( ! isset( $args['orderby'] ) ) {
		$args['orderby'] = 'meta_value';
	}
	$bktsk_yt_live_posts = new WP_Query( $args );
	return $bktsk_yt_live_posts;
}

// make ics data (only for VEVENT)
// line brake would be \n, becaouse the data will be converted after completed.
function bktsk_yt_live_make_events_ics( $args = array() ) {

	if ( ! isset( $args['posts_per_page'] ) ) {
		$args['posts_per_page'] = -1;
	}
	$bktsk_live_events = bktsk_yt_live_get_posts( $args );

	if ( $bktsk_live_events->have_posts() ) {

		$wp_timezone = get_option( 'timezone_string' );

		$bktsk_yt_live_ics_events = '';

		while ( $bktsk_live_events->have_posts() ) {
			$bktsk_live_events->the_post();

			$bktsk_live_id = get_the_ID();

			$bktsk_dtstamp_obj = new DateTime( '', new DateTimeZone( 'UTC' ) );
			$bktsk_dtstamp     = $bktsk_dtstamp_obj->format( 'Ymd\THis\Z' );

			$bktsk_created_obj = new DateTime( get_the_date( 'Y-m-d H:i:s' ) );
			$bktsk_created     = $bktsk_created_obj->format( 'Ymd\THis\Z' );

			$bktsk_modified_obj = new DateTime( get_the_modified_date( 'Y-m-d H:i:s' ) );
			$bktsk_modified     = $bktsk_modified_obj->format( 'Ymd\THis\Z' );

			$bktsk_live_title     = get_the_title();
			$bktsk_live_desc_text = get_the_excerpt();
			$bktsk_live_desc_html = apply_filters( 'the_content', get_the_content() );
			$bktsk_live_desc_html = preg_replace( "/\r\n|\r|\n/", '', $bktsk_live_desc_html );

			$bktsk_live_type = get_post_meta( $bktsk_live_id, 'bktsk_yt_live_type', true );

			$bktsk_yt_live_options   = get_option( 'bktsk_yt_scheduler_options' );
			$bktsk_live_canceled_tag = $bktsk_yt_live_options['canceled_tag'];
			$bktsk_live_notfixed_tag = $bktsk_yt_live_options['notfixed_tag'];
			$bktsk_live_dayoff_tag   = $bktsk_yt_live_options['dayoff_tag'];

			switch ( $bktsk_live_type ) {

				case 'live_schedule':
				case 'canceled_live_schedule':
					if ( 'canceled_live_schedule' === $bktsk_live_type ) {
						$bktsk_live_title = $bktsk_live_canceled_tag . $bktsk_live_title;
					}
					$bktsk_live_start_meta     = get_post_meta( $bktsk_live_id, 'bktsk_yt_live_start', true );
					$bktsk_live_start_datetime = new DateTime( $bktsk_live_start_meta, new DateTimeZone( 'UTC' ) );
					$bktsk_live_start_format   = $bktsk_live_start_datetime->format( 'Ymd\THis\Z' );
					$bktsk_live_start          = 'DTSTART:' . $bktsk_live_start_format;

					$bktsk_live_end_meta     = get_post_meta( $bktsk_live_id, 'bktsk_yt_live_end', true );
					$bktsk_live_end_datetime = new DateTime( $bktsk_live_end_meta, new DateTimeZone( 'UTC' ) );
					$bktsk_live_end_format   = $bktsk_live_end_datetime->format( 'Ymd\THis\Z' );
					$bktsk_live_end          = 'DTEND:' . $bktsk_live_end_format;
					break;

				case 'all_day_live_schedule':
				case 'canceled_all_day_live_schedule':
					if ( 'canceled_all_day_live_schedule' === $bktsk_live_type ) {
						$bktsk_live_title = $bktsk_live_canceled_tag . $bktsk_live_title;
					} else {
						$bktsk_live_title = $bktsk_live_notfixed_tag . $bktsk_live_title;
					}
					$bktsk_live_start_meta     = get_post_meta( $bktsk_live_id, 'bktsk_yt_all_day_live_start', true );
					$bktsk_live_start_datetime = new DateTime( $bktsk_live_start_meta, new DateTimeZone( 'UTC' ) );
					$bktsk_live_start_format   = $bktsk_live_start_datetime->format( 'Ymd' );
					$bktsk_live_start          = 'DTSTART;VALUE=DATE:' . $bktsk_live_start_format;

					$bktsk_live_end_meta     = get_post_meta( $bktsk_live_id, 'bktsk_yt_all_day_live_end', true );
					$bktsk_live_end_datetime = new DateTime( $bktsk_live_end_meta, new DateTimeZone( 'UTC' ) );
					$bktsk_live_end_format   = $bktsk_live_end_datetime->modify( '+1 day' )->format( 'Ymd' );
					$bktsk_live_end          = 'DTEND;VALUE=DATE:' . $bktsk_live_end_format;
					break;

				case 'day_off':
					$bktsk_live_title          = $bktsk_live_dayoff_tag . $bktsk_live_title;
					$bktsk_live_start_meta     = get_post_meta( $bktsk_live_id, 'bktsk_yt_day_off_start', true );
					$bktsk_live_start_datetime = new DateTime( $bktsk_live_start_meta, new DateTimeZone( 'UTC' ) );
					$bktsk_live_start_format   = $bktsk_live_start_datetime->format( 'Ymd' );
					$bktsk_live_start          = 'DTSTART;VALUE=DATE:' . $bktsk_live_start_format;

					$bktsk_live_end_meta     = get_post_meta( $bktsk_live_id, 'bktsk_yt_day_off_end', true );
					$bktsk_live_end_datetime = new DateTime( $bktsk_live_end_meta, new DateTimeZone( 'UTC' ) );
					$bktsk_live_end_format   = $bktsk_live_end_datetime->modify( '+1 day' )->format( 'Ymd' );
					$bktsk_live_end          = 'DTEND;VALUE=DATE:' . $bktsk_live_end_format;
					break;
			}

			$bktsk_live_url = get_post_meta( $bktsk_live_id, 'bktsk_yt_live_url', true );
			if ( empty( $bktsk_live_url ) ) {
				$bktsk_live_url = get_the_permalink();
			}

			$bktsk_live_uid = $bktsk_live_id . '@' . parse_url( get_bloginfo( 'url' ), PHP_URL_HOST );

			$bktsk_yt_live_ics_event = <<<EOF
BEGIN:VEVENT
UID:$bktsk_live_uid
DTSTAMP:$bktsk_dtstamp
STATUS:CONFIRMED
CREATED:$bktsk_created
LAST-MODIFIED:$bktsk_modified
SUMMARY:$bktsk_live_title
DESCRIPTION:$bktsk_live_desc_text
X-ALT-DESC;FMTTYPE=text/html:$bktsk_live_desc_html
$bktsk_live_start
$bktsk_live_end
TRANSP:TRANSPARENT
PRIORITY:0
CLASS:PUBLIC
URL;VALUE=URI:$bktsk_live_url
END:VEVENT
EOF;

			$bktsk_yt_live_ics_events .= "\n" . $bktsk_yt_live_ics_event;
		}
		wp_reset_postdata();
		$bktsk_yt_live_ics_events_l10n = apply_filters( 'gettext', $bktsk_yt_live_ics_events );
		return $bktsk_yt_live_ics_events_l10n;
	}
}
