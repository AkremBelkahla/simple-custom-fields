<?php
/**
 * Plugin Name: Simple Custom Fields
 * Description: Un plugin simple pour créer et gérer des champs personnalisés dans WordPress.
 * Version: 1.4.1
 * Author: Akrem Belkahla
 * Author URI: https://infinityweb.tn
 * Author Agency: Infinity Web
 * Text Domain: simple-custom-fields
 * Domain Path: /languages
 * 
 * Security: Protection CSRF, Rate Limiting, Validation stricte
 */

if (!defined('ABSPATH')) {
    exit;
}

// Définition des constantes
define('SCF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SCF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SCF_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Chargement des fichiers de classes
require_once SCF_PLUGIN_DIR . 'includes/class-scf-database.php';
require_once SCF_PLUGIN_DIR . 'includes/class-scf-security.php';
require_once SCF_PLUGIN_DIR . 'includes/class-scf-simple-custom-fields.php';
require_once SCF_PLUGIN_DIR . 'includes/class-scf-admin-page.php';
require_once SCF_PLUGIN_DIR . 'includes/class-scf-meta-boxes.php';
require_once SCF_PLUGIN_DIR . 'includes/class-scf-fields.php';
require_once SCF_PLUGIN_DIR . 'includes/class-scf-import-export.php';

// Fonction pour récupérer un champ personnalisé
function scf_get_field($field_name, $post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    error_log("=== SCF GET FIELD START ===");
    error_log('Recherche du champ : ' . $field_name . ' pour le post ID : ' . $post_id);

    // Récupérer seulement les groupes de champs actifs et correspondant au type de contenu actuel
    $current_post_type = get_post_type($post_id);
    error_log('Type de contenu actuel : ' . $current_post_type);

    $groups = get_posts(array(
        'post_type' => 'scf-field-group',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));

    error_log('Nombre de groupes trouvés : ' . count($groups));

    foreach ($groups as $group) {
        $rules = get_post_meta($group->ID, 'scf_rules', true);
        error_log('Règles du groupe ' . $group->ID . ' : ' . print_r($rules, true));

        // Vérifier si ce groupe s'applique au type de contenu actuel
        if (empty($rules) || $rules['type'] !== 'post_type' || $rules['value'] !== $current_post_type) {
            error_log('Groupe ' . $group->ID . ' ignoré - ne correspond pas au type de contenu');
            continue;
        }

        $fields = get_post_meta($group->ID, 'scf_fields', true);
        $db = SCF_Database::get_instance();
        $values = array();
        
        foreach ($fields as $field) {
            $db_field = $db->get_field($post_id, $group->ID, $field['name']);
            if ($db_field) {
                $values[$field['name']] = $db_field->field_value;
            }
        }
        
        error_log('Champs du groupe : ' . print_r($fields, true));
        error_log('Valeurs trouvées : ' . print_r($values, true));
        
        if (!empty($fields) && is_array($values)) {
            foreach ($fields as $field) {
                if ($field['name'] === $field_name && isset($values[$field_name])) {
                    $value = $values[$field_name];
                    error_log('Valeur trouvée pour ' . $field_name . ' : ' . print_r($value, true));
                    
                    if ($value === '') {
                        error_log("=== SCF GET FIELD END - EMPTY ===");
                        return '—';
                    }

                    // Formater la valeur selon le type de champ
                    switch ($field['type']) {
                        case 'checkbox':
                            if (!is_array($value)) {
                                $value = array($value);
                            }
                            $formatted = !empty($value) ? implode(', ', array_map('esc_html', $value)) : '—';
                            error_log('Valeur formatée (checkbox) : ' . $formatted);
                            return $formatted;
                            
                        case 'email':
                            $formatted = !empty($value) ? sprintf('<a href="mailto:%1$s">%1$s</a>', esc_attr($value)) : '—';
                            error_log('Valeur formatée (email) : ' . $formatted);
                            return $formatted;
                            
                        case 'textarea':
                            $formatted = !empty($value) ? nl2br(esc_html($value)) : '—';
                            error_log('Valeur formatée (textarea) : ' . $formatted);
                            return $formatted;
                            
                        default:
                            $formatted = !empty($value) ? esc_html($value) : '—';
                            error_log('Valeur formatée (default) : ' . $formatted);
                            return $formatted;
                    }
                }
            }
        }
    }

    error_log('Aucune valeur trouvée pour ' . $field_name);
    error_log("=== SCF GET FIELD END - NOT FOUND ===");
    return null;
}

// Chargement des traductions avec le nouveau système
function scf_load_textdomain() {
    // Utiliser le gestionnaire de traductions si disponible
    if (class_exists('SCF\Utilities\I18nManager')) {
        $i18n = SCF\Utilities\I18nManager::getInstance();
        $i18n->init();
    } else {
        // Fallback vers l'ancien système
        load_plugin_textdomain('simple-custom-fields', false, dirname(SCF_PLUGIN_BASENAME) . '/languages');
    }
}
add_action('plugins_loaded', 'scf_load_textdomain', 1);

// Fonction pour afficher les champs personnalisés sur le front-end via shortcode
function scf_display_custom_fields_shortcode($atts) {
    error_log("=== SCF SHORTCODE START ===");
    
    $post_id = get_the_ID();
    $current_post_type = get_post_type($post_id);
    
    error_log('Post ID: ' . $post_id);
    error_log('Post Type: ' . $current_post_type);

    // Récupérer les groupes actifs
    $groups = get_posts(array(
        'post_type' => 'scf-field-group',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));

    $output = '';

    foreach ($groups as $group) {
        $rules = get_post_meta($group->ID, 'scf_rules', true);
        error_log('Checking group ' . $group->ID . ' with rules: ' . print_r($rules, true));

        if (!empty($rules) && $rules['type'] === 'post_type' && $rules['value'] === $current_post_type) {
            $fields = get_post_meta($group->ID, 'scf_fields', true);
            $db = SCF_Database::get_instance();
            $values = array();
            
            foreach ($fields as $field) {
                $db_field = $db->get_field($post_id, $group->ID, $field['name']);
                if ($db_field) {
                    $values[$field['name']] = $db_field->field_value;
                }
            }
            
            error_log('Group matches post type. Fields: ' . print_r($fields, true));
            error_log('Values: ' . print_r($values, true));

            if (!empty($fields) && !empty($values)) {
                $output .= '<div class="scf-frontend-container">';
                $output .= '<h2>' . esc_html($group->post_title) . '</h2>';
                $output .= '<div class="scf-fields-container">';

                foreach ($fields as $field) {
                    if (isset($values[$field['name']])) {
                        $value = $values[$field['name']];
                        $output .= '<div class="scf-field-row">';
                        $output .= '<label>' . esc_html($field['label']) . '</label>';
                        $output .= '<div class="scf-field-value">';
                        $output .= scf_get_field($field['name'], $post_id);
                        $output .= '</div></div>';
                    }
                }

                $output .= '</div></div>';
            }
        }
    }

    error_log('Generated output: ' . $output);
    error_log("=== SCF SHORTCODE END ===");
    return $output;
}
add_shortcode('scf_fields', 'scf_display_custom_fields_shortcode');

// Hooks d'activation/désactivation
register_activation_hook(__FILE__, function() {
    error_log('=== SCF PLUGIN ACTIVATION START ===');
    
    // Enregistrer le type de post personnalisé
    $plugin = SCF_Simple_Custom_Fields::get_instance();
    $plugin->register_field_group_post_type();
    error_log('Post type registered');
    
    // Créer la table personnalisée
    $db = SCF_Database::get_instance();
    $result = $db->create_table();
    error_log('Table creation result: ' . ($result ? 'success' : 'failed'));
    
    // Vérifier si la table existe
    global $wpdb;
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}scf_fields'") == "{$wpdb->prefix}scf_fields";
    error_log('Table verification: ' . ($table_exists ? 'exists' : 'missing'));
    
    error_log('=== SCF PLUGIN ACTIVATION END ===');
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function() {
    error_log('SCF Plugin Deactivation');
    flush_rewrite_rules();
});

// Initialisation séquentielle du plugin
$scf_plugin = SCF_Simple_Custom_Fields::get_instance();

// 1. Chargement des traductions
add_action('plugins_loaded', 'scf_load_textdomain', 1);

// 2. Enregistrement du type de post personnalisé
add_action('init', function() use ($scf_plugin) {
    error_log('=== SCF INIT START ===');
    $scf_plugin->register_field_group_post_type();
    error_log('Post type registration complete');
}, 1);

// 3. Initialisation des composants principaux
add_action('init', function() {
    error_log('SCF Component Init');
    SCF_Admin_Page::get_instance();
    SCF_Fields::get_instance();
}, 5);

// 4. Initialisation des meta boxes (après l'enregistrement du type de post)
add_action('init', function() {
    error_log('SCF Meta Boxes Init');
    SCF_Meta_Boxes::get_instance();
    
    // Vérification du type de post
    $post_type = get_post_type_object('scf-field-group');
    error_log('SCF post type check: ' . ($post_type ? 'registered' : 'not registered'));
}, 10);
