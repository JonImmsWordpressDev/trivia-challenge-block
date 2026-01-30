/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	RangeControl,
	ToggleControl,
} from '@wordpress/components';

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
	edit: ( { attributes, setAttributes } ) => {
		const blockProps = useBlockProps();
		const {
			defaultCategory,
			defaultDifficulty,
			questionsPerQuiz,
			timerDuration,
			showTimer,
			showStreak,
			enableSound,
		} = attributes;

		const categoryOptions = [
			{ label: __( 'Mixed - All Topics', 'trivia-challenge-block' ), value: 'mixed' },
			{ label: __( 'General Knowledge', 'trivia-challenge-block' ), value: 'general' },
			{ label: __( 'Science & Nature', 'trivia-challenge-block' ), value: 'science' },
			{ label: __( 'History', 'trivia-challenge-block' ), value: 'history' },
			{ label: __( 'Geography', 'trivia-challenge-block' ), value: 'geography' },
			{ label: __( 'Entertainment: Film & TV', 'trivia-challenge-block' ), value: 'entertainment' },
			{ label: __( 'Sports', 'trivia-challenge-block' ), value: 'sports' },
			{ label: __( 'Computers & Technology', 'trivia-challenge-block' ), value: 'computers' },
			{ label: __( 'Mathematics', 'trivia-challenge-block' ), value: 'mathematics' },
			{ label: __( 'Mythology', 'trivia-challenge-block' ), value: 'mythology' },
			{ label: __( 'Animals', 'trivia-challenge-block' ), value: 'animals' },
		];

		const difficultyOptions = [
			{ label: __( 'Easy', 'trivia-challenge-block' ), value: 'easy' },
			{ label: __( 'Medium', 'trivia-challenge-block' ), value: 'medium' },
			{ label: __( 'Hard', 'trivia-challenge-block' ), value: 'hard' },
			{ label: __( 'Any Difficulty', 'trivia-challenge-block' ), value: 'any' },
		];

		const getCategoryLabel = ( value ) => {
			const option = categoryOptions.find( ( opt ) => opt.value === value );
			return option ? option.label : value;
		};

		const getDifficultyLabel = ( value ) => {
			const option = difficultyOptions.find( ( opt ) => opt.value === value );
			return option ? option.label : value;
		};

		return (
			<>
				<InspectorControls>
					<PanelBody
						title={ __( 'Quiz Settings', 'trivia-challenge-block' ) }
						initialOpen={ true }
					>
						<SelectControl
							label={ __( 'Default Category', 'trivia-challenge-block' ) }
							value={ defaultCategory }
							options={ categoryOptions }
							onChange={ ( value ) => setAttributes( { defaultCategory: value } ) }
							help={ __( 'The category shown when the quiz loads', 'trivia-challenge-block' ) }
						/>
						<SelectControl
							label={ __( 'Default Difficulty', 'trivia-challenge-block' ) }
							value={ defaultDifficulty }
							options={ difficultyOptions }
							onChange={ ( value ) => setAttributes( { defaultDifficulty: value } ) }
							help={ __( 'The difficulty shown when the quiz loads', 'trivia-challenge-block' ) }
						/>
						<RangeControl
							label={ __( 'Questions Per Quiz', 'trivia-challenge-block' ) }
							value={ questionsPerQuiz }
							onChange={ ( value ) => setAttributes( { questionsPerQuiz: value } ) }
							min={ 5 }
							max={ 20 }
							help={ __( 'Number of questions in each quiz', 'trivia-challenge-block' ) }
						/>
					</PanelBody>
					<PanelBody
						title={ __( 'Display Settings', 'trivia-challenge-block' ) }
						initialOpen={ false }
					>
						<RangeControl
							label={ __( 'Timer Duration (seconds)', 'trivia-challenge-block' ) }
							value={ timerDuration }
							onChange={ ( value ) => setAttributes( { timerDuration: value } ) }
							min={ 10 }
							max={ 60 }
							disabled={ ! showTimer }
						/>
						<ToggleControl
							label={ __( 'Show Timer', 'trivia-challenge-block' ) }
							checked={ showTimer }
							onChange={ ( value ) => setAttributes( { showTimer: value } ) }
							help={ __( 'Display countdown timer for each question', 'trivia-challenge-block' ) }
						/>
						<ToggleControl
							label={ __( 'Show Streak Counter', 'trivia-challenge-block' ) }
							checked={ showStreak }
							onChange={ ( value ) => setAttributes( { showStreak: value } ) }
							help={ __( 'Display streak bonus for consecutive correct answers', 'trivia-challenge-block' ) }
						/>
						<ToggleControl
							label={ __( 'Enable Sound Effects', 'trivia-challenge-block' ) }
							checked={ enableSound }
							onChange={ ( value ) => setAttributes( { enableSound: value } ) }
							help={ __( 'Play sounds for correct/wrong answers', 'trivia-challenge-block' ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<div className="trivia-challenge-editor-preview">
						<div className="trivia-preview-card">
							<div className="trivia-preview-content">
								<span className="trivia-icon">🧠</span>
								<h3>
									{ __( 'Trivia Challenge Block', 'trivia-challenge-block' ) }
								</h3>
								<p>
									{ __( 'Interactive quiz will appear here on the frontend', 'trivia-challenge-block' ) }
								</p>
								<div className="trivia-preview-settings">
									<span>{ getCategoryLabel( defaultCategory ) }</span>
									<span>{ getDifficultyLabel( defaultDifficulty ) }</span>
									<span>{ questionsPerQuiz } { __( 'questions', 'trivia-challenge-block' ) }</span>
									{ showTimer && <span>{ timerDuration }s { __( 'timer', 'trivia-challenge-block' ) }</span> }
								</div>
								<div className="trivia-preview-features">
									{ showTimer && <span>✓ { __( 'Timer', 'trivia-challenge-block' ) }</span> }
									{ showStreak && <span>✓ { __( 'Streak Bonus', 'trivia-challenge-block' ) }</span> }
									{ enableSound && <span>✓ { __( 'Sound Effects', 'trivia-challenge-block' ) }</span> }
								</div>
							</div>
						</div>
					</div>
				</div>
			</>
		);
	},

	/**
	 * Save component (we use render_callback in PHP, so this returns null)
	 */
	save: () => {
		return null;
	},
} );
