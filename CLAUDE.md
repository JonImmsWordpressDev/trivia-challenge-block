# Trivia Challenge Block - Development Notes

## Planned Improvements (Brainstormed Jan 2026)

### Visual Design
- **Direction**: Modern polish with gamification elements
- Smoother animations, micro-interactions, sophisticated visual hierarchy
- Progress bars, achievement feedback, celebratory effects

### New Features
- **Leaderboard system** using browser localStorage
- Shows "Your Best Scores" per device
- No backend changes required - simple and self-contained

### Editor Experience
- **Full settings panel** with InspectorControls sidebar
- Expose all block attributes: defaultCategory, defaultDifficulty, questionsPerQuiz, timerDuration, showTimer, showStreak, enableSound
- Currently the edit component is just a static preview with no controls

### Performance/Bugs
- To be assessed during implementation

## Technical Context

### Current Architecture
- React frontend with WordPress Element
- Fetches from Open Trivia DB API via WordPress REST proxy (`/wp-json/trivia-challenge/v1/questions`)
- Fallback questions when API unavailable
- Caching via WordPress transients (1 hour default)
- State persistence via localStorage for resume functionality

### Key Files
- `src/frontend.js` - Main React app (SetupScreen, QuizScreen, ResultsScreen)
- `src/index.js` - Block registration and editor component
- `src/style.scss` - Frontend styles
- `includes/class-trivia-challenge-api.php` - REST API and caching
- `includes/class-trivia-challenge-admin.php` - Admin settings

### Block Attributes (from block.json)
- `defaultCategory` (string, default: "mixed")
- `defaultDifficulty` (string, default: "medium")
- `questionsPerQuiz` (number, default: 10)
- `timerDuration` (number, default: 20)
- `showTimer` (boolean, default: true)
- `showStreak` (boolean, default: true)
- `enableSound` (boolean, default: false)
