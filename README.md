# Shadcn

A modern, lightweight WordPress theme built with Shadcn UI components and contemporary web technologies.

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![License](https://img.shields.io/badge/license-GPL%20v3+-green)
![WordPress](https://img.shields.io/badge/WordPress-6.4+-blue)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple)

## 🎨 Description

WP Shadcn is designed for developers and content creators who value clean code and excellent user experience. This theme emphasizes performance, accessibility, and ease of customization.

## ✨ Features

- 🎯 **Modern Block-Based Theme Architecture** - Full-site editing support with flexible block patterns
- 🌙 **Dark Mode Support** - Automatic detection of user system preferences
- 📱 **Responsive Design** - Beautiful on all devices and screen sizes
- 💻 **Clean & Semantic HTML** - Follows WordPress best practices
- ⚡ **Performance Optimized** - Lightweight and fast-loading
- 🎨 **Built with Shadcn UI** - Component design principles for consistency
- 🎨 **Customizable Color Palette** - Easily adjust theme colors
- 📐 **Layout Options** - Full-width and centered layout modes
- 🔧 **Header & Footer Customization** - Full control over your site structure
- 🧩 **Custom Block Patterns** - Pre-designed content blocks for quick creation

## 📋 Requirements

- **WordPress:** 6.4 or higher
- **PHP:** 7.4 or higher

## 🚀 Installation

1. **Upload the theme:**
   ```bash
   # Copy the shadcn folder to your WordPress themes directory
   cp -r shadcn /path/to/wp-content/themes/
   ```

2. **Activate the theme:**
   - Go to **Appearance → Themes** in the WordPress admin dashboard
   - Find "WP Shadcn" and click "Activate"

3. **Configure settings (Optional):**
   - Go to **Appearance → Editor** to customize theme settings
   - Adjust header and footer content through the WordPress Site Editor

## 🎯 Getting Started

### 1. **Access Site Editor**
   Navigate to **Appearance → Editor** to customize your theme

### 2. **Configure Theme Settings**
   - Adjust header and footer content through the WordPress Site Editor
   - Customize colors and typography

### 3. **Create Content**
   - Use the included block patterns for quick page creation
   - Access patterns when editing pages/posts in the Block Editor

### 4. **Enable Dark Mode**
   - The theme automatically detects user system preferences
   - No manual configuration needed

## ❓ FAQ

**Q: Does this theme support dark mode?**
> Yes! The theme includes built-in dark mode support that automatically detects user system preferences.

**Q: Can I customize the colors?**
> Yes, you can customize colors through the WordPress Customizer (**Appearance → Customize**).

**Q: Is this theme SEO friendly?**
> Yes, the theme follows WordPress best practices and semantic HTML standards for better SEO.

**Q: How do I use the block patterns?**
> When creating or editing pages/posts, look for the "Patterns" section in the Block Editor to insert pre-designed content blocks.

**Q: Can I modify the theme code?**
> Absolutely! The theme is built with developers in mind. All code is clean and well-structured for easy customization.

## 📦 File Structure

```
shadcn/
├── assets/
│   ├── css/              # Stylesheets
│   ├── fonts/            # Font files
│   └── js/               # JavaScript files
├── parts/                # Reusable template parts
├── patterns/             # Block patterns
├── templates/            # Page templates
├── functions.php         # Theme functions
├── style.css             # Main stylesheet
├── theme.json            # Theme configuration
└── README.md             # This file
```

## 🔄 Changelog

### Version 1.0.0
- ✅ Initial release
- ✅ Modern block-based theme architecture
- ✅ Dark mode support
- ✅ Responsive design
- ✅ Block patterns for quick content creation

### Version 1.0.1
- ✅ Some small issues

### Version 1.0.2
- ✅ Update Navigation style
- ✅ Update layout

## 🎓 Built With

- **[Shadcn UI](https://shadcn.com/)** - Component design principles
- **WordPress Block Editor** - Modern theme development

## 👨‍💻 Development

This section is for developers who want to contribute or customize the theme.

### Prerequisites

Before you begin development, make sure you have the following installed:

- **PHP** 7.4 or higher
- **WordPress** 6.4 or higher
- **Composer** (for PHP dependencies)
- **Node.js & Yarn** (for asset management)
- **Git** (for version control)

### Setting Up Development Environment

1. **Clone or download the theme:**
   ```bash
   cd /path/to/wp-content/themes/
   git clone [your-repo-url] shadcn
   cd shadcn
   ```

2. **Install dependencies:**
   ```bash
   # Install PHP dependencies
   ./run.sh dev-init
   # or manually: composer install
   
   # Install JavaScript/asset dependencies (if package.json exists)
   yarn install
   ```

3. **Start development mode:**
   ```bash
   ./run.sh dev
   # or manually: yarn run dev
   ```

### Development Commands

The theme includes a `run.sh` script with helpful commands:

```bash
# Initialize development environment (install composer dependencies)
./run.sh dev-init

# Start development mode (watch for changes, compile assets)
./run.sh dev

# Create a release build (generates a zip file for distribution)
./run.sh release

# Show all available commands
./run.sh help
```

### Project Structure for Developers

```
shadcn/
├── assets/
│   ├── css/              # Compiled stylesheets
│   ├── images/           # Theme images and icons
│   └── js/               # JavaScript files
├── inc/
│   ├── BlockSettings/    # Block customization settings
│   ├── Core/             # Core theme functionality
│   ├── Integrations/     # Third-party integrations (WooCommerce, etc.)
│   ├── Interfaces/       # PHP interfaces
│   └── Traits/           # Reusable PHP traits
├── parts/                # Reusable template parts (header, footer, etc.)
├── patterns/             # Block patterns (PHP files)
├── styles/
│   └── blocks/           # Block-specific style variations (JSON)
├── templates/            # Page templates (HTML)
├── functions.php         # Main theme functions file
├── theme.json            # Theme configuration (colors, typography, etc.)
├── style.css             # Main stylesheet with theme metadata
├── phpcs.xml             # PHP CodeSniffer configuration
└── run.sh                # Development helper script
```

### Coding Standards

This theme follows WordPress coding standards:

- **PHP:** WordPress Coding Standards (enforced via PHP CodeSniffer)
- **JavaScript:** WordPress JavaScript Coding Standards
- **CSS:** WordPress CSS Coding Standards

**Run PHP CodeSniffer to check code quality:**

```bash
# Check all PHP files
./vendor/bin/phpcs

# Check specific file
./vendor/bin/phpcs inc/Core.php

# Auto-fix fixable issues
./vendor/bin/phpcbf
```

### Creating Custom Block Patterns

Block patterns are located in the `patterns/` directory. To create a new pattern:

1. Create a new PHP file in `patterns/` (e.g., `my-pattern.php`)
2. Define the pattern metadata and HTML content
3. Register the pattern in your theme

Example pattern structure:

```php
<?php
/**
 * Title: My Custom Pattern
 * Slug: shadcn/my-pattern
 * Categories: featured
 */
?>
<!-- Your block markup here -->
```

### Customizing Block Styles

Block style variations are defined in `styles/blocks/` as JSON files. These files follow the WordPress `theme.json` schema for block-specific styling.

### Working with Dark Mode

The theme includes built-in dark mode support:

- JavaScript: `assets/js/dark-mode.js`
- Styles: Configured in `theme.json` color palette
- PHP: `inc/DarkMode.php`

### Testing Your Changes

1. **Test in WordPress Site Editor:**
   - Go to **Appearance → Editor**
   - Test template changes and block patterns

2. **Test responsive design:**
   - Use browser dev tools to check different screen sizes

3. **Test dark mode:**
   - Toggle system dark mode preference
   - Verify all elements render correctly

### Building for Production

When you're ready to create a distribution package:

```bash
./run.sh release
```

This will:
- Clean up development files
- Create a `release/` directory
- Generate a `shadcn.zip` file ready for distribution
- Exclude files listed in `.distignore`

### Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Make your changes following coding standards
4. Test thoroughly
5. Commit your changes (`git commit -m 'Add my feature'`)
6. Push to the branch (`git push origin feature/my-feature`)
7. Create a Pull Request

### Debugging Tips

- Enable WordPress debug mode in `wp-config.php`:
  ```php
  define('WP_DEBUG', true);
  define('WP_DEBUG_LOG', true);
  define('WP_DEBUG_DISPLAY', false);
  ```

- Check debug logs at: `wp-content/debug.log`
- Use browser console for JavaScript debugging
- Inspect block markup in the Site Editor

## 📄 License

This theme is licensed under the **GNU General Public License v2 or later**.

See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) for more details.

## 👤 Credits

- Built with Shadcn UI principles
- Block-based architecture inspired by modern WordPress theme development

## 📞 Support

For support, questions, or to report bugs, please contact the theme author or visit the theme repository.

---

**Copyright © 2025 Shadcn**  
WP Shadcn is distributed under the terms of the GNU GPL
