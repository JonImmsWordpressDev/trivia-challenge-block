/**
 * WordPress dependencies
 */
import { render, useState, useEffect, useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Get settings from localized script
 */
const getSettings = () => {
	return (
		window.triviaChallengeSettings || {
			apiUrl: '/wp-json/trivia-challenge/v1/',
			nonce: '',
			defaultCategory: 'mixed',
			defaultDifficulty: 'medium',
			questionsPerQuiz: 10,
			timerDuration: 20,
			showTimer: true,
			showStreak: true,
			enableSound: false,
		}
	);
};

/**
 * Category mapping: Internal category names to OpenTDB category IDs
 */
const CATEGORY_MAP = {
	mixed: null,
	science: 17,
	history: 23,
	geography: 22,
	entertainment: 11,
	sports: 21,
	general: 9,
	computers: 18,
	mathematics: 19,
	mythology: 20,
	animals: 27,
};

/**
 * Fallback questions database (used when API is unavailable)
 */
const fallbackQuestions = {
	mixed: [
		{
			q: __(
				'What is the largest planet in our solar system?',
				'trivia-challenge-block'
			),
			a: [
				__( 'Jupiter', 'trivia-challenge-block' ),
				__( 'Saturn', 'trivia-challenge-block' ),
				__( 'Neptune', 'trivia-challenge-block' ),
				__( 'Earth', 'trivia-challenge-block' ),
			],
			correct: 0,
		},
		{
			q: __( 'Who painted the Mona Lisa?', 'trivia-challenge-block' ),
			a: [
				__( 'Leonardo da Vinci', 'trivia-challenge-block' ),
				__( 'Michelangelo', 'trivia-challenge-block' ),
				__( 'Raphael', 'trivia-challenge-block' ),
				__( 'Donatello', 'trivia-challenge-block' ),
			],
			correct: 0,
		},
		{
			q: __(
				'What is the capital of Australia?',
				'trivia-challenge-block'
			),
			a: [
				__( 'Canberra', 'trivia-challenge-block' ),
				__( 'Sydney', 'trivia-challenge-block' ),
				__( 'Melbourne', 'trivia-challenge-block' ),
				__( 'Brisbane', 'trivia-challenge-block' ),
			],
			correct: 0,
		},
	],
	science: [
		{
			q: __(
				'What is the chemical formula for water?',
				'trivia-challenge-block'
			),
			a: [ 'H2O', 'CO2', 'H2O2', 'O2' ],
			correct: 0,
		},
	],
	history: [
		{
			q: __(
				'Who was the first President of the United States?',
				'trivia-challenge-block'
			),
			a: [
				__( 'George Washington', 'trivia-challenge-block' ),
				__( 'Thomas Jefferson', 'trivia-challenge-block' ),
				__( 'John Adams', 'trivia-challenge-block' ),
				__( 'Benjamin Franklin', 'trivia-challenge-block' ),
			],
			correct: 0,
		},
	],
	geography: [
		{
			q: __(
				'What is the longest river in the world?',
				'trivia-challenge-block'
			),
			a: [
				__( 'Nile River', 'trivia-challenge-block' ),
				__( 'Amazon River', 'trivia-challenge-block' ),
				__( 'Yangtze River', 'trivia-challenge-block' ),
				__( 'Mississippi River', 'trivia-challenge-block' ),
			],
			correct: 0,
		},
	],
	entertainment: [
		{
			q: __(
				'Who directed the movie "Jurassic Park"?',
				'trivia-challenge-block'
			),
			a: [
				__( 'Steven Spielberg', 'trivia-challenge-block' ),
				__( 'James Cameron', 'trivia-challenge-block' ),
				__( 'George Lucas', 'trivia-challenge-block' ),
				__( 'Christopher Nolan', 'trivia-challenge-block' ),
			],
			correct: 0,
		},
	],
	sports: [
		{
			q: __(
				'How many players are on a soccer team on the field?',
				'trivia-challenge-block'
			),
			a: [ '11', '10', '12', '9' ],
			correct: 0,
		},
	],
};

/**
 * Utility function to shuffle array
 */
const shuffleArray = ( array ) => {
	const newArray = [ ...array ];
	for ( let i = newArray.length - 1; i > 0; i-- ) {
		const j = Math.floor( Math.random() * ( i + 1 ) );
		[ newArray[ i ], newArray[ j ] ] = [ newArray[ j ], newArray[ i ] ];
	}
	return newArray;
};

/**
 * LocalStorage helpers
 */
const STORAGE_KEY = 'trivia_challenge_state';

const saveQuizState = ( state ) => {
	try {
		localStorage.setItem( STORAGE_KEY, JSON.stringify( state ) );
	} catch ( error ) {
		console.warn( 'Failed to save quiz state:', error );
	}
};

const loadQuizState = () => {
	try {
		const state = localStorage.getItem( STORAGE_KEY );
		return state ? JSON.parse( state ) : null;
	} catch ( error ) {
		console.warn( 'Failed to load quiz state:', error );
		return null;
	}
};

const clearQuizState = () => {
	try {
		localStorage.removeItem( STORAGE_KEY );
	} catch ( error ) {
		console.warn( 'Failed to clear quiz state:', error );
	}
};

/**
 * Leaderboard localStorage helpers
 */
const LEADERBOARD_KEY = 'trivia_challenge_leaderboard';

const loadLeaderboard = () => {
	try {
		const data = localStorage.getItem( LEADERBOARD_KEY );
		return data ? JSON.parse( data ) : {};
	} catch ( error ) {
		console.warn( 'Failed to load leaderboard:', error );
		return {};
	}
};

const saveLeaderboard = ( leaderboard ) => {
	try {
		localStorage.setItem( LEADERBOARD_KEY, JSON.stringify( leaderboard ) );
	} catch ( error ) {
		console.warn( 'Failed to save leaderboard:', error );
	}
};

const updateLeaderboard = ( category, score, total, difficulty ) => {
	const leaderboard = loadLeaderboard();
	const existing = leaderboard[ category ];

	// Only update if new score is better (higher percentage, or same percentage with harder difficulty)
	const newPercentage = score / total;
	const existingPercentage = existing ? existing.score / existing.total : 0;
	const difficultyRank = { easy: 1, medium: 2, hard: 3, any: 2 };

	if (
		! existing ||
		newPercentage > existingPercentage ||
		( newPercentage === existingPercentage &&
			difficultyRank[ difficulty ] > difficultyRank[ existing.difficulty ] )
	) {
		leaderboard[ category ] = {
			score,
			total,
			difficulty,
			date: new Date().toISOString(),
		};
		saveLeaderboard( leaderboard );
		return true; // New best!
	}
	return false;
};

const clearLeaderboard = () => {
	try {
		localStorage.removeItem( LEADERBOARD_KEY );
	} catch ( error ) {
		console.warn( 'Failed to clear leaderboard:', error );
	}
};

/**
 * Fetch questions from WordPress REST API (which proxies to Open Trivia DB)
 */
const fetchTriviaQuestions = async (
	category,
	difficulty = 'medium',
	amount = 10
) => {
	const settings = getSettings();

	try {
		const url = new URL(
			settings.apiUrl + 'questions',
			window.location.origin
		);
		url.searchParams.append( 'category', category );
		url.searchParams.append( 'difficulty', difficulty );
		url.searchParams.append( 'amount', amount );

		const response = await fetch( url.toString(), {
			headers: {
				'X-WP-Nonce': settings.nonce,
			},
		} );

		if ( ! response.ok ) {
			throw new Error( 'API request failed' );
		}

		const data = await response.json();

		if ( data.success && data.data && data.data.length > 0 ) {
			return data.data;
		}

		throw new Error( 'No questions returned' );
	} catch ( error ) {
		console.warn(
			'Failed to fetch questions from API, using fallback:',
			error
		);
		return null;
	}
};

/**
 * Setup Screen Component
 */
const SetupScreen = ( { onStart } ) => {
	const settings = getSettings();
	const [ selectedCategory, setSelectedCategory ] = useState(
		settings.defaultCategory
	);
	const [ selectedDifficulty, setSelectedDifficulty ] = useState(
		settings.defaultDifficulty
	);
	const [ isLoading, setIsLoading ] = useState( false );
	const [ hasResumeState, setHasResumeState ] = useState( false );

	useEffect( () => {
		const savedState = loadQuizState();
		setHasResumeState(
			savedState &&
				savedState.questions &&
				savedState.questions.length > 0
		);
	}, [] );

	const handleStartClick = async () => {
		setIsLoading( true );
		await onStart( selectedCategory, selectedDifficulty, false );
		setIsLoading( false );
	};

	const handleResumeClick = () => {
		onStart( null, null, true );
	};

	return (
		<div
			className="trivia-setup"
			role="main"
			aria-labelledby="trivia-title"
		>
			<h1 id="trivia-title" className="trivia-title">
				{ __( 'Trivia Challenge', 'trivia-challenge-block' ) }
			</h1>
			<p className="trivia-subtitle">
				{ __(
					'Test your knowledge with questions from around the world!',
					'trivia-challenge-block'
				) }
			</p>

			<div className="trivia-category-select">
				<label htmlFor="trivia-category">
					{ __( 'Choose a Category:', 'trivia-challenge-block' ) }
				</label>
				<select
					id="trivia-category"
					value={ selectedCategory }
					onChange={ ( e ) => setSelectedCategory( e.target.value ) }
					disabled={ isLoading }
					aria-label={ __(
						'Select quiz category',
						'trivia-challenge-block'
					) }
				>
					<option value="mixed">
						{ __( 'Mixed - All Topics', 'trivia-challenge-block' ) }
					</option>
					<option value="general">
						{ __( 'General Knowledge', 'trivia-challenge-block' ) }
					</option>
					<option value="science">
						{ __( 'Science & Nature', 'trivia-challenge-block' ) }
					</option>
					<option value="history">
						{ __( 'History', 'trivia-challenge-block' ) }
					</option>
					<option value="geography">
						{ __( 'Geography', 'trivia-challenge-block' ) }
					</option>
					<option value="entertainment">
						{ __(
							'Entertainment: Film & TV',
							'trivia-challenge-block'
						) }
					</option>
					<option value="sports">
						{ __( 'Sports', 'trivia-challenge-block' ) }
					</option>
					<option value="computers">
						{ __(
							'Computers & Technology',
							'trivia-challenge-block'
						) }
					</option>
					<option value="mathematics">
						{ __( 'Mathematics', 'trivia-challenge-block' ) }
					</option>
					<option value="mythology">
						{ __( 'Mythology', 'trivia-challenge-block' ) }
					</option>
					<option value="animals">
						{ __( 'Animals', 'trivia-challenge-block' ) }
					</option>
				</select>
			</div>

			<div className="trivia-category-select">
				<label htmlFor="trivia-difficulty">
					{ __( 'Choose Difficulty:', 'trivia-challenge-block' ) }
				</label>
				<select
					id="trivia-difficulty"
					value={ selectedDifficulty }
					onChange={ ( e ) =>
						setSelectedDifficulty( e.target.value )
					}
					disabled={ isLoading }
					aria-label={ __(
						'Select difficulty level',
						'trivia-challenge-block'
					) }
				>
					<option value="easy">
						{ __( 'Easy', 'trivia-challenge-block' ) }
					</option>
					<option value="medium">
						{ __( 'Medium', 'trivia-challenge-block' ) }
					</option>
					<option value="hard">
						{ __( 'Hard', 'trivia-challenge-block' ) }
					</option>
					<option value="any">
						{ __( 'Any Difficulty', 'trivia-challenge-block' ) }
					</option>
				</select>
			</div>

			<div className="trivia-button-group">
				<button
					className="trivia-btn trivia-btn-primary"
					onClick={ handleStartClick }
					disabled={ isLoading }
					aria-busy={ isLoading }
				>
					{ isLoading
						? __( 'Loading Questions...', 'trivia-challenge-block' )
						: __( 'Start Quiz', 'trivia-challenge-block' ) }
				</button>

				{ hasResumeState && (
					<button
						className="trivia-btn trivia-btn-secondary"
						onClick={ handleResumeClick }
						aria-label={ __(
							'Resume previous quiz',
							'trivia-challenge-block'
						) }
					>
						{ __( 'Resume Quiz', 'trivia-challenge-block' ) }
					</button>
				) }
			</div>
		</div>
	);
};

/**
 * Quiz Screen Component
 */
const QuizScreen = ( { questions, onComplete, onRestart, resumeState } ) => {
	const settings = getSettings();
	const [ currentQuestionIndex, setCurrentQuestionIndex ] = useState(
		resumeState?.currentQuestionIndex || 0
	);
	const [ score, setScore ] = useState( resumeState?.score || 0 );
	const [ correctAnswers, setCorrectAnswers ] = useState(
		resumeState?.correctAnswers || 0
	);
	const [ answered, setAnswered ] = useState( false );
	const [ selectedAnswer, setSelectedAnswer ] = useState( null );
	const [ shuffledAnswers, setShuffledAnswers ] = useState( [] );
	const [ timeLeft, setTimeLeft ] = useState( settings.timerDuration );
	const [ streak, setStreak ] = useState( resumeState?.streak || 0 );
	const [ scoreAnimating, setScoreAnimating ] = useState( false );
	const [ streakAnimating, setStreakAnimating ] = useState( false );
	const timerRef = useRef( null );
	const announceRef = useRef( null );

	const currentQuestion = questions[ currentQuestionIndex ];

	// Timer with setInterval
	useEffect( () => {
		if ( ! answered && settings.showTimer && timeLeft > 0 ) {
			timerRef.current = setInterval( () => {
				setTimeLeft( ( prev ) => {
					if ( prev <= 1 ) {
						clearInterval( timerRef.current );
						handleTimeout();
						return 0;
					}
					return prev - 1;
				} );
			}, 1000 );

			return () => {
				if ( timerRef.current ) {
					clearInterval( timerRef.current );
				}
			};
		}
	}, [ answered, currentQuestionIndex ] );

	// Shuffle answers when question changes
	useEffect( () => {
		const shuffled = currentQuestion.a.map( ( answer, index ) => ( {
			answer,
			originalIndex: index,
		} ) );
		setShuffledAnswers( shuffleArray( shuffled ) );
		setAnswered( false );
		setSelectedAnswer( null );
		setTimeLeft( settings.timerDuration );
	}, [ currentQuestionIndex ] );

	// Save state to localStorage
	useEffect( () => {
		if ( questions.length > 0 ) {
			saveQuizState( {
				questions,
				currentQuestionIndex,
				score,
				correctAnswers,
				streak,
				timestamp: Date.now(),
			} );
		}
	}, [ currentQuestionIndex, score, correctAnswers, streak ] );

	const handleTimeout = () => {
		setAnswered( true );
		setSelectedAnswer( -1 );
		setStreak( 0 );

		if ( announceRef.current ) {
			announceRef.current.textContent = __(
				'Time is up!',
				'trivia-challenge-block'
			);
		}
	};

	const handleAnswerClick = ( originalIndex, answerIndex ) => {
		if ( answered ) {
			return;
		}

		if ( timerRef.current ) {
			clearInterval( timerRef.current );
		}

		setAnswered( true );
		setSelectedAnswer( answerIndex );

		const isCorrect = originalIndex === currentQuestion.correct;
		if ( isCorrect ) {
			const timeBonus = settings.showTimer
				? Math.floor( timeLeft / 2 )
				: 0;
			const streakBonus = settings.showStreak ? streak * 5 : 0;
			const totalPoints = 10 + timeBonus + streakBonus;
			setScore( score + totalPoints );
			setCorrectAnswers( correctAnswers + 1 );
			setStreak( streak + 1 );
			setStreakAnimating( true );
			setTimeout( () => setStreakAnimating( false ), 400 );
			setScoreAnimating( true );
			setTimeout( () => setScoreAnimating( false ), 300 );

			if ( announceRef.current ) {
				announceRef.current.textContent = __(
					'Correct!',
					'trivia-challenge-block'
				);
			}
		} else {
			setStreak( 0 );

			if ( announceRef.current ) {
				announceRef.current.textContent = __(
					'Incorrect',
					'trivia-challenge-block'
				);
			}
		}
	};

	const handleNext = () => {
		if ( currentQuestionIndex < questions.length - 1 ) {
			setCurrentQuestionIndex( currentQuestionIndex + 1 );
		} else {
			clearQuizState();
			onComplete( correctAnswers, questions.length );
		}
	};

	const isCorrectAnswer = ( originalIndex ) =>
		originalIndex === currentQuestion.correct;
	const isIncorrectAnswer = ( answerIndex ) =>
		answered &&
		selectedAnswer === answerIndex &&
		shuffledAnswers[ answerIndex ].originalIndex !==
			currentQuestion.correct;

	return (
		<div
			className="trivia-quiz"
			role="main"
			aria-labelledby="current-question"
		>
			{ /* Progress bar */ }
			<div
				className="trivia-progress-bar"
				role="progressbar"
				aria-valuenow={ currentQuestionIndex + 1 }
				aria-valuemin={ 1 }
				aria-valuemax={ questions.length }
				aria-label={ __( 'Quiz progress', 'trivia-challenge-block' ) }
			>
				<div
					className="trivia-progress-fill"
					style={ { width: `${ ( ( currentQuestionIndex + 1 ) / questions.length ) * 100 }%` } }
				/>
			</div>

			{ /* Screen reader announcements */ }
			<div
				ref={ announceRef }
				className="sr-only"
				role="status"
				aria-live="polite"
				aria-atomic="true"
			/>

			<div
				className="trivia-score-board"
				role="region"
				aria-label={ __( 'Score board', 'trivia-challenge-block' ) }
			>
				<div className="trivia-score-item">
					<div className="trivia-score-label">
						{ __( 'Question', 'trivia-challenge-block' ) }
					</div>
					<div className="trivia-score-value" aria-live="polite">
						{ currentQuestionIndex + 1 }/{ questions.length }
					</div>
				</div>
				<div className="trivia-score-item">
					<div className="trivia-score-label">
						{ __( 'Score', 'trivia-challenge-block' ) }
					</div>
					<div className={ `trivia-score-value${ scoreAnimating ? ' score-pop' : '' }` } aria-live="polite">
						{ score }
					</div>
				</div>
				{ settings.showTimer && (
					<div className="trivia-score-item trivia-timer-item">
						<div className="trivia-score-label">
							{ __( 'Timer', 'trivia-challenge-block' ) }
						</div>
						<div className="trivia-timer-container">
							<div
								className={ `trivia-timer-bar${ timeLeft <= 5 ? ' trivia-timer-warning' : '' }` }
								style={ { width: `${ ( timeLeft / settings.timerDuration ) * 100 }%` } }
								role="timer"
								aria-live="assertive"
								aria-valuenow={ timeLeft }
								aria-valuemax={ settings.timerDuration }
							/>
							<span className="trivia-timer-text">{ timeLeft }s</span>
						</div>
					</div>
				) }
				{ settings.showStreak && streak > 0 && (
					<div className={ `trivia-score-item trivia-streak${ streakAnimating ? ' streak-glow' : '' }${ streak >= 5 ? ' streak-fire' : '' }` }>
						<div className="trivia-score-label">
							{ __( 'Streak', 'trivia-challenge-block' ) }
						</div>
						<div className="trivia-score-value" aria-live="polite">
							🔥 { streak }
						</div>
					</div>
				) }
			</div>

			<div className="trivia-question-container">
				<div className="trivia-question-number">
					{ __( 'Question', 'trivia-challenge-block' ) }{ ' ' }
					{ currentQuestionIndex + 1 }
				</div>
				<div
					id="current-question"
					className="trivia-question"
					tabIndex="-1"
				>
					{ currentQuestion.q }
				</div>

				<div
					className="trivia-answers"
					role="group"
					aria-labelledby="current-question"
				>
					{ shuffledAnswers.map( ( item, index ) => {
						const isCorrect = isCorrectAnswer( item.originalIndex );
						const isIncorrect = isIncorrectAnswer( index );

						let className = 'trivia-answer-btn';
						if ( answered ) {
							if ( isCorrect ) {
								className += ' correct';
							}
							if ( isIncorrect ) {
								className += ' incorrect';
							}
						}

						return (
							<button
								key={ index }
								className={ className }
								onClick={ () =>
									handleAnswerClick(
										item.originalIndex,
										index
									)
								}
								disabled={ answered }
								aria-pressed={ selectedAnswer === index }
								aria-label={ `${ __(
									'Answer',
									'trivia-challenge-block'
								) } ${ index + 1 }: ${ item.answer }` }
							>
								{ item.answer }
							</button>
						);
					} ) }
				</div>

				{ answered && (
					<div
						className={ `trivia-feedback ${
							selectedAnswer !== null &&
							shuffledAnswers[ selectedAnswer ]?.originalIndex ===
								currentQuestion.correct
								? 'correct'
								: 'incorrect'
						}` }
						role="alert"
						aria-live="assertive"
					>
						{ selectedAnswer !== null &&
						shuffledAnswers[ selectedAnswer ]?.originalIndex ===
							currentQuestion.correct
							? __(
									'Correct! Well done! 🎉',
									'trivia-challenge-block'
							  )
							: `${ __(
									'Incorrect. The correct answer was:',
									'trivia-challenge-block'
							  ) } ${
									currentQuestion.a[ currentQuestion.correct ]
							  }` }
					</div>
				) }
			</div>

			<div className="trivia-control-buttons">
				<button
					className="trivia-btn trivia-btn-primary"
					onClick={ handleNext }
					disabled={ ! answered }
					aria-label={
						currentQuestionIndex < questions.length - 1
							? __(
									'Go to next question',
									'trivia-challenge-block'
							  )
							: __( 'See quiz results', 'trivia-challenge-block' )
					}
				>
					{ currentQuestionIndex < questions.length - 1
						? __( 'Next Question', 'trivia-challenge-block' )
						: __( 'See Results', 'trivia-challenge-block' ) }
				</button>
				<button
					className="trivia-btn trivia-btn-secondary"
					onClick={ onRestart }
					aria-label={ __(
						'Restart the quiz from the beginning',
						'trivia-challenge-block'
					) }
				>
					{ __( 'Restart Quiz', 'trivia-challenge-block' ) }
				</button>
			</div>
		</div>
	);
};

/**
 * Results Screen Component
 */
const ResultsScreen = ( { correctAnswers, totalQuestions, onRestart } ) => {
	const percentage = Math.round( ( correctAnswers / totalQuestions ) * 100 );

	let message = '';
	if ( percentage === 100 ) {
		message = __(
			"Perfect score! You're a trivia master! 🏆",
			'trivia-challenge-block'
		);
	} else if ( percentage >= 80 ) {
		message = __(
			'Excellent work! You really know your stuff! 🌟',
			'trivia-challenge-block'
		);
	} else if ( percentage >= 60 ) {
		message = __(
			'Good job! Keep learning and improving! 👍',
			'trivia-challenge-block'
		);
	} else if ( percentage >= 40 ) {
		message = __(
			"Not bad! There's room for improvement! 📚",
			'trivia-challenge-block'
		);
	} else {
		message = __(
			"Keep practicing! You'll get better! 💪",
			'trivia-challenge-block'
		);
	}

	return (
		<div
			className="trivia-results"
			role="main"
			aria-labelledby="results-heading"
		>
			<h2 id="results-heading">
				{ __( 'Quiz Complete! 🎉', 'trivia-challenge-block' ) }
			</h2>
			<div className="trivia-results-score" aria-live="polite">
				<span className="sr-only">
					{ __( 'You got', 'trivia-challenge-block' ) }{ ' ' }
					{ correctAnswers }{ ' ' }
					{ __( 'out of', 'trivia-challenge-block' ) }{ ' ' }
					{ totalQuestions }{ ' ' }
					{ __( 'questions correct', 'trivia-challenge-block' ) }
				</span>
				<span aria-hidden="true">
					{ correctAnswers }/{ totalQuestions }
				</span>
			</div>
			<div className="trivia-results-percentage" aria-live="polite">
				{ percentage }%
			</div>
			<div className="trivia-results-message">{ message }</div>
			<button
				className="trivia-btn trivia-btn-primary"
				onClick={ onRestart }
				aria-label={ __(
					'Play the quiz again',
					'trivia-challenge-block'
				) }
			>
				{ __( 'Play Again', 'trivia-challenge-block' ) }
			</button>
		</div>
	);
};

/**
 * Main App Component
 */
const TriviaApp = () => {
	const settings = getSettings();
	const [ screen, setScreen ] = useState( 'setup' );
	const [ questions, setQuestions ] = useState( [] );
	const [ results, setResults ] = useState( { correct: 0, total: 0 } );
	const [ error, setError ] = useState( null );
	const [ resumeState, setResumeState ] = useState( null );

	const handleStart = async ( category, difficulty, resume = false ) => {
		setError( null );

		if ( resume ) {
			const savedState = loadQuizState();
			if ( savedState && savedState.questions ) {
				setQuestions( savedState.questions );
				setResumeState( savedState );
				setScreen( 'quiz' );
				return;
			}
		}

		let fetchedQuestions = await fetchTriviaQuestions(
			category,
			difficulty,
			settings.questionsPerQuiz
		);

		if ( ! fetchedQuestions || fetchedQuestions.length === 0 ) {
			const fallback =
				fallbackQuestions[ category ] || fallbackQuestions.mixed;
			fetchedQuestions = shuffleArray( [ ...fallback ] ).slice(
				0,
				settings.questionsPerQuiz
			);
			setError(
				__(
					'Using offline questions - check your internet connection',
					'trivia-challenge-block'
				)
			);
		}

		setQuestions( fetchedQuestions );
		setResumeState( null );
		setScreen( 'quiz' );
	};

	const handleComplete = ( correct, total ) => {
		setResults( { correct, total } );
		setScreen( 'results' );
	};

	const handleRestart = () => {
		clearQuizState();
		setScreen( 'setup' );
		setQuestions( [] );
		setResults( { correct: 0, total: 0 } );
		setResumeState( null );
		setError( null );
	};

	return (
		<div className="trivia-challenge-container">
			{ error && (
				<div
					className="trivia-error-banner"
					role="alert"
					aria-live="polite"
				>
					⚠️ { error }
				</div>
			) }
			{ screen === 'setup' && <SetupScreen onStart={ handleStart } /> }
			{ screen === 'quiz' && (
				<QuizScreen
					questions={ questions }
					onComplete={ handleComplete }
					onRestart={ handleRestart }
					resumeState={ resumeState }
				/>
			) }
			{ screen === 'results' && (
				<ResultsScreen
					correctAnswers={ results.correct }
					totalQuestions={ results.total }
					onRestart={ handleRestart }
				/>
			) }
		</div>
	);
};

/**
 * Initialize the app
 */
const initTriviaApp = () => {
	const rootElement = document.getElementById( 'trivia-challenge-root' );

	if ( rootElement && ! rootElement.dataset.initialized ) {
		rootElement.dataset.initialized = 'true';
		render( <TriviaApp />, rootElement );
	}
};

// Initialize when DOM is ready
if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', initTriviaApp );
} else {
	initTriviaApp();
}

// Also handle dynamic content loading (for block themes, etc.)
if ( typeof window !== 'undefined' ) {
	window.addEventListener( 'load', initTriviaApp );
}
