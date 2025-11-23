# Contributing to Trivia Challenge Block

Thank you for your interest in contributing to Trivia Challenge Block! This document provides guidelines and instructions for contributing.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Testing](#testing)
- [Submitting Changes](#submitting-changes)
- [Reporting Bugs](#reporting-bugs)
- [Feature Requests](#feature-requests)

## Code of Conduct

This project adheres to a code of conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to the project maintainers.

## Getting Started

### Prerequisites

- Node.js 16.x or higher
- npm 8.x or higher
- PHP 7.4 or higher
- WordPress 6.0 or higher
- Composer (for PHP dependencies)

### Setup

1. Fork the repository
2. Clone your fork:
   ```bash
   git clone https://github.com/YOUR-USERNAME/trivia-challenge-block.git
   cd trivia-challenge-block
   ```

3. Install dependencies:
   ```bash
   npm install
   composer install
   ```

4. Start development mode:
   ```bash
   npm run start
   ```

## Development Workflow

### Branch Naming

- `feature/feature-name` - For new features
- `fix/bug-name` - For bug fixes
- `docs/doc-name` - For documentation updates
- `refactor/refactor-name` - For code refactoring

### Making Changes

1. Create a new branch from `main`:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. Make your changes following our [coding standards](#coding-standards)

3. Test your changes:
   ```bash
   npm run test
   npm run lint:js
   npm run lint:css
   ```

4. Build for production:
   ```bash
   npm run build
   ```

5. Commit your changes with a clear message:
   ```bash
   git commit -m "Add feature: description of your feature"
   ```

## Coding Standards

### PHP

We follow [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).

- Run PHP Code Sniffer:
  ```bash
  composer run-script phpcs
  ```

- Auto-fix issues:
  ```bash
  composer run-script phpcbf
  ```

### JavaScript

We use [@wordpress/eslint-plugin](https://www.npmjs.com/package/@wordpress/eslint-plugin).

- Run ESLint:
  ```bash
  npm run lint:js
  ```

- Auto-fix issues:
  ```bash
  npm run lint:js -- --fix
  ```

### CSS/SCSS

- Run Stylelint:
  ```bash
  npm run lint:css
  ```

- Auto-fix issues:
  ```bash
  npm run lint:css -- --fix
  ```

### General Guidelines

- Use meaningful variable and function names
- Write comments for complex logic
- Keep functions small and focused
- Follow DRY (Don't Repeat Yourself) principles
- Use WordPress hooks and filters where appropriate

## Testing

### PHP Unit Tests

```bash
composer run-script test
```

### JavaScript Tests

```bash
npm run test:unit
```

### E2E Tests

```bash
npm run test:e2e
```

### Writing Tests

- All new features should include tests
- Bug fixes should include a test that fails without the fix
- Maintain or improve code coverage

## Submitting Changes

### Pull Request Process

1. Update README.md with details of changes if needed
2. Update the changelog in readme.txt
3. Ensure all tests pass
4. Ensure coding standards are met
5. Submit a pull request to the `main` branch

### Pull Request Guidelines

- **Title**: Clear and descriptive
- **Description**: Explain what and why (not how)
- **Related Issues**: Link to any related issues
- **Screenshots**: Include for UI changes
- **Testing**: Describe how you tested the changes

### PR Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
How was this tested?

## Checklist
- [ ] Code follows WordPress coding standards
- [ ] Tests added/updated
- [ ] Documentation updated
- [ ] Changelog updated
```

## Reporting Bugs

### Before Submitting

- Check existing issues
- Test with latest version
- Test with default WordPress theme
- Disable other plugins to check for conflicts

### Bug Report Template

```markdown
## Bug Description
Clear description of the bug

## Steps to Reproduce
1. Step one
2. Step two
3. Step three

## Expected Behavior
What should happen

## Actual Behavior
What actually happens

## Environment
- WordPress Version:
- PHP Version:
- Plugin Version:
- Browser:
- Theme:
```

## Feature Requests

We welcome feature requests! Please:

1. Check if the feature already exists
2. Check if there's an existing request
3. Provide a clear use case
4. Explain why this would be useful to most users

### Feature Request Template

```markdown
## Feature Description
Clear description of the feature

## Use Case
Why is this feature needed?

## Proposed Solution
How should this work?

## Alternatives Considered
What other solutions did you consider?
```

## Development Tips

### Building Blocks

The plugin uses `@wordpress/scripts` for building:

```bash
# Development mode with hot reload
npm run start

# Production build
npm run build

# Format code
npm run format
```

### Working with i18n

- Use WordPress i18n functions: `__()`, `_e()`, `_n()`, `_x()`
- Text domain: `trivia-challenge-block`
- Generate POT file:
  ```bash
  wp i18n make-pot . languages/trivia-challenge-block.pot
  ```

### WP-CLI Commands

Test WP-CLI commands locally:

```bash
wp trivia-challenge clear-cache
wp trivia-challenge test-api
wp trivia-challenge stats
wp trivia-challenge verify
```

## Release Process

(For maintainers only)

1. Update version in all files:
   - `trivia-challenge-block.php`
   - `package.json`
   - `readme.txt`
   - `src/block.json`

2. Update changelog in `readme.txt`

3. Build production assets:
   ```bash
   npm run build
   ```

4. Tag release:
   ```bash
   git tag -a v2.0.0 -m "Release version 2.0.0"
   git push origin v2.0.0
   ```

5. Deploy to WordPress.org SVN

## Questions?

If you have questions, please:

1. Check the documentation
2. Search existing issues
3. Create a new issue with the "question" label

## License

By contributing, you agree that your contributions will be licensed under the GPL-2.0-or-later license.

## Thank You!

Your contributions make this plugin better for everyone. Thank you for taking the time to contribute!
