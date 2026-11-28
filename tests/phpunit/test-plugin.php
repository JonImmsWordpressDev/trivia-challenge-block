<?php
/**
 * Class Plugin_Test
 *
 * @package TriviaChallenge
 */

/**
 * Sample test case.
 */
class Plugin_Test extends WP_UnitTestCase {

	/**
	 * Test plugin is loaded
	 */
	public function test_plugin_loaded() {
		$this->assertTrue( defined( 'TRIVIA_CHALLENGE_BLOCK_VERSION' ) );
		$this->assertEquals( '2.0.0', TRIVIA_CHALLENGE_BLOCK_VERSION );
	}

	/**
	 * Test block is registered
	 */
	public function test_block_registered() {
		$this->assertTrue( WP_Block_Type_Registry::get_instance()->is_registered( 'trivia-challenge/quiz-block' ) );
	}

	/**
	 * Test REST API routes are registered
	 */
	public function test_rest_routes() {
		$routes = rest_get_server()->get_routes();
		$this->assertArrayHasKey( '/trivia-challenge/v1/questions', $routes );
		$this->assertArrayHasKey( '/trivia-challenge/v1/clear-cache', $routes );
	}

	/**
	 * Test settings defaults
	 */
	public function test_settings_defaults() {
		$settings = \TriviaChallenge\Trivia_Challenge_Admin::get_default_settings();
		$this->assertIsArray( $settings );
		$this->assertEquals( 'mixed', $settings['default_category'] );
		$this->assertEquals( 'medium', $settings['default_difficulty'] );
		$this->assertEquals( 10, $settings['questions_per_quiz'] );
		$this->assertEquals( 20, $settings['timer_duration'] );
	}

	/**
	 * Hard mode returns the requested number of questions, even above 15
	 */
	public function test_hard_mode_returns_requested_amount() {
		$requested_urls = array();
		$fake_api       = function ( $pre, $args, $url ) use ( &$requested_urls ) {
			$requested_urls[] = $url;
			parse_str( (string) wp_parse_url( $url, PHP_URL_QUERY ), $query );
			$results = array();
			for ( $i = 0; $i < (int) $query['amount']; $i++ ) {
				$results[] = array(
					'question'          => 'Hard question number ' . $i . ' with enough words to count as long?',
					'correct_answer'    => 'Right',
					'incorrect_answers' => array( 'Wrong 1', 'Wrong 2', 'Wrong 3' ),
					'difficulty'        => 'hard',
				);
			}
			return array(
				'headers'  => array(),
				'body'     => wp_json_encode(
					array(
						'response_code' => 0,
						'results'       => $results,
					)
				),
				'response' => array(
					'code'    => 200,
					'message' => 'OK',
				),
				'cookies'  => array(),
			);
		};
		add_filter( 'pre_http_request', $fake_api, 10, 3 );

		$request = new WP_REST_Request( 'GET', '/trivia-challenge/v1/questions' );
		$request->set_param( 'category', 'science' );
		$request->set_param( 'difficulty', 'hard' );
		$request->set_param( 'amount', 20 );
		$response = \TriviaChallenge\Trivia_Challenge_API::get_questions( $request );

		remove_filter( 'pre_http_request', $fake_api, 10 );

		$data = $response->get_data();
		$this->assertCount( 20, $data['data'] );
		$this->assertStringContainsString( 'amount=50', $requested_urls[0] );
	}

	/**
	 * Build a fake Open Trivia DB reply for the pre_http_request filter
	 *
	 * @param array $results       Raw question results.
	 * @param int   $response_code Open Trivia DB response code.
	 * @return array
	 */
	private function fake_api_reply( $results, $response_code = 0 ) {
		return array(
			'headers'  => array(),
			'body'     => wp_json_encode(
				array(
					'response_code' => $response_code,
					'results'       => $results,
				)
			),
			'response' => array(
				'code'    => 200,
				'message' => 'OK',
			),
			'cookies'  => array(),
		);
	}

	/**
	 * Build a request for the questions route
	 *
	 * @param string $category   Category.
	 * @param string $difficulty Difficulty.
	 * @param int    $amount     Amount.
	 * @return WP_REST_Request
	 */
	private function questions_request( $category, $difficulty, $amount ) {
		$request = new WP_REST_Request( 'GET', '/trivia-challenge/v1/questions' );
		$request->set_param( 'category', $category );
		$request->set_param( 'difficulty', $difficulty );
		$request->set_param( 'amount', $amount );
		return $request;
	}

