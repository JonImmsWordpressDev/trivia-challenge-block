# Trivia Challenge Block - Feature Updates Design

**Date:** 2026-01-29
**Status:** Approved

## Overview

Four feature updates to improve the trivia block's editor experience, visual polish, engagement, and audio feedback.

## Features

### 1. Editor Controls (Sidebar Only)

Add InspectorControls to `src/index.js` with two panels:

**Quiz Settings Panel:**
- Category dropdown (defaultCategory) - 11 categories + "Mixed"
- Difficulty dropdown (defaultDifficulty) - Easy, Medium, Hard, Any
- Questions per quiz (questionsPerQuiz) - NumberControl, range 5-20

**Display Settings Panel:**
- Timer duration (timerDuration) - NumberControl, range 10-60 seconds
- Show timer toggle (showTimer)
- Show streak toggle (showStreak)
- Enable sound toggle (enableSound)

**Editor Preview:**
Update the preview card to reflect current settings (e.g., "10 questions - Medium - Timer: 20s").

### 2. Visual Polish (Subtle Animations)

**Correct Answer Feedback:**
- Smooth green background transition (0.3s ease)
- Gentle scale pulse (1.0 → 1.02 → 1.0)
- Score number "pop" animation on increment
- Checkmark icon fade-in next to correct answer

**Wrong Answer Feedback:**
- Red background with refined shake animation
- Correct answer highlights green simultaneously

**Screen Transitions:**
- Fade/slide between Setup → Quiz → Results (0.3s)
- Questions crossfade rather than hard-swap

**Progress Indication:**
- Thin progress bar at top of quiz screen
- Smooth width transition as user progresses

**Timer Polish:**
- Bar or circular visualization
- Smooth countdown animation
- Refined pulse at ≤5 seconds

**Streak Feedback:**
- Brief glow/highlight on increment
- Subtle gradient animation at streak ≥5

### 3. Leaderboard System

**Storage:**
- Key: `trivia_challenge_leaderboard`
- Structure: Object keyed by category
- Per-category data: score, total, difficulty, date

```json
{
  "science": { "score": 9, "total": 10, "difficulty": "hard", "date": "2026-01-29T..." },
  "history": { "score": 7, "total": 10, "difficulty": "medium", "date": "2026-01-28T..." }
}
```

**UI (SetupScreen):**
- "Your Best Scores" section below category/difficulty selectors
- List categories played with best score
- Star/checkmark for perfect scores
- "Clear Scores" link with confirmation
- Only show categories that have been played

**Update Logic:**
- Check/save on quiz completion
- "New Best!" badge on ResultsScreen when record beaten

### 4. Sound Effects

**Three sounds:**
1. Correct answer - pleasant chime (~0.3s)
2. Wrong answer - soft buzz/thud (~0.3s)
3. Quiz complete - success fanfare (~1s)

**Implementation:**
- Sound files in `assets/sounds/` directory (MP3 format)
- Load on demand, play when `enableSound` is true
- Respect browser autoplay policy (require user interaction)
- Source: royalty-free sounds (freesound.org or similar)

## Out of Scope

- Adaptive difficulty progression
- Share results / copy to clipboard
- Toolbar or inline editor controls
- Playful animations (confetti, bounces, emoji)
- Extended audio (timer ticks, button clicks, milestone sounds)

## Files to Modify

- `src/index.js` - Add InspectorControls, update edit component
- `src/frontend.js` - Leaderboard logic, sound playback, animation triggers
- `src/style.scss` - New animations, progress bar, leaderboard styles
- `src/editor.scss` (new) - Editor preview styles
- `assets/sounds/` (new) - Three MP3 sound files
