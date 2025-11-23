# Trivia Challenge Block

A custom WordPress Gutenberg block that adds an interactive general knowledge trivia quiz to your website.

## Features

- 🎯 **6 Quiz Categories**: Mixed, Science & Nature, History, Geography, Entertainment, and Sports
- 📚 **150+ Questions**: Curated trivia questions across all categories
- 🎨 **Customizable Colors**: Change primary and secondary colors in the block settings
- 📊 **Score Tracking**: Real-time scoring with visual feedback
- 📱 **Fully Responsive**: Works perfectly on desktop, tablet, and mobile devices
- ⚡ **Fast & Lightweight**: Built with React and optimized for performance
- ♿ **Accessible**: Keyboard navigation and screen reader friendly

## Installation

### Method 1: Upload via WordPress Admin

1. Download the plugin folder
2. Zip the `trivia-challenge-block` folder
3. In WordPress admin, go to **Plugins > Add New > Upload Plugin**
4. Choose the zip file and click **Install Now**
5. Activate the plugin

### Method 2: Manual Installation

1. Upload the `trivia-challenge-block` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress

### Method 3: Development Setup

If you want to modify the block:

```bash
cd wp-content/plugins/trivia-challenge-block
npm install
npm run start
```

For production build:
```bash
npm run build
```

## Usage

1. Create or edit a page/post in WordPress
2. Click the **+** button to add a new block
3. Search for **"Trivia Challenge"** or find it in the **Trivia Challenge** category
4. Insert the block into your page
5. (Optional) Customize the colors in the block settings sidebar
6. Publish your page

## Customization

### Changing Colors

You can customize the quiz colors directly in the block editor:

1. Select the Trivia Challenge block
2. Open the **Block Settings** sidebar (right panel)
3. Expand the **Color Settings** panel
4. Choose your **Primary Color** and **Secondary Color**

### Adding More Questions

To add more questions, edit the `src/frontend.js` file and add questions to the `triviaQuestions` object:

```javascript
const triviaQuestions = {
    mixed: [
        { 
            q: "Your question here?", 
            a: ["Correct Answer", "Wrong 1", "Wrong 2", "Wrong 3"], 
            correct: 0 
        },
        // Add more questions...
    ],
    // Other categories...
};
```

After adding questions, rebuild the plugin:
```bash
npm run build
```

### Creating New Categories

1. Add a new category to the `triviaQuestions` object in `src/frontend.js`
2. Add the category option in `src/frontend.js` in the SetupScreen component
3. Rebuild the plugin

## File Structure

```
trivia-challenge-block/
├── build/                  # Compiled files (generated)
├── src/
│   ├── index.js           # Block registration & editor
│   ├── frontend.js        # React frontend component
│   ├── editor.scss        # Editor styles
│   └── style.scss         # Frontend styles
├── package.json           # Dependencies
├── trivia-challenge-block.php  # Main plugin file
└── README.md             # This file
```

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- Modern browser with JavaScript enabled

## Development

### Build Commands

- `npm run start` - Start development mode with hot reload
- `npm run build` - Build for production
- `npm run format` - Format code
- `npm run lint:js` - Lint JavaScript files
- `npm run lint:css` - Lint CSS/SCSS files

### Technologies Used

- React
- WordPress Block Editor (Gutenberg)
- SASS/SCSS
- @wordpress/scripts

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## License

GPL-2.0+

## Author

Jon Imms - [jonimms.com](https://jonimms.com)

## Support

For issues or questions, please visit [your support page or GitHub issues]

## Changelog

### 1.0.0
- Initial release
- 6 quiz categories
- 150+ questions
- Customizable colors
- Fully responsive design
- Score tracking
