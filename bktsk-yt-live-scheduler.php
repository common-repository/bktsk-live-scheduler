<?php
/*
Plugin Name: BKTSK Live Scheduler
Plugin URI:
Description: Show Live Schedule in WordPress
Version: 0.4.0
Author: SASAGAWA Kiyoshi
Author URI: https://kent-and-co.com
License: GPL v2 or later
Text Domain: bktsk-live-scheduler
Domain Path: /languages/

Copyright 2019 SASAGAWA Kiyoshi (email : sasagawa@kent-and-co.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function bktsk_yt_scheduler_load_textdomain() {
	load_plugin_textdomain(
		'bktsk-live-scheduler',
		false,
		plugin_basename( dirname( __FILE__ ) ) . '/languages'
	);
}
add_action( 'plugins_loaded', 'bktsk_yt_scheduler_load_textdomain' );

require_once dirname( __FILE__ ) . '/lib/add-post-type.php'; // for Post Type
require_once dirname( __FILE__ ) . '/lib/add-taxonomy.php'; // for custom taxonomy
require_once dirname( __FILE__ ) . '/lib/make-ics.php'; // for ics response
require_once dirname( __FILE__ ) . '/lib/get-post-type.php'; // for post data from the post type
require_once dirname( __FILE__ ) . '/lib/class-bktskytscheduleradminpage.php'; // for Admin Menus
require_once dirname( __FILE__ ) . '/lib/class-bktskytschedulershortcode.php'; // for Shortcode
