<?php
/**
 * Uninstall script for Trivia Challenge Block
 *
 * This file is executed when the plugin is deleted via the WordPress admin.
 * It cleans up all data created by the plugin.
 *
 * @package TriviaChallenge
 */

// Exit if accessed directly or not uninstalling.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Delete all plugin options from the database
 */
function trivia_challenge_delete_options() {
	// Delete plugin settings.
	delete_option( 'trivia_challenge_settings' );
	delete_option( 'trivia_challenge_version' );
	delete_option( 'trivia_challenge_api_key' );

	// For multisite installations.
	delete_site_option( 'trivia_challenge_settings' );
	delete_site_option( 'trivia_challenge_version' );
}

/**
 * Delete all plugin transients from the database
 */
function trivia_challenge_delete_transients() {
	global $wpdb;

	// Delete all transients with our prefix.
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
			$wpdb->esc_like( '_transient_trivia_challenge_' ) . '%',
			$wpdb->esc_like( '_transient_timeout_trivia_challenge_' ) . '%'
		)
	);

	// For multisite.
	if ( is_multisite() ) {
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s OR meta_key LIKE %s",
				$wpdb->esc_like( '_site_transient_trivia_challenge_' ) . '%',
				$wpdb->esc_like( '_site_transient_timeout_trivia_challenge_' ) . '%'
			)
		);
	}
}

/**
 * Delete all plugin user meta
 */
function trivia_challenge_delete_user_meta() {
	global $wpdb;

	// Delete user meta (if we add any in the future).
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s",
			$wpdb->esc_like( 'trivia_challenge_' ) . '%'
		)
	);
}

/**
 * Delete any custom database tables (if created in future versions)
 */
function trivia_challenge_delete_custom_tables() {
	global $wpdb;

	// Example for future leaderboard table.
	$table_name = $wpdb->prefix . 'trivia_challenge_scores';
	$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	// Example for future custom questions table.
	$table_name = $wpdb->prefix . 'trivia_challenge_questions';
	$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/**
 * Clear any scheduled cron jobs
 */
function trivia_challenge_clear_scheduled_events() {
	wp_clear_scheduled_hook( 'trivia_challenge_clear_old_cache' );
	wp_clear_scheduled_hook( 'trivia_challenge_update_questions' );
}

/**
 * Execute the uninstall cleanup
 */
trivia_challenge_delete_options();
trivia_challenge_delete_transients();
trivia_challenge_delete_user_meta();
trivia_challenge_delete_custom_tables();
trivia_challenge_clear_scheduled_events();

// Clear any cached data.
wp_cache_flush();
