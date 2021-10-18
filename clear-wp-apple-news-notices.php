<?php
/*
Plugin Name: Clear WordPress Apple News Notices
Description: Delete apple news user meta notices from the db on a cron schedule
Version: 0.01
Author: patmcm
*/

/**
 * When plugin is activated, set event to
 * delete the user meta 'apple_news_notice' twice per day
 */
function activate_plugin_callback_clear_apple_news_notices() {
	if ( ! wp_next_scheduled ( 'clear_apple_news_notices_twicedaily_cron' ) ) {
		wp_schedule_event( time(), 'twicedaily', 'clear_apple_news_notices_twicedaily_cron' );
	}
}
register_activation_hook( __FILE__, 'activate_plugin_callback_clear_apple_news_notices' );

function deactivate_plugin_callback_clear_apple_news_notices() {
	wp_clear_scheduled_hook( 'clear_apple_news_notices_twicedaily_cron' );
}
register_deactivation_hook( __FILE__, 'deactivate_plugin_callback_clear_apple_news_notices' );

/**
 * Gets the apple_news_notice meta value
 * and deletes any notices that aren't ones meant to be
 * seen and dismissed by a user in wp-admin
 * @uses get_users
 * @uses get_user_meta
 * @uses update_user_meta
 * @see https://github.com/alleyinteractive/apple-news/blob/develop/admin/class-admin-apple-notice.php
 */
function clear_apple_news_notices_delete_user_meta() {
	$users = get_users( array(
		'meta_key'     => 'apple_news_notice',
		'fields'       => 'ID',
	));

	if ( empty( $users ) ) {
		return null;
	}

	foreach ( $users as $user_id ) {
		$apple_notice_meta = get_user_meta( $user_id, 'apple_news_notice', true );

		if ( empty( $apple_notice_meta ) || $apple_notice_meta === '' ) {
			continue;
		}

		// When there are 100 or more notices, reset without looping as the array has become too large
		if ( count( $apple_notice_meta ) >= 100 ) {
			update_user_meta( $user_id, 'apple_news_notice', [] );
			continue;
		}

		// Keep notices that are dismissable and haven't been dismissed. All others will be deleted
		$updated_notices = array();
		foreach ( $apple_notice_meta as $notice ) {
			if ( ! empty( $notice['dismissible'] ) && ( isset( $notice['dismissed'] ) && $notice['dismissed'] === false ) ) {
				$updated_notices[] = $notice;
			}
		}
		update_user_meta( $user_id, 'apple_news_notice', $updated_notices );
	}

	return null;
}
add_action( 'clear_apple_news_notices_twicedaily_cron', 'clear_apple_news_notices_delete_user_meta' );