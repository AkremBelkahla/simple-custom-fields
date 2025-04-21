# 🔧 Simple Custom Fields

## ⚠️ WARNING
This plugin is currently in development and should NOT be used on production sites.

## 📝 Description
Simple Custom Fields is a WordPress plugin that allows you to create and manage custom fields for different content types 📄.

The plugin uses a dedicated database table (`wp_scf_fields`) to store all custom field values, providing better performance and scalability compared to WordPress postmeta. This table is automatically created during plugin activation and includes:
- Optimized indexes for fast queries
- Timestamp tracking for all changes
- Serialized field value storage
- Relationship tracking between posts, field groups and fields

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
  - 🔜 Image upload
  - 🔜 WYSIWYG editor  
  - 🔜 Tabs
  - 🔜 Repeater fields
  - 🔜 URL field
  - 🔜 File upload
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

### Shortcode Usage
```
[scf_fields] 
```
Displays all fields for current post in a formatted layout

### Security Notes
- Always escape output with `esc_html()` for text fields
- Use `wp_kses_post()` for HTML content
- For emails, use `antispambot()` function

## 📜 Changelog
- Version 1.0.0: Initial release of the plugin 🎉
