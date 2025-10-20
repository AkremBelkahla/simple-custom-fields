<?php
if (!defined('ABSPATH')) exit;

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
?>
<div class="wrap scf-edit-group-page">
    <div class="scf-page-header">
        <div class="scf-page-header-left">
            <h1 class="wp-heading-inline">
                <span class="dashicons dashicons-edit"></span>
                <?php echo $group_id ? 'Modifier le groupe' : 'Ajouter un groupe'; ?>
            </h1>
            <p class="scf-page-subtitle">
                <?php echo $group_id ? 'Modifiez les paramètres de votre groupe de champs' : 'Créez un nouveau groupe de champs personnalisés'; ?>
            </p>
        </div>
        <div class="scf-page-header-right">
            <a href="<?php echo admin_url('admin.php?page=simple-custom-fields'); ?>"
               class="page-title-action scf-btn-back">
                <span class="dashicons dashicons-arrow-left-alt2"></span>
                Retour à la liste
            </a>
        </div>
    </div>
    <hr class="wp-header-end">

    <form id="scf-form"
          method="post"
          action="<?php echo admin_url('admin-post.php'); ?>">
        <?php wp_nonce_field('scf_save_field_group', 'scf_nonce'); ?>
        <input type="hidden"
               name="action"
               value="scf_save_field_group">
        <?php if ($group_id): ?>
        <input type="hidden"
               name="group_id"
               value="<?php echo $group_id; ?>">
        <?php endif; ?>

        <div id="poststuff">
            <div id="post-body"
                 class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="scf-title-card">
                        <div class="scf-card-icon">
                            <span class="dashicons dashicons-category"></span>
                        </div>
                        <div class="scf-card-content">
                            <label for="title" class="scf-card-label">Titre du groupe</label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   class="scf-title-input"
                                   value="<?php echo $group ? esc_attr($group->post_title) : ''; ?>"
                                   placeholder="Ex: Informations produit, Détails événement..."
                                   spellcheck="true"
                                   autocomplete="off"
                                   required>
                        </div>
                    </div>
                </div>

                <div id="postbox-container-2"
                     class="postbox-container">
                    <div id="normal-sortables"
                         class="meta-box-sortables ui-sortable">
                        <div id="scf-fields"
                             class="scf-modern-card">
                            <div class="scf-card-header">
                                <div class="scf-card-header-left">
                                    <span class="dashicons dashicons-admin-settings"></span>
                                    <h2>Champs personnalisés</h2>
                                </div>
                                <div class="scf-card-header-badge">
                                    <span class="scf-fields-count"><?php echo count($fields); ?></span> champ<?php echo count($fields) > 1 ? 's' : ''; ?>
                                </div>
                            </div>
                            <div class="inside">
                                <div class="scf-fields-accordion">
                                    <?php if (!empty($fields)): ?>
                                    <?php foreach ($fields as $index => $field): ?>
                                    <?php 
                                    $field_types = array(
                                        'text' => array('icon' => 'dashicons-editor-textcolor', 'label' => 'Texte'),
                                        'textarea' => array('icon' => 'dashicons-editor-alignleft', 'label' => 'Zone de texte'),
                                        'number' => array('icon' => 'dashicons-calculator', 'label' => 'Nombre'),
                                        'email' => array('icon' => 'dashicons-email', 'label' => 'Email'),
                                        'url' => array('icon' => 'dashicons-admin-links', 'label' => 'URL'),
                                        'date' => array('icon' => 'dashicons-calendar-alt', 'label' => 'Date'),
                                        'time' => array('icon' => 'dashicons-clock', 'label' => 'Heure'),
                                        'color' => array('icon' => 'dashicons-art', 'label' => 'Couleur'),
                                        'select' => array('icon' => 'dashicons-list-view', 'label' => 'Liste déroulante'),
                                        'radio' => array('icon' => 'dashicons-marker', 'label' => 'Boutons radio'),
                                        'checkbox' => array('icon' => 'dashicons-yes', 'label' => 'Cases à cocher'),
                                        'wysiwyg' => array('icon' => 'dashicons-editor-paste-text', 'label' => 'Éditeur WYSIWYG'),
                                        'image' => array('icon' => 'dashicons-format-image', 'label' => 'Image'),
                                        'file' => array('icon' => 'dashicons-media-default', 'label' => 'Fichier')
                                    );
                                    $current_type = $field_types[$field['type']] ?? $field_types['text'];
                                    $show_options = in_array($field['type'], array('select', 'radio', 'checkbox'));
                                    $options = isset($field['options']) ? $field['options'] : array();
                                    ?>
                                    
                                    <div class="scf-field-accordion-item" data-field-index="<?php echo $index; ?>">
                                        <!-- Header de l'accordéon -->
                                        <div class="scf-field-accordion-header">
                                            <div class="scf-field-number">
                                                <span class="scf-field-drag-handle dashicons dashicons-menu"></span>
                                                <?php echo ($index + 1); ?>
                                            </div>
                                            
                                            <div class="scf-field-type-badge">
                                                <span class="dashicons <?php echo $current_type['icon']; ?>"></span>
                                                <?php echo $current_type['label']; ?>
                                            </div>
                                            
                                            <div class="scf-field-label-display">
                                                <?php echo esc_html($field['label']); ?>
                                                <?php if (!empty($field['required'])): ?>
                                                <span class="required">*</span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="scf-field-quick-actions">
                                                <button type="button" class="scf-field-toggle" title="Ouvrir/Fermer">
                                                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                                                </button>
                                                <button type="button" class="scf-field-delete" title="Supprimer">
                                                    <span class="dashicons dashicons-trash"></span>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Contenu de l'accordéon -->
                                        <div class="scf-field-accordion-content">
                                            <div class="scf-field-form-grid">
                                                <div class="scf-field-form-group">
                                                    <label>Libellé du champ</label>
                                                    <input type="text" 
                                                           name="fields[<?php echo $index; ?>][label]" 
                                                           class="scf-field-label-input"
                                                           value="<?php echo esc_attr($field['label']); ?>"
                                                           placeholder="Ex: Titre du produit">
                                                </div>
                                                
                                                <div class="scf-field-form-group">
                                                    <label>Nom du champ</label>
                                                    <input type="text" 
                                                           name="fields[<?php echo $index; ?>][name]" 
                                                           class="scf-field-name-input"
                                                           value="<?php echo esc_attr($field['name']); ?>"
                                                           placeholder="Ex: titre_produit">
                                                </div>
                                                
                                                <div class="scf-field-form-group">
                                                    <label>Type de champ</label>
                                                    <select name="fields[<?php echo $index; ?>][type]" class="scf-field-type-select">
                                                        <option value="text" <?php selected($field['type'], 'text'); ?>>Texte</option>
                                                        <option value="textarea" <?php selected($field['type'], 'textarea'); ?>>Zone de texte</option>
                                                        <option value="number" <?php selected($field['type'], 'number'); ?>>Nombre</option>
                                                        <option value="email" <?php selected($field['type'], 'email'); ?>>Email</option>
                                                        <option value="url" <?php selected($field['type'], 'url'); ?>>URL</option>
                                                        <option value="date" <?php selected($field['type'], 'date'); ?>>Date</option>
                                                        <option value="time" <?php selected($field['type'], 'time'); ?>>Heure</option>
                                                        <option value="color" <?php selected($field['type'], 'color'); ?>>Couleur</option>
                                                        <option value="select" <?php selected($field['type'], 'select'); ?>>Liste déroulante</option>
                                                        <option value="radio" <?php selected($field['type'], 'radio'); ?>>Boutons radio</option>
                                                        <option value="checkbox" <?php selected($field['type'], 'checkbox'); ?>>Cases à cocher</option>
                                                        <option value="wysiwyg" <?php selected($field['type'], 'wysiwyg'); ?>>Éditeur WYSIWYG</option>
                                                        <option value="image" <?php selected($field['type'], 'image'); ?>>Image</option>
                                                        <option value="file" <?php selected($field['type'], 'file'); ?>>Fichier</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="scf-field-form-group">
                                                    <label>Champ requis</label>
                                                    <select name="fields[<?php echo $index; ?>][required]">
                                                        <option value="0" <?php selected(empty($field['required'])); ?>>Non</option>
                                                        <option value="1" <?php selected(!empty($field['required'])); ?>>Oui</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <!-- Section Options -->
                                            <div class="scf-field-options-section" style="<?php echo !$show_options ? 'display:none;' : ''; ?>">
                                                <h4>
                                                    <span class="dashicons dashicons-list-view"></span>
                                                    Options du champ
                                                </h4>
                                                
                                                <div class="scf-options-list">
                                                    <?php if (!empty($options)): ?>
                                                    <?php foreach ($options as $opt_index => $option): ?>
                                                    <div class="scf-option-item">
                                                        <input type="text" 
                                                               name="fields[<?php echo $index; ?>][options][<?php echo $opt_index; ?>][label]" 
                                                               class="scf-option-label"
                                                               value="<?php echo esc_attr($option['label']); ?>"
                                                               placeholder="Libellé">
                                                        <input type="text" 
                                                               name="fields[<?php echo $index; ?>][options][<?php echo $opt_index; ?>][value]" 
                                                               class="scf-option-value"
                                                               value="<?php echo esc_attr($option['value']); ?>"
                                                               placeholder="Valeur">
                                                        <button type="button" class="scf-option-remove">
                                                            <span class="dashicons dashicons-trash"></span>
                                                        </button>
                                                    </div>
                                                    <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <button type="button" class="button scf-add-option-btn">
                                                    <span class="dashicons dashicons-plus-alt2"></span>
                                                    Ajouter une option
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="scf-add-field-wrapper">
                                <button type="button"
                                        class="button button-primary scf-add-field">
                                    <span class="dashicons dashicons-plus-alt2"
                                          style="margin-top: 5px;"></span>
                                    Ajouter un champ
                                </button>
                            </div>
                        </div>

                        <div id="scf-description"
                             class="scf-modern-card">
                            <div class="scf-card-header">
                                <div class="scf-card-header-left">
                                    <span class="dashicons dashicons-text-page"></span>
                                    <h2>Description du groupe</h2>
                                </div>
                            </div>
                            <div class="inside">
                                <textarea name="description"
                                          class="widefat"
                                          rows="4"
                                          placeholder="Description du groupe de champs"><?php echo $group ? esc_textarea($group->post_content) : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="postbox-container-1"
                     class="postbox-container">
                    <div id="side-sortables"
                         class="meta-box-sortables ui-sortable">
                        <div id="submitdiv"
                             class="scf-modern-card scf-publish-card">
                            <div class="scf-card-header">
                                <div class="scf-card-header-left">
                                    <span class="dashicons dashicons-yes-alt"></span>
                                    <h2>Publication</h2>
                                </div>
                            </div>
                            <div class="inside">
                                <div class="submitbox"
                                     id="submitpost">
                                    <div id="major-publishing-actions">
                                        <div id="publishing-action">
                                            <span class="spinner"></span>
                                            <input type="submit"
                                                   class="button button-primary button-large"
                                                   value="<?php echo $group_id ? 'Mettre à jour' : 'Publier'; ?>"
                                                   id="publish"
                                                   name="publish">
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="scf-rules"
                             class="scf-modern-card">
                            <div class="scf-card-header">
                                <div class="scf-card-header-left">
                                    <span class="dashicons dashicons-admin-post"></span>
                                    <h2>Type de contenu</h2>
                                </div>
                            </div>
                            <div class="inside">
                                <p><label>Sélectionnez le type de contenu :</label></p>
                                <select name="rules[value]" class="widefat">
                                    <?php 
                                    $post_types = get_post_types(array('public' => true), 'objects');
                                    foreach ($post_types as $post_type) {
                                        $selected = '';
                                        if (isset($rules['value']) && $rules['value'] === $post_type->name) {
                                            $selected = 'selected';
                                        }
                                        echo '<option value="' . esc_attr($post_type->name) . '" ' . $selected . '>';
                                        echo esc_html($post_type->labels->singular_name);
                                        echo '</option>';
                                    }
                                    ?>
                                </select>
                                <input type="hidden" name="rules[type]" value="post_type">
                                <input type="hidden" name="rules[operator]" value="=">
                            </div>
                        </div>

                        <div id="scf-activation"
                             class="scf-modern-card">
                            <div class="scf-card-header">
                                <div class="scf-card-header-left">
                                    <span class="dashicons dashicons-admin-plugins"></span>
                                    <h2>Activation</h2>
                                </div>
                            </div>
                            <div class="inside">
                                <div class="">
                                    <label for="group_status">Statut du groupe</label>
                                    <select name="status"
                                            id="group_status"
                                            class="widefat">
                                        <option value="publish"
                                                <?php echo (!$group || $group->post_status === 'publish') ? 'selected' : ''; ?>>
                                            Activé
                                        </option>
                                        <option value="draft"
                                                <?php echo ($group && $group->post_status === 'draft') ? 'selected' : ''; ?>>
                                            Désactivé
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template pour un nouveau champ -->
<script type="text/template" id="field-template">
    <tr class="scf-field-row">
        <td class="column-order">
            <span class="dashicons dashicons-menu"></span>
        </td>
        <td class="column-type">
            <select name="fields[{index}][type]" class="field-type-select">
                <option value="text">Texte</option>
                <option value="textarea">Zone de texte</option>
                <option value="number">Nombre</option>
                <option value="email">Email</option>
                <option value="select">Liste déroulante</option>
                <option value="radio">Boutons radio</option>
                <option value="checkbox">Cases à cocher</option>
            </select>
        </td>
        <td class="column-label">
            <input type="text" name="fields[{index}][label]" class="widefat" placeholder="Libellé du champ">
        </td>
        <td class="column-name">
            <input type="text" name="fields[{index}][name]" class="widefat" placeholder="Nom du champ">
        </td>
        <td class="column-actions">
            <button type="button" class="button button-primary edit-options" style="display: none;">
                <span class="dashicons dashicons-list-view" title="Options"></span>
            </button>
            <button type="button" class="button button-primary remove-field">
                <span class="dashicons dashicons-trash" title="Supprimer"></span>
            </button>
            <input type="hidden" name="fields[{index}][options]" class="field-options" value="[]">
        </td>
    </tr>
