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
	public static function get_instance(): ?Trivia_Challenge_Block {
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
		add_filter( 'block_type_metadata', array( $this, 'apply_admin_defaults' ) );
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
	 * Use the Settings > Trivia Challenge values as the block's attribute defaults
	 *
	 * The registered defaults are what the editor gives a new block and what
	 * render.php gets for any attribute a block has not saved, so both follow
	 * the admin settings. Attributes a block has saved are left alone.
	 *
	 * @param array $metadata Block metadata from block.json.
	 * @return array
	 */
	public function apply_admin_defaults( $metadata ) {
		if ( ! isset( $metadata['name'] ) || 'trivia-challenge/quiz-block' !== $metadata['name'] ) {
			return $metadata;
		}

		$settings = Trivia_Challenge_Admin::get_settings();
		$defaults = array();

		if ( Trivia_Challenge_API::validate_category( $settings['default_category'] ) ) {
			$defaults['defaultCategory'] = $settings['default_category'];
		}
		if ( Trivia_Challenge_API::validate_difficulty( $settings['default_difficulty'] ) ) {
			$defaults['defaultDifficulty'] = $settings['default_difficulty'];
		}
		if ( absint( $settings['questions_per_quiz'] ) > 0 ) {
			$defaults['questionsPerQuiz'] = absint( $settings['questions_per_quiz'] );
		}
		if ( absint( $settings['timer_duration'] ) > 0 ) {
			$defaults['timerDuration'] = absint( $settings['timer_duration'] );
		}

		foreach ( $defaults as $attribute => $value ) {
			if ( isset( $metadata['attributes'][ $attribute ] ) ) {
				$metadata['attributes'][ $attribute ]['default'] = $value;
			}
		}

		return $metadata;
	}

	/**
	 * Register custom block category
	 *
	 * @param array                   $categories Array of block categories.
	 * @param WP_Block_Editor_Context $context    Block editor context.
	 * @return array Modified categories array.
	 */
	public function register_block_category( $categories, $context ): array {
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
