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
		$this->assertStringContainsString( 'amount=25', $requested_urls[0] );
	}
}
