<?php
/**
 * Plugin Name: Trivia Challenge Block
 * Plugin URI: https://wordpress.org/plugins/trivia-challenge-block/
 * Description: A custom Gutenberg block for an interactive general knowledge trivia quiz with 11 categories, multiple difficulty levels, and real-time scoring.
 * Version: 2.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Tested up to: 6.7
 * Author: Jon Imms
 * Author URI: https://jonimms.com
 * License: GPL-2.0-or-later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: trivia-challenge-block
 * Domain Path: /languages
 * Update URI: https://wordpress.org/plugins/trivia-challenge-block/
 *
 * @package TriviaChallenge
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'TRIVIA_CHALLENGE_BLOCK_VERSION', '2.0.0' );
define( 'TRIVIA_CHALLENGE_BLOCK_PATH', plugin_dir_path( __FILE__ ) );
define( 'TRIVIA_CHALLENGE_BLOCK_URL', plugin_dir_url( __FILE__ ) );
define( 'TRIVIA_CHALLENGE_BLOCK_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Load plugin classes
 */
require_once TRIVIA_CHALLENGE_BLOCK_PATH . 'includes/class-trivia-challenge-block.php';
require_once TRIVIA_CHALLENGE_BLOCK_PATH . 'includes/class-trivia-challenge-api.php';
require_once TRIVIA_CHALLENGE_BLOCK_PATH . 'includes/class-trivia-challenge-admin.php';

/**
 * Initialize the plugin
 */
function trivia_challenge_init() {
	// Initialize main plugin class.
	\TriviaChallenge\Trivia_Challenge_Block::get_instance();

	// Initialize API handler.
	\TriviaChallenge\Trivia_Challenge_API::init();

	// Initialize admin functionality.
	if ( is_admin() ) {
		\TriviaChallenge\Trivia_Challenge_Admin::init();
	}

	// Register WP-CLI commands if available.
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		require_once TRIVIA_CHALLENGE_BLOCK_PATH . 'includes/class-trivia-challenge-cli.php';
		WP_CLI::add_command( 'trivia-challenge', '\TriviaChallenge\Trivia_Challenge_CLI' );
	}
}
add_action( 'plugins_loaded', 'trivia_challenge_init' );

/**
 * Activation hook
 */
function trivia_challenge_activate() {
	// Set default options.
	if ( ! get_option( 'trivia_challenge_settings' ) ) {
		update_option( 'trivia_challenge_settings', \TriviaChallenge\Trivia_Challenge_Admin::get_default_settings() );
	}

	// Store plugin version.
	update_option( 'trivia_challenge_version', TRIVIA_CHALLENGE_BLOCK_VERSION );

	// Flush rewrite rules.
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'trivia_challenge_activate' );

/**
 * Deactivation hook
 */
function trivia_challenge_deactivate() {
	// Clear scheduled events.
	wp_clear_scheduled_hook( 'trivia_challenge_clear_old_cache' );

	// Flush rewrite rules.
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'trivia_challenge_deactivate' );
