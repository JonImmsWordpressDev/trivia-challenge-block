<?php
/**
 * Main Plugin Class
 *
 * @package TriviaChallenge
 */

namespace TriviaChallenge;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class
 */
class Trivia_Challenge_Block {
	/**
	 * Plugin instance
	 *
	 * @var Trivia_Challenge_Block
	 */
	private static $instance = null;

	/**
	 * Get plugin instance
	 *
	 * @return Trivia_Challenge_Block
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize WordPress hooks
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_filter( 'block_categories_all', array( $this, 'register_block_category' ), 10, 2 );
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_block_assets' ) );
	}

	/**
	 * Load plugin textdomain for translations
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'trivia-challenge-block',
			false,
			dirname( TRIVIA_CHALLENGE_BLOCK_BASENAME ) . '/languages'
		);
	}

	/**
	 * Register the block using block.json
	 */
	public function register_block() {
		// Register the block from block.json.
		register_block_type( TRIVIA_CHALLENGE_BLOCK_PATH . 'build' );
	}

	/**
	 * Register custom block category
	 *
	 * @param array                   $categories Array of block categories.
	 * @param WP_Block_Editor_Context $context    Block editor context.
	 * @return array Modified categories array.
	 */
	public function register_block_category( $categories, $context ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'trivia-challenge',
					'title' => __( 'Trivia Challenge', 'trivia-challenge-block' ),
					'icon'  => 'games',
				),
			)
		);
	}

	/**
	 * Enqueue block assets for both editor and frontend
	 */
	public function enqueue_block_assets() {
		// Assets are automatically enqueued by block.json.
		// This method is here for any additional assets if needed.
	}
}
