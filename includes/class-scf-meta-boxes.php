<?php
if (!defined('ABSPATH')) exit;

class SCF_Meta_Boxes {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        error_log('SCF Meta Boxes construct');
        // S'assurer que nos meta boxes sont ajoutées au bon moment
        add_action('admin_init', function() {
            if (!post_type_exists('scf-field-group')) {
                error_log('SCF Error: Post type not registered at admin_init');
                return;
            }
            error_log('SCF: Adding meta boxes hook');
            add_action('add_meta_boxes', array($this, 'add_custom_fields_meta_box'), 20, 2);
        });

        add_action('save_post', array($this, 'save_custom_fields'));

        // Debug
        add_action('admin_notices', function() {
            if (isset($_GET['post_type']) && $_GET['post_type'] === 'page') {
                $screen = get_current_screen();
                error_log('Current screen: ' . print_r($screen, true));
                error_log('Meta boxes status: ' . (has_action('add_meta_boxes') ? 'registered' : 'not registered'));
            }
        });

        // Debug
        add_action('admin_init', function() {
            error_log('SCF Meta Boxes hooks registered');
            error_log('add_meta_boxes action exists: ' . (has_action('add_meta_boxes') ? 'yes' : 'no'));
            error_log('save_post action exists: ' . (has_action('save_post') ? 'yes' : 'no'));
        });
    }

    public function add_custom_fields_meta_box($post_type, $post) {
        error_log('=== ADD META BOXES START ===');
        error_log('Post type: ' . $post_type);
        error_log('Post ID: ' . ($post ? $post->ID : 'No post'));

        // Vérifier si nous sommes dans le bon contexte
        if (!in_array($post_type, array('post', 'page'))) {
            error_log('Post type non pris en charge: ' . $post_type);
            return;
        }
        // Récupérer tous les groupes de champs
        $groups = get_posts(array(
            'post_type' => 'scf-field-group',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));

        foreach ($groups as $group) {
            $rules = get_post_meta($group->ID, 'scf_rules', true);
            $fields = get_post_meta($group->ID, 'scf_fields', true);

            // On s'assure d'avoir des règles valides
            if (empty($rules) || !isset($rules['type']) || !isset($rules['value'])) {
                $rules = array(
                    'type' => 'post_type',
                    'value' => 'page'
                );
            }

            error_log(sprintf(
                'Checking group %d for post_type "%s" against rule value "%s"',
                $group->ID,
                $post_type,
                $rules['value']
            ));
            
            if ($rules['type'] === 'post_type' && $rules['value'] === $post_type) {
                error_log(sprintf('Adding meta box for group %d - Fields count: %d',
                    $group->ID,
                    count($fields)
                ));
                
                add_meta_box(
                    'scf-' . $group->ID,
                    $group->post_title,
                    array($this, 'render_meta_box'),
                    $post_type,
                    'normal',
                    'high',
                    array(
                        'fields' => $fields,
                        'group_id' => $group->ID,
                        '__block_editor_compatible_meta_box' => true,
                        '__back_compat_meta_box' => false,
                    )
                );
                
                // Ajouter la classe CSS à la meta box
                add_filter('postbox_classes_' . $post_type . '_scf-' . $group->ID, function($classes) {
                    $classes[] = 'scf-meta-box';
                    return $classes;
                });
            } else {
                error_log('Group ' . $group->ID . ' does not apply to post type ' . $post_type);
            }
        }
    }

    public function render_meta_box($post, $meta_box) {
        $fields = $meta_box['args']['fields'];
        $group_id = $meta_box['args']['group_id'];
        $db = SCF_Database::get_instance();
        $values = array();
        
        foreach ($fields as $field) {
            $db_field = $db->get_field($post->ID, $group_id, $field['name']);
            if ($db_field) {
                $values[$field['name']] = $db_field->field_value;
            }
        }
        
        error_log('Rendering meta box for post ' . $post->ID . ' and group ' . $group_id);
        error_log('Fields: ' . print_r($fields, true));
        error_log('Values: ' . print_r($values, true));
        
        wp_nonce_field('scf_meta_box', 'scf_meta_box_nonce');
        
        require SCF_PLUGIN_DIR . 'templates/meta-box.php';
    }

    public function save_custom_fields($post_id) {
        error_log('Starting save_custom_fields for post ' . $post_id);

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            error_log('Skipping autosave');
            return;
        }

        if (!isset($_POST['scf_meta_box_nonce']) || !wp_verify_nonce($_POST['scf_meta_box_nonce'], 'scf_meta_box')) {
            error_log('Invalid nonce');
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            error_log('User cannot edit post');
            return;
        }

        if (!isset($_POST['scf_fields']) || !is_array($_POST['scf_fields'])) {
            error_log('No fields to save');
            return;
        }

        error_log('Posted fields: ' . print_r($_POST['scf_fields'], true));

        $db = SCF_Database::get_instance();
        $groups = get_posts(array(
            'post_type' => 'scf-field-group',
            'posts_per_page' => -1
        ));

        // Vérifier le format des données POST
        error_log('Full POST data: ' . print_r($_POST, true));
        
        foreach ($_POST['scf_fields'] as $group_id => $group_fields) {
            $fields = get_post_meta($group_id, 'scf_fields', true);
            error_log('Processing group ' . $group_id . ' with fields: ' . print_r($fields, true));
            
            if (!empty($fields)) {
                foreach ($fields as $field) {
                    $field_name = $field['name'];
                    $post_field_name = 'scf_fields['.$group_id.']['.$field_name.']';
                    
                    if (isset($_POST['scf_fields'][$group_id][$field_name])) {
                        $value = $_POST['scf_fields'][$group_id][$field_name];
                        
                        // Sanitize selon le type de champ
                        switch ($field['type']) {
                            case 'checkbox':
                                $sanitized_value = array();
                                if (is_array($value)) {
                                    foreach ($value as $val) {
                                        $sanitized_value[] = sanitize_text_field($val);
                                    }
                                } else {
                                    $sanitized_value = array(sanitize_text_field($value));
                                }
                                break;
                                
                            case 'email':
                                $sanitized_value = sanitize_email($value);
                                break;
                                
                            case 'textarea':
                                $sanitized_value = sanitize_textarea_field($value);
                                break;
                                
                            default:
                                $sanitized_value = sanitize_text_field($value);
                                break;
                        }
                        
                        // Sauvegarder dans la nouvelle table
                        $db->save_field($post_id, $group_id, $field_name, $sanitized_value);
                        error_log('Saved field ' . $field_name . ' to database');
                    }
                }
            }
        }
    }
}
