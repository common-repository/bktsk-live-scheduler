<?php
/**
 * @package bktsk-yt-live-scheduler
 * @author SASAGAWA Kiyoshi
 * @license GPL-2.0+
 */

// making post type and add

add_action( 'init', 'bktsk_yt_scheduler_post_type_init' );

function bktsk_yt_scheduler_post_type_init() {
	$bktsk_yt_live_options       = get_option( 'bktsk_yt_scheduler_options' );
	$bktsk_yt_live_posttype_slug = $bktsk_yt_live_options['posttype_slug'];

	if ( empty( $bktsk_yt_live_posttype_slug ) ) {
		$bktsk_yt_live_posttype_slug = 'live_schedule';
	}

	$labels = array(
		'name'               => _x( 'YouTube Live Schedules', 'post type general name', 'bktsk-live-scheduler' ),
		'singular_name'      => _x( 'YouTube Live Schedule', 'post type singular name', 'bktsk-live-scheduler' ),
		'menu_name'          => _x( 'YT Live', 'admin menu', 'bktsk-live-scheduler' ),
		'name_admin_bar'     => _x( 'YT Live', 'add new on admin bar', 'bktsk-live-scheduler' ),
		'add_new'            => _x( 'Add New', 'Live', 'bktsk-live-scheduler' ),
		'add_new_item'       => __( 'Add New Live', 'bktsk-live-scheduler' ),
		'new_item'           => __( 'New Live', 'bktsk-live-scheduler' ),
		'edit_item'          => __( 'Edit Live', 'bktsk-live-scheduler' ),
		'view_item'          => __( 'View Live', 'bktsk-live-scheduler' ),
		'all_items'          => __( 'All Live Schedules', 'bktsk-live-scheduler' ),
		'search_items'       => __( 'Search Live Schedules', 'bktsk-live-scheduler' ),
		'parent_item_colon'  => __( 'Parent Lives:', 'bktsk-live-scheduler' ),
		'not_found'          => __( 'No Live found.', 'bktsk-live-scheduler' ),
		'not_found_in_trash' => __( 'No Live found in Trash.', 'bktsk-live-scheduler' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => $bktsk_yt_live_posttype_slug ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 5,
		'menu_icon'          => 'dashicons-calendar-alt',
		'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
	);

	register_post_type( 'bktskytlive', $args );
}

add_filter( 'use_block_editor_for_post', 'bktsk_yt_scheduler_block_disabler', 10, 2 );

function bktsk_yt_scheduler_block_disabler( $use_block_editor, $post ) {
	if ( 'bktskytlive' === $post->post_type ) {
		$use_block_editor = false;
	}
	return $use_block_editor;
}


// add meta_box for custom post type

add_action( 'admin_init', 'bktsk_yt_scheduler_add_meta_box' );
function bktsk_yt_scheduler_add_meta_box() {
	add_meta_box( 'bktsk_yt_scheduler_meta_box', __( 'Live Date/Time', 'bktsk-live-scheduler' ), 'bktsk_yt_scheduler_meta_html', 'bktskytlive', 'normal' );
}

function bktsk_yt_scheduler_meta_html() {
	global $post;
	$bktsk_yt_scheduler_custom = get_post_custom( $post->ID );
	$wp_timezone               = get_option( 'timezone_string' );
	$bktsk_yt_live_type        = 'live_schedule';

	//メタキーがあったら
	if ( ! empty( $bktsk_yt_scheduler_custom['bktsk_yt_live_type'] ) ) {
		if ( isset( $bktsk_yt_scheduler_custom['bktsk_yt_live_type'][0] ) ) {
			$bktsk_yt_live_type = $bktsk_yt_scheduler_custom['bktsk_yt_live_type'][0];
		}
	}

	if ( ! empty( $bktsk_yt_scheduler_custom['bktsk_yt_live_start'] ) ) {
		$live_start = new DateTime( $bktsk_yt_scheduler_custom['bktsk_yt_live_start'][0], new DateTimeZone( 'UTC' ) );

		$live_start->setTimezone( new DateTimeZone( $wp_timezone ) );
		$bktsk_yt_live_start_date = $live_start->format( 'Y-m-d' );
		$bktsk_yt_live_start_time = $live_start->format( 'H:i' );
	}

	if ( ! empty( $bktsk_yt_scheduler_custom['bktsk_yt_live_end'] ) ) {
		$live_end = new DateTime( $bktsk_yt_scheduler_custom['bktsk_yt_live_end'][0], new DateTimeZone( 'UTC' ) );

		$live_end->setTimezone( new DateTimeZone( $wp_timezone ) );

		$bktsk_yt_live_end_date = $live_end->format( 'Y-m-d' );
		$bktsk_yt_live_end_time = $live_end->format( 'H:i' );
	}

	if ( ! empty( $bktsk_yt_scheduler_custom['bktsk_yt_all_day_live_start'] ) ) {
		$all_day_live_start = new DateTime( $bktsk_yt_scheduler_custom['bktsk_yt_all_day_live_start'][0] );

		$bktsk_yt_all_day_live_start_date = $all_day_live_start->format( 'Y-m-d' );
	}

	if ( ! empty( $bktsk_yt_scheduler_custom['bktsk_yt_all_day_live_end'] ) ) {
		$all_day_live_end = new DateTime( $bktsk_yt_scheduler_custom['bktsk_yt_all_day_live_end'][0] );

		$bktsk_yt_all_day_live_end_date = $all_day_live_end->format( 'Y-m-d' );
	}

	if ( ! empty( $bktsk_yt_scheduler_custom['bktsk_yt_day_off_start'] ) ) {
		$day_off_start = new DateTime( $bktsk_yt_scheduler_custom['bktsk_yt_day_off_start'][0] );

		$bktsk_yt_day_off_start_date = $day_off_start->format( 'Y-m-d' );
	}

	if ( ! empty( $bktsk_yt_scheduler_custom['bktsk_yt_day_off_end'] ) ) {
		$day_off_end = new DateTime( $bktsk_yt_scheduler_custom['bktsk_yt_day_off_end'][0] );

		$bktsk_yt_day_off_end_date = $day_off_end->format( 'Y-m-d' );
	}

	if ( ! empty( $bktsk_yt_scheduler_custom['bktsk_yt_live_url'] ) ) {
		$bktsk_yt_live_url = $bktsk_yt_scheduler_custom['bktsk_yt_live_url'][0];
	}

	$timezone = new DateTime( null, new DateTimeZone( $wp_timezone ) );
	wp_nonce_field( 'bktskytlive-live-info-update', 'bktskytlive-live-nonce' );

	//入力フィールドの表示
	?>
	<style type="text/css">
	#live-time table th {
			text-align: left;
			font-weight: normal;
			padding-right: 10px;
	}
	.bktsk_yt_live_notice {
		display: inline-block;
		margin-left: 20px;
		color: #999999;
	}
	</style>
	<script>
	jQuery(document).ready(function ($) {
		// initialize input widgets first
		$('#bktsk_yt_live .time').timepicker({
			'showDuration': true,
			'timeFormat': 'H:i'
		});

		$('#bktsk_yt_live .date').datepicker({
			'dateFormat': 'yy-mm-dd',
			'autoclose': true
		});

		$('#bktsk_yt_all_day_live .date').datepicker({
			'dateFormat': 'yy-mm-dd',
			'autoclose': true
		});

		$('#bktsk_yt_day_off .date').datepicker({
			'dateFormat': 'yy-mm-dd',
			'autoclose': true
		});

		// initialize datepair
		$('#bktsk_yt_live').datepair({
			parseDate: function (el) {
				var val = $(el).datepicker('getDate');
				if (!val) {
					return null;
				}
				var utc = new Date(val);
				return utc && new Date(utc.getTime() + (utc.getTimezoneOffset() * 60000));
			},
			updateDate: function (el, v) {
				$(el).datepicker('setDate', new Date(v.getTime() - (v.getTimezoneOffset() * 60000)));
			}
		});

		// initialize datepair
		$('#bktsk_yt_all_day_live').datepair({
			parseDate: function (el) {
				var val = $(el).datepicker('getDate');
				if (!val) {
					return null;
				}
				var utc = new Date(val);
				return utc && new Date(utc.getTime() + (utc.getTimezoneOffset() * 60000));
			},
			updateDate: function (el, v) {
				$(el).datepicker('setDate', new Date(v.getTime() - (v.getTimezoneOffset() * 60000)));
			}
		});

		// initialize datepair
		$('#bktsk_yt_day_off').datepair({
			parseDate: function (el) {
				var val = $(el).datepicker('getDate');
				if (!val) {
					return null;
				}
				var utc = new Date(val);
				return utc && new Date(utc.getTime() + (utc.getTimezoneOffset() * 60000));
			},
			updateDate: function (el, v) {
				$(el).datepicker('setDate', new Date(v.getTime() - (v.getTimezoneOffset() * 60000)));
			}
		});

		$('#bktsk_yt_live_type').val("<?php echo $bktsk_yt_live_type; ?>");

		bktsk_yt_live_meta_selector ("<?php echo $bktsk_yt_live_type; ?>");

		// change Type
		$('#bktsk_yt_live_type').change(function() {
			var bktsk_yt_type = $(this).val();
			bktsk_yt_live_meta_selector(bktsk_yt_type);
		});

		// function to change select elements
		function bktsk_yt_live_meta_selector (bktsk_yt_live_type) {
			switch (bktsk_yt_live_type) {
				case 'live_schedule':
				case 'canceled_live_schedule':
					$('#bktsk_yt_live_schedule').show();
					$('#bktsk_yt_all_day_live_schedule').hide();
					$('#bktsk_yt_day_off_schedule').hide();
					break;

					case 'all_day_live_schedule':
					case 'canceled_all_day_live_schedule':
					$('#bktsk_yt_live_schedule').hide();
					$('#bktsk_yt_all_day_live_schedule').show();
					$('#bktsk_yt_day_off_schedule').hide();
					break;

				case 'day_off':
					$('#bktsk_yt_live_schedule').hide();
					$('#bktsk_yt_all_day_live_schedule').hide();
					$('#bktsk_yt_day_off_schedule').show();
					break;
			}
		}
	});
	</script>
	<div id="live-time">
	<select id='bktsk_yt_live_type' name="bktsk_yt_live_type">
		<option value="live_schedule"><?php _e( 'Live Schedule (time fixed)', 'bktsk-live-scheduler' ); ?></option>
		<option value="canceled_live_schedule"><?php _e( 'Canceled Live Schedule (time fixed)', 'bktsk-live-scheduler' ); ?></option>
		<option value="all_day_live_schedule"><?php _e( 'Live Date (time not fixed)', 'bktsk-live-scheduler' ); ?></option>
		<option value="canceled_all_day_live_schedule"><?php _e( 'Canceled Live Date (time not fixed)', 'bktsk-live-scheduler' ); ?></option>
		<option value="day_off"><?php _e( 'Live Day Off (decided not to live)', 'bktsk-live-scheduler' ); ?></option>
	</select>

	<!-- form area for live schedule (time fixed) -->
	<table id="bktsk_yt_live_schedule">
		<tr>
			<th><?php _e( 'Live Schedule', 'bktsk-live-scheduler' ); ?></th>
			<td id="bktsk_yt_live">
				<input type="text" class="date start" name="bktsk_yt_live_start_date" autocomplete="off"
				<?php
				if ( isset( $bktsk_yt_live_start_date ) ) {
					echo ' value="' . $bktsk_yt_live_start_date . '"';
				}
				?>
				>
				<input type="text" class="time start" name="bktsk_yt_live_start_time" autocomplete="off"
				<?php
				if ( isset( $bktsk_yt_live_start_time ) ) {
					echo ' value="' . $bktsk_yt_live_start_time . '"';
				}
				?>
				> <?php _e( 'to', 'bktsk-live-scheduler' ); ?>
				<input type="text" class="date end" name="bktsk_yt_live_end_date" autocomplete="off"
				<?php
				if ( isset( $bktsk_yt_live_end_date ) ) {
					echo ' value="' . $bktsk_yt_live_end_date . '"';
				}
				?>
				>
				<input type="text" class="time end" name="bktsk_yt_live_end_time" autocomplete="off"
				<?php
				if ( isset( $bktsk_yt_live_end_time ) ) {
					echo ' value="' . $bktsk_yt_live_end_time . '"';
				}
				?>
				>
			</td>
		</tr>
	</table>

	<!-- form for live schedule without time (time not fixed but scheduled) -->
	<table id="bktsk_yt_all_day_live_schedule">
		<tr>
			<th><?php _e( 'Live Date', 'bktsk-live-scheduler' ); ?></th>
			<td id="bktsk_yt_all_day_live">
				<input type="text" class="date start" name="bktsk_yt_all_day_live_start_date" autocomplete="off"
				<?php
				if ( isset( $bktsk_yt_all_day_live_start_date ) ) {
					echo ' value="' . $bktsk_yt_all_day_live_start_date . '"';
				}
				?>
				> <?php _e( 'to', 'bktsk-live-scheduler' ); ?>
				<input type="text" class="date end" name="bktsk_yt_all_day_live_end_date" autocomplete="off"
				<?php
				if ( isset( $bktsk_yt_all_day_live_end_date ) ) {
					echo ' value="' . $bktsk_yt_all_day_live_end_date . '"';
				}
				?>
				>
			</td>
		</tr>
	</table>

	<!-- form for day off (decided not to live) -->
	<table id="bktsk_yt_day_off_schedule">
		<tr>
			<th><?php _e( 'Date of Day Off', 'bktsk-live-scheduler' ); ?></th>
			<td id="bktsk_yt_day_off">
				<input type="text" class="date start" name="bktsk_yt_day_off_start_date" autocomplete="off"
				<?php
				if ( isset( $bktsk_yt_day_off_start_date ) ) {
					echo ' value="' . $bktsk_yt_day_off_start_date . '"';
				}
				?>
				> <?php _e( 'to', 'bktsk-live-scheduler' ); ?>
				<input type="text" class="date end" name="bktsk_yt_day_off_end_date" autocomplete="off"
				<?php
				if ( isset( $bktsk_yt_day_off_end_date ) ) {
					echo ' value="' . $bktsk_yt_day_off_end_date . '"';
				}
				?>
				>
			</td>
		</tr>
	</table>

	<table>
		<tr>
			<th><?php _e( 'TimeZone', 'bktsk-live-scheduler' ); ?></th>
			<td><?php echo $timezone->format( 'e (P)' ); ?> <span class="bktsk_yt_live_notice">* <?php _e( 'This can be changed at settings page from dashboard.', 'bktsk-live-scheduler' ); ?></span></td>
		</tr>
	</table>

	<table>
		<tr>
			<th><?php _e( 'URL', 'bktsk-live-scheduler' ); ?></th>
			<td><input type="text" class="url" name="bktsk_yt_live_url" autocomplete="off"
			<?php
			if ( isset( $bktsk_yt_live_url ) ) {
				echo ' value="' . $bktsk_yt_live_url . '"';
			}
			?>
			></td>
		</tr>
	</table>

</div>
	<?php
}


