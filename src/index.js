/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './style.scss';
import './editor.scss';

/**
 * Block registration
 */
registerBlockType( 'trivia-challenge/quiz-block', {
	title: __( 'Trivia Challenge', 'trivia-challenge-block' ),
	description: __(
		'An interactive general knowledge trivia quiz',
		'trivia-challenge-block'
	),
	category: 'trivia-challenge',
	icon: 'games',
	keywords: [
		__( 'trivia', 'trivia-challenge-block' ),
		__( 'quiz', 'trivia-challenge-block' ),
		__( 'game', 'trivia-challenge-block' ),
	],
	supports: {
		html: false,
		align: [ 'wide', 'full' ],
	},

	/**
	 * Edit component
	 */
	edit: () => {
		return (
			<div className="trivia-challenge-editor-preview">
				<div className="trivia-preview-card">
					<div className="trivia-preview-content">
						<span className="trivia-icon">🧠</span>
						<h3>
							{ __(
								'Trivia Challenge Block',
								'trivia-challenge-block'
							) }
						</h3>
						<p>
							{ __(
								'Interactive quiz will appear here on the frontend',
								'trivia-challenge-block'
							) }
						</p>
						<div className="trivia-preview-features">
							<span>✓ 11 Categories</span>
							<span>✓ 4,000+ Questions</span>
							<span>✓ 3 Difficulty Levels</span>
							<span>✓ Timer & Streak Bonuses</span>
							<span>✓ Mobile Responsive</span>
						</div>
					</div>
				</div>
			</div>
		);
	},

	/**
	 * Save component (we use render_callback in PHP, so this returns null)
	 */
	save: () => {
		return null;
	},
} );
