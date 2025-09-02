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
        add_action('wp_ajax_scf_get_field_settings', array($this, 'ajax_get_field_settings'));
    }

    public function enqueue_admin_scripts($hook) {
        $allowed_pages = array(
            'toplevel_page_simple-custom-fields',
            'simple-custom-fields_page_scf-add-group',
            'admin_page_simple-custom-fields'
        );

        if (in_array($hook, $allowed_pages)) {
            wp_enqueue_script('jquery');
            wp_enqueue_script('scf-admin', SCF_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), '1.0.1_' . time(), true);

            wp_localize_script('scf-admin', 'scf_vars', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('scf_nonce'),
                'action' => 'scf_delete_group',
                'isAdmin' => current_user_can('manage_options') ? '1' : '0'
            ));

            // Ajout des variables JavaScript directement dans le template groups-page.php
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
        
        // Récupérer les types de champs disponibles
        $field_types = SCF_Fields::get_instance()->get_field_types();

        require_once SCF_PLUGIN_DIR . 'templates/add-group-page.php';
    }

    public function save_field_group() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires pour effectuer cette action.'));
        }

        if (!isset($_POST['scf_nonce']) || !wp_verify_nonce($_POST['scf_nonce'], 'scf_save_field_group')) {
            wp_die(__('Nonce de sécurité invalide.'));
        }

        $group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;
        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
        $fields = isset($_POST['fields']) ? $_POST['fields'] : array();
        $fields = $this->sanitize_fields($fields);
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

        update_post_meta($group_id, 'scf_fields', $fields);
        update_post_meta($group_id, 'scf_rules', $rules);

        wp_redirect(admin_url('admin.php?page=simple-custom-fields&message=success'));
        exit;
    }

    public function delete_group() {
        try {
            // Vérification de la méthode HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Méthode non autorisée');
            }

            // Vérification AJAX
            if (!defined('DOING_AJAX') || !DOING_AJAX) {
                throw new Exception('Requête invalide');
            }

            // Vérification des permissions
            if (!current_user_can('manage_options')) {
                throw new Exception('Permissions insuffisantes');
            }

            if (!isset($_POST['nonce'])) {
                throw new Exception('Nonce manquant');
            }

            $nonce = sanitize_text_field($_POST['nonce']);

            if (!wp_verify_nonce($nonce, 'scf_nonce')) {
                throw new Exception('Session expirée - Veuillez rafraîchir la page');
            }

            // Vérification du referrer
            $referrer = wp_get_referer();
            if (!$referrer || strpos($referrer, admin_url()) !== 0) {
                throw new Exception('Origine de la requête non autorisée');
            }

            // Récupération et validation de l'ID
            $group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;
            if (!$group_id) {
                throw new Exception('ID de groupe invalide');
            }

            // Vérification du groupe
            $group = get_post($group_id);
            error_log('Group object: ' . print_r($group, true));
            if (!$group || $group->post_type !== 'scf-field-group') {
                throw new Exception('Groupe non trouvé ou type invalide');
            }

            // Suppression des meta données
            $fields_deleted = delete_post_meta($group_id, 'scf_fields');
            $rules_deleted = delete_post_meta($group_id, 'scf_rules');
            error_log('Metadata deletion result - fields: ' . $fields_deleted . ', rules: ' . $rules_deleted);

            // Suppression du post
            $result = wp_delete_post($group_id, true);
            if (!$result) {
                throw new Exception(__('Échec de la suppression du groupe', 'simple-custom-fields'));
            }

            wp_send_json_success(array(
                'message' => 'Groupe supprimé avec succès',
                'group_id' => $group_id
            ));

        } catch (Exception $e) {
            error_log('Delete group error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            wp_send_json_error(array(
                'message' => $e->getMessage(),
                'trace' => WP_DEBUG ? $e->getTraceAsString() : null,
                'debug_info' => array(
                    'nonce' => isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 'not set',
                    'user_can_manage' => current_user_can('manage_options'),
                    'request_method' => $_SERVER['REQUEST_METHOD']
                )
            ));
        }

        wp_die();
    }

    private function sanitize_fields($fields) {
        if (!is_array($fields)) {
            return array();
        }

        $sanitized = array();
        foreach ($fields as $field) {
            if (empty($field['name']) || empty($field['type'])) {
                continue;
            }

            $sanitized_field = array(
                'name' => sanitize_key($field['name']),
                'label' => sanitize_text_field($field['label']),
                'type' => sanitize_key($field['type'])
            );

            // Récupérer les paramètres du champ
            if (isset($field['settings']) && is_array($field['settings'])) {
                $sanitized_settings = array();
                foreach ($field['settings'] as $key => $value) {
                    $sanitized_key = sanitize_key($key);
                    
                    // Traitement spécifique selon le type de paramètre
                    if (is_array($value)) {
                        $sanitized_settings[$sanitized_key] = $this->sanitize_array_recursive($value);
                    } elseif (is_numeric($value)) {
                        $sanitized_settings[$sanitized_key] = floatval($value);
                    } else {
                        $sanitized_settings[$sanitized_key] = sanitize_text_field($value);
                    }
                }
                $sanitized_field['settings'] = $sanitized_settings;
            }

            // Gestion des options pour les champs select, radio et checkbox
            if (in_array($field['type'], array('select', 'radio', 'checkbox'))) {
                $options = isset($field['options']) ? $field['options'] : '';

                // Si les options sont une chaîne JSON, on les décode
                if (is_string($options)) {
                    $options = stripslashes($options);
                    $decoded = json_decode($options, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $options = $decoded;
                    } else {
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
                                'value' => !empty($option['value']) ? $option['value'] : sanitize_key($option['label'])
                            );
                            $sanitized_options[] = $sanitized_option;
                        }
                    }
                    $sanitized_field['options'] = $sanitized_options;
                } else {
                    $sanitized_field['options'] = array();
                }
            }

            $sanitized[] = $sanitized_field;
        }

        return $sanitized;
    }
    
    /**
     * Sanitize un tableau de manière récursive
     */
    private function sanitize_array_recursive($array) {
        if (!is_array($array)) {
            return sanitize_text_field($array);
        }
        
        $sanitized = array();
        foreach ($array as $key => $value) {
            $sanitized_key = sanitize_key($key);
            
            if (is_array($value)) {
                $sanitized[$sanitized_key] = $this->sanitize_array_recursive($value);
            } elseif (is_numeric($value)) {
                $sanitized[$sanitized_key] = floatval($value);
            } else {
                $sanitized[$sanitized_key] = sanitize_text_field($value);
            }
        }
        
        return $sanitized;
    }

    private function sanitize_rules($rules) {
        error_log('Sanitizing rules: ' . print_r($rules, true));
        
        if (!is_array($rules) || empty($rules)) {
            $default_rules = array(
                'type' => 'post_type',
                'operator' => '=',
                'value' => 'page'  // Changé de 'post' à 'page' par défaut
            );
            error_log('Using default rules: ' . print_r($default_rules, true));
            return $default_rules;
        }

        // Vérifier si le type de contenu existe
        $post_types = get_post_types(array('public' => true), 'names');
        error_log('Available post types: ' . print_r($post_types, true));

        $sanitized_rules = array(
            'type' => 'post_type',
            'operator' => '=',
            'value' => isset($rules['value']) && in_array($rules['value'], $post_types) ? $rules['value'] : 'page'
        );

        error_log('Sanitized rules: ' . print_r($sanitized_rules, true));
        return $sanitized_rules;
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
                'value' => $option['value'],
                'label' => sanitize_text_field($option['label'])
            );
        }

        return $sanitized;
    }
    
    /**
     * Callback AJAX pour récupérer les paramètres d'un type de champ
     */
    public function ajax_get_field_settings() {
        // Vérifier le nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'scf_nonce')) {
            wp_send_json_error('Nonce invalide');
        }
        
        // Vérifier les permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permissions insuffisantes');
        }
        
        // Récupérer le type de champ
        $field_type = isset($_POST['field_type']) ? sanitize_text_field($_POST['field_type']) : '';
        
        if (empty($field_type)) {
            wp_send_json_error('Type de champ non spécifié');
        }
        
        // Récupérer les paramètres du champ via la classe SCF_Fields
        $fields = SCF_Fields::get_instance();
        $settings = $fields->get_field_settings($field_type);
        
        // Générer le HTML pour les paramètres
        $html = '';
        
        if (!empty($settings)) {
            foreach ($settings as $key => $setting) {
                $html .= '<div class="scf-field-setting">';
                $html .= '<label for="scf-setting-' . esc_attr($key) . '">' . esc_html($setting['label']) . '</label>';
                
                switch ($setting['type']) {
                    case 'text':
                        $default = isset($setting['default']) ? $setting['default'] : '';
                        $html .= '<input type="text" id="scf-setting-' . esc_attr($key) . '" name="scf-setting-' . esc_attr($key) . '" value="' . esc_attr($default) . '">';
                        break;
                        
                    case 'number':
                        $default = isset($setting['default']) ? $setting['default'] : '';
                        $html .= '<input type="number" id="scf-setting-' . esc_attr($key) . '" name="scf-setting-' . esc_attr($key) . '" value="' . esc_attr($default) . '">';
                        break;
                        
                    case 'textarea':
                        $default = isset($setting['default']) ? $setting['default'] : '';
                        $html .= '<textarea id="scf-setting-' . esc_attr($key) . '" name="scf-setting-' . esc_attr($key) . '">' . esc_textarea($default) . '</textarea>';
                        break;
                        
                    case 'checkbox':
                        $checked = isset($setting['default']) && $setting['default'] ? 'checked' : '';
                        $html .= '<input type="checkbox" id="scf-setting-' . esc_attr($key) . '" name="scf-setting-' . esc_attr($key) . '" ' . $checked . '>';
                        break;
                }
                
                $html .= '</div>';
            }
        } else {
            $html = '<p>' . __('Aucun paramètre disponible pour ce type de champ.', 'simple-custom-fields') . '</p>';
        }
        
        wp_send_json_success(array(
            'html' => $html
        ));
    }
}
