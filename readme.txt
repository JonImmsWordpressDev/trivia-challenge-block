=== Trivia Challenge Block ===
Contributors: jonimms
Tags: trivia, quiz, gutenberg, block, game, education, interactive, knowledge, entertainment
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add an interactive trivia quiz to your WordPress site with 11 categories, multiple difficulty levels, and real-time scoring.

== Description ==

**Trivia Challenge Block** brings interactive trivia quizzes to your WordPress website through a beautiful, responsive Gutenberg block. Perfect for educational sites, entertainment blogs, or anyone looking to engage their audience with fun, challenging questions.

= Key Features =

* **11 Quiz Categories** - Mixed, General Knowledge, Science & Nature, History, Geography, Entertainment, Sports, Computers, Mathematics, Mythology, and Animals
* **Multiple Difficulty Levels** - Easy, Medium, Hard, or Mixed difficulty
* **4,000+ Questions** - Powered by the Open Trivia Database API with offline fallback
* **Real-Time Scoring** - Track scores with time bonuses and streak multipliers
* **Timer System** - 20-second countdown per question adds excitement
* **Streak Bonuses** - Reward consecutive correct answers with bonus points
* **Fully Responsive** - Works perfectly on desktop, tablet, and mobile devices
* **Accessible** - Keyboard navigation and screen reader friendly
* **Customizable** - Future support for custom colors and settings
* **No Account Required** - No API keys or external accounts needed
* **Privacy Focused** - No tracking or personal data collection
* **Lightweight** - Fast loading with optimized assets

= Perfect For =

* Educational websites and online courses
* Entertainment and gaming blogs
* Corporate training and team building
* Student engagement and learning
* Blog content enhancement
* Community interaction
* Knowledge testing and assessment

= How It Works =

1. Add the **Trivia Challenge** block to any page or post
2. Visitors select their preferred category and difficulty
3. Answer 10 questions with a 20-second timer per question
4. Get immediate feedback on each answer
5. See final results with personalized messages
6. Play again to improve scores

= Powered by Open Trivia Database =

Questions are fetched from the free Open Trivia Database API, ensuring fresh and varied content. The plugin includes offline fallback questions for uninterrupted gameplay.

= Developer Friendly =

* Clean, modern code following WordPress standards
* React-based frontend for smooth interactions
* Comprehensive hooks and filters for customization
* WP-CLI commands for advanced management
* Transient caching for optimal performance
* Well-documented and extensible

= Upcoming Features =

* Leaderboards and high scores
* Custom question import
* Shortcode support
* Multiple quiz instances per page
* Social sharing
* Color customization
* Admin dashboard with statistics

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin dashboard
2. Navigate to **Plugins > Add New**
3. Search for "Trivia Challenge Block"
4. Click **Install Now** next to the Trivia Challenge Block plugin
5. Click **Activate** once installation is complete

= Manual Installation =

1. Download the plugin zip file
2. Log in to your WordPress admin dashboard
3. Navigate to **Plugins > Add New > Upload Plugin**
4. Choose the downloaded zip file
5. Click **Install Now**
6. Click **Activate Plugin**

= After Activation =

1. Create or edit a page/post
2. Click the **+** (Add Block) button in the editor
3. Search for "Trivia Challenge" or find it in the **Trivia Challenge** category
4. Click to insert the block
5. Publish your page and view it on the frontend
6. Enjoy your interactive trivia quiz!

== Frequently Asked Questions ==

= Do I need an API key to use this plugin? =

No! The plugin uses the free Open Trivia Database API which doesn't require any API keys or accounts. It works out of the box.

= What happens if the API is down? =

The plugin includes offline fallback questions to ensure your quiz always works, even if the external API is temporarily unavailable.

= Can I add my own custom questions? =

Custom question import is planned for a future release. Currently, questions come from the Open Trivia Database API and built-in fallback questions.

= Is the quiz mobile-friendly? =

Yes! The quiz is fully responsive and works beautifully on all devices including smartphones and tablets.

= Can I customize the colors? =

Color customization will be added in a future update through the block settings panel.

