<?php
/**
 * Admin Settings Class
 *
 * @package TriviaChallenge
 */

namespace TriviaChallenge;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin settings class
 */
class Trivia_Challenge_Admin {
	/**
	 * Settings option name
	 *
	 * @var string
	 */
	const OPTION_NAME = 'trivia_challenge_settings';

	/**
	 * Initialize admin functionality
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Add admin menu page
	 */
	public static function add_admin_menu() {
		add_options_page(
			__( 'Trivia Challenge Settings', 'trivia-challenge-block' ),
			__( 'Trivia Challenge', 'trivia-challenge-block' ),
			'manage_options',
			'trivia-challenge-settings',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	/**
	 * Register plugin settings
	 */
	public static function register_settings() {
		register_setting(
			'trivia_challenge_settings_group',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_settings' ),
				'default'           => self::get_default_settings(),
			)
		);

		// General Settings Section.
		add_settings_section(
			'trivia_challenge_general',
			__( 'General Settings', 'trivia-challenge-block' ),
			array( __CLASS__, 'render_general_section' ),
			'trivia-challenge-settings'
		);

		add_settings_field(
			'default_category',
			__( 'Default Category', 'trivia-challenge-block' ),
			array( __CLASS__, 'render_category_field' ),
			'trivia-challenge-settings',
			'trivia_challenge_general'
		);

		add_settings_field(
			'default_difficulty',
			__( 'Default Difficulty', 'trivia-challenge-block' ),
			array( __CLASS__, 'render_difficulty_field' ),
			'trivia-challenge-settings',
			'trivia_challenge_general'
		);

		add_settings_field(
			'questions_per_quiz',
			__( 'Questions Per Quiz', 'trivia-challenge-block' ),
			array( __CLASS__, 'render_questions_field' ),
			'trivia-challenge-settings',
			'trivia_challenge_general'
		);

		add_settings_field(
			'timer_duration',
			__( 'Timer Duration (seconds)', 'trivia-challenge-block' ),
			array( __CLASS__, 'render_timer_field' ),
			'trivia-challenge-settings',
			'trivia_challenge_general'
		);

		// Cache Settings Section.
		add_settings_section(
			'trivia_challenge_cache',
			__( 'Cache Settings', 'trivia-challenge-block' ),
			array( __CLASS__, 'render_cache_section' ),
			'trivia-challenge-settings'
		);

		add_settings_field(
			'cache_duration',
			__( 'Cache Duration (hours)', 'trivia-challenge-block' ),
			array( __CLASS__, 'render_cache_duration_field' ),
			'trivia-challenge-settings',
			'trivia_challenge_cache'
		);
	}

	/**
	 * Get default settings
	 *
	 * @return array
	 */
	public static function get_default_settings(): array {
		return array(
			'default_category'   => 'mixed',
			'default_difficulty' => 'medium',
			'questions_per_quiz' => 10,
			'timer_duration'     => 20,
			'cache_duration'     => 1,
			'enable_stats'       => false,
		);
	}

	/**
	 * Get current settings
	 *
	 * @return array
	 */
	public static function get_settings(): array {
		$settings = get_option( self::OPTION_NAME, array() );
		return wp_parse_args( $settings, self::get_default_settings() );
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $input Settings input.
	 * @return array
	 */
	public static function sanitize_settings( $input ): array {
		$sanitized = array();

		if ( isset( $input['default_category'] ) ) {
			$sanitized['default_category'] = sanitize_text_field( $input['default_category'] );
		}

		if ( isset( $input['default_difficulty'] ) ) {
			$sanitized['default_difficulty'] = sanitize_text_field( $input['default_difficulty'] );
		}

		if ( isset( $input['questions_per_quiz'] ) ) {
			$sanitized['questions_per_quiz'] = absint( $input['questions_per_quiz'] );
			if ( $sanitized['questions_per_quiz'] < 1 ) {
				$sanitized['questions_per_quiz'] = 10;
			}
			if ( $sanitized['questions_per_quiz'] > 50 ) {
				$sanitized['questions_per_quiz'] = 50;
			}
		}

		if ( isset( $input['timer_duration'] ) ) {
			$sanitized['timer_duration'] = absint( $input['timer_duration'] );
			if ( $sanitized['timer_duration'] < 5 ) {
				$sanitized['timer_duration'] = 5;
			}
			if ( $sanitized['timer_duration'] > 120 ) {
				$sanitized['timer_duration'] = 120;
			}
		}

		if ( isset( $input['cache_duration'] ) ) {
			$sanitized['cache_duration'] = absint( $input['cache_duration'] );
			if ( $sanitized['cache_duration'] < 1 ) {
				$sanitized['cache_duration'] = 1;
			}
		}

		$sanitized['enable_stats'] = ! empty( $input['enable_stats'] );

		return $sanitized;
	}

	/**
	 * Render settings page
	 */
	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Handle cache clear action.
		if ( isset( $_POST['clear_cache'] ) && check_admin_referer( 'trivia_challenge_clear_cache' ) ) {
			Trivia_Challenge_API::clear_cache();
			add_settings_error(
				'trivia_challenge_messages',
				'trivia_challenge_cache_cleared',
				__( 'Cache cleared successfully!', 'trivia-challenge-block' ),
				'success'
			);
		}

		settings_errors( 'trivia_challenge_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'trivia_challenge_settings_group' );
				do_settings_sections( 'trivia-challenge-settings' );
				submit_button( __( 'Save Settings', 'trivia-challenge-block' ) );
				?>
			</form>

			<hr>

			<h2><?php esc_html_e( 'Cache Management', 'trivia-challenge-block' ); ?></h2>
			<p><?php esc_html_e( 'Clear the question cache to fetch fresh questions from the API.', 'trivia-challenge-block' ); ?></p>
			<form method="post">
				<?php wp_nonce_field( 'trivia_challenge_clear_cache' ); ?>
				<input type="submit" name="clear_cache" class="button button-secondary" value="<?php esc_attr_e( 'Clear Cache', 'trivia-challenge-block' ); ?>">
			</form>
		</div>
		<?php
	}

	/**
	 * Render general section
	 */
	public static function render_general_section() {
		echo '<p>' . esc_html__( 'Configure default settings for the trivia quiz.', 'trivia-challenge-block' ) . '</p>';
	}

	/**
	 * Render cache section
	 */
	public static function render_cache_section() {
		echo '<p>' . esc_html__( 'Configure how long API responses are cached.', 'trivia-challenge-block' ) . '</p>';
	}

	/**
	 * Render category field
	 */
	public static function render_category_field() {
		$settings = self::get_settings();
		$value    = $settings['default_category'];
		?>
		<select name="<?php echo esc_attr( self::OPTION_NAME ); ?>[default_category]" id="default_category">
			<option value="mixed" <?php selected( $value, 'mixed' ); ?>><?php esc_html_e( 'Mixed - All Topics', 'trivia-challenge-block' ); ?></option>
			<option value="general" <?php selected( $value, 'general' ); ?>><?php esc_html_e( 'General Knowledge', 'trivia-challenge-block' ); ?></option>
			<option value="science" <?php selected( $value, 'science' ); ?>><?php esc_html_e( 'Science & Nature', 'trivia-challenge-block' ); ?></option>
			<option value="history" <?php selected( $value, 'history' ); ?>><?php esc_html_e( 'History', 'trivia-challenge-block' ); ?></option>
			<option value="geography" <?php selected( $value, 'geography' ); ?>><?php esc_html_e( 'Geography', 'trivia-challenge-block' ); ?></option>
			<option value="entertainment" <?php selected( $value, 'entertainment' ); ?>><?php esc_html_e( 'Entertainment', 'trivia-challenge-block' ); ?></option>
			<option value="sports" <?php selected( $value, 'sports' ); ?>><?php esc_html_e( 'Sports', 'trivia-challenge-block' ); ?></option>
			<option value="computers" <?php selected( $value, 'computers' ); ?>><?php esc_html_e( 'Computers', 'trivia-challenge-block' ); ?></option>
			<option value="mathematics" <?php selected( $value, 'mathematics' ); ?>><?php esc_html_e( 'Mathematics', 'trivia-challenge-block' ); ?></option>
			<option value="mythology" <?php selected( $value, 'mythology' ); ?>><?php esc_html_e( 'Mythology', 'trivia-challenge-block' ); ?></option>
			<option value="animals" <?php selected( $value, 'animals' ); ?>><?php esc_html_e( 'Animals', 'trivia-challenge-block' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Render difficulty field
	 */
	public static function render_difficulty_field() {
		$settings = self::get_settings();
		$value    = $settings['default_difficulty'];
		?>
		<select name="<?php echo esc_attr( self::OPTION_NAME ); ?>[default_difficulty]" id="default_difficulty">
			<option value="easy" <?php selected( $value, 'easy' ); ?>><?php esc_html_e( 'Easy', 'trivia-challenge-block' ); ?></option>
			<option value="medium" <?php selected( $value, 'medium' ); ?>><?php esc_html_e( 'Medium', 'trivia-challenge-block' ); ?></option>
			<option value="hard" <?php selected( $value, 'hard' ); ?>><?php esc_html_e( 'Hard', 'trivia-challenge-block' ); ?></option>
			<option value="any" <?php selected( $value, 'any' ); ?>><?php esc_html_e( 'Any Difficulty', 'trivia-challenge-block' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Render questions field
	 */
	public static function render_questions_field() {
		$settings = self::get_settings();
		$value    = $settings['questions_per_quiz'];
		?>
		<input type="number" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[questions_per_quiz]" id="questions_per_quiz" value="<?php echo esc_attr( $value ); ?>" min="1" max="50">
		<p class="description"><?php esc_html_e( 'Number of questions per quiz (1-50)', 'trivia-challenge-block' ); ?></p>
		<?php
	}

	/**
	 * Render timer field
	 */
	public static function render_timer_field() {
		$settings = self::get_settings();
		$value    = $settings['timer_duration'];
		?>
		<input type="number" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[timer_duration]" id="timer_duration" value="<?php echo esc_attr( $value ); ?>" min="5" max="120">
		<p class="description"><?php esc_html_e( 'Seconds per question (5-120)', 'trivia-challenge-block' ); ?></p>
		<?php
	}

	/**
	 * Render cache duration field
	 */
	public static function render_cache_duration_field() {
		$settings = self::get_settings();
		$value    = $settings['cache_duration'];
		?>
		<input type="number" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[cache_duration]" id="cache_duration" value="<?php echo esc_attr( $value ); ?>" min="1">
		<p class="description"><?php esc_html_e( 'How long to cache API responses (in hours)', 'trivia-challenge-block' ); ?></p>
		<?php
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @param string $hook Current admin page hook.
	 */
	public static function enqueue_admin_scripts( $hook ) {
		if ( 'settings_page_trivia-challenge-settings' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'trivia-challenge-admin',
			TRIVIA_CHALLENGE_BLOCK_URL . 'assets/admin-style.css',
			array(),
			TRIVIA_CHALLENGE_BLOCK_VERSION
		);
	}
}
