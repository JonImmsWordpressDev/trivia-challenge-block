# WordPress.org Submission Assets Checklist

This checklist covers all required assets and files for submitting the Trivia Challenge Block to the WordPress.org plugin repository.

## Required Plugin Files

### Core Files
- [x] `trivia-challenge-block.php` - Main plugin file with proper headers
- [x] `readme.txt` - WordPress.org format readme
- [x] `README.md` - GitHub format readme
- [x] `uninstall.php` - Proper cleanup on uninstall
- [x] `LICENSE` or `LICENSE.txt` - GPL-2.0-or-later license
- [x] `.gitignore` - Git ignore file

### Documentation
- [x] `CONTRIBUTING.md` - Contribution guidelines
- [x] `INSTALLATION.md` - Installation instructions
- [ ] `CHANGELOG.md` - Detailed changelog (optional, in readme.txt)

### Configuration Files
- [x] `.phpcs.xml.dist` - PHP Code Sniffer configuration
- [x] `.eslintrc.js` - ESLint configuration
- [x] `.editorconfig` - Editor configuration
- [x] `package.json` - NPM dependencies and scripts
- [x] `webpack.config.js` or using `@wordpress/scripts`

## WordPress.org SVN Assets

These files go in the `/assets` directory of your SVN repository (NOT in the plugin folder):

### Plugin Icons (Required)
- [ ] `icon-128x128.png` - 128x128px PNG icon
- [ ] `icon-256x256.png` - 256x256px PNG icon
- [ ] `icon.svg` - SVG icon (optional, recommended)

### Plugin Banners (Required)
- [ ] `banner-772x250.png` - Low resolution banner (772x250px)
- [ ] `banner-1544x500.png` - High resolution banner (1544x500px, Retina)

### Screenshots (Highly Recommended)
- [ ] `screenshot-1.png` - Category selection screen
- [ ] `screenshot-2.png` - Quiz in progress
- [ ] `screenshot-3.png` - Answer feedback
- [ ] `screenshot-4.png` - Results screen
- [ ] `screenshot-5.png` - Block editor view
- [ ] `screenshot-6.png` - Admin settings page

## Plugin Headers Checklist

Verify all required headers in `trivia-challenge-block.php`:

- [x] Plugin Name: Trivia Challenge Block
- [x] Plugin URI: https://wordpress.org/plugins/trivia-challenge-block/
- [x] Description: Comprehensive description
- [x] Version: 2.0.0
- [x] Requires at least: 6.0
- [x] Requires PHP: 7.4
- [x] Tested up to: 6.7
- [x] Author: Jon Imms
- [x] Author URI: https://jonimms.com
- [x] License: GPL-2.0-or-later
- [x] License URI: http://www.gnu.org/licenses/gpl-2.0.txt
- [x] Text Domain: trivia-challenge-block
- [x] Domain Path: /languages

## readme.txt Requirements

Verify `readme.txt` contains:

- [x] Plugin name in header
- [x] Contributors (WordPress.org usernames)
- [x] Tags (max 12, comma-separated)
- [x] Requires at least: 6.0
- [x] Tested up to: 6.7
- [x] Requires PHP: 7.4
- [x] Stable tag: 2.0.0
- [x] License information
- [x] Short description (under 150 characters)
- [x] Long description with features
- [x] Installation instructions
- [x] FAQ section
- [x] Screenshots section
- [x] Changelog
- [x] Upgrade notices

## Security Review Checklist

### Input Validation
- [x] All user inputs are validated and sanitized
- [x] Nonces used for form submissions
- [x] Capability checks for admin functions
- [x] SQL queries use prepared statements
- [x] No eval() or create_function()

### Output Escaping
- [x] All output is escaped appropriately
- [x] Using esc_html(), esc_attr(), esc_url(), etc.
- [x] wp_kses() for allowed HTML

### Data Storage
- [x] Options properly prefixed
- [x] Transients properly prefixed
- [x] No sensitive data in browser storage
- [x] Proper uninstall cleanup

### External Requests
- [x] Using wp_remote_get/post for HTTP requests
- [x] Validating external API responses
- [x] Proper timeout handling
- [x] Caching external API responses

## Code Quality Checklist

### PHP Standards
- [ ] Passes WordPress PHP Coding Standards (run `composer phpcs`)
- [x] No PHP errors or warnings
- [x] Compatible with PHP 7.4+
- [x] Namespaced or prefixed functions
- [x] Proper file documentation blocks

### JavaScript Standards
- [ ] Passes WordPress JavaScript Coding Standards (run `npm run lint:js`)
- [x] No console.log in production code (warn/error only)
- [x] Proper i18n implementation
- [x] No jQuery dependencies (uses @wordpress/element)

### Accessibility
- [x] ARIA labels on interactive elements
- [x] Proper heading hierarchy
- [x] Keyboard navigation support
- [x] Screen reader announcements for dynamic content
- [x] Color contrast meets WCAG AA standards

