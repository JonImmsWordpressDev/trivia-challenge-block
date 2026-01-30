# Trivia Block Feature Updates - Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add editor controls, visual polish, leaderboard, and sound effects to the trivia challenge block.

**Architecture:** Four independent features that share code in `frontend.js` and `style.scss`. Editor controls require new imports in `index.js`. Sound files stored in `assets/sounds/`. All localStorage interactions go through helper functions.

**Tech Stack:** WordPress Block Editor (InspectorControls, PanelBody, SelectControl, ToggleControl, RangeControl), React hooks, CSS animations, Web Audio API for sound playback.

---

## Task 1: Editor Controls - InspectorControls Setup

**Files:**
- Modify: `src/index.js`

**Step 1: Add WordPress component imports**

At top of `src/index.js`, replace existing imports with:

```javascript
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
```

**Step 2: Build the full build to verify imports work**

Run: `npm run build`
Expected: Build succeeds with no import errors

**Step 3: Commit**

```bash
git add src/index.js
git commit -m "feat(editor): add WordPress component imports for InspectorControls"
```

---

## Task 2: Editor Controls - Edit Component with Settings Panels

**Files:**
- Modify: `src/index.js`

**Step 1: Replace the edit function**

Replace the entire `edit: () => { ... }` block with:

```javascript
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
```

**Step 2: Build and verify**

Run: `npm run build`
Expected: Build succeeds

**Step 3: Commit**

```bash
git add src/index.js
git commit -m "feat(editor): add InspectorControls with Quiz and Display settings panels"
```

---

## Task 3: Editor Controls - Update Editor Styles

**Files:**
- Modify: `src/editor.scss`

**Step 1: Add styles for the settings preview**

Replace entire contents of `src/editor.scss` with:

```scss
/**
 * Editor Styles
 */

.trivia-challenge-editor-preview {
	padding: 20px;

	.trivia-preview-card {
		background: var(--wp--preset--color--base, #f8f9fa);
		border: 2px solid var(--wp--preset--color--contrast, #dee2e6);
		border-radius: 8px;
		padding: 60px 40px;
		text-align: center;

		.trivia-preview-content {
			max-width: 600px;
			margin: 0 auto;

			.trivia-icon {
				font-size: 4em;
				display: block;
				margin-bottom: 20px;
			}

			h3 {
				font-size: 2em;
				margin: 0 0 15px;
				color: var(--wp--preset--color--contrast, #000);
				font-weight: 700;
			}

			p {
				font-size: 1.1em;
				margin: 0 0 20px;
				color: var(--wp--preset--color--contrast, #666);
			}

			.trivia-preview-settings {
				display: flex;
				flex-wrap: wrap;
				justify-content: center;
				gap: 8px;
				margin-bottom: 20px;

				span {
					background: var(--wp--preset--color--contrast, #000);
					color: var(--wp--preset--color--base, #fff);
					padding: 6px 14px;
					border-radius: 4px;
					font-size: 0.85em;
					font-weight: 600;
				}
			}

			.trivia-preview-features {
				display: flex;
				flex-wrap: wrap;
				justify-content: center;
				gap: 10px;

				span {
					background: var(--wp--preset--color--base, #fff);
					border: 1px solid var(--wp--preset--color--contrast, #dee2e6);
					padding: 6px 12px;
					border-radius: 4px;
					font-size: 0.85em;
					color: var(--wp--preset--color--contrast, #000);
				}
			}
		}
	}
}
```

**Step 2: Build and verify**

Run: `npm run build`
Expected: Build succeeds

**Step 3: Commit**

```bash
git add src/editor.scss
git commit -m "feat(editor): add styles for settings preview badges"
```

---

## Task 4: Visual Polish - Progress Bar

**Files:**
- Modify: `src/frontend.js`
- Modify: `src/style.scss`

**Step 1: Add progress bar to QuizScreen**

