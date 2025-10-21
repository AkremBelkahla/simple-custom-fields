# 🔧 Simple Custom Fields

[![Version](https://img.shields.io/badge/version-1.5.0-blue.svg)](https://github.com)
[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)](LICENSE)

> 🌍 **English version** | **[Version française](README.fr.md)**

A modern and secure WordPress plugin to create and manage custom fields with PSR-4 architecture.

## ⚠️ Warning
This plugin is under active development. Version 1.5.0 brings a major refactoring with new features.

## 📝 Description
Simple Custom Fields is a WordPress plugin that allows you to create and manage custom fields for different content types 📄.

**Modern and professional interface** with:
- 🎨 Modern color palette
- 📦 Card-based layout
- ✨ Smooth animations
- 📱 Responsive design
- 🎯 Intuitive focus states

The plugin uses a dedicated database table (`wp_scf_fields`) to store all custom field values, providing better performance and scalability compared to WordPress postmeta. This table is automatically created during plugin activation and includes:
- Optimized indexes for fast queries.
- Timestamp tracking for all changes.
- Serialized field value storage.
- Relationship tracking between posts, field groups and fields.

## ✨ Features
- 📁 Create custom field groups
- 📝 Supported field types:
  - ✅ Text
  - ✅ Textarea 
  - ✅ Number
  - ✅ Email
  - ✅ Select dropdown
  - ✅ Radio buttons
  - ✅ Checkbox
  - ✅ Date
  - ✅ URL field
  - ✅ File upload
  - 🔜 Image upload
  - 🔜 WYSIWYG editor  
  - 🔜 Tabs
  - 🔜 Repeater fields
  - 🔜 True/False toggle
  - 🔜 Link field
  - 🔜 Post object relationship
  - 🔜 Taxonomy selector
- 🔍 Configure display rules by content type
- 🔄 Enable/disable field groups
- 🗃️ Store custom fields in dedicated database table for better performance
- 🌐 Display custom fields on the front-end using a dedicated function

## 🚀 Installation
1. 📥 Download the plugin folder
2. 📦 Unzip the folder into the `wp-content/plugins` directory of your WordPress installation
3. ✅ Activate the plugin through the "Plugins" menu in WordPress

## 📚 Usage
1. 🔗 Access the "Simple Custom Fields" menu in the WordPress admin interface
2. 🛠️ Create a new field group or edit an existing one
3. ➕ Add custom fields as needed
4. 🔧 Configure display rules for the field group
5. 💻 Display fields on the front-end using:

### Basic Usage
```php
<?php 
$value = scf_get_field('field_name'); 
if ($value) {
    echo $value; 
}
?>
```

### With Post ID
```php 
<?php
$value = scf_get_field('field_name', 123); // 123 = Post ID
echo $value ?: 'No value';
?>
```

### In Template Files
```php
<div class="custom-field">
    <h3><?php echo esc_html__('Field Label', 'text-domain'); ?></h3>
    <p><?php echo esc_html(scf_get_field('field_name')); ?></p>
</div>
```

### Security Notes
- Always escape output with `esc_html()` for text fields
- Use `wp_kses_post()` for HTML content
- For emails, use `antispambot()` function

## 🆕 What's New in Version 1.5.0

### Architecture & Code
- ✅ **PSR-4 Architecture** with Composer autoloader
- ✅ **Modern Namespaces** (`SCF\Core`, `SCF\Services`, etc.)
- ✅ **Centralized Logging System** with 8 levels
- ✅ **Structured Error Handling** with admin notifications
- ✅ **Centralized Configuration** in `SCF\Core\Config`

### Security
- ✅ **Strict Validation** by field type
- ✅ **Fixed Nonce Bug** during deletion
- ✅ **Specific Nonces** per AJAX action
- ✅ **Enhanced Rate Limiting**
- ✅ **HTTP Security Headers**

### Development
- ✅ **Unit Tests** with PHPUnit and Brain Monkey
- ✅ **PHPStan** (level 5) for static analysis
- ✅ **PHPCS** with WordPress standards
- ✅ **Complete Documentation** (Architecture, API, Security)

### Services
- ✅ `FieldGroupService`: Isolated business logic
- ✅ `FieldValidator`: Type-based validation
- ✅ `Logger`: Structured logs with rotation
- ✅ `ErrorHandler`: Centralized error management

## 📚 Documentation

- 📖 [Architecture](docs/ARCHITECTURE.md) - Technical architecture
- 🔌 [API](docs/API.md) - Public API documentation
- 🔒 [Security](docs/SECURITY.md) - Security guide
- 🤝 [Contributing](docs/CONTRIBUTING.md) - Contribution guide
- 📝 [Changelog](CHANGELOG.md) - Complete version history
- 🌍 [Internationalization](docs/I18N_GUIDE.md) - Translation guide

## 🛠️ Development

### Installation

```bash
# Clone the repository
git clone https://github.com/infinityweb/simple-custom-fields.git
cd simple-custom-fields

# Install dependencies
composer install
```

### Tests

```bash
# All tests
composer test

# Unit tests
composer test-unit

# Integration tests
composer test-integration
```

### Code Quality

```bash
# Check code
composer phpcs

# Static analysis
composer phpstan

# Check everything
composer lint
```

### Changelog Management

```bash
# Add a changelog entry
composer changelog:add <type> <message>

# Examples
composer changelog:add added "New feature"
composer changelog:add fixed "Bug fix"
composer changelog:add security "Security improvement"

# Release a new version
composer changelog:release <version>

# Example
composer changelog:release 1.5.0

# Show changelog
composer changelog:show

# Help
composer changelog:help
```

**Available types:** `added`, `changed`, `fixed`, `security`, `performance`, `ui`, `docs`, `migration`, `deprecated`, `removed`

📖 **Complete documentation:** [Changelog Management Guide](docs/CHANGELOG_GUIDE.md)  
💡 **Practical examples:** [Usage Examples](CHANGELOG_EXAMPLES.md)

### Translation Management

```bash
# Generate MO files
composer i18n:generate-mo

# Scan translatable strings
composer i18n:scan

# Translation statistics
composer i18n:stats fr_FR
```

**Supported languages:** French (fr_FR), English (en_US)

📖 **Translation guide:** [Internationalization Guide](docs/I18N_GUIDE.md)

## 📜 Changelog

See [CHANGELOG.md](CHANGELOG.md) for complete version history.

## 🌍 Internationalization

The plugin automatically detects WordPress language:
- 🇫🇷 French if WordPress is in French
- 🇬🇧 English for all other languages

**No configuration needed!**

150+ strings fully translated in French and English.

📖 **Translation guide:** [I18N Guide](docs/I18N_GUIDE.md)

## 👨‍💻 Author

**Akrem Belkahla**
- Email: akrem.belkahla@infinityweb.tn
- Website: [Infinity Web](https://infinityweb.tn)
- GitHub: [@AkremBelkahla](https://github.com/AkremBelkahla)

## 📄 License

GPL-2.0-or-later - See LICENSE file for details.
