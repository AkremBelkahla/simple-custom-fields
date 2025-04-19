<?php
if (!defined('ABSPATH')) exit;

class SCF_Fields {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_fields'));
    }

    public function add_meta_boxes() {
        $post_type = get_post_type();
        
        // Récupérer tous les groupes de champs
        $groups = get_posts(array(
            'post_type' => 'scf-field-group',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));

        foreach ($groups as $group) {
            $rules = get_post_meta($group->ID, 'scf_rules', true);
            
            // Vérifier si le groupe doit être affiché pour ce type de contenu
            if ($rules && isset($rules['param']) && $rules['param'] === 'post_type' 
                && isset($rules['value']) && $rules['value'] === $post_type) {
                
                add_meta_box(
                    'scf-fields-' . $group->ID,
                    $group->post_title,
                    array($this, 'render_meta_box'),
                    $post_type,
                    'normal',
                    'high',
                    array('group_id' => $group->ID)
                );
            }
        }
    }

    public function render_meta_box($post, $metabox) {
        $group_id = $metabox['args']['group_id'];
        $fields = get_post_meta($group_id, 'scf_fields', true);
        $values = get_post_meta($post->ID, '_scf_values_' . $group_id, true);

        wp_nonce_field('scf_save_fields_' . $group_id, 'scf_nonce_' . $group_id);

        if (!empty($fields)): ?>
            <div class="scf-fields-container">
                <?php foreach ($fields as $field): 
                    $field_name = 'scf[' . $group_id . '][' . $field['name'] . ']';
                    $field_value = isset($values[$field['name']]) ? $values[$field['name']] : '';
                ?>
                    <div class="scf-field-row">
                        <label for="<?php echo esc_attr($field_name); ?>">
                            <?php echo esc_html($field['label']); ?>
                        </label>
                        
                        <?php switch ($field['type']):
                            case 'text': ?>
                                <input type="text" 
                                       id="<?php echo esc_attr($field_name); ?>" 
                                       name="<?php echo esc_attr($field_name); ?>" 
                                       value="<?php echo esc_attr($field_value); ?>" 
                                       class="widefat">
                                <?php break;

                            case 'textarea': ?>
                                <textarea id="<?php echo esc_attr($field_name); ?>" 
                                          name="<?php echo esc_attr($field_name); ?>" 
                                          class="widefat" 
                                          rows="4"><?php echo esc_textarea($field_value); ?></textarea>
                                <?php break;

                            case 'select': ?>
                                <select id="<?php echo esc_attr($field_name); ?>" 
                                        name="<?php echo esc_attr($field_name); ?>" 
                                        class="widefat">
                                    <option value="">-- Sélectionner --</option>
                                    <?php if (!empty($field['options'])): 
                                        foreach ($field['options'] as $option): ?>
                                            <option value="<?php echo esc_attr($option['value']); ?>" 
                                                    <?php selected($field_value, $option['value']); ?>>
                                                <?php echo esc_html($option['label']); ?>
                                            </option>
                                        <?php endforeach;
                                    endif; ?>
                                </select>
                                <?php break;

                            case 'radio': ?>
                                <?php if (!empty($field['options'])): 
                                    foreach ($field['options'] as $option): ?>
                                        <label class="scf-radio-label">
                                            <input type="radio" 
                                                   name="<?php echo esc_attr($field_name); ?>" 
                                                   value="<?php echo esc_attr($option['value']); ?>"
                                                   <?php checked($field_value, $option['value']); ?>>
                                            <?php echo esc_html($option['label']); ?>
                                        </label>
                                    <?php endforeach;
                                endif; ?>
                                <?php break;

                            case 'checkbox': ?>
                                <?php if (!empty($field['options'])): 
                                    foreach ($field['options'] as $option): 
                                        $checked = is_array($field_value) && in_array($option['value'], $field_value);
                                    ?>
                                        <label class="scf-checkbox-label">
                                            <input type="checkbox" 
                                                   name="<?php echo esc_attr($field_name); ?>[]" 
                                                   value="<?php echo esc_attr($option['value']); ?>"
                                                   <?php checked($checked); ?>>
                                            <?php echo esc_html($option['label']); ?>
                                        </label>
                                    <?php endforeach;
                                endif; ?>
                                <?php break;

                        endswitch; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <style>
                .scf-fields-container {
                    padding: 10px;
                }
                .scf-field-row {
                    margin-bottom: 15px;
                }
                .scf-field-row label {
                    display: block;
                    margin-bottom: 5px;
                    font-weight: bold;
                }
                .scf-radio-label,
                .scf-checkbox-label {
                    display: block;
                    margin: 5px 0;
                }
            </style>
        <?php endif;
    }

    public function save_fields($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Récupérer tous les groupes pour ce type de contenu
        $post_type = get_post_type($post_id);
        $groups = get_posts(array(
            'post_type' => 'scf-field-group',
            'posts_per_page' => -1
        ));

        foreach ($groups as $group) {
            $group_id = $group->ID;
            $nonce_name = 'scf_nonce_' . $group_id;
            
            if (!isset($_POST[$nonce_name]) || !wp_verify_nonce($_POST[$nonce_name], 'scf_save_fields_' . $group_id)) {
                continue;
            }

            $rules = get_post_meta($group->ID, 'scf_rules', true);
            if (!$rules || $rules['param'] !== 'post_type' || $rules['value'] !== $post_type) {
                continue;
            }

            $fields = get_post_meta($group_id, 'scf_fields', true);
            $values = array();

            if (!empty($_POST['scf'][$group_id]) && is_array($_POST['scf'][$group_id])) {
                foreach ($_POST['scf'][$group_id] as $field_name => $field_value) {
                    // Trouver le type de champ
                    $field_type = '';
                    foreach ($fields as $field) {
                        if ($field['name'] === $field_name) {
                            $field_type = $field['type'];
                            break;
                        }
                    }

                    // Sanitization selon le type
                    switch ($field_type) {
                        case 'text':
                            $values[$field_name] = sanitize_text_field($field_value);
                            break;
                        case 'textarea':
                            $values[$field_name] = sanitize_textarea_field($field_value);
                            break;
                        case 'select':
                        case 'radio':
                            $values[$field_name] = sanitize_text_field($field_value);
                            break;
                        case 'checkbox':
                            $values[$field_name] = array_map('sanitize_text_field', (array)$field_value);
                            break;
                        default:
                            $values[$field_name] = sanitize_text_field($field_value);
                    }
                }
            }

            update_post_meta($post_id, '_scf_values_' . $group_id, $values);
        }
    }
}