In `src/frontend.js`, inside the `QuizScreen` component's return statement, add progress bar right after the opening `<div className="trivia-quiz"` and before the screen reader div:

Find this section:
```javascript
return (
	<div
		className="trivia-quiz"
		role="main"
		aria-labelledby="current-question"
	>
		{ /* Screen reader announcements */ }
```

Replace with:
```javascript
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
```

**Step 2: Add progress bar styles**

In `src/style.scss`, add after the `.trivia-challenge-container` block (around line 45):

```scss
// Progress Bar
.trivia-progress-bar {
	height: 6px;
	background: var(--wp--preset--color--contrast, rgba(0, 0, 0, 0.1));
	border-radius: 3px;
	margin-bottom: 24px;
	overflow: hidden;
}

.trivia-progress-fill {
	height: 100%;
	background: var(--wp--preset--color--contrast, #000);
	border-radius: 3px;
	transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
```

**Step 3: Build and verify**

Run: `npm run build`
Expected: Build succeeds

**Step 4: Commit**

```bash
git add src/frontend.js src/style.scss
git commit -m "feat(visual): add progress bar to quiz screen"
```

---

## Task 5: Visual Polish - Score Pop Animation

**Files:**
- Modify: `src/frontend.js`
- Modify: `src/style.scss`

**Step 1: Add state for score animation**

In `QuizScreen` component, add a new state after the existing states (around line 421):

```javascript
const [ scoreAnimating, setScoreAnimating ] = useState( false );
```

**Step 2: Trigger animation on correct answer**

In the `handleAnswerClick` function, inside the `if ( isCorrect )` block, after `setScore( score + totalPoints );`, add:

```javascript
setScoreAnimating( true );
setTimeout( () => setScoreAnimating( false ), 300 );
```

**Step 3: Apply animation class to score display**

Find the score display div (around line 578):
```javascript
<div className="trivia-score-value" aria-live="polite">
	{ score }
</div>
```

Replace with:
```javascript
<div className={ `trivia-score-value${ scoreAnimating ? ' score-pop' : '' }` } aria-live="polite">
	{ score }
</div>
```

**Step 4: Add CSS animation**

In `src/style.scss`, add after the `@keyframes pulse` block:

```scss
@keyframes scorePop {
	0% { transform: scale(1); }
	50% { transform: scale(1.2); }
	100% { transform: scale(1); }
}

.trivia-score-value.score-pop {
	animation: scorePop 0.3s ease-out;
}
```

**Step 5: Build and verify**

Run: `npm run build`
Expected: Build succeeds

**Step 6: Commit**

```bash
git add src/frontend.js src/style.scss
git commit -m "feat(visual): add score pop animation on correct answer"
```

---

## Task 6: Visual Polish - Correct Answer Pulse and Checkmark

**Files:**
- Modify: `src/style.scss`

**Step 1: Enhance correct answer button styles**

In `src/style.scss`, find the `.trivia-answer-btn` block and replace the `&.correct` section with:

```scss
&.correct {
	background: #28a745;
	color: white;
	border-color: #28a745;
	animation: correctPulse 0.4s ease-out;

	&::after {
		content: '✓';
		position: absolute;
		right: 20px;
		font-size: 1.3em;
		font-weight: bold;
		opacity: 0;
		animation: checkmarkFadeIn 0.3s ease-out 0.1s forwards;
	}
}
```

**Step 2: Add new animations**

Add after the `@keyframes shake` block:

```scss
@keyframes correctPulse {
	0% { transform: scale(1); }
	30% { transform: scale(1.02); }
	100% { transform: scale(1); }
}

@keyframes checkmarkFadeIn {
	from {
		opacity: 0;
		transform: scale(0.5);
	}
	to {
		opacity: 1;
		transform: scale(1);
	}
}
```

**Step 3: Remove the old ::before from correct (if present)**

The old code had `&::before { content: '✓'; ... }`. We're replacing it with `&::after` that has animation. Make sure the old `::before` is removed from the `&.correct` block.