	/**
	 * Play Again gets a new selection from the cached pool, without a new API call
	 */
	public function test_replay_draws_new_questions_from_cached_pool() {
		$requested_urls = array();
		$fake_api       = function ( $pre, $args, $url ) use ( &$requested_urls ) {
			$requested_urls[] = $url;
			parse_str( (string) wp_parse_url( $url, PHP_URL_QUERY ), $query );
			$results = array();
			for ( $i = 0; $i < (int) $query['amount']; $i++ ) {
				$results[] = array(
					'question'          => 'Question ' . $i,
					'correct_answer'    => 'Right',
					'incorrect_answers' => array( 'Wrong 1', 'Wrong 2', 'Wrong 3' ),
					'difficulty'        => 'medium',
				);
			}
			return $this->fake_api_reply( $results );
		};
		add_filter( 'pre_http_request', $fake_api, 10, 3 );

		$first  = \TriviaChallenge\Trivia_Challenge_API::get_questions( $this->questions_request( 'history', 'medium', 10 ) )->get_data();
		$second = \TriviaChallenge\Trivia_Challenge_API::get_questions( $this->questions_request( 'history', 'medium', 10 ) )->get_data();

		remove_filter( 'pre_http_request', $fake_api, 10 );

		$this->assertCount( 1, $requested_urls );
		$this->assertStringContainsString( 'amount=50', $requested_urls[0] );
		$this->assertFalse( $first['cached'] );
		$this->assertTrue( $second['cached'] );
		$this->assertCount( 10, $first['data'] );
		$this->assertCount( 10, $second['data'] );
		$this->assertNotSame( wp_list_pluck( $first['data'], 'q' ), wp_list_pluck( $second['data'], 'q' ) );
	}

	/**
	 * A category with fewer questions than a full pool still gets a quiz
	 */
	public function test_small_category_falls_back_to_quiz_size() {
		$requested_urls = array();
		$fake_api       = function ( $pre, $args, $url ) use ( &$requested_urls ) {
			$requested_urls[] = $url;
			parse_str( (string) wp_parse_url( $url, PHP_URL_QUERY ), $query );
			if ( (int) $query['amount'] > 20 ) {
				return $this->fake_api_reply( array(), 1 );
			}
			$results = array();
			for ( $i = 0; $i < (int) $query['amount']; $i++ ) {
				$results[] = array(
					'question'          => 'Question ' . $i,
					'correct_answer'    => 'Right',
					'incorrect_answers' => array( 'Wrong 1', 'Wrong 2', 'Wrong 3' ),
					'difficulty'        => 'easy',
				);
			}
			return $this->fake_api_reply( $results );
		};
		add_filter( 'pre_http_request', $fake_api, 10, 3 );

		$first = \TriviaChallenge\Trivia_Challenge_API::get_questions( $this->questions_request( 'mythology', 'easy', 10 ) )->get_data();

		// Once the pool expires, the next fetch goes straight to the quiz size.
		delete_transient( 'trivia_challenge_pool_' . md5( 'mythology' . 'easy' ) );
		$second = \TriviaChallenge\Trivia_Challenge_API::get_questions( $this->questions_request( 'mythology', 'easy', 10 ) )->get_data();

		remove_filter( 'pre_http_request', $fake_api, 10 );

		$this->assertCount( 10, $first['data'] );
		$this->assertCount( 10, $second['data'] );
		$this->assertCount( 3, $requested_urls );
		$this->assertStringContainsString( 'amount=50', $requested_urls[0] );
		$this->assertStringContainsString( 'amount=10', $requested_urls[1] );
		$this->assertStringContainsString( 'amount=10', $requested_urls[2] );
	}