</script>

<!-- Modal des options -->
<div id="optionsModal" class="scf-modal" style="display: none;">
    <div class="scf-modal-content">
        <div class="scf-modal-header">
            <h2>Options</h2>
            <button type="button" class="close-modal">×</button>
        </div>
        <div class="scf-modal-body">
            <div class="options-list">
                <!-- Les options seront ajoutées ici dynamiquement -->
            </div>
            <button type="button" class="button add-option scf-add-field">
                <span class="dashicons dashicons-plus-alt2"></span>
                Ajouter une option
            </button>
        </div>
        <div class="scf-modal-footer">
            <button type="button" class="button button-secondary close-modal scf-add-field">Annuler</button>
            <button type="button" class="button button-primary save-options scf-add-field">Enregistrer</button>
        </div>
    </div>
</div>

<!-- Template pour une option -->
<script type="text/template" id="option-template">
    <div class="option-row">
        <div class="option-field">
            <label>Libellé</label>
            <input type="text" class="option-label scf-input" placeholder="Libellé" value="{label}">
        </div>
        <div class="option-field">
            <label>Valeur</label>
            <input type="text" class="option-value scf-input" placeholder="Valeur" value="{value}">
        </div>
        <button type="button" class="button remove-option scf-btn-delete">
            <span class="dashicons dashicons-trash"></span>
        </button>
    </div>
</script>

<style>
.scf-modal {
    display: none;
    position: fixed;
    z-index: 100000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.scf-modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    width: 50%;
    max-width: 500px;
    position: relative;
    border-radius: 4px;
}

.scf-modal-header {
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.scf-modal-header h2 {
    margin: 0;
    padding: 0;
}

.scf-modal-body {
    margin-bottom: 20px;
}

.scf-modal-footer {
    padding-top: 10px;
    border-top: 1px solid #ddd;
    text-align: right;
}

.option-row {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
    align-items: flex-end;
}

.option-field {
    flex: 1;
}

.option-field label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    font-size: 13px;
}

.option-field input {
    width: 100%;
}

.option-row input {
    flex: 1;
}

.option-row .remove-option {
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

</style>