**Step 4: Build and verify**

Run: `npm run build`
Expected: Build succeeds

**Step 5: Commit**

```bash
git add src/style.scss
git commit -m "feat(visual): add pulse animation and animated checkmark for correct answers"
```

---

## Task 7: Visual Polish - Streak Glow Effect

**Files:**
- Modify: `src/frontend.js`
- Modify: `src/style.scss`

**Step 1: Add streak animation state**

In `QuizScreen`, add state after scoreAnimating:

```javascript
const [ streakAnimating, setStreakAnimating ] = useState( false );
```

**Step 2: Trigger streak animation**

In `handleAnswerClick`, in the `if ( isCorrect )` block, after `setStreak( streak + 1 );`, add:

```javascript
setStreakAnimating( true );
setTimeout( () => setStreakAnimating( false ), 400 );
```

**Step 3: Apply animation class**

Find the streak display (around line 599-608):
```javascript
{ settings.showStreak && streak > 0 && (
	<div className="trivia-score-item trivia-streak">
```

Replace with:
```javascript
{ settings.showStreak && streak > 0 && (
	<div className={ `trivia-score-item trivia-streak${ streakAnimating ? ' streak-glow' : '' }${ streak >= 5 ? ' streak-fire' : '' }` }>
```

**Step 4: Add streak glow and fire styles**

In `src/style.scss`, after the `.trivia-streak` block, add:

```scss
.trivia-streak.streak-glow {
	animation: streakGlow 0.4s ease-out;
}

.trivia-streak.streak-fire {
	background: linear-gradient(135deg, #ffc107 0%, #ff6b35 50%, #ffc107 100%) !important;
	background-size: 200% 200% !important;
	animation: fireGradient 1.5s ease infinite !important;
}

@keyframes streakGlow {
	0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
	50% { box-shadow: 0 0 20px 10px rgba(255, 193, 7, 0.4); }
	100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
}

@keyframes fireGradient {
	0% { background-position: 0% 50%; }
	50% { background-position: 100% 50%; }
	100% { background-position: 0% 50%; }
}
```

**Step 5: Build and verify**

Run: `npm run build`
Expected: Build succeeds

**Step 6: Commit**

```bash
git add src/frontend.js src/style.scss
git commit -m "feat(visual): add streak glow and fire effect for high streaks"
```

---

## Task 8: Visual Polish - Timer Bar

**Files:**
- Modify: `src/frontend.js`
- Modify: `src/style.scss`

**Step 1: Replace timer number with visual bar**

In `QuizScreen`, find the timer display section (around line 582-598):

```javascript
{ settings.showTimer && (
	<div className="trivia-score-item">
		<div className="trivia-score-label">
			{ __( 'Timer', 'trivia-challenge-block' ) }
		</div>
		<div
			className={ `trivia-score-value ${
				timeLeft <= 5 ? 'trivia-timer-warning' : ''
			}` }
			role="timer"
			aria-live="assertive"
			aria-atomic="true"
		>
			{ timeLeft }s
		</div>
	</div>
) }
```

Replace with:

```javascript
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
```

**Step 2: Add timer bar styles**

In `src/style.scss`, add after the timer warning styles:

```scss
.trivia-timer-item {
	.trivia-timer-container {
		position: relative;
		height: 32px;
		background: var(--wp--preset--color--base, #e9ecef);
		border-radius: 4px;
		overflow: hidden;
	}

	.trivia-timer-bar {
		position: absolute;
		top: 0;
		left: 0;
		height: 100%;
		background: var(--wp--preset--color--contrast, #000);
		border-radius: 4px;
		transition: width 0.3s linear;

		&.trivia-timer-warning {
			background: #dc3545;
			animation: pulse 0.5s ease-in-out infinite;
		}
	}

	.trivia-timer-text {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		font-size: 1em;
		font-weight: 700;
		color: var(--wp--preset--color--contrast, #000);
		z-index: 1;
		mix-blend-mode: difference;
	}
}
```

