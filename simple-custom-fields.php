<?php
/**
 * Plugin Name: Simple Custom Fields
 * Description: Un plugin simple pour créer et gérer des champs personnalisés dans WordPress.
 * Version: 1.0.0
 * Author: Akrem Belkahla
 * Author URI: https://infintyweb.tn
 * Author Agency: Infinity Web
 * Text Domain: simple-custom-fields
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

// Définition des constantes
define('SCF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SCF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SCF_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Chargement des fichiers de classes
require_once SCF_PLUGIN_DIR . 'includes/class-scf-simple-custom-fields.php';
require_once SCF_PLUGIN_DIR . 'includes/class-scf-admin-page.php';
require_once SCF_PLUGIN_DIR . 'includes/class-scf-meta-boxes.php';
require_once SCF_PLUGIN_DIR . 'includes/class-scf-fields.php';

// Fonction pour récupérer un champ personnalisé
function scf_get_field($field_name, $post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    // Récupérer tous les groupes de champs
    $groups = get_posts(array(
        'post_type' => 'scf-field-group',
        'posts_per_page' => -1,
        'post_status' => array('publish')
    ));

    foreach ($groups as $group) {
        $fields = get_post_meta($group->ID, 'scf_fields', true);
        $values = get_post_meta($post_id, '_scf_values_' . $group->ID, true);

        if (!empty($fields)) {
            foreach ($fields as $field) {
                if ($field['name'] === $field_name) {
                    return isset($values[$field_name]) ? $values[$field_name] : null;
                }
            }
        }
    }

    return null;
}

// Chargement des traductions
function scf_load_textdomain() {
    load_plugin_textdomain('simple-custom-fields', false, SCF_PLUGIN_DIR . 'languages');
}
add_action('plugins_loaded', 'scf_load_textdomain');

// Initialisation du plugin
function scf_init() {
    $plugin = SCF_Simple_Custom_Fields::get_instance();
    $plugin->init();
}
add_action('plugins_loaded', 'scf_init');

// Activation/Deactivation hooks
register_activation_hook(__FILE__, 'scf_activate');
register_deactivation_hook(__FILE__, 'scf_deactivate');

function scf_activate() {
    // Enregistrer le type de post personnalisé
    $plugin = SCF_Simple_Custom_Fields::get_instance();
    $plugin->register_field_group_post_type();
    
    // Vider le cache des règles de réécriture
    flush_rewrite_rules();
}

function scf_deactivate() {
    // Vider le cache des règles de réécriture
    flush_rewrite_rules();
}

// Activation des classes principales
function scf_boot() {
    SCF_Admin_Page::get_instance();
    SCF_Fields::get_instance();
}
add_action('plugins_loaded', 'scf_boot');
