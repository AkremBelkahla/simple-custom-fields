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

    // Debug: Afficher le champ recherché et l'ID du post
    error_log('Recherche du champ : ' . $field_name . ' pour le post ID : ' . $post_id);

    // Récupérer seulement les groupes de champs actifs
    $groups = get_posts(array(
        'post_type' => 'scf-field-group',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));

    // Debug: Afficher le nombre de groupes trouvés
    error_log('Nombre de groupes trouvés : ' . count($groups));

    foreach ($groups as $group) {
        $fields = get_post_meta($group->ID, 'scf_fields', true);
        $values = get_post_meta($post_id, '_scf_values_' . $group->ID, true);

        // Debug: Afficher les champs et valeurs trouvés
        error_log('Champs trouvés dans le groupe ' . $group->ID . ' : ' . print_r($fields, true));
        error_log('Valeurs trouvées pour le post : ' . print_r($values, true));

        if (!empty($fields)) {
            foreach ($fields as $field) {
                if ($field['name'] === $field_name) {
                    $result = isset($values[$field_name]) ? $values[$field_name] : null;
                    error_log('Valeur trouvée pour ' . $field_name . ' : ' . print_r($result, true));
                    return $result;
                }
            }
        }
    }

    error_log('Aucune valeur trouvée pour ' . $field_name);
    return null;
}

// Chargement des traductions
function scf_load_textdomain() {
    load_plugin_textdomain('simple-custom-fields', false, SCF_PLUGIN_DIR . 'languages');
}
add_action('plugins_loaded', 'scf_load_textdomain');

// Fonction pour afficher les champs personnalisés sur le front-end via shortcode
function scf_display_custom_fields_shortcode() {
    $post_id = get_the_ID();
    $groups = get_posts(array(
        'post_type' => 'scf-field-group',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));

    $output = '';

    foreach ($groups as $group) {
        $rules = get_post_meta($group->ID, 'scf_rules', true);
        if ($rules && $rules['param'] === 'post_type' && $rules['value'] === 'page') {
            $fields = get_post_meta($group->ID, 'scf_fields', true);
            $values = get_post_meta($post_id, '_scf_values_' . $group->ID, true);

            if (!empty($fields)) {
                $output .= '<div class="scf-frontend-container">';
                $output .= '<h2>' . esc_html($group->post_title) . '</h2>';
                $output .= '<div class="scf-fields-container">';

                foreach ($fields as $field) {
                    $field_value = isset($values[$field['name']]) ? $values[$field['name']] : '';
                    $output .= '<div class="scf-field-row">';
                    $output .= '<label>' . esc_html($field['label']) . '</label>';

                    switch ($field['type']) {
                        case 'text':
                        case 'email':
                            $output .= '<p>' . esc_html($field_value) . '</p>';
                            break;
                        case 'textarea':
                            $output .= '<p>' . esc_textarea($field_value) . '</p>';
                            break;
                        case 'select':
                        case 'radio':
                            $output .= '<p>' . esc_html($field_value) . '</p>';
                            break;
                        case 'checkbox':
                            if (is_array($field_value)) {
                                $output .= '<ul>';
                                foreach ($field_value as $value) {
                                    $output .= '<li>' . esc_html($value) . '</li>';
                                }
                                $output .= '</ul>';
                            }
                            break;
                    }

                    $output .= '</div>';
                }

                $output .= '</div></div>';
            }
        }
    }

    return $output;
}
add_shortcode('scf_fields', 'scf_display_custom_fields_shortcode');

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
    error_log('Initializing SCF plugin');
    $admin = SCF_Admin_Page::get_instance();
    $fields = SCF_Fields::get_instance();
    
    // Debug hooks
    error_log('Admin hooks registered: ' . has_action('admin_enqueue_scripts', array($admin, 'enqueue_admin_scripts')));
    error_log('Admin menu registered: ' . has_action('admin_menu', array($admin, 'add_admin_menu')));
}
add_action('plugins_loaded', 'scf_boot', 1); // Priorité plus haute