**Step 3: Remove old .trivia-score-value.trivia-timer-warning styling**

Find and remove this from the `.trivia-score-value` block:

```scss
&.trivia-timer-warning {
	color: #dc3545;
	animation: pulse 0.5s ease-in-out infinite;
}
```

**Step 4: Build and verify**

Run: `npm run build`
Expected: Build succeeds

**Step 5: Commit**

```bash
git add src/frontend.js src/style.scss
git commit -m "feat(visual): replace timer number with animated bar visualization"
```

---

## Task 9: Visual Polish - Screen Transitions

**Files:**
- Modify: `src/style.scss`

**Step 1: Add screen transition animations**

In `src/style.scss`, add after the container styles:

```scss
// Screen transitions
.trivia-setup,
.trivia-quiz,
.trivia-results {
	animation: screenFadeIn 0.3s ease-out;
}

@keyframes screenFadeIn {
	from {
		opacity: 0;
		transform: translateY(10px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}
```

**Step 2: Build and verify**

Run: `npm run build`
Expected: Build succeeds

**Step 3: Commit**

```bash
git add src/style.scss
git commit -m "feat(visual): add fade-in transitions for screen changes"
```

---

## Task 10: Leaderboard - Storage Helpers

**Files:**
- Modify: `src/frontend.js`

**Step 1: Add leaderboard storage constants and helpers**

After the existing localStorage helpers (around line 193), add:

```javascript
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
```

**Step 2: Build and verify**

Run: `npm run build`
Expected: Build succeeds

**Step 3: Commit**

```bash
git add src/frontend.js
git commit -m "feat(leaderboard): add localStorage helpers for leaderboard"
```

---

## Task 11: Leaderboard - SetupScreen UI

**Files:**
- Modify: `src/frontend.js`
- Modify: `src/style.scss`

**Step 1: Add leaderboard state to SetupScreen**

In `SetupScreen` component, after the existing useState calls, add:

```javascript
const [ leaderboard, setLeaderboard ] = useState( {} );
const [ showClearConfirm, setShowClearConfirm ] = useState( false );

useEffect( () => {
	setLeaderboard( loadLeaderboard() );
}, [] );

const handleClearLeaderboard = () => {
	clearLeaderboard();
	setLeaderboard( {} );
	setShowClearConfirm( false );
};
```

**Step 2: Add leaderboard display**

In `SetupScreen`, after the button group div and before the closing `</div>` of `trivia-setup`, add:

```javascript
{ Object.keys( leaderboard ).length > 0 && (
	<div className="trivia-leaderboard">
		<h3 className="trivia-leaderboard-title">
			{ __( 'Your Best Scores', 'trivia-challenge-block' ) }
		</h3>
		<div className="trivia-leaderboard-list">
			{ Object.entries( leaderboard ).map( ( [ category, data ] ) => {
				const percentage = Math.round( ( data.score / data.total ) * 100 );
				const isPerfect = percentage === 100;
				return (
					<div key={ category } className="trivia-leaderboard-item">
						<span className="trivia-leaderboard-category">
							{ isPerfect && <span className="trivia-perfect-badge">⭐</span> }
							{ category.charAt( 0 ).toUpperCase() + category.slice( 1 ) }
						</span>
						<span className="trivia-leaderboard-score">
							{ data.score }/{ data.total }
							<span className="trivia-leaderboard-difficulty">
								({ data.difficulty })
							</span>
						</span>
					</div>
				);
			} ) }
		</div>
		{ showClearConfirm ? (
			<div className="trivia-clear-confirm">
				<span>{ __( 'Clear all scores?', 'trivia-challenge-block' ) }</span>
				<button
					className="trivia-btn-link"
					onClick={ handleClearLeaderboard }
				>
					{ __( 'Yes', 'trivia-challenge-block' ) }
				</button>
				<button
					className="trivia-btn-link"
					onClick={ () => setShowClearConfirm( false ) }
				>
					{ __( 'No', 'trivia-challenge-block' ) }
				</button>
			</div>
		) : (
			<button
				className="trivia-btn-link trivia-clear-scores"
				onClick={ () => setShowClearConfirm( true ) }
			>
				{ __( 'Clear Scores', 'trivia-challenge-block' ) }
			</button>
		) }
	</div>
) }
```