// add JavaScript and CSS files
add_action( 'admin_enqueue_scripts', 'bktsk_yt_scheduler_load_jquery' );

function bktsk_yt_scheduler_load_jquery( $hook ) {
	if ( 'post-new.php' == $hook || 'post.php' == $hook ) {
		global $post;
		if ( 'bktskytlive' === $post->post_type ) {
			$js_url    = plugins_url( '../js/', __FILE__ );
			$style_url = plugins_url( '../style/', __FILE__ );
			wp_enqueue_script( 'datepair', $js_url . 'datepair.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'jquery-datepair', $js_url . 'jquery.datepair.js', array( 'jquery' ) );
			//wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-timepicker', $js_url . 'jquery.timepicker.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'moment' );
			//wp_enqueue_script( 'moment', $js_url . 'moment.min.js', array( 'jquery' ) );

			wp_enqueue_style( 'jquery-ui', $style_url . 'jquery-ui.min.css' );
			wp_enqueue_style( 'jquery-ui-structure', $style_url . 'jquery-ui.structure.min.css' );
			wp_enqueue_style( 'jquery-ui-theme', $style_url . 'jquery-ui.theme.min.css' );
			wp_enqueue_style( 'jquery-timepicker', $style_url . 'jquery.timepicker.min.css' );
		}
	}
}

// save meta data for custom post type

add_action( 'save_post_bktskytlive', 'bktsk_yt_scheduler_save_fields' );

function bktsk_yt_scheduler_save_fields( $post_id ) {
		$bktskytlive_live_box_nonce = isset( $_POST['bktskytlive-live-nonce'] ) ? $_POST['bktskytlive-live-nonce'] : null;
	if ( ! wp_verify_nonce( $bktskytlive_live_box_nonce, 'bktskytlive-live-info-update' ) ) {
		return;
	}

		$wp_timezone = get_option( 'timezone_string' );

		$bktsk_yt_live_type = sanitize_text_field( $_POST['bktsk_yt_live_type'] );
		update_post_meta( $post_id, 'bktsk_yt_live_type', $bktsk_yt_live_type );

		$bktsk_yt_live_url = esc_url( $_POST['bktsk_yt_live_url'] );
	if ( ! empty( $bktsk_yt_live_url ) ) {
		update_post_meta( $post_id, 'bktsk_yt_live_url', $bktsk_yt_live_url );
	} else {
		delete_post_meta( $post_id, 'bktsk_yt_live_url' );
	}

	switch ( $bktsk_yt_live_type ) {
		case 'live_schedule':
		case 'canceled_live_schedule':
			if ( isset( $_POST['bktsk_yt_live_start_date'] ) && isset( $_POST['bktsk_yt_live_start_time'] ) ) {
				$bktsk_yt_live_start_update = new DateTime( sanitize_text_field( $_POST['bktsk_yt_live_start_date'] ) . 'T' . sanitize_text_field( $_POST['bktsk_yt_live_start_time'] ), new DateTimeZone( $wp_timezone ) );
				$bktsk_yt_live_start_update->setTimezone( new DateTimeZone( 'UTC' ) );
				update_post_meta( $post_id, 'bktsk_yt_live_frontpage_start', $bktsk_yt_live_start_update->format( 'Y-m-d H:i:s' ) );
				update_post_meta( $post_id, 'bktsk_yt_live_start', $bktsk_yt_live_start_update->format( DateTime::ISO8601 ) );
			} else {
				delete_post_meta( $post_id, 'bktsk_yt_live_frontpage_start' );
				delete_post_meta( $post_id, 'bktsk_yt_live_start' );
			}

			if ( isset( $_POST['bktsk_yt_live_end_date'] ) && isset( $_POST['bktsk_yt_live_end_time'] ) ) {
				$bktsk_yt_live_end_update = new DateTime( sanitize_text_field( $_POST['bktsk_yt_live_end_date'] ) . 'T' . sanitize_text_field( $_POST['bktsk_yt_live_end_time'] ), new DateTimeZone( $wp_timezone ) );
				update_post_meta( $post_id, 'bktsk_yt_live_frontpage_due', $bktsk_yt_live_end_update->format( 'Y-m-d H:i:s' ) );
				$bktsk_yt_live_end_update->setTimezone( new DateTimeZone( 'UTC' ) );
				update_post_meta( $post_id, 'bktsk_yt_live_end', $bktsk_yt_live_end_update->format( DateTime::ISO8601 ) );
			} else {
				delete_post_meta( $post_id, 'bktsk_yt_live_frontpage_due' );
				delete_post_meta( $post_id, 'bktsk_yt_live_end' );
			}

			delete_post_meta( $post_id, 'bktsk_yt_all_day_live_start' );
			delete_post_meta( $post_id, 'bktsk_yt_all_day_live_end' );
			delete_post_meta( $post_id, 'bktsk_yt_day_off_start' );
			delete_post_meta( $post_id, 'bktsk_yt_day_off_end' );
			break;

		case 'all_day_live_schedule':
		case 'canceled_all_day_live_schedule':
			if ( isset( $_POST['bktsk_yt_all_day_live_start_date'] ) ) {
				$bktsk_yt_all_day_live_start_update = new DateTime( sanitize_text_field( $_POST['bktsk_yt_all_day_live_start_date'] ), new DateTimeZone( $wp_timezone ) );
				update_post_meta( $post_id, 'bktsk_yt_all_day_live_start', $bktsk_yt_all_day_live_start_update->format( 'Y-m-d' ) );
				update_post_meta( $post_id, 'bktsk_yt_live_frontpage_start', $bktsk_yt_all_day_live_start_update->format( 'Y-m-d H:i:s' ) );
			} else {
				delete_post_meta( $post_id, 'bktsk_yt_live_frontpage_start' );
				delete_post_meta( $post_id, 'bktsk_yt_all_day_live_start' );
			}

			if ( isset( $_POST['bktsk_yt_all_day_live_end_date'] ) ) {
				$bktsk_yt_all_day_live_end_update = new DateTime( sanitize_text_field( $_POST['bktsk_yt_all_day_live_end_date'] ), new DateTimeZone( $wp_timezone ) );
				update_post_meta( $post_id, 'bktsk_yt_all_day_live_end', $bktsk_yt_all_day_live_end_update->format( 'Y-m-d' ) );
				update_post_meta( $post_id, 'bktsk_yt_live_frontpage_due', $bktsk_yt_all_day_live_end_update->modify( '+1 day' )->format( 'Y-m-d H:i:s' ) );
			} else { //題名未入力の場合
				delete_post_meta( $post_id, 'bktsk_yt_live_frontpage_due' );
				delete_post_meta( $post_id, 'bktsk_yt_all_day_live_end' );
			}

			delete_post_meta( $post_id, 'bktsk_yt_live_start' );
			delete_post_meta( $post_id, 'bktsk_yt_live_end' );
			delete_post_meta( $post_id, 'bktsk_yt_day_off_start' );
			delete_post_meta( $post_id, 'bktsk_yt_day_off_end' );
			break;

		case 'day_off':
			if ( isset( $_POST['bktsk_yt_day_off_start_date'] ) ) {
				$bktsk_yt_day_off_start_update = new DateTime( sanitize_text_field( $_POST['bktsk_yt_day_off_start_date'] ), new DateTimeZone( $wp_timezone ) );
				update_post_meta( $post_id, 'bktsk_yt_day_off_start', $bktsk_yt_day_off_start_update->format( 'Y-m-d' ) ); //値を保存
				update_post_meta( $post_id, 'bktsk_yt_live_frontpage_start', $bktsk_yt_day_off_start_update->format( 'Y-m-d H:i:s' ) );
			} else {
				delete_post_meta( $post_id, 'bktsk_yt_live_frontpage_start' );
				delete_post_meta( $post_id, 'bktsk_yt_day_off_start' );
			}

			if ( isset( $_POST['bktsk_yt_day_off_end_date'] ) ) {
				$bktsk_yt_day_off_end_update = new DateTime( sanitize_text_field( $_POST['bktsk_yt_day_off_end_date'] ), new DateTimeZone( $wp_timezone ) );
				update_post_meta( $post_id, 'bktsk_yt_day_off_end', $bktsk_yt_day_off_end_update->format( 'Y-m-d' ) ); //値を保存
				update_post_meta( $post_id, 'bktsk_yt_live_frontpage_due', $bktsk_yt_day_off_end_update->modify( '+1 day' )->format( 'Y-m-d H:i:s' ) );
			} else {
				delete_post_meta( $post_id, 'bktsk_yt_live_frontpage_due' );
				delete_post_meta( $post_id, 'bktsk_yt_day_off_end' );
			}

			delete_post_meta( $post_id, 'bktsk_yt_live_start' );
			delete_post_meta( $post_id, 'bktsk_yt_live_end' );
			delete_post_meta( $post_id, 'bktsk_yt_all_day_live_start' );
			delete_post_meta( $post_id, 'bktsk_yt_all_day_live_end' );
			break;
	}
}

// add columns on the edit.php

function bktsk_yt_live_admin_add_columns( $bktsk_yt_live_defaults ) {
	$bktsk_yt_live_defaults['bktsk_yt_live_start'] = __( 'Live Start', 'bktsk-live-scheduler' ); // ID と 表示させるラベル
	$bktsk_yt_live_defaults['bktsk_yt_live_end']   = __( 'Live End', 'bktsk-live-scheduler' );
	$bktsk_yt_live_defaults['bktsk_yt_live_url']   = __( 'Live URL', 'bktsk-live-scheduler' );
	return $bktsk_yt_live_defaults;
}
add_filter( 'manage_bktskytlive_posts_columns', 'bktsk_yt_live_admin_add_columns' );

function bktsk_yt_live_admin_custom_column( $bktsk_yt_live_column, $post_id ) {
	$wp_timezone = get_option( 'timezone_string' );
	switch ( $bktsk_yt_live_column ) {
		case 'bktsk_yt_live_start': // ID
			if ( null != get_post_meta( $post_id, 'bktsk_yt_live_start', true ) || null != get_post_meta( $post_id, 'canceled_bktsk_yt_live_start', true ) ) {
				$bktsk_yt_live_post_meta = get_post_meta( $post_id, 'bktsk_yt_live_start', true );
			} elseif ( null != get_post_meta( $post_id, 'bktsk_yt_all_day_live_start', true ) || null != get_post_meta( $post_id, 'canceled_bktsk_yt_all_day_live_start', true ) ) {
				$bktsk_yt_live_post_meta = get_post_meta( $post_id, 'bktsk_yt_all_day_live_start', true );
			} elseif ( null != get_post_meta( $post_id, 'bktsk_yt_day_off_start', true ) ) {
				$bktsk_yt_live_post_meta = get_post_meta( $post_id, 'bktsk_yt_day_off_start', true );
			}
			break;

		case 'bktsk_yt_live_end':
			if ( null != get_post_meta( $post_id, 'bktsk_yt_live_end', true ) || null != get_post_meta( $post_id, 'canceled_bktsk_yt_live_end', true ) ) {
				$bktsk_yt_live_post_meta = get_post_meta( $post_id, 'bktsk_yt_live_end', true );
			} elseif ( null != get_post_meta( $post_id, 'bktsk_yt_all_day_live_end', true ) || null != get_post_meta( $post_id, 'canceled_bktsk_yt_all_day_live_end', true ) ) {
				$bktsk_yt_live_post_meta = get_post_meta( $post_id, 'bktsk_yt_all_day_live_end', true );
			} elseif ( null != get_post_meta( $post_id, 'bktsk_yt_day_off_end', true ) ) {
				$bktsk_yt_live_post_meta = get_post_meta( $post_id, 'bktsk_yt_day_off_end', true );
			}
			break;

		case 'bktsk_yt_live_url':
			$bktsk_yt_live_url = get_post_meta( $post_id, 'bktsk_yt_live_url', true );
			break;
	}

	if ( isset( $bktsk_yt_live_post_meta ) ) {
		if ( 'live_schedule' === get_post_meta( $post_id, 'bktsk_yt_live_type', true ) ) {
			$bktsk_yt_live_time_format = 'Y-m-d H:i';
		} else {
			$bktsk_yt_live_time_format = 'Y-m-d';
		}
		$bktsk_yt_live_post_meta = new DateTime( $bktsk_yt_live_post_meta, new DateTimeZone( 'UTC' ) );
		$bktsk_yt_live_post_meta->setTimezone( new DateTimeZone( $wp_timezone ) );
		echo $bktsk_yt_live_post_meta->format( $bktsk_yt_live_time_format ) . '<br>(' . $wp_timezone . ')';
	} elseif ( $bktsk_yt_live_url ) {
		echo $bktsk_yt_live_url;
	} else {
		echo 'none';
	}
}
add_action( 'manage_posts_custom_column', 'bktsk_yt_live_admin_custom_column', 10, 2 );

function bktsk_yt_live_admin_post_order( $wp_query ) {
	global $pagenow;
	if ( is_admin() && 'edit.php' == $pagenow && ! isset( $_GET['orderby'] ) && 'bktskytlive' == $wp_query->query_vars['post_type'] ) {
		$wp_query->set( 'orderby', 'meta_value' );
		$wp_query->set( 'order', 'DESC' );
		$wp_query->set( 'meta_key', 'bktsk_yt_live_frontpage_start' );
	}
}
add_filter( 'pre_get_posts', 'bktsk_yt_live_admin_post_order' );


// showing quick edit
function bktsk_yt_live_admin_quickmenu( $column_name, $post_type ) {
	static $print_nonce = true;
	if ( $print_nonce ) {
		$print_nonce = false;
		wp_nonce_field( 'quick_edit_action', $post_type . '_edit_nonce' ); //CSRF対策
	}
	?>
<fieldset class="inline-edit-col-right inline-custom-meta">
<div class="inline-edit-col column-<?php echo $column_name; ?>">
			<label class="inline-edit-group"></p>
			<?php
			switch ( $column_name ) {
				case 'bktsk_yt_live_url':
					?>
						<span class="title"><?php _e( 'Live URL', 'bktsk-live-scheduler' ); ?></span><input name="bktsk_yt_live_url" />
					<?php
					break;
			}
			?>
			</label>
		</div>
</fieldset>
	<?php
}
add_action( 'quick_edit_custom_box', 'bktsk_yt_live_admin_quickmenu', 10, 2 );

function bktsk_yt_live_admin_custom_meta( $post_id ) {
	$slug = 'bktskytlive';
	if ( get_post_type( $post_id ) !== $slug ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$_POST += array( "{$slug}_edit_nonce" => '' );
	if ( ! wp_verify_nonce( $_POST[ "{$slug}_edit_nonce" ], 'quick_edit_action' ) ) {
		return;
	}
	if ( isset( $_REQUEST['bktsk_yt_live_url'] ) && ! empty( $_REQUEST['bktsk_yt_live_url'] ) ) {
		update_post_meta( $post_id, 'bktsk_yt_live_url', esc_url( $_REQUEST['bktsk_yt_live_url'] ) );
	} else {
		delete_post_meta( $post_id, 'bktsk_yt_live_url' );
	}
}
add_action( 'save_post', 'bktsk_yt_live_admin_custom_meta' );

// load show-customfields.js only in edit.js
function bktsk_yt_live_admin_script() {
	wp_enqueue_script( 'bktsk_yt_live_admin_script', plugins_url( '../js/', __FILE__ ) . '/show-customfields.js', array( 'jquery', 'inline-edit-post' ) );
}
add_action( 'admin_enqueue_scripts', 'bktsk_yt_live_admin_script' );