	/**
	 * Two blocks on one page each render their own settings, with no shared id
	 */
	public function test_each_block_renders_its_own_settings() {
		$science = render_block(
			array(
				'blockName'    => 'trivia-challenge/quiz-block',
				'attrs'        => array(
					'defaultCategory'  => 'science',
					'questionsPerQuiz' => 5,
				),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);
		$history = render_block(
			array(
				'blockName'    => 'trivia-challenge/quiz-block',
				'attrs'        => array( 'defaultCategory' => 'history' ),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);

		foreach ( array( $science, $history ) as $html ) {
			$this->assertStringNotContainsString( 'id="trivia-challenge-root"', $html );
			$this->assertStringContainsString( 'class="trivia-challenge-root"', $html );
		}

		preg_match( '/data-settings="([^"]*)"/', $science, $science_match );
		preg_match( '/data-settings="([^"]*)"/', $history, $history_match );
		$science_settings = json_decode( html_entity_decode( $science_match[1], ENT_QUOTES ), true );
		$history_settings = json_decode( html_entity_decode( $history_match[1], ENT_QUOTES ), true );

		$this->assertSame( 'science', $science_settings['defaultCategory'] );
		$this->assertSame( 5, $science_settings['questionsPerQuiz'] );
		$this->assertSame( 'history', $history_settings['defaultCategory'] );
		$this->assertSame( 10, $history_settings['questionsPerQuiz'] );
	}

	/**
	 * The admin settings become the block's attribute defaults
	 */
	public function test_admin_settings_become_block_defaults() {
		update_option(
			'trivia_challenge_settings',
			array(
				'default_category'   => 'history',
				'default_difficulty' => 'hard',
				'questions_per_quiz' => 15,
				'timer_duration'     => 30,
			)
		);

		$metadata = \TriviaChallenge\Trivia_Challenge_Block::get_instance()->apply_admin_defaults(
			wp_json_file_decode( TRIVIA_CHALLENGE_BLOCK_PATH . 'build/block.json', array( 'associative' => true ) )
		);

		$this->assertSame( 'history', $metadata['attributes']['defaultCategory']['default'] );
		$this->assertSame( 'hard', $metadata['attributes']['defaultDifficulty']['default'] );
		$this->assertSame( 15, $metadata['attributes']['questionsPerQuiz']['default'] );
		$this->assertSame( 30, $metadata['attributes']['timerDuration']['default'] );
		$this->assertTrue( $metadata['attributes']['showTimer']['default'] );

		delete_option( 'trivia_challenge_settings' );
	}

	/**
	 * An unknown category or difficulty in the option keeps the block.json default
	 */
	public function test_invalid_admin_values_keep_block_json_defaults() {
		update_option(
			'trivia_challenge_settings',
			array(
				'default_category'   => 'not-a-category',
				'default_difficulty' => 'impossible',
			)
		);

		$metadata = \TriviaChallenge\Trivia_Challenge_Block::get_instance()->apply_admin_defaults(
			wp_json_file_decode( TRIVIA_CHALLENGE_BLOCK_PATH . 'build/block.json', array( 'associative' => true ) )
		);

		$this->assertSame( 'mixed', $metadata['attributes']['defaultCategory']['default'] );
		$this->assertSame( 'medium', $metadata['attributes']['defaultDifficulty']['default'] );

		delete_option( 'trivia_challenge_settings' );
	}

	/**
	 * A block renders the admin defaults for attributes it has not saved, and keeps saved ones
	 */
	public function test_render_uses_admin_defaults_for_unsaved_attributes() {
		update_option(
			'trivia_challenge_settings',
			array(
				'default_category'   => 'history',
				'questions_per_quiz' => 15,
			)
		);
		unregister_block_type( 'trivia-challenge/quiz-block' );
		\TriviaChallenge\Trivia_Challenge_Block::get_instance()->register_block();

		$html = render_block(
			array(
				'blockName'    => 'trivia-challenge/quiz-block',
				'attrs'        => array( 'questionsPerQuiz' => 5 ),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);
		preg_match( '/data-settings="([^"]*)"/', $html, $match );
		$settings = json_decode( html_entity_decode( $match[1], ENT_QUOTES ), true );

		$this->assertSame( 'history', $settings['defaultCategory'] );
		$this->assertSame( 5, $settings['questionsPerQuiz'] );

		delete_option( 'trivia_challenge_settings' );
		unregister_block_type( 'trivia-challenge/quiz-block' );
		\TriviaChallenge\Trivia_Challenge_Block::get_instance()->register_block();
	}

	/**
	 * The question pool is cached for the admin cache duration, in hours
	 */
	public function test_cache_duration_setting_sets_pool_expiry() {
		update_option( 'trivia_challenge_settings', array( 'cache_duration' => 24 ) );
		delete_transient( 'trivia_challenge_pool_' . md5( 'geographyeasy' ) );

		$results = array();
		for ( $i = 0; $i < 50; $i++ ) {
			$results[] = array(
				'question'          => 'Geography question ' . $i . '?',
				'correct_answer'    => 'Right',
				'incorrect_answers' => array( 'Wrong 1', 'Wrong 2', 'Wrong 3' ),
				'difficulty'        => 'easy',
			);
		}
		$fake_api = function () use ( $results ) {
			return $this->fake_api_reply( $results );
		};
		$duration = null;
		$capture  = function ( $seconds ) use ( &$duration ) {
			$duration = $seconds;
			return $seconds;
		};
		add_filter( 'pre_http_request', $fake_api );
		add_filter( 'trivia_challenge_api_cache_duration', $capture );

		\TriviaChallenge\Trivia_Challenge_API::get_questions( $this->questions_request( 'geography', 'easy', 10 ) );

		remove_filter( 'pre_http_request', $fake_api );
		remove_filter( 'trivia_challenge_api_cache_duration', $capture );
		delete_option( 'trivia_challenge_settings' );

		$this->assertSame( 24 * HOUR_IN_SECONDS, $duration );
	}
}