**Step 3: Add leaderboard styles**

In `src/style.scss`, add before the responsive media queries:

```scss
// Leaderboard
.trivia-leaderboard {
	margin-top: 40px;
	padding-top: 32px;
	border-top: 1px solid var(--wp--preset--color--contrast, rgba(0, 0, 0, 0.1));
}

.trivia-leaderboard-title {
	font-size: 1.1em;
	font-weight: 600;
	color: var(--wp--preset--color--contrast, #000);
	margin-bottom: 16px;
	text-align: center;
}

.trivia-leaderboard-list {
	display: flex;
	flex-direction: column;
	gap: 8px;
}

.trivia-leaderboard-item {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 12px 16px;
	background: var(--wp--preset--color--base, #f8f9fa);
	border-radius: 4px;
	border: 1px solid var(--wp--preset--color--contrast, rgba(0, 0, 0, 0.1));
}

.trivia-leaderboard-category {
	font-weight: 600;
	color: var(--wp--preset--color--contrast, #000);
	display: flex;
	align-items: center;
	gap: 8px;
}

.trivia-perfect-badge {
	font-size: 1.2em;
}

.trivia-leaderboard-score {
	font-weight: 700;
	color: var(--wp--preset--color--contrast, #000);
}

.trivia-leaderboard-difficulty {
	font-weight: 400;
	font-size: 0.85em;
	opacity: 0.7;
	margin-left: 6px;
}

.trivia-btn-link {
	background: none;
	border: none;
	color: var(--wp--preset--color--contrast, #666);
	font-size: 0.9em;
	cursor: pointer;
	padding: 4px 8px;
	text-decoration: underline;
	font-family: inherit;

	&:hover {
		color: var(--wp--preset--color--contrast, #000);
	}
}

.trivia-clear-scores {
	display: block;
	margin: 16px auto 0;
	opacity: 0.7;
}

.trivia-clear-confirm {
	display: flex;
	justify-content: center;
	align-items: center;
	gap: 12px;
	margin-top: 16px;
	font-size: 0.9em;
}
```

**Step 4: Build and verify**

Run: `npm run build`
Expected: Build succeeds

**Step 5: Commit**

```bash
git add src/frontend.js src/style.scss
git commit -m "feat(leaderboard): add best scores display on setup screen"
```

---

## Task 12: Leaderboard - Results Screen Integration

**Files:**
- Modify: `src/frontend.js`

**Step 1: Add props to ResultsScreen**

Update ResultsScreen to accept category and difficulty props. Find the component definition:

```javascript
const ResultsScreen = ( { correctAnswers, totalQuestions, onRestart } ) => {
```

Change to:

```javascript
const ResultsScreen = ( { correctAnswers, totalQuestions, onRestart, category, difficulty } ) => {
	const [ isNewBest, setIsNewBest ] = useState( false );

	useEffect( () => {
		if ( category ) {
			const newBest = updateLeaderboard( category, correctAnswers, totalQuestions, difficulty );
			setIsNewBest( newBest );
		}
	}, [ category, correctAnswers, totalQuestions, difficulty ] );
```

**Step 2: Add "New Best!" badge to results**

In ResultsScreen, after the `<div className="trivia-results-score"` section and before percentage, add:

```javascript
{ isNewBest && (
	<div className="trivia-new-best" aria-live="polite">
		{ __( '🎉 New Best Score!', 'trivia-challenge-block' ) }
	</div>
) }
```

**Step 3: Track category and difficulty in TriviaApp**

In `TriviaApp`, add state for quiz metadata:

