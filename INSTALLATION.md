# Trivia Challenge Block - Installation & Setup Guide

## Quick Start Guide

### Step 1: Install Dependencies

Navigate to your plugin directory and install the required npm packages:

```bash
cd wp-content/plugins/trivia-challenge-block
npm install
```

This will install all the WordPress scripts and dependencies needed to build the block.

### Step 2: Build the Plugin

Build the production-ready files:

```bash
npm run build
```

This creates the `/build` directory with all compiled JavaScript and CSS files.

### Step 3: Activate the Plugin

1. Go to your WordPress admin dashboard
2. Navigate to **Plugins > Installed Plugins**
3. Find **Trivia Challenge Block**
4. Click **Activate**

### Step 4: Use the Block

1. Create or edit a page/post
2. Click the **+** button to add a block
3. Search for "Trivia Challenge"
4. Click to insert the block
5. Publish!

---

## Development Workflow

If you want to make changes to the block:

### Start Development Mode

```bash
npm run start
```

This will:
- Watch for file changes
- Automatically rebuild on save
- Enable hot module replacement
- Generate source maps for debugging

### Make Your Changes

Edit files in the `/src` directory:
- `index.js` - Block registration and editor interface
- `frontend.js` - Frontend React component
- `editor.scss` - Editor styles
- `style.scss` - Frontend styles

### Build for Production

When ready to deploy:

```bash
npm run build
```

---

## Customization Guide

### Changing the Default Colors

Edit `trivia-challenge-block.php`:

```php
'attributes' => array(
    'primaryColor' => array(
        'type' => 'string',
        'default' => '#667eea'  // Change this
    ),
    'secondaryColor' => array(
        'type' => 'string',
        'default' => '#764ba2'  // Change this
    )
),
```

### Adding More Questions

Edit `src/frontend.js` and add to the `triviaQuestions` object:

```javascript
const triviaQuestions = {
    mixed: [
        // Add new questions here
        { 
            q: "What is your new question?", 
            a: ["Correct Answer", "Wrong 1", "Wrong 2", "Wrong 3"], 
            correct: 0 
        },
    ],
    // ... other categories
};
```

The `correct` property is the zero-based index of the correct answer in the array.

### Creating a New Category

1. Add the category to `triviaQuestions` in `src/frontend.js`:

```javascript
const triviaQuestions = {
    // ... existing categories
    technology: [
        { q: "What does CPU stand for?", a: ["Central Processing Unit", "..."], correct: 0 },
        // More questions...
    ]
};
```

2. Add the category option in the SetupScreen component:

```javascript
<select>
    {/* ... existing options */}
    <option value="technology">Technology</option>
</select>
```

3. Rebuild:
```bash
npm run build
```

### Styling Changes

#### Frontend Styles
Edit `src/style.scss` to change the appearance on the frontend

#### Editor Styles
Edit `src/editor.scss` to change how it looks in the block editor

After making style changes, rebuild:
```bash
npm run build
```

---

## File Structure Explained

```
trivia-challenge-block/
│
├── trivia-challenge-block.php    # Main plugin file (registers block)
├── package.json                   # NPM dependencies and scripts
├── webpack.config.js              # Build configuration
├── README.md                      # Documentation
│
├── src/                           # Source files (edit these)
│   ├── index.js                  # Block registration & editor UI
│   ├── frontend.js               # React app for frontend
│   ├── editor.scss               # Editor styles
│   └── style.scss                # Frontend styles
│
└── build/                         # Compiled files (auto-generated)
    ├── index.js                  # Compiled block registration
    ├── frontend.js               # Compiled React app
    ├── editor.css                # Compiled editor styles
    ├── style.css                 # Compiled frontend styles
    └── *.asset.php               # WordPress asset files
```

---

## Troubleshooting

### Block Doesn't Appear in Editor

1. Make sure the plugin is activated
2. Clear your browser cache
3. Rebuild the plugin: `npm run build`
4. Check browser console for JavaScript errors

### Styles Not Loading

1. Rebuild: `npm run build`
2. Clear WordPress cache (if using a caching plugin)
3. Hard refresh your browser (Ctrl+Shift+R or Cmd+Shift+R)

### Build Errors

If you get errors when running `npm install` or `npm run build`:

1. Make sure you have Node.js 14+ installed:
   ```bash
   node --version
   ```

2. Clear npm cache and reinstall:
   ```bash
   npm cache clean --force
   rm -rf node_modules
   npm install
   ```

3. Try with legacy peer deps:
   ```bash
   npm install --legacy-peer-deps
   ```

### Questions Not Showing Up

1. Check that questions are properly formatted in `src/frontend.js`
2. Make sure the `correct` index is valid (0-3 for 4 answers)
3. Rebuild the plugin

---

## Advanced Customization

### Adding Block Variations

You can create preset variations (e.g., "Science Quiz Only") by adding to `src/index.js`:

```javascript
variations: [
    {
        name: 'science-only',
        title: 'Science Quiz',
        attributes: {
            defaultCategory: 'science',
            primaryColor: '#00bcd4',
            secondaryColor: '#009688'
        },
    },
],
```

### Adding Block Transforms

Allow users to transform from other blocks:

```javascript
transforms: {
    from: [
        {
            type: 'block',
            blocks: ['core/paragraph'],
            transform: () => {
                return createBlock('trivia-challenge/quiz-block');
            },
        },
    ],
},
```

### Custom Fonts

To use custom fonts, edit `src/style.scss`:

```scss
.trivia-challenge-container {
    font-family: 'Your Custom Font', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
```

Then enqueue the font in `trivia-challenge-block.php`:

```php
function trivia_challenge_enqueue_fonts() {
    wp_enqueue_style(
        'custom-font',
        'https://fonts.googleapis.com/css2?family=YourFont:wght@400;600;700&display=swap'
    );
}
add_action('wp_enqueue_scripts', 'trivia_challenge_enqueue_fonts');
```

---

## Deployment Checklist

Before deploying to production:

- [ ] Run `npm run build` to create production files
- [ ] Test on a staging site first
- [ ] Check all categories work correctly
- [ ] Test on mobile devices
- [ ] Test with different WordPress themes
- [ ] Verify no console errors
- [ ] Test with caching plugins enabled
- [ ] Check accessibility with keyboard navigation
- [ ] Verify colors match your brand

---

## Support & Resources

- **WordPress Block Editor Handbook**: https://developer.wordpress.org/block-editor/
- **React Documentation**: https://react.dev/
- **@wordpress/scripts**: https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/

---

## Next Steps

Now that your block is installed and working:

1. **Customize the colors** to match your brand
2. **Add your own questions** to make it unique
3. **Create custom categories** relevant to your niche
4. **Style it** to match your theme
5. **Test thoroughly** on different devices

Enjoy your new Trivia Challenge Block! 🎉
