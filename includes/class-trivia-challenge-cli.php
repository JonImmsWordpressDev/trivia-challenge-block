<?php
/**
 * WP-CLI Commands
 *
 * @package TriviaChallenge
 */

namespace TriviaChallenge;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP-CLI command class
 */
class Trivia_Challenge_CLI {
	/**
	 * Clear the trivia question cache
	 *
	 * ## EXAMPLES
	 *
	 *     wp trivia-challenge clear-cache
	 *
	 * @when after_wp_load
	 */
	public function clear_cache( $args, $assoc_args ) {
		\WP_CLI::log( 'Clearing trivia question cache...' );

		$result = Trivia_Challenge_API::clear_cache();

		if ( $result ) {
			\WP_CLI::success( 'Cache cleared successfully!' );
		} else {
			\WP_CLI::error( 'Failed to clear cache.' );
		}
	}

	/**
	 * Test the Open Trivia Database API connection
	 *
	 * ## OPTIONS
	 *
	 * [--category=<category>]
	 * : Category to test (default: mixed)
	 *
	 * [--difficulty=<difficulty>]
	 * : Difficulty to test (default: medium)
	 *
	 * ## EXAMPLES
	 *
	 *     wp trivia-challenge test-api
	 *     wp trivia-challenge test-api --category=science --difficulty=hard
	 *
	 * @when after_wp_load
	 */
	public function test_api( $args, $assoc_args ) {
		$category   = isset( $assoc_args['category'] ) ? $assoc_args['category'] : 'mixed';
		$difficulty = isset( $assoc_args['difficulty'] ) ? $assoc_args['difficulty'] : 'medium';

		\WP_CLI::log( sprintf( 'Testing API with category: %s, difficulty: %s', $category, $difficulty ) );

		$url      = 'https://opentdb.com/api.php?amount=1&type=multiple';
		$response = wp_remote_get( $url, array( 'timeout' => 10 ) );

		if ( is_wp_error( $response ) ) {
			\WP_CLI::error( 'API connection failed: ' . $response->get_error_message() );
			return;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( 200 !== $code ) {
			\WP_CLI::error( sprintf( 'API returned status code: %d', $code ) );
			return;
		}

		if ( ! $data || 0 !== $data['response_code'] ) {
			\WP_CLI::error( 'API returned invalid response' );
			return;
		}

		\WP_CLI::success( 'API connection successful!' );
		\WP_CLI::log( sprintf( 'Questions available: %d', count( $data['results'] ) ) );
	}

	/**
	 * Display plugin usage statistics
	 *
	 * ## EXAMPLES
	 *
	 *     wp trivia-challenge stats
	 *
	 * @when after_wp_load
	 */
	public function stats( $args, $assoc_args ) {
		global $wpdb;

		\WP_CLI::log( 'Fetching Trivia Challenge statistics...' );

		// Count cached items.
		$cache_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( '_transient_trivia_challenge_' ) . '%'
			)
		);

		// Get settings.
		$settings = Trivia_Challenge_Admin::get_settings();

		// Find posts with the block.
		$posts_with_block = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_content LIKE %s AND post_status = 'publish'",
				'%' . $wpdb->esc_like( 'wp:trivia-challenge/quiz-block' ) . '%'
			)
		);

		// Display stats.
		$stats = array(
			array(
				'Metric'      => 'Cached Questions',
				'Value'       => $cache_count,
				'Description' => 'Number of cached API responses',
			),
			array(
				'Metric'      => 'Published Quizzes',
				'Value'       => $posts_with_block,
				'Description' => 'Posts/pages with trivia block',
			),
			array(
				'Metric'      => 'Default Category',
				'Value'       => $settings['default_category'],
				'Description' => 'Default quiz category',
			),
			array(
				'Metric'      => 'Default Difficulty',
				'Value'       => $settings['default_difficulty'],
				'Description' => 'Default quiz difficulty',
			),
			array(
				'Metric'      => 'Questions Per Quiz',
				'Value'       => $settings['questions_per_quiz'],
				'Description' => 'Default questions per quiz',
			),
			array(
				'Metric'      => 'Cache Duration',
				'Value'       => $settings['cache_duration'] . ' hours',
				'Description' => 'How long responses are cached',
			),
		);

		\WP_CLI\Utils\format_items( 'table', $stats, array( 'Metric', 'Value', 'Description' ) );
	}

	/**
	 * Verify plugin installation and requirements
	 *
	 * ## EXAMPLES
	 *
	 *     wp trivia-challenge verify
	 *
	 * @when after_wp_load
	 */
	public function verify( $args, $assoc_args ) {
		\WP_CLI::log( 'Verifying Trivia Challenge installation...' );

		$checks = array();

		// Check PHP version.
		$php_version = phpversion();
		$checks[]    = array(
			'Check'  => 'PHP Version',
			'Status' => version_compare( $php_version, '7.4', '>=' ) ? 'Pass' : 'Fail',
			'Value'  => $php_version,
		);

		// Check WordPress version.
		global $wp_version;
		$checks[] = array(
			'Check'  => 'WordPress Version',
			'Status' => version_compare( $wp_version, '6.0', '>=' ) ? 'Pass' : 'Fail',
			'Value'  => $wp_version,
		);

		// Check if build directory exists.
		$build_exists = file_exists( TRIVIA_CHALLENGE_BLOCK_PATH . 'build/index.js' );
		$checks[]     = array(
			'Check'  => 'Build Files',
			'Status' => $build_exists ? 'Pass' : 'Fail',
			'Value'  => $build_exists ? 'Present' : 'Missing',
		);

		// Check API connectivity.
		$api_response = wp_remote_get( 'https://opentdb.com/api.php?amount=1', array( 'timeout' => 5 ) );
		$api_working  = ! is_wp_error( $api_response ) && 200 === wp_remote_retrieve_response_code( $api_response );
		$checks[]     = array(
			'Check'  => 'API Connection',
			'Status' => $api_working ? 'Pass' : 'Fail',
			'Value'  => $api_working ? 'Connected' : 'Failed',
		);

		// Check write permissions.
		$uploads_dir  = wp_upload_dir();
		$is_writable  = wp_is_writable( $uploads_dir['basedir'] );
		$checks[]     = array(
			'Check'  => 'Upload Directory',
			'Status' => $is_writable ? 'Pass' : 'Warning',
			'Value'  => $is_writable ? 'Writable' : 'Not Writable',
		);

		\WP_CLI\Utils\format_items( 'table', $checks, array( 'Check', 'Status', 'Value' ) );

		$failed = array_filter(
			$checks,
			function ( $check ) {
				return 'Fail' === $check['Status'];
			}
		);

		if ( empty( $failed ) ) {
			\WP_CLI::success( 'All checks passed!' );
		} else {
			\WP_CLI::warning( sprintf( '%d check(s) failed.', count( $failed ) ) );
		}
	}
}