```javascript
const [ quizMeta, setQuizMeta ] = useState( { category: null, difficulty: null } );
```

**Step 4: Set metadata on quiz start**

In `handleStart`, after `setQuestions( fetchedQuestions );`, add:

```javascript
setQuizMeta( { category, difficulty } );
```

**Step 5: Pass metadata to ResultsScreen**

Find the ResultsScreen render:

```javascript
{ screen === 'results' && (
	<ResultsScreen
		correctAnswers={ results.correct }
		totalQuestions={ results.total }
		onRestart={ handleRestart }
	/>
) }
```

Change to:

```javascript
{ screen === 'results' && (
	<ResultsScreen
		correctAnswers={ results.correct }
		totalQuestions={ results.total }
		onRestart={ handleRestart }
		category={ quizMeta.category }
		difficulty={ quizMeta.difficulty }
	/>
) }
```

**Step 6: Add "New Best" styles**

In `src/style.scss`, add in the results section:

```scss
.trivia-new-best {
	font-size: 1.2em;
	font-weight: 700;
	color: #28a745;
	margin: 16px 0;
	animation: newBestPop 0.5s ease-out;
}

@keyframes newBestPop {
	0% { transform: scale(0.5); opacity: 0; }
	50% { transform: scale(1.1); }
	100% { transform: scale(1); opacity: 1; }
}
```

**Step 7: Build and verify**

Run: `npm run build`
Expected: Build succeeds

**Step 8: Commit**

```bash
git add src/frontend.js src/style.scss
git commit -m "feat(leaderboard): save scores and show 'New Best' on results screen"
```

---

## Task 13: Sound Effects - Add Sound Files

**Files:**
- Create: `assets/sounds/correct.mp3`
- Create: `assets/sounds/wrong.mp3`
- Create: `assets/sounds/complete.mp3`

**Step 1: Create assets directory**

```bash
mkdir -p assets/sounds
```

**Step 2: Download royalty-free sounds**

Download these free sounds (or similar):
- Correct: A pleasant short chime (search "correct answer sound effect free" on freesound.org)
- Wrong: A soft buzz or thud (search "wrong answer sound effect free")
- Complete: A brief success jingle (search "quiz complete sound effect free")

Save them as MP3 files in `assets/sounds/`.

Note: For this plan, we'll use placeholder files initially. The sounds should be:
- Under 50KB each
- Short duration (0.3-1 second)
- Mixed at appropriate levels

**Step 3: Commit**

```bash
git add assets/sounds/
git commit -m "feat(sound): add sound effect audio files"
```

---

## Task 14: Sound Effects - Sound Playback Utility

**Files:**
- Modify: `src/frontend.js`

**Step 1: Add sound utility functions**

After the leaderboard helpers, add:

```javascript
/**
 * Sound effect utilities
 */
const soundCache = {};

const preloadSound = ( name, url ) => {
	if ( ! soundCache[ name ] ) {
		const audio = new Audio( url );
		audio.preload = 'auto';
		soundCache[ name ] = audio;
	}
	return soundCache[ name ];
};

const playSound = ( name, settings ) => {
	if ( ! settings.enableSound ) {
		return;
	}

	const audio = soundCache[ name ];
	if ( audio ) {
		audio.currentTime = 0;
		audio.play().catch( ( error ) => {
			// Browser may block autoplay before user interaction
			console.warn( 'Sound playback failed:', error );
		} );
	}
};

const getSoundUrl = ( filename ) => {
	// Get the plugin URL from settings or construct from current script
	const settings = getSettings();
	if ( settings.pluginUrl ) {
		return `${ settings.pluginUrl }assets/sounds/${ filename }`;
	}
	// Fallback: try to detect from current script location
	const scripts = document.querySelectorAll( 'script[src*="trivia-challenge"]' );
	if ( scripts.length > 0 ) {
		const src = scripts[ 0 ].src;
		const baseUrl = src.substring( 0, src.lastIndexOf( '/build/' ) + 1 );
		return `${ baseUrl }assets/sounds/${ filename }`;
	}
	return '';
};
```

