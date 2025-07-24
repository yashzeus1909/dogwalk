# Contributing to PawWalk

Thank you for your interest in contributing to PawWalk! This document provides guidelines for contributing to the project.

## Getting Started

1. Fork the repository on GitHub
2. Clone your fork locally
3. Set up the development environment
4. Create a new branch for your feature

## Development Setup

```bash
# Clone your fork
git clone https://github.com/yourusername/pawwalk-dog-walker.git
cd pawwalk-dog-walker

# Set up the database
php setup_database.php

# Start the development server
php -S localhost:5000 server.php
```

## Code Style Guidelines

### PHP
- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Add comments for complex logic
- Use prepared statements for all database queries

### HTML/CSS
- Use semantic HTML5 elements
- Follow BEM methodology for CSS classes
- Ensure responsive design principles
- Maintain accessibility standards

### JavaScript
- Use modern ES6+ features where appropriate
- Follow consistent naming conventions
- Add JSDoc comments for functions
- Ensure cross-browser compatibility

## Making Changes

1. Create a feature branch:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. Make your changes following the code style guidelines

3. Test your changes thoroughly:
   - Test all user workflows
   - Verify database operations
   - Check responsive design
   - Test API endpoints

4. Commit your changes:
   ```bash
   git add .
   git commit -m "Add feature: description of your changes"
   ```

5. Push to your fork:
   ```bash
   git push origin feature/your-feature-name
   ```

6. Submit a Pull Request

## Pull Request Guidelines

- Provide a clear description of the changes
- Include screenshots for UI changes
- Reference any related issues
- Ensure all tests pass
- Update documentation if needed

## Reporting Issues

When reporting issues, please include:
- Steps to reproduce the issue
- Expected vs actual behavior
- Browser and PHP version
- Error messages or logs
- Screenshots if applicable

## Feature Requests

For feature requests, please:
- Describe the feature clearly
- Explain the use case
- Consider the impact on existing functionality
- Discuss implementation approach

## Code Review Process

1. All submissions require review
2. Maintainers will review for:
   - Code quality and style
   - Security considerations
   - Performance impact
   - Documentation updates

## Security

If you discover a security vulnerability, please:
- Do not open a public issue
- Email security@pawwalk.com
- Provide detailed information about the vulnerability

## Questions?

If you have questions about contributing, feel free to:
- Open an issue for discussion
- Email the maintainers
- Join our community discussions

Thank you for contributing to PawWalk! 🐕