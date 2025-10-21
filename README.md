# 🔧 Simple Custom Fields

[![Version](https://img.shields.io/badge/version-1.5.0-blue.svg)](https://github.com)
[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)](LICENSE)

Un plugin WordPress moderne et sécurisé pour créer et gérer des champs personnalisés avec une architecture PSR-4.

## ⚠️ Avertissement
Ce plugin est en développement actif. La version 1.5.0 apporte une refactorisation majeure avec de nouvelles fonctionnalités.

## 📝 Description
Simple Custom Fields is a WordPress plugin that allows you to create and manage custom fields for different content types 📄.

**Interface moderne et professionnelle** avec :
- 🎨 Palette de couleurs moderne
- 📦 Layout en cards
- ✨ Animations fluides
- 📱 Design responsive
- 🎯 Focus states intuitifs

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

## 🆕 Nouveautés Version 1.5.0

### Architecture & Code
- ✅ **Architecture PSR-4** avec autoloader Composer
- ✅ **Namespaces modernes** (`SCF\Core`, `SCF\Services`, etc.)
- ✅ **Système de logging** centralisé avec 8 niveaux
- ✅ **Gestion d'erreurs** structurée avec notifications admin
- ✅ **Configuration centralisée** dans `SCF\Core\Config`

### Sécurité
- ✅ **Validation stricte** par type de champ
- ✅ **Correction du bug de nonce** lors de la suppression
- ✅ **Nonces spécifiques** par action AJAX
- ✅ **Rate limiting** renforcé
- ✅ **Headers de sécurité** HTTP

### Développement
- ✅ **Tests unitaires** avec PHPUnit et Brain Monkey
- ✅ **PHPStan** (niveau 5) pour analyse statique
- ✅ **PHPCS** avec standards WordPress
- ✅ **Documentation complète** (Architecture, API, Sécurité)

### Services
- ✅ `FieldGroupService` : Logique métier isolée
- ✅ `FieldValidator` : Validation par type
- ✅ `Logger` : Logs structurés avec rotation
- ✅ `ErrorHandler` : Gestion centralisée des erreurs

## 📚 Documentation

- 📖 [Architecture](docs/ARCHITECTURE.md) - Architecture technique du plugin
- 🔌 [API](docs/API.md) - Documentation de l'API publique
- 🔒 [Sécurité](docs/SECURITY.md) - Guide de sécurité
- 🤝 [Contribution](docs/CONTRIBUTING.md) - Guide de contribution
- 📝 [Changelog](CHANGELOG.md) - Historique complet des versions

## 🛠️ Développement

### Installation

```bash
# Cloner le repository
git clone https://github.com/infinityweb/simple-custom-fields.git
cd simple-custom-fields

# Installer les dépendances
composer install
```

### Tests

```bash
# Tous les tests
composer test

# Tests unitaires
composer test-unit

# Tests d'intégration
composer test-integration
```

### Qualité de code

```bash
# Vérifier le code
composer phpcs

# Analyse statique
composer phpstan

# Tout vérifier
composer lint
```

## 📜 Changelog

Voir [CHANGELOG.md](CHANGELOG.md) pour l'historique complet des versions.

## 👨‍💻 Auteur

**Akrem Belkahla**
- Email: akrem.belkahla@infinityweb.tn
- Site: [Infinity Web](https://infinityweb.tn)
- GitHub: [@AkremBelkahla](https://github.com/AkremBelkahla)

## 📄 Licence

GPL-2.0-or-later - Voir le fichier LICENSE pour plus de détails.