**Step 2: Build and verify**

Run: `npm run build`
Expected: Build succeeds

**Step 3: Commit**

```bash
git add src/frontend.js
git commit -m "feat(sound): add sound playback utility functions"
```

---

## Task 15: Sound Effects - Integrate with Quiz

**Files:**
- Modify: `src/frontend.js`

**Step 1: Preload sounds in TriviaApp**

In `TriviaApp`, add useEffect to preload sounds:

```javascript
// Preload sounds
useEffect( () => {
	if ( settings.enableSound ) {
		preloadSound( 'correct', getSoundUrl( 'correct.mp3' ) );
		preloadSound( 'wrong', getSoundUrl( 'wrong.mp3' ) );
		preloadSound( 'complete', getSoundUrl( 'complete.mp3' ) );
	}
}, [ settings.enableSound ] );
```

**Step 2: Play sounds in QuizScreen**

In `handleAnswerClick`, play correct/wrong sounds. In the `if ( isCorrect )` block, add:

```javascript
playSound( 'correct', settings );
```

In the `else` block (wrong answer), add:

```javascript
playSound( 'wrong', settings );
```

**Step 3: Play complete sound in ResultsScreen**

In `ResultsScreen`, add useEffect to play completion sound:

```javascript
useEffect( () => {
	const settings = getSettings();
	playSound( 'complete', settings );
}, [] );
```

**Step 4: Build and verify**

Run: `npm run build`
Expected: Build succeeds

**Step 5: Commit**

```bash
git add src/frontend.js
git commit -m "feat(sound): integrate sound effects with quiz flow"
```

---

## Task 16: Sound Effects - Add Plugin URL to Settings

**Files:**
- Modify: `includes/class-trivia-challenge-block.php`

**Step 1: Add pluginUrl to localized settings**

Find the `wp_localize_script` call in the PHP file. Add `pluginUrl` to the settings array:

```php
'pluginUrl' => plugin_dir_url( dirname( __FILE__ ) ),
```

**Step 2: Commit**

```bash
git add includes/class-trivia-challenge-block.php
git commit -m "feat(sound): add plugin URL to frontend settings for sound file paths"
```

---

## Task 17: Final Build and Test

**Files:**
- All modified files

**Step 1: Full rebuild**

```bash
npm run build
```

Expected: Build succeeds with no errors

**Step 2: Lint check**

```bash
npm run lint:js
npm run lint:css
```

Fix any linting errors if present.

**Step 3: Manual testing checklist**

Test in WordPress:
- [ ] Editor: Block shows settings in sidebar
- [ ] Editor: Changing settings updates preview
- [ ] Frontend: Progress bar visible during quiz
- [ ] Frontend: Score pops on correct answer
- [ ] Frontend: Correct answer has pulse + checkmark animation
- [ ] Frontend: Streak glows on increment, fire effect at 5+
- [ ] Frontend: Timer shows as bar with smooth countdown
- [ ] Frontend: Screens fade in smoothly
- [ ] Frontend: Leaderboard shows on setup screen after completing a quiz
- [ ] Frontend: "New Best!" shows when beating previous score
- [ ] Frontend: Sounds play when enabled (after user clicks)

**Step 4: Final commit**

```bash
git add .
git commit -m "chore: final build and lint fixes"
```

---

## Summary

| Task | Description |
|------|-------------|
| 1-3 | Editor Controls - imports, edit component, styles |
| 4-9 | Visual Polish - progress bar, score pop, correct pulse, streak glow, timer bar, transitions |
| 10-12 | Leaderboard - storage helpers, setup UI, results integration |
| 13-16 | Sound Effects - files, playback utility, quiz integration, PHP settings |
| 17 | Final build and test |

Total: 17 tasks, each with explicit steps, code, and commit points.
