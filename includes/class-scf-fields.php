<?php

if (!defined('ABSPATH')) {
    exit;
}

class SCF_Fields {
    private static $instance = null;
    private $available_fields = array();

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Initialiser les types de champs disponibles
        $this->register_default_fields();
        
        // Ajouter les hooks nécessaires
        add_action('wp_ajax_scf_get_field_settings', array($this, 'ajax_get_field_settings'));
    }

    /**
     * Enregistre les types de champs par défaut
     */
    public function register_default_fields() {
        $this->available_fields = array(
            'text' => array(
                'label' => __('Texte', 'simple-custom-fields'),
                'class' => 'SCF\\Fields\\Text',
                'settings' => array(
                    'placeholder' => array(
                        'label' => __('Placeholder', 'simple-custom-fields'),
                        'type' => 'text'
                    ),
                    'default_value' => array(
                        'label' => __('Valeur par défaut', 'simple-custom-fields'),
                        'type' => 'text'
                    )
                )
            ),
            'textarea' => array(
                'label' => __('Zone de texte', 'simple-custom-fields'),
                'settings' => array(
                    'placeholder' => array(
                        'label' => __('Placeholder', 'simple-custom-fields'),
                        'type' => 'text'
                    ),
                    'rows' => array(
                        'label' => __('Nombre de lignes', 'simple-custom-fields'),
                        'type' => 'number',
                        'default' => 5
                    )
                )
            ),
            'email' => array(
                'label' => __('Email', 'simple-custom-fields'),
                'settings' => array(
                    'placeholder' => array(
                        'label' => __('Placeholder', 'simple-custom-fields'),
                        'type' => 'text'
                    )
                )
            ),
            'number' => array(
                'label' => __('Nombre', 'simple-custom-fields'),
                'settings' => array(
                    'min' => array(
                        'label' => __('Valeur minimum', 'simple-custom-fields'),
                        'type' => 'number'
                    ),
                    'max' => array(
                        'label' => __('Valeur maximum', 'simple-custom-fields'),
                        'type' => 'number'
                    ),
                    'step' => array(
                        'label' => __('Pas', 'simple-custom-fields'),
                        'type' => 'number',
                        'default' => 1
                    )
                )
            ),
            'checkbox' => array(
                'label' => __('Case à cocher', 'simple-custom-fields'),
                'settings' => array(
                    'options' => array(
                        'label' => __('Options (une par ligne)', 'simple-custom-fields'),
                        'type' => 'textarea'
                    )
                )
            ),
            'select' => array(
                'label' => __('Liste déroulante', 'simple-custom-fields'),
                'settings' => array(
                    'options' => array(
                        'label' => __('Options (une par ligne)', 'simple-custom-fields'),
                        'type' => 'textarea'
                    ),
                    'multiple' => array(
                        'label' => __('Sélection multiple', 'simple-custom-fields'),
                        'type' => 'checkbox'
                    )
                )
            ),
            'radio' => array(
                'label' => __('Boutons radio', 'simple-custom-fields'),
                'settings' => array(
                    'options' => array(
                        'label' => __('Options (une par ligne)', 'simple-custom-fields'),
                        'type' => 'textarea'
                    )
                )
            ),
            'date' => array(
                'label' => __('Date', 'simple-custom-fields'),
                'settings' => array(
                    'format' => array(
                        'label' => __('Format de date', 'simple-custom-fields'),
                        'type' => 'text',
                        'default' => 'Y-m-d'
                    )
                )
            ),
            'url' => array(
                'label' => __('URL', 'simple-custom-fields'),
                'settings' => array(
                    'placeholder' => array(
                        'label' => __('Placeholder', 'simple-custom-fields'),
                        'type' => 'text'
                    )
                )
            ),
            'file' => array(
                'label' => __('Fichier', 'simple-custom-fields'),
                'settings' => array(
                    'allowed_types' => array(
                        'label' => __('Types de fichiers autorisés (séparés par des virgules)', 'simple-custom-fields'),
                        'type' => 'text',
                        'default' => 'jpg,jpeg,png,pdf,doc,docx'
                    )
                )
            )
        );
    }

    /**
     * Récupère la liste des types de champs disponibles
     */
    public function get_field_types() {
        return apply_filters('scf_field_types', $this->available_fields);
    }

    /**
     * Récupère les paramètres d'un type de champ spécifique
     */
    public function get_field_settings($field_type) {
        $field_types = $this->get_field_types();
        
        if (isset($field_types[$field_type])) {
            return $field_types[$field_type]['settings'];
        }
        
        return array();
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
        
        // Récupérer les paramètres du champ
        $settings = $this->get_field_settings($field_type);
        
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

    /**
     * Rendu d'un champ pour l'administration
     */
    public function render_admin_field($field, $value = '') {
        $field_types = $this->get_field_types();
        
        if (!isset($field_types[$field['type']])) {
            return '';
        }
        
        $output = '<div class="scf-field-wrapper scf-field-type-' . esc_attr($field['type']) . '">';
        
        switch ($field['type']) {
            case 'text':
                $placeholder = isset($field['settings']['placeholder']) ? $field['settings']['placeholder'] : '';
                $output .= '<input type="text" name="scf_fields[' . esc_attr($field['name']) . ']" id="scf-field-' . esc_attr($field['name']) . '" value="' . esc_attr($value) . '" placeholder="' . esc_attr($placeholder) . '">';
                break;
                
            case 'textarea':
                $placeholder = isset($field['settings']['placeholder']) ? $field['settings']['placeholder'] : '';
                $rows = isset($field['settings']['rows']) ? intval($field['settings']['rows']) : 5;
                $output .= '<textarea name="scf_fields[' . esc_attr($field['name']) . ']" id="scf-field-' . esc_attr($field['name']) . '" rows="' . esc_attr($rows) . '" placeholder="' . esc_attr($placeholder) . '">' . esc_textarea($value) . '</textarea>';
                break;
                
            case 'email':
                $placeholder = isset($field['settings']['placeholder']) ? $field['settings']['placeholder'] : '';
                $output .= '<input type="email" name="scf_fields[' . esc_attr($field['name']) . ']" id="scf-field-' . esc_attr($field['name']) . '" value="' . esc_attr($value) . '" placeholder="' . esc_attr($placeholder) . '">';
                break;
                
            case 'number':
                $min = isset($field['settings']['min']) ? ' min="' . esc_attr($field['settings']['min']) . '"' : '';
                $max = isset($field['settings']['max']) ? ' max="' . esc_attr($field['settings']['max']) . '"' : '';
                $step = isset($field['settings']['step']) ? ' step="' . esc_attr($field['settings']['step']) . '"' : '';
                $output .= '<input type="number" name="scf_fields[' . esc_attr($field['name']) . ']" id="scf-field-' . esc_attr($field['name']) . '" value="' . esc_attr($value) . '"' . $min . $max . $step . '>';
                break;
                
            case 'checkbox':
                $options = isset($field['settings']['options']) ? explode("\n", $field['settings']['options']) : array();
                
                if (!is_array($value)) {
                    $value = array($value);
                }
                
                foreach ($options as $option) {
                    $option = trim($option);
                    $checked = in_array($option, $value) ? ' checked' : '';
                    $output .= '<label><input type="checkbox" name="scf_fields[' . esc_attr($field['name']) . '][]" value="' . esc_attr($option) . '"' . $checked . '> ' . esc_html($option) . '</label><br>';
                }
                break;
                
            case 'select':
                $options = isset($field['settings']['options']) ? explode("\n", $field['settings']['options']) : array();
                $multiple = isset($field['settings']['multiple']) && $field['settings']['multiple'] ? ' multiple' : '';
                
                if (!is_array($value)) {
                    $value = array($value);
                }
                
                $output .= '<select name="scf_fields[' . esc_attr($field['name']) . ']' . ($multiple ? '[]' : '') . '" id="scf-field-' . esc_attr($field['name']) . '"' . $multiple . '>';
                
                foreach ($options as $option) {
                    $option = trim($option);
                    $selected = in_array($option, $value) ? ' selected' : '';
                    $output .= '<option value="' . esc_attr($option) . '"' . $selected . '>' . esc_html($option) . '</option>';
                }
                
                $output .= '</select>';
                break;
                
            case 'radio':
                $options = isset($field['settings']['options']) ? explode("\n", $field['settings']['options']) : array();
                
                foreach ($options as $option) {
                    $option = trim($option);
                    $checked = $option === $value ? ' checked' : '';
                    $output .= '<label><input type="radio" name="scf_fields[' . esc_attr($field['name']) . ']" value="' . esc_attr($option) . '"' . $checked . '> ' . esc_html($option) . '</label><br>';
                }
                break;
                
            case 'date':
                $format = isset($field['settings']['format']) ? $field['settings']['format'] : 'Y-m-d';
                $output .= '<input type="date" name="scf_fields[' . esc_attr($field['name']) . ']" id="scf-field-' . esc_attr($field['name']) . '" value="' . esc_attr($value) . '">';
                break;
                
            case 'url':
                $placeholder = isset($field['settings']['placeholder']) ? $field['settings']['placeholder'] : '';
                $output .= '<input type="url" name="scf_fields[' . esc_attr($field['name']) . ']" id="scf-field-' . esc_attr($field['name']) . '" value="' . esc_attr($value) . '" placeholder="' . esc_attr($placeholder) . '">';
                break;
                
            case 'file':
                $allowed_types = isset($field['settings']['allowed_types']) ? $field['settings']['allowed_types'] : 'jpg,jpeg,png,pdf,doc,docx';
                $output .= '<input type="file" name="scf_fields[' . esc_attr($field['name']) . ']" id="scf-field-' . esc_attr($field['name']) . '" accept="' . esc_attr($allowed_types) . '">';
                
                if (!empty($value)) {
                    $output .= '<div class="scf-file-preview">';
                    $output .= '<p>' . __('Fichier actuel:', 'simple-custom-fields') . ' ' . esc_html(basename($value)) . '</p>';
                    $output .= '</div>';
                }
                break;
        }
        
        $output .= '</div>';
        
        return $output;
    }
}
