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

// Pass settings to JavaScript.
wp_localize_script(
	'trivia-challenge-frontend',
	'triviaChallengeSettings',
	array(
		'apiUrl'            => rest_url( 'trivia-challenge/v1/' ),
		'nonce'             => wp_create_nonce( 'wp_rest' ),
		'defaultCategory'   => isset( $attributes['defaultCategory'] ) ? $attributes['defaultCategory'] : 'mixed',
		'defaultDifficulty' => isset( $attributes['defaultDifficulty'] ) ? $attributes['defaultDifficulty'] : 'medium',
		'questionsPerQuiz'  => isset( $attributes['questionsPerQuiz'] ) ? $attributes['questionsPerQuiz'] : 10,
		'timerDuration'     => isset( $attributes['timerDuration'] ) ? $attributes['timerDuration'] : 20,
		'showTimer'         => isset( $attributes['showTimer'] ) ? $attributes['showTimer'] : true,
		'showStreak'        => isset( $attributes['showStreak'] ) ? $attributes['showStreak'] : true,
		'enableSound'       => isset( $attributes['enableSound'] ) ? $attributes['enableSound'] : false,
	)
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
	<div id="trivia-challenge-root"></div>
</div>
