<?php
/**
 * Server-side render callback for the Trivia Challenge block
 *
 * @package TriviaChallenge
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      The block instance.
 * @return string The rendered block HTML.
 */

// Load asset file for dependencies and version.
$asset_file = include TRIVIA_CHALLENGE_BLOCK_PATH . 'build/frontend.asset.php';

// Enqueue the frontend script with proper dependencies.
wp_enqueue_script(
	'trivia-challenge-frontend',
	TRIVIA_CHALLENGE_BLOCK_URL . 'build/frontend.js',
	$asset_file['dependencies'],
	$asset_file['version'],
	true
);

// Pass the settings every block on the page shares to JavaScript.
wp_localize_script(
	'trivia-challenge-frontend',
	'triviaChallengeSettings',
	array(
		'apiUrl'    => rest_url( 'trivia-challenge/v1/' ),
		'nonce'     => wp_create_nonce( 'wp_rest' ),
		'pluginUrl' => TRIVIA_CHALLENGE_BLOCK_URL,
	)
);

// This block's own settings go on its root, so several blocks on one page keep their own.
// WordPress fills unsaved attributes from the registered defaults, which follow the admin
// settings (Trivia_Challenge_Block::apply_admin_defaults()); these fallbacks match them.
$admin_settings = \TriviaChallenge\Trivia_Challenge_Admin::get_settings();
$block_settings = array(
	'defaultCategory'   => isset( $attributes['defaultCategory'] ) ? $attributes['defaultCategory'] : $admin_settings['default_category'],
	'defaultDifficulty' => isset( $attributes['defaultDifficulty'] ) ? $attributes['defaultDifficulty'] : $admin_settings['default_difficulty'],
	'questionsPerQuiz'  => isset( $attributes['questionsPerQuiz'] ) ? $attributes['questionsPerQuiz'] : (int) $admin_settings['questions_per_quiz'],
	'timerDuration'     => isset( $attributes['timerDuration'] ) ? $attributes['timerDuration'] : (int) $admin_settings['timer_duration'],
	'showTimer'         => isset( $attributes['showTimer'] ) ? $attributes['showTimer'] : true,
	'showStreak'        => isset( $attributes['showStreak'] ) ? $attributes['showStreak'] : true,
	'enableSound'       => isset( $attributes['enableSound'] ) ? $attributes['enableSound'] : false,
);

// Get block wrapper attributes.
$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'trivia-challenge-wrapper',
	)
);

// Render the block HTML.
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="trivia-challenge-root" data-settings="<?php echo esc_attr( wp_json_encode( $block_settings ) ); ?>"></div>
</div>
