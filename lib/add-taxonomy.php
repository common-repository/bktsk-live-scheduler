<?php
/**
 * @package bktsk-yt-live-scheduler
 * @author SASAGAWA Kiyoshi
 * @license GPL-2.0+
 */

// add taxonomy to the custom post type
function bktsk_yt_live_add_taxonomy() {

	$bktsk_yt_live_options       = get_option( 'bktsk_yt_scheduler_options' );
	$bktsk_yt_live_taxonomy_slug = $bktsk_yt_live_options['taxonomy_slug'];

	if ( empty( $bktsk_yt_live_taxonomy_slug ) ) {
		$bktsk_yt_live_taxonomy_slug = 'live_category';
	}

	register_taxonomy(
		'bktsk-yt-live-taxonomy',
		'bktskytlive',
		array(
			'label'             => __( 'Live Categories', 'bktsk-live-scheduler' ),
			'singular_label'    => __( 'Live Category', 'bktsk-live-scheduler' ),
			'labels'            => array(
				'all_items'    => __( 'All live categories', 'bktsk-live-scheduler' ),
				'add_new_item' => __( 'Add new live category', 'bktsk-live-scheduler' ),
			),
			'public'            => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'hierarchical'      => true,
			'rewrite'           => array(
				'slug' => $bktsk_yt_live_taxonomy_slug,
			),
		)
	);
}

add_action( 'init', 'bktsk_yt_live_add_taxonomy' );