= How many questions are in each quiz? =

Each quiz session includes 10 questions. This provides a good balance between engagement and completion time.

= Can I have multiple quizzes on the same page? =

Multiple quiz instances per page will be supported in a future update.

= Does this work with the classic editor? =

This plugin requires the Gutenberg block editor. Shortcode support for the classic editor is planned for a future release.

= Is this plugin GDPR compliant? =

Yes! The plugin doesn't collect, store, or transmit any personal data. Quiz results are stored only in the browser's memory and are lost when the page is closed.

= Can I see quiz statistics? =

An admin dashboard with quiz statistics and analytics is planned for a future release.

= Does this plugin slow down my site? =

No! The plugin is optimized for performance with conditional script loading and caching. Scripts only load on pages where the block is used.

= What browsers are supported? =

All modern browsers are supported: Chrome, Firefox, Safari, Edge, and their mobile versions.

= Can I translate this plugin? =

Yes! The plugin is fully internationalized and ready for translation. Translation files are included.

== Screenshots ==

1. Category and difficulty selection screen
2. Quiz in progress with timer and score tracking
3. Answer feedback with correct/incorrect highlighting
4. Final results screen with personalized message
5. Block editor preview showing the Trivia Challenge block
6. Responsive mobile view of the quiz

== Changelog ==

= 2.0.0 - 2025-01-22 =
* Major update with WordPress.org compliance improvements
* Added: Complete internationalization support
* Added: Server-side API proxy with transient caching
* Added: Admin settings page for configuration
* Added: Accessibility improvements (ARIA labels, live regions)
* Added: Quiz state persistence with localStorage
* Added: WP-CLI command support
* Added: Uninstall cleanup functionality
* Improved: Modern block.json registration
* Improved: Timer implementation for better performance
* Improved: Error handling and fallback system
* Improved: Code quality and WordPress Coding Standards compliance
* Fixed: Version consistency across all files
* Fixed: Asset dependency handling
* Fixed: Security enhancements

= 1.0.0 - 2024-11-21 =
* Initial release
* 11 quiz categories
* Multiple difficulty levels
* Open Trivia Database API integration
* Offline fallback questions
* Real-time scoring with timer
* Streak bonus system
* Fully responsive design
* Accessible keyboard navigation

== Upgrade Notice ==

= 2.0.0 =
Major update with performance improvements, accessibility enhancements, and WordPress.org compliance. Recommended for all users.

= 1.0.0 =
Initial release of Trivia Challenge Block.

== Privacy Policy ==

This plugin does not collect, store, or transmit any personal data. Quiz results are stored only in the browser's local memory and are not sent to any server. The plugin uses the Open Trivia Database API to fetch questions, which has its own privacy policy available at https://opentdb.com/.

== Support ==

For support, feature requests, or bug reports, please visit:
* Plugin support forum: https://wordpress.org/support/plugin/trivia-challenge-block/
* GitHub repository: [Your GitHub URL]

== Credits ==

* Questions provided by Open Trivia Database (https://opentdb.com/)
* Developed by Jon Imms (https://jonimms.com)

== Developer Resources ==

= Hooks & Filters =

`trivia_challenge_api_cache_duration` - Filter the API cache duration (default: 1 hour)
`trivia_challenge_questions_per_quiz` - Filter the number of questions per quiz (default: 10)
`trivia_challenge_timer_duration` - Filter the timer duration per question (default: 20 seconds)
`trivia_challenge_fallback_questions` - Filter the fallback questions array

= WP-CLI Commands =

`wp trivia-challenge clear-cache` - Clear the API question cache
`wp trivia-challenge test-api` - Test the Open Trivia Database API connection
`wp trivia-challenge stats` - View plugin usage statistics

= Constants =

`TRIVIA_CHALLENGE_BLOCK_VERSION` - Plugin version number
`TRIVIA_CHALLENGE_BLOCK_PATH` - Plugin directory path
`TRIVIA_CHALLENGE_BLOCK_URL` - Plugin directory URL

For more developer documentation, visit the GitHub repository.
