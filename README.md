# 🔧 Simple Custom Fields

**Design inspiré d'Advanced Custom Fields (ACF)**

[![Version](https://img.shields.io/badge/version-1.4.0-blue.svg)](https://github.com)
[![ACF Inspired](https://img.shields.io/badge/design-ACF%20inspired-0783BE.svg)](https://www.advancedcustomfields.com/)
[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org)

## ⚠️ WARNING
This plugin is currently in development and should NOT be used on production sites.

## 📝 Description
Simple Custom Fields is a WordPress plugin that allows you to create and manage custom fields for different content types.

**Interface moderne inspirée d'Advanced Custom Fields (ACF)** avec :
- 🎨 Palette de couleurs ACF (#0783BE)
- 📦 Layout en cards moderne
- ✨ Animations fluides
- 📱 Design responsive
- 🎯 Focus states bleus

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

## 🎨 Design inspiré d'ACF

Ce plugin s'inspire fortement du design d'**Advanced Custom Fields (ACF)** pour offrir une expérience utilisateur familière et professionnelle.

### Références ACF
- **Couleur primaire** : `#0783BE` (Bleu signature ACF)
- **Layout** : Cards inspirées de la page "Field Groups" d'ACF
- **Animations** : Transitions fluides comme dans ACF Pro
- **Typography** : Hiérarchie similaire à l'interface ACF

### Documentation complète
- 📖 [ACF-REFERENCES.md](ACF-REFERENCES.md) - Guide complet des références ACF
- 📊 [ACF-COMPARISON.md](ACF-COMPARISON.md) - Comparaison détaillée ACF vs SCF
- 🎨 [DESIGN-IMPROVEMENTS.md](DESIGN-IMPROVEMENTS.md) - Améliorations du design
- 📝 [CHANGELOG-DESIGN.md](CHANGELOG-DESIGN.md) - Historique des changements

### Crédits
**Simple Custom Fields** est un plugin indépendant créé par **Akrem Belkahla** (Infinity Web).

Le design s'inspire d'**Advanced Custom Fields (ACF)** développé par **Delicious Brains**, avec respect et admiration pour leur travail exceptionnel.

**Note** : ACF est une marque déposée de Delicious Brains. Simple Custom Fields n'est pas affilié, approuvé ou sponsorisé par Delicious Brains.

## 📜 Changelog
- **Version 1.4.0** : Refonte complète du design inspiré d'ACF 🎨
  - Nouvelle palette de couleurs ACF (#0783BE)
  - Layout en cards moderne
  - Animations fluides partout
  - Design responsive complet
  - Modales avec backdrop blur
  - Drag & drop amélioré
- Version 1.3.0: Amélioration des performances et correction de bugs 🐛
- Version 1.2.0: Ajout du support pour les shortcodes 🔗
- Version 1.1.0: Ajout de nouveaux types de champs et amélioration de l'interface 💅
- Version 1.0.0: Initial release of the plugin 🎉
