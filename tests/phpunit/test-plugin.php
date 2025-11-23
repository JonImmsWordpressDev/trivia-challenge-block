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
}
