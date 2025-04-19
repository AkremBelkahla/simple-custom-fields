<?php if (!defined('ABSPATH')) exit; ?>

<div class="scf-fields">
    <?php if (!empty($fields)): ?>
        <?php foreach ($fields as $field): ?>
            <?php
            $field_id = 'scf_' . $field['name'];
            $value = get_post_meta($post->ID, $field_id, true);
            ?>
            <div class="scf-field">
                <label for="<?php echo esc_attr($field_id); ?>">
                    <?php echo esc_html($field['label']); ?>
                </label>

                <?php switch ($field['type']):
                    case 'textarea': ?>
                        <textarea id="<?php echo esc_attr($field_id); ?>" 
                                name="scf_fields[<?php echo esc_attr($field['name']); ?>]" 
                                rows="5"><?php echo esc_textarea($value); ?></textarea>
                        <?php break;

                    case 'select': ?>
                        <select id="<?php echo esc_attr($field_id); ?>" 
                                name="scf_fields[<?php echo esc_attr($field['name']); ?>]">
                            <?php if (!empty($field['options'])): ?>
                                <?php foreach ($field['options'] as $option): ?>
                                    <option value="<?php echo esc_attr($option['value']); ?>" 
                                            <?php selected($value, $option['value']); ?>>
                                        <?php echo esc_html($option['label']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php break;

                    case 'radio': ?>
                        <?php if (!empty($field['options'])): ?>
                            <?php foreach ($field['options'] as $option): ?>
                                <label>
                                    <input type="radio" 
                                           name="scf_fields[<?php echo esc_attr($field['name']); ?>]" 
                                           value="<?php echo esc_attr($option['value']); ?>" 
                                           <?php checked($value, $option['value']); ?>>
                                    <?php echo esc_html($option['label']); ?>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php break;

                    case 'checkbox': ?>
                        <?php if (!empty($field['options'])): ?>
                            <?php foreach ($field['options'] as $option): ?>
                                <label>
                                    <input type="checkbox" 
                                           name="scf_fields[<?php echo esc_attr($field['name']); ?>][]" 
                                           value="<?php echo esc_attr($option['value']); ?>" 
                                           <?php checked(in_array($option['value'], (array)$value)); ?>>
                                    <?php echo esc_html($option['label']); ?>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php break;

                    default: ?>
                        <input type="<?php echo esc_attr($field['type']); ?>" 
                               id="<?php echo esc_attr($field_id); ?>" 
                               name="scf_fields[<?php echo esc_attr($field['name']); ?>]" 
                               value="<?php echo esc_attr($value); ?>">
                <?php endswitch; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>