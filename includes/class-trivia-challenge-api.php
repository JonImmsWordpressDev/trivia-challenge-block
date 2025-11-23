<?php
/**
 * API Handler Class
 *
 * Handles API requests to Open Trivia Database with caching
 *
 * @package TriviaChallenge
 */

namespace TriviaChallenge;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * API Handler class
 */
class Trivia_Challenge_API {
	/**
	 * API Base URL
	 *
	 * @var string
	 */
	const API_BASE_URL = 'https://opentdb.com/api.php';

	/**
	 * Category mapping
	 *
	 * @var array
	 */
	const CATEGORY_MAP = array(
		'mixed'        => null,
		'science'      => 17,
		'history'      => 23,
		'geography'    => 22,
		'entertainment' => 11,
		'sports'       => 21,
		'general'      => 9,
		'computers'    => 18,
		'mathematics'  => 19,
		'mythology'    => 20,
		'animals'      => 27,
	);

	/**
	 * Initialize the API handler
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_rest_routes' ) );
	}

	/**
	 * Register REST API routes
	 */
	public static function register_rest_routes() {
		register_rest_route(
			'trivia-challenge/v1',
			'/questions',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_questions' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'category'   => array(
						'required'          => false,
						'default'           => 'mixed',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => array( __CLASS__, 'validate_category' ),
					),
					'difficulty' => array(
						'required'          => false,
						'default'           => 'medium',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => array( __CLASS__, 'validate_difficulty' ),
					),
					'amount'     => array(
						'required'          => false,
						'default'           => 10,
						'sanitize_callback' => 'absint',
						'validate_callback' => array( __CLASS__, 'validate_amount' ),
					),
				),
			)
		);

		register_rest_route(
			'trivia-challenge/v1',
			'/clear-cache',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'clear_cache' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	/**
	 * Validate category parameter
	 *
	 * @param string $param Category parameter.
	 * @return bool
	 */
	public static function validate_category( $param ): bool {
		return array_key_exists( $param, self::CATEGORY_MAP );
	}

	/**
	 * Validate difficulty parameter
	 *
	 * @param string $param Difficulty parameter.
	 * @return bool
	 */
	public static function validate_difficulty( $param ): bool {
		return in_array( $param, array( 'easy', 'medium', 'hard', 'any' ), true );
	}

	/**
	 * Validate amount parameter
	 *
	 * @param int $param Amount parameter.
	 * @return bool
	 */
	public static function validate_amount( $param ): bool {
		return $param >= 1 && $param <= 50;
	}

	/**
	 * Get questions from API or cache
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function get_questions( $request ) {
		$category   = $request->get_param( 'category' );
		$difficulty = $request->get_param( 'difficulty' );
		$amount     = $request->get_param( 'amount' );

		// Create cache key.
		$cache_key = 'trivia_challenge_' . md5( $category . $difficulty . $amount );

		// Try to get from cache.
		$cached_questions = get_transient( $cache_key );
		if ( false !== $cached_questions ) {
			return new \WP_REST_Response(
				array(
					'success' => true,
					'data'    => $cached_questions,
					'cached'  => true,
				),
				200
			);
		}

		// Fetch from API.
		$questions = self::fetch_from_api( $category, $difficulty, $amount );

		if ( is_wp_error( $questions ) ) {
			return $questions;
		}

		// Cache the results.
		$cache_duration = apply_filters( 'trivia_challenge_api_cache_duration', HOUR_IN_SECONDS );
		set_transient( $cache_key, $questions, $cache_duration );

		return new \WP_REST_Response(
			array(
				'success' => true,
				'data'    => $questions,
				'cached'  => false,
			),
			200
		);
	}

	/**
	 * Fetch questions from external API
	 *
	 * @param string $category   Category.
	 * @param string $difficulty Difficulty.
	 * @param int    $amount     Number of questions.
	 * @return array|WP_Error
	 */
	private static function fetch_from_api( $category, $difficulty, $amount ) {
		// Build API URL.
		$fetch_amount = 'hard' === $difficulty ? 15 : $amount;
		$url          = self::API_BASE_URL . '?amount=' . $fetch_amount . '&type=multiple';

		if ( self::CATEGORY_MAP[ $category ] ) {
			$url .= '&category=' . self::CATEGORY_MAP[ $category ];
		}

		if ( 'any' !== $difficulty ) {
			$url .= '&difficulty=' . $difficulty;
		}

		// Make API request.
		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 15,
				'headers' => array(
					'Accept' => 'application/json',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return new \WP_Error(
				'api_error',
				__( 'Failed to fetch questions from API', 'trivia-challenge-block' ),
				array( 'status' => 500 )
			);
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! $data || 0 !== $data['response_code'] || empty( $data['results'] ) ) {
			return new \WP_Error(
				'api_error',
				__( 'API returned no questions', 'trivia-challenge-block' ),
				array( 'status' => 500 )
			);
		}

		// Process and format questions.
		$questions = array();
		foreach ( $data['results'] as $item ) {
			$all_answers = array_merge( $item['incorrect_answers'], array( $item['correct_answer'] ) );
			shuffle( $all_answers );
			$correct_index = array_search( $item['correct_answer'], $all_answers, true );

			$questions[] = array(
				'q'          => html_entity_decode( $item['question'], ENT_QUOTES, 'UTF-8' ),
				'a'          => array_map(
					function ( $ans ) {
						return html_entity_decode( $ans, ENT_QUOTES, 'UTF-8' );
					},
					$all_answers
				),
				'correct'    => $correct_index,
				'difficulty' => $item['difficulty'],
			);
		}

		// Filter long questions for hard mode.
		if ( 'hard' === $difficulty ) {
			$long_questions = array_filter(
				$questions,
				function ( $q ) {
					return strlen( $q['q'] ) > 50;
				}
			);

			if ( count( $long_questions ) >= $amount ) {
				$questions = array_slice( $long_questions, 0, $amount );
			} else {
				$questions = array_slice( $questions, 0, $amount );
			}
		} else {
			$questions = array_slice( $questions, 0, $amount );
		}

		return $questions;
	}

	/**
	 * Clear API cache
	 *
	 * @return WP_REST_Response
	 */
	public static function clear_cache() {
		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
				$wpdb->esc_like( '_transient_trivia_challenge_' ) . '%',
				$wpdb->esc_like( '_transient_timeout_trivia_challenge_' ) . '%'
			)
		);

		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Cache cleared successfully', 'trivia-challenge-block' ),
			),
			200
		);
	}
}
