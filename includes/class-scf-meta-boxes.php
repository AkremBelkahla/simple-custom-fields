<?php
if (!defined('ABSPATH')) exit;

class SCF_Meta_Boxes {
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_custom_fields_meta_box'));
        add_action('save_post', array($this, 'save_custom_fields'));
    }

    public function add_custom_fields_meta_box() {
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

            if (!empty($rules)) {
                $post_types = array('post', 'page');
                foreach ($post_types as $post_type) {
                    add_meta_box(
                        'scf-' . $group->ID,
                        $group->post_title,
                        array($this, 'render_meta_box'),
                        $post_type,
                        'normal',
                        'high',
                        array('fields' => $fields)
                    );
                }
            }
        }
    }

    public function render_meta_box($post, $meta_box) {
        $fields = $meta_box['args']['fields'];
        wp_nonce_field('scf_meta_box', 'scf_meta_box_nonce');
        require SCF_PLUGIN_DIR . 'templates/meta-box.php';
    }

    public function save_custom_fields($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!isset($_POST['scf_meta_box_nonce']) || !wp_verify_nonce($_POST['scf_meta_box_nonce'], 'scf_meta_box')) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $groups = get_posts(array(
            'post_type' => 'scf-field-group',
            'posts_per_page' => -1
        ));

        foreach ($groups as $group) {
            $fields = get_post_meta($group->ID, 'scf_fields', true);
            if (!empty($fields)) {
                foreach ($fields as $field) {
                    if (isset($_POST['scf_fields'][$field['name']])) {
                        update_post_meta(
                            $post_id,
                            'scf_' . $field['name'],
                            sanitize_text_field($_POST['scf_fields'][$field['name']])
                        );
                    }
                }
            }
        }
    }
}