### Internationalization
- [x] All strings use i18n functions
- [x] Correct text domain used
- [x] POT file generated (optional at submission)
- [x] Domain path set correctly

## Testing Checklist

### Functionality
- [ ] Plugin activates without errors
- [ ] Plugin deactivates without errors
- [ ] Uninstall removes all data
- [ ] Block appears in block inserter
- [ ] Block works in editor
- [ ] Block renders correctly on frontend
- [ ] All features work as expected
- [ ] API integration works
- [ ] Fallback questions work

### Compatibility
- [ ] Works with latest WordPress version (6.7)
- [ ] Works with WordPress 6.0 (minimum version)
- [ ] Works with PHP 7.4
- [ ] Works with PHP 8.0+
- [ ] Works with default WordPress themes
- [ ] No conflicts with popular plugins

### Performance
- [ ] No slow queries
- [ ] Assets are minified
- [ ] Scripts only load when needed
- [ ] Proper caching implemented
- [ ] No memory leaks

## Build Process

Before submission:

1. [ ] Run production build:
   ```bash
   npm run build
   ```

2. [ ] Verify build directory contains:
   - [ ] `index.js` - Editor script
   - [ ] `index.asset.php` - Dependencies
   - [ ] `frontend.js` - Frontend script
   - [ ] `frontend.asset.php` - Dependencies
   - [ ] `style-index.css` - Frontend styles
   - [ ] `editor.css` - Editor styles

3. [ ] Remove development files from release:
   - [ ] `node_modules/`
   - [ ] `src/` (keep for development, exclude from release)
   - [ ] `.git/`
   - [ ] Development config files (if not needed)

## SVN Repository Structure

```
trunk/
├── build/
├── includes/
├── languages/
├── tests/ (optional)
├── trivia-challenge-block.php
├── readme.txt
├── uninstall.php
└── ... (other plugin files)

tags/
└── 2.0.0/
    └── (copy of trunk at release)

assets/
├── icon-128x128.png
├── icon-256x256.png
├── banner-772x250.png
├── banner-1544x500.png
├── screenshot-1.png
├── screenshot-2.png
└── ... (other assets)
```

## Asset Creation Guidelines

### Icons
- Size: 128x128px and 256x256px
- Format: PNG with transparency
- Style: Simple, recognizable at small sizes
- Use plugin's brain/game theme
- Recommended colors: Match plugin branding

### Banners
- Size: 772x250px (low-res), 1544x500px (high-res)
- Format: PNG or JPG
- Include plugin name
- Visually appealing design
- Not too text-heavy
- Use high-quality graphics

### Screenshots
- Size: At least 772px wide
- Format: PNG or JPG
- Show actual plugin functionality
- In order matching readme.txt descriptions
- High quality, clear, and professional
- Use real, not lorem ipsum content

## Pre-Submission Final Checks

- [ ] All code is original or properly licensed
- [ ] No trademarked terms in plugin name
- [ ] No "WordPress" in plugin name
- [ ] Plugin slug is unique (search WordPress.org)
- [ ] All external links are working
- [ ] Privacy policy compliance (GDPR)
- [ ] No phone-home or unauthorized external requests
- [ ] No affiliate links without disclosure
- [ ] No upselling in free version

## Submission Process

1. [ ] Create WordPress.org account
2. [ ] Submit plugin via https://wordpress.org/plugins/developers/add/
3. [ ] Wait for review (usually 2-10 days)
4. [ ] Address any feedback from reviewers
5. [ ] Receive SVN credentials
6. [ ] Check out SVN repository:
   ```bash
   svn co https://plugins.svn.wordpress.org/trivia-challenge-block trivia-challenge-block-svn
   ```

7. [ ] Add files to trunk:
   ```bash
   cd trivia-challenge-block-svn
   # Copy plugin files to trunk/
   # Copy assets to assets/
   svn add trunk/* assets/*
   svn commit -m "Initial commit of version 2.0.0"
   ```

8. [ ] Tag first release:
   ```bash
   svn cp trunk tags/2.0.0
   svn commit -m "Tagging version 2.0.0"
   ```

9. [ ] Verify plugin page on WordPress.org

## Post-Launch

- [ ] Monitor support forums
- [ ] Respond to reviews
- [ ] Fix reported bugs
- [ ] Plan future updates
- [ ] Maintain regular releases

## Resources

- [WordPress Plugin Developer Handbook](https://developer.wordpress.org/plugins/)
- [Plugin Review Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
- [SVN Access Guide](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)

## Notes

- Keep this checklist updated as you prepare assets
- Screenshots should be created after final styling is complete
- Icons and banners may need multiple iterations to get right
- Test in a clean WordPress installation before submission
- Consider beta testing with community before official release
