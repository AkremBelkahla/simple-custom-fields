<?php
if (!defined('ABSPATH')) exit;

class SCF_Admin_Page {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_post_scf_save_field_group', array($this, 'save_field_group'));
        add_action('wp_ajax_scf_delete_group', array($this, 'delete_group'));
    }

    public function enqueue_admin_scripts($hook) {
        if ($hook === 'toplevel_page_simple-custom-fields') {
            wp_enqueue_script('scf-admin', SCF_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), '1.0.0', true);
            wp_localize_script('scf-admin', 'scf_vars', array(
                'nonce' => wp_create_nonce('scf_delete_group')
            ));
        }
    }

    public function add_admin_menu() { 
        add_menu_page(
            'Simple Custom Fields',
            'Simple Custom Fields',
            'manage_options',
            'simple-custom-fields',
            array($this, 'render_groups_page'),
            'dashicons-admin-generic',
            30
        );

        add_submenu_page(
            'simple-custom-fields',
            'Ajouter un groupe',
            'Ajouter un groupe',
            'manage_options',
            'scf-add-group',
            array($this, 'render_add_group_page')
        );
    }

    public function render_groups_page() {
        $groups = get_posts(array(
            'post_type' => 'scf-field-group',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_status' => array('publish', 'draft')
        ));
        require_once SCF_PLUGIN_DIR . 'templates/groups-page.php';
    }

    public function render_add_group_page() {
        $group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
        $group = null;
        $fields = array();
        $rules = array();

        if ($group_id) {
            $group = get_post($group_id);
            if ($group) {
                $fields = get_post_meta($group_id, 'scf_fields', true) ?: array();
                $rules = get_post_meta($group_id, 'scf_rules', true) ?: array();
            }
        }

        require_once SCF_PLUGIN_DIR . 'templates/add-group-page.php';
    }

    public function save_field_group() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires pour effectuer cette action.'));
        }

        if (!isset($_POST['scf_nonce']) || !wp_verify_nonce($_POST['scf_nonce'], 'scf_save_field_group')) {
            wp_die(__('Nonce de sécurité invalide.'));
        }

        // Débogage
        error_log('POST Data: ' . print_r($_POST, true));

        $group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;
        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
        $fields = isset($_POST['fields']) ? $_POST['fields'] : array();
        
        // Débogage des champs avant sanitization
        error_log('Fields before sanitization: ' . print_r($fields, true));
        
        $fields = $this->sanitize_fields($fields);
        
        // Débogage des champs après sanitization
        error_log('Fields after sanitization: ' . print_r($fields, true));

        $rules = isset($_POST['rules']) ? $this->sanitize_rules($_POST['rules']) : array();

        if (empty($title)) {
            wp_die(__('Le titre du groupe est obligatoire.'));
        }

        $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
        $status = isset($_POST['status']) ? $_POST['status'] : 'draft';

        $post_data = array(
            'post_title' => $title,
            'post_type' => 'scf-field-group',
            'post_status' => $status,
            'post_content' => $description
        );

        if ($group_id) {
            $post_data['ID'] = $group_id;
            $group_id = wp_update_post($post_data);
        } else {
            $group_id = wp_insert_post($post_data);
        }

        if (is_wp_error($group_id)) {
            wp_die($group_id->get_error_message());
        }

        // Débogage avant la sauvegarde
        error_log('Saving fields for group ' . $group_id . ': ' . print_r($fields, true));
        
        update_post_meta($group_id, 'scf_fields', $fields);
        update_post_meta($group_id, 'scf_rules', $rules);

        wp_redirect(admin_url('admin.php?page=simple-custom-fields&message=success'));
        exit;
    }

    public function delete_group() {
        check_ajax_referer('scf_delete_group', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }

        $group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;
        if (!$group_id) {
            wp_send_json_error('Invalid group ID');
        }

        $result = wp_delete_post($group_id, true);
        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to delete group');
        }
    }

    private function sanitize_fields($fields) {
        if (!is_array($fields)) {
            error_log('Les champs ne sont pas un tableau');
            return array();
        }

        error_log('Champs reçus: ' . print_r($fields, true));

        $sanitized = array();
        foreach ($fields as $field) {
            if (empty($field['name']) || empty($field['type'])) {
                error_log('Champ ignoré car nom ou type manquant: ' . print_r($field, true));
                continue;
            }

            $sanitized_field = array(
                'name' => sanitize_key($field['name']),
                'label' => sanitize_text_field($field['label']),
                'type' => sanitize_key($field['type'])
            );

            error_log('Traitement du champ: ' . print_r($sanitized_field, true));

            // Gestion des options pour les champs select, radio et checkbox
            if (in_array($field['type'], array('select', 'radio', 'checkbox'))) {
                error_log('Champ avec options détecté: ' . $field['type']);
                
                $options = isset($field['options']) ? $field['options'] : '';
                error_log('Options brutes: ' . print_r($options, true));
                
                // Si les options sont une chaîne JSON, on les décode
                if (is_string($options)) {
                    // Suppression des backslashes en trop
                    $options = stripslashes($options);
                    error_log('Options après stripslashes: ' . $options);
                    
                    $decoded = json_decode($options, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $options = $decoded;
                        error_log('JSON décodé avec succès: ' . print_r($options, true));
                    } else {
                        error_log('Erreur JSON: ' . json_last_error_msg());
                        $options = array();
                    }
                }

                // Traitement des options
                if (is_array($options)) {
                    $sanitized_options = array();
                    foreach ($options as $option) {
                        if (!empty($option['label'])) {
                            $sanitized_option = array(
                                'label' => sanitize_text_field($option['label']),
                                'value' => !empty($option['value']) ? sanitize_key($option['value']) : sanitize_key($option['label'])
                            );
                            $sanitized_options[] = $sanitized_option;
                            error_log('Option sanitizée: ' . print_r($sanitized_option, true));
                        }
                    }
                    $sanitized_field['options'] = $sanitized_options;
                } else {
                    error_log('Les options ne sont pas un tableau');
                    $sanitized_field['options'] = array();
                }

                error_log('Options finales: ' . print_r($sanitized_field['options'], true));
            }

            $sanitized[] = $sanitized_field;
        }

        error_log('Champs sanitizés: ' . print_r($sanitized, true));
        return $sanitized;
    }

    private function sanitize_rules($rules) {
        if (!is_array($rules) || empty($rules)) {
            return array(
                'param' => 'post_type',
                'operator' => '=',
                'value' => 'post'
            );
        }

        return array(
            'param' => isset($rules['type']) ? sanitize_key($rules['type']) : 'post_type',
            'operator' => isset($rules['operator']) && in_array($rules['operator'], array('==', '!=')) ? $rules['operator'] : '==',
            // Modification ici pour utiliser directement la valeur sans sanitize_key
            'value' => isset($rules['value']) ? $rules['value'] : 'post'
        );
    }

    private function sanitize_options($options) {
        if (!is_array($options)) {
            return array();
        }

        $sanitized = array();
        foreach ($options as $option) {
            if (empty($option['value'])) {
                continue;
            }

            $sanitized[] = array(
                'value' => sanitize_text_field($option['value']),
                'label' => sanitize_text_field($option['label'])
            );
        }

        return $sanitized;
    }
}
