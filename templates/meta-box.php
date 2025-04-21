<?php if (!defined('ABSPATH')) exit; ?>

<div class="scf-fields">
    <?php if (!empty($fields)): ?>
        <?php foreach ($fields as $field): ?>
            <?php
            $field_id = 'scf_' . $group_id . '_' . $field['name'];
            $meta_key = '_scf_values_' . $group_id;
            $field_value = isset($values[$field['name']]) ? $values[$field['name']] : '';
            ?>
            <div class="scf-field">
                <div class="scf-field-label">
                    <label for="<?php echo esc_attr($field_id); ?>">
                        <?php echo esc_html($field['label']); ?>
                    </label>
                </div>

                <div class="scf-field-input">
                <?php switch ($field['type']):
                    case 'textarea': ?>
                        <textarea id="<?php echo esc_attr($field_id); ?>" 
                                name="scf_fields[<?php echo esc_attr($group_id); ?>][<?php echo esc_attr($field['name']); ?>]"
                                rows="5"
                                class="scf-textarea"><?php echo esc_textarea($field_value); ?></textarea>
                        <?php break;

                    case 'select': ?>
                        <select id="<?php echo esc_attr($field_id); ?>" 
                                name="scf_fields[<?php echo esc_attr($group_id); ?>][<?php echo esc_attr($field['name']); ?>]"
                                class="scf-select">
                            <?php if (!empty($field['options'])): ?>
                                <?php foreach ($field['options'] as $option): ?>
                                    <?php 
                                    // Forcer une valeur non vide
                                    $option_value = !empty($option['value']) ? $option['value'] : $option['label'];
                                    ?>
                                    <option value="<?php echo esc_attr($option_value); ?>" 
                                            <?php selected($field_value, $option_value); ?>>
                                        <?php echo esc_html($option['label']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php break;

                    case 'radio': ?>
                        <?php if (!empty($field['options'])): ?>
                            <div class="scf-radio-options">
                            <?php foreach ($field['options'] as $option): ?>
                                <label class="scf-radio-label">
                                    <input type="radio"
                                           class="scf-radio"
                                           name="scf_fields[<?php echo esc_attr($group_id); ?>][<?php echo esc_attr($field['name']); ?>]" 
                                           value="<?php echo esc_attr(!empty($option['value']) ? $option['value'] : sanitize_title($option['label'])); ?>" 
                                           <?php checked($field_value, !empty($option['value']) ? $option['value'] : sanitize_title($option['label'])); ?>>
                                    <span class="scf-option-label"><?php echo esc_html($option['label']); ?></span>
                                </label>
                            <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php break;

                    case 'checkbox': ?>
                        <?php if (!empty($field['options'])): ?>
                            <div class="scf-checkbox-options">
                            <?php foreach ($field['options'] as $option): ?>
                                <label class="scf-checkbox-label">
                                    <input type="checkbox"
                                           class="scf-checkbox"
                                           name="scf_fields[<?php echo esc_attr($group_id); ?>][<?php echo esc_attr($field['name']); ?>][]" 
                                           value="<?php echo esc_attr(!empty($option['value']) ? $option['value'] : sanitize_title($option['label'])); ?>" 
                                           <?php 
                                           $checked_values = is_array($field_value) ? $field_value : array($field_value);
                                           checked(in_array(!empty($option['value']) ? $option['value'] : sanitize_title($option['label']), $checked_values)); 
                                           ?>>
                                    <span class="scf-option-label"><?php echo esc_html($option['label']); ?></span>
                                </label>
                            <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php break;

                    default: ?>
                        <input type="<?php echo esc_attr($field['type']); ?>" 
                               id="<?php echo esc_attr($field_id); ?>" 
                               name="scf_fields[<?php echo esc_attr($group_id); ?>][<?php echo esc_attr($field['name']); ?>]" 
                               value="<?php echo esc_attr($field_value); ?>"
                               class="scf-input">
                <?php endswitch; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
</div>
