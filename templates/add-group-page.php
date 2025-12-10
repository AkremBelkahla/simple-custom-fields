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
                                        
                                        <!-- Contenu de l'accordéon avec onglets -->
                                        <div class="scf-field-accordion-content">
                                            <div class="scf-tabs-wrapper">
                                                <!-- Navigation des onglets -->
                                                <ul class="scf-tabs-nav">
                                                    <li class="scf-tab-nav-item active">
                                                        <button type="button" data-tab="general">
                                                            <span class="dashicons dashicons-admin-settings"></span>
                                                            Général
                                                        </button>
                                                    </li>
                                                    <li class="scf-tab-nav-item">
                                                        <button type="button" data-tab="validation">
                                                            <span class="dashicons dashicons-yes-alt"></span>
                                                            Validation
                                                        </button>
                                                    </li>
                                                    <li class="scf-tab-nav-item">
                                                        <button type="button" data-tab="presentation">
                                                            <span class="dashicons dashicons-art"></span>
                                                            Présentation
                                                        </button>
                                                    </li>
                                                    <li class="scf-tab-nav-item scf-tab-choices" style="<?php echo !$show_options ? 'display:none;' : ''; ?>">
                                                        <button type="button" data-tab="choices">
                                                            <span class="dashicons dashicons-list-view"></span>
                                                            Choix
                                                        </button>
                                                    </li>
                                                </ul>
                                                
                                                <!-- Contenu des onglets -->
                                                <div class="scf-tabs-content">
                                                    <!-- Onglet Général -->
                                                    <div class="scf-tab-panel active" id="tab-general">
                                                        <div class="scf-tab-section">
                                                            <h4>
                                                                <span class="dashicons dashicons-edit"></span>
                                                                Informations du champ
                                                            </h4>
                                                            <div class="scf-field-row">
                                                                <div class="scf-field-col">
                                                                    <label class="scf-field-label">Libellé du champ</label>
                                                                    <input type="text" 
                                                                           name="fields[<?php echo $index; ?>][label]" 
                                                                           class="scf-field-input"
                                                                           value="<?php echo esc_attr($field['label']); ?>"
                                                                           placeholder="Ex: Titre du produit">
                                                                    <p class="scf-field-description">Nom affiché dans l'interface d'édition</p>
                                                                </div>
                                                                
                                                                <div class="scf-field-col">
                                                                    <label class="scf-field-label">Nom du champ</label>
                                                                    <input type="text" 
                                                                           name="fields[<?php echo $index; ?>][name]" 
                                                                           class="scf-field-input"
                                                                           value="<?php echo esc_attr($field['name']); ?>"
                                                                           placeholder="Ex: titre_produit">
                                                                    <p class="scf-field-description">Identifiant unique (pas d'espaces, pas de caractères spéciaux)</p>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="scf-field-row">
                                                                <div class="scf-field-col">
                                                                    <label class="scf-field-label">Type de champ</label>
                                                                    <select name="fields[<?php echo $index; ?>][type]" class="scf-field-select scf-field-type-select-tabs">
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
                                                                    <p class="scf-field-description">Définit le type de saisie pour ce champ</p>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="scf-field-row">
                                                                <div class="scf-field-col-full">
                                                                    <label class="scf-field-label">Instructions</label>
                                                                    <textarea name="fields[<?php echo $index; ?>][instructions]" 
                                                                              class="scf-field-textarea"
                                                                              rows="3"
                                                                              placeholder="Instructions pour les éditeurs de contenu. Affichées lors de la saisie des données."><?php echo isset($field['instructions']) ? esc_textarea($field['instructions']) : ''; ?></textarea>
                                                                    <p class="scf-field-description">Instructions pour les éditeurs de contenu. Affichées lors de la saisie des données.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Onglet Validation -->
                                                    <div class="scf-tab-panel" id="tab-validation">
                                                        <div class="scf-tab-section">
                                                            <h4>
                                                                <span class="dashicons dashicons-yes-alt"></span>
                                                                Validation requise
                                                            </h4>
                                                            <div class="scf-checkbox-group">
                                                                <div class="scf-checkbox-item">
                                                                    <input type="checkbox" 
                                                                           name="fields[<?php echo $index; ?>][required]" 
                                                                           value="1"
                                                                           id="required_<?php echo $index; ?>"
                                                                           <?php echo !empty($field['required']) ? 'checked' : ''; ?>>
                                                                    <label for="required_<?php echo $index; ?>">Obligatoire</label>
                                                                    <p class="scf-field-description">Cochez cette case pour rendre ce champ obligatoire</p>
                                                                </div>
                                                                
                                                                <div class="scf-checkbox-item">
                                                                    <input type="checkbox" 
                                                                           name="fields[<?php echo $index; ?>][allow_empty]" 
                                                                           value="1"
                                                                           id="allow_empty_<?php echo $index; ?>"
                                                                           <?php echo isset($field['allow_empty']) && $field['allow_empty'] ? 'checked' : ''; ?>>
                                                                    <label for="allow_empty_<?php echo $index; ?>">Autoriser une valeur vide</label>
                                                                    <p class="scf-field-description">Autorise la soumission d'une valeur vide même si le champ est obligatoire</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="scf-tab-section">
                                                            <h4>
                                                                <span class="dashicons dashicons-shield"></span>
                                                                Validation personnalisée
                                                            </h4>
                                                            <div class="scf-field-row">
                                                                <div class="scf-field-col">
                                                                    <label class="scf-field-label">Motif de validation (regex)</label>
                                                                    <input type="text" 
                                                                           name="fields[<?php echo $index; ?>][pattern]" 
                                                                           class="scf-field-input"
                                                                           value="<?php echo isset($field['pattern']) ? esc_attr($field['pattern']) : ''; ?>"
                                                                           placeholder="^[a-zA-Z0-9]+$">
                                                                    <p class="scf-field-description">Expression régulière pour valider le format de la saisie</p>
                                                                </div>
                                                                
                                                                <div class="scf-field-col">
                                                                    <label class="scf-field-label">Message d'erreur personnalisé</label>
                                                                    <input type="text" 
                                                                           name="fields[<?php echo $index; ?>][error_message]" 
                                                                           class="scf-field-input"
                                                                           value="<?php echo isset($field['error_message']) ? esc_attr($field['error_message']) : ''; ?>"
                                                                           placeholder="Veuillez entrer une valeur valide">
                                                                    <p class="scf-field-description">Message affiché en cas d'erreur de validation</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Onglet Présentation -->
                                                    <div class="scf-tab-panel" id="tab-presentation">
                                                        <div class="scf-tab-section">
                                                            <h4>
                                                                <span class="dashicons dashicons-text-page"></span>
                                                                Apparence du champ
                                                            </h4>
                                                            <div class="scf-field-row">
                                                                <div class="scf-field-col">
                                                                    <label class="scf-field-label">Texte de placeholder</label>
                                                                    <input type="text" 
                                                                           name="fields[<?php echo $index; ?>][placeholder]" 
                                                                           class="scf-field-input"
                                                                           value="<?php echo isset($field['placeholder']) ? esc_attr($field['placeholder']) : ''; ?>"
                                                                           placeholder="Texte d'exemple">
                                                                    <p class="scf-field-description">Texte affiché lorsque le champ est vide</p>
                                                                </div>
                                                                
                                                                <div class="scf-field-col">
                                                                    <label class="scf-field-label">Classe CSS personnalisée</label>
                                                                    <input type="text" 
                                                                           name="fields[<?php echo $index; ?>][class]" 
                                                                           class="scf-field-input"
                                                                           value="<?php echo isset($field['class']) ? esc_attr($field['class']) : ''; ?>"
                                                                           placeholder="custom-field-class">
                                                                    <p class="scf-field-description">Classe CSS additionnelle pour le champ</p>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="scf-field-row">
                                                                <div class="scf-field-col">
                                                                    <label class="scf-field-label">Largeur du champ</label>
                                                                    <select name="fields[<?php echo $index; ?>][width]" class="scf-field-select">
                                                                        <option value="">Par défaut</option>
                                                                        <option value="25" <?php echo isset($field['width']) && $field['width'] === '25' ? 'selected' : ''; ?>>25%</option>
                                                                        <option value="50" <?php echo isset($field['width']) && $field['width'] === '50' ? 'selected' : ''; ?>>50%</option>
                                                                        <option value="75" <?php echo isset($field['width']) && $field['width'] === '75' ? 'selected' : ''; ?>>75%</option>
                                                                        <option value="100" <?php echo isset($field['width']) && $field['width'] === '100' ? 'selected' : ''; ?>>100%</option>
                                                                    </select>
                                                                    <p class="scf-field-description">Largeur du champ dans le formulaire</p>
                                                                </div>
                                                                
                                                                <div class="scf-field-col">
                                                                    <label class="scf-field-label">Positionnement</label>
                                                                    <select name="fields[<?php echo $index; ?>][position]" class="scf-field-select">
                                                                        <option value="">Par défaut</option>
                                                                        <option value="left" <?php echo isset($field['position']) && $field['position'] === 'left' ? 'selected' : ''; ?>>Gauche</option>
                                                                        <option value="right" <?php echo isset($field['position']) && $field['position'] === 'right' ? 'selected' : ''; ?>>Droite</option>
                                                                        <option value="top" <?php echo isset($field['position']) && $field['position'] === 'top' ? 'selected' : ''; ?>>Dessus</option>
                                                                    </select>
                                                                    <p class="scf-field-description">Position du libellé par rapport au champ</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="scf-tab-section">
                                                            <h4>
                                                                <span class="dashicons dashicons-hidden"></span>
                                                                Visibilité
                                                            </h4>
                                                            <div class="scf-checkbox-group">
                                                                <div class="scf-checkbox-item">
                                                                    <input type="checkbox" 
                                                                           name="fields[<?php echo $index; ?>][readonly]" 
                                                                           value="1"
                                                                           id="readonly_<?php echo $index; ?>"
                                                                           <?php echo isset($field['readonly']) && $field['readonly'] ? 'checked' : ''; ?>>
                                                                    <label for="readonly_<?php echo $index; ?>">Lecture seule</label>
                                                                    <p class="scf-field-description">Rend le champ non modifiable</p>
                                                                </div>
                                                                
                                                                <div class="scf-checkbox-item">
                                                                    <input type="checkbox" 
                                                                           name="fields[<?php echo $index; ?>][disabled]" 
                                                                           value="1"
                                                                           id="disabled_<?php echo $index; ?>"
                                                                           <?php echo isset($field['disabled']) && $field['disabled'] ? 'checked' : ''; ?>>
                                                                    <label for="disabled_<?php echo $index; ?>">Désactivé</label>
                                                                    <p class="scf-field-description">Désactive complètement le champ</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Onglet Choix -->
                                                    <div class="scf-tab-panel" id="tab-choices" style="<?php echo !$show_options ? 'display:none;' : ''; ?>">
                                                        <div class="scf-tab-section">
                                                            <h4>
                                                                <span class="dashicons dashicons-list-view"></span>
                                                                Options du champ
                                                            </h4>
                                                            
                                                            <div class="scf-field-row">
                                                                <div class="scf-field-col">
                                                                    <label class="scf-field-label">Autoriser plusieurs sélections</label>
                                                                    <select name="fields[<?php echo $index; ?>][multiple]" class="scf-field-select">
                                                                        <option value="0" <?php echo empty($field['multiple']) ? 'selected' : ''; ?>>Non</option>
                                                                        <option value="1" <?php echo !empty($field['multiple']) ? 'selected' : ''; ?>>Oui</option>
                                                                    </select>
                                                                    <p class="scf-field-description">Autorise la sélection de plusieurs options</p>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="scf-options-list">
                                                                <?php if (!empty($options)): ?>
                                                                <?php foreach ($options as $opt_index => $option): ?>
                                                                <div class="scf-option-item scf-field-row">
                                                                    <div class="scf-field-col">
                                                                        <label class="scf-field-label">Libellé</label>
                                                                        <input type="text" 
                                                                               name="fields[<?php echo $index; ?>][options][<?php echo $opt_index; ?>][label]" 
                                                                               class="scf-field-input"
                                                                               value="<?php echo esc_attr($option['label']); ?>"
                                                                               placeholder="Libellé">
                                                                    </div>
                                                                    <div class="scf-field-col">
                                                                        <label class="scf-field-label">Valeur</label>
                                                                        <input type="text" 
                                                                               name="fields[<?php echo $index; ?>][options][<?php echo $opt_index; ?>][value]" 
                                                                               class="scf-field-input"
                                                                               value="<?php echo esc_attr($option['value']); ?>"
                                                                               placeholder="Valeur">
                                                                    </div>
                                                                    <div class="scf-field-col" style="flex: 0 0 auto;">
                                                                        <label class="scf-field-label">&nbsp;</label>
                                                                        <button type="button" class="button scf-option-remove">
                                                                            <span class="dashicons dashicons-trash"></span>
                                                                        </button>
                                                                    </div>
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
    <div class="scf-field-accordion-item" data-field-index="{index}">
        <!-- Header de l'accordéon -->
        <div class="scf-field-accordion-header">
            <div class="scf-field-number">
                <span class="scf-field-drag-handle dashicons dashicons-menu"></span>
                {index_display}
            </div>
            
            <div class="scf-field-type-badge">
                <span class="dashicons dashicons-editor-textcolor"></span>
                Texte
            </div>
            
            <div class="scf-field-label-display">
                Nouveau champ
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
        
        <!-- Contenu de l'accordéon avec onglets -->
        <div class="scf-field-accordion-content">
            <div class="scf-tabs-wrapper">
                <!-- Navigation des onglets -->
                <ul class="scf-tabs-nav">
                    <li class="scf-tab-nav-item active">
                        <button type="button" data-tab="general">
                            <span class="dashicons dashicons-admin-settings"></span>
                            Général
                        </button>
                    </li>
                    <li class="scf-tab-nav-item">
                        <button type="button" data-tab="validation">
                            <span class="dashicons dashicons-yes-alt"></span>
                            Validation
                        </button>
                    </li>
                    <li class="scf-tab-nav-item">
                        <button type="button" data-tab="presentation">
                            <span class="dashicons dashicons-art"></span>
                            Présentation
                        </button>
                    </li>
                    <li class="scf-tab-nav-item scf-tab-choices" style="display:none;">
                        <button type="button" data-tab="choices">
                            <span class="dashicons dashicons-list-view"></span>
                            Choix
                        </button>
                    </li>
                </ul>
                
                <!-- Contenu des onglets -->
                <div class="scf-tabs-content">
                    <!-- Onglet Général -->
                    <div class="scf-tab-panel active" id="tab-general">
                        <div class="scf-tab-section">
                            <h4>
                                <span class="dashicons dashicons-edit"></span>
                                Informations du champ
                            </h4>
                            <div class="scf-field-row">
                                <div class="scf-field-col">
                                    <label class="scf-field-label">Libellé du champ</label>
                                    <input type="text" 
                                           name="fields[{index}][label]" 
                                           class="scf-field-input"
                                           placeholder="Ex: Titre du produit">
                                    <p class="scf-field-description">Nom affiché dans l'interface d'édition</p>
                                </div>
                                
                                <div class="scf-field-col">
                                    <label class="scf-field-label">Nom du champ</label>
                                    <input type="text" 
                                           name="fields[{index}][name]" 
                                           class="scf-field-input"
                                           placeholder="Ex: titre_produit">
                                    <p class="scf-field-description">Identifiant unique (pas d'espaces, pas de caractères spéciaux)</p>
                                </div>
                            </div>
                            
                            <div class="scf-field-row">
                                <div class="scf-field-col">
                                    <label class="scf-field-label">Type de champ</label>
                                    <select name="fields[{index}][type]" class="scf-field-select scf-field-type-select-tabs">
                                        <option value="text">Texte</option>
                                        <option value="textarea">Zone de texte</option>
                                        <option value="number">Nombre</option>
                                        <option value="email">Email</option>
                                        <option value="url">URL</option>
                                        <option value="date">Date</option>
                                        <option value="time">Heure</option>
                                        <option value="color">Couleur</option>
                                        <option value="select">Liste déroulante</option>
                                        <option value="radio">Boutons radio</option>
                                        <option value="checkbox">Cases à cocher</option>
                                        <option value="wysiwyg">Éditeur WYSIWYG</option>
                                        <option value="image">Image</option>
                                        <option value="file">Fichier</option>
                                    </select>
                                    <p class="scf-field-description">Définit le type de saisie pour ce champ</p>
                                </div>
                            </div>
                            
                            <div class="scf-field-row">
                                <div class="scf-field-col-full">
                                    <label class="scf-field-label">Instructions</label>
                                    <textarea name="fields[{index}][instructions]" 
                                              class="scf-field-textarea"
                                              rows="3"
                                              placeholder="Instructions pour les éditeurs de contenu. Affichées lors de la saisie des données."></textarea>
                                    <p class="scf-field-description">Instructions pour les éditeurs de contenu. Affichées lors de la saisie des données.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Onglet Validation -->
                    <div class="scf-tab-panel" id="tab-validation">
                        <div class="scf-tab-section">
                            <h4>
                                <span class="dashicons dashicons-yes-alt"></span>
                                Validation requise
                            </h4>
                            <div class="scf-checkbox-group">
                                <div class="scf-checkbox-item">
                                    <input type="checkbox" 
                                           name="fields[{index}][required]" 
                                           value="1"
                                           id="required_{index}">
                                    <label for="required_{index}">Obligatoire</label>
                                    <p class="scf-field-description">Cochez cette case pour rendre ce champ obligatoire</p>
                                </div>
                                
                                <div class="scf-checkbox-item">
                                    <input type="checkbox" 
                                           name="fields[{index}][allow_empty]" 
                                           value="1"
                                           id="allow_empty_{index}">
                                    <label for="allow_empty_{index}">Autoriser une valeur vide</label>
                                    <p class="scf-field-description">Autorise la soumission d'une valeur vide même si le champ est obligatoire</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="scf-tab-section">
                            <h4>
                                <span class="dashicons dashicons-shield"></span>
                                Validation personnalisée
                            </h4>
                            <div class="scf-field-row">
                                <div class="scf-field-col">
                                    <label class="scf-field-label">Motif de validation (regex)</label>
                                    <input type="text" 
                                           name="fields[{index}][pattern]" 
                                           class="scf-field-input"
                                           placeholder="^[a-zA-Z0-9]+$">
                                    <p class="scf-field-description">Expression régulière pour valider le format de la saisie</p>
                                </div>
                                
                                <div class="scf-field-col">
                                    <label class="scf-field-label">Message d'erreur personnalisé</label>
                                    <input type="text" 
                                           name="fields[{index}][error_message]" 
                                           class="scf-field-input"
                                           placeholder="Veuillez entrer une valeur valide">
                                    <p class="scf-field-description">Message affiché en cas d'erreur de validation</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Onglet Présentation -->
                    <div class="scf-tab-panel" id="tab-presentation">
                        <div class="scf-tab-section">
                            <h4>
                                <span class="dashicons dashicons-text-page"></span>
                                Apparence du champ
                            </h4>
                            <div class="scf-field-row">
                                <div class="scf-field-col">
                                    <label class="scf-field-label">Texte de placeholder</label>
                                    <input type="text" 
                                           name="fields[{index}][placeholder]" 
                                           class="scf-field-input"
                                           placeholder="Texte d'exemple">
                                    <p class="scf-field-description">Texte affiché lorsque le champ est vide</p>
                                </div>
                                
                                <div class="scf-field-col">
                                    <label class="scf-field-label">Classe CSS personnalisée</label>
                                    <input type="text" 
                                           name="fields[{index}][class]" 
                                           class="scf-field-input"
                                           placeholder="custom-field-class">
                                    <p class="scf-field-description">Classe CSS additionnelle pour le champ</p>
                                </div>
                            </div>
                            
                            <div class="scf-field-row">
                                <div class="scf-field-col">
                                    <label class="scf-field-label">Largeur du champ</label>
                                    <select name="fields[{index}][width]" class="scf-field-select">
                                        <option value="">Par défaut</option>
                                        <option value="25">25%</option>
                                        <option value="50">50%</option>
                                        <option value="75">75%</option>
                                        <option value="100">100%</option>
                                    </select>
                                    <p class="scf-field-description">Largeur du champ dans le formulaire</p>
                                </div>
                                
                                <div class="scf-field-col">
                                    <label class="scf-field-label">Positionnement</label>
                                    <select name="fields[{index}][position]" class="scf-field-select">
                                        <option value="">Par défaut</option>
                                        <option value="left">Gauche</option>
                                        <option value="right">Droite</option>
                                        <option value="top">Dessus</option>
                                    </select>
                                    <p class="scf-field-description">Position du libellé par rapport au champ</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="scf-tab-section">
                            <h4>
                                <span class="dashicons dashicons-hidden"></span>
                                Visibilité
                            </h4>
                            <div class="scf-checkbox-group">
                                <div class="scf-checkbox-item">
                                    <input type="checkbox" 
                                           name="fields[{index}][readonly]" 
                                           value="1"
                                           id="readonly_{index}">
                                    <label for="readonly_{index}">Lecture seule</label>
                                    <p class="scf-field-description">Rend le champ non modifiable</p>
                                </div>
                                
                                <div class="scf-checkbox-item">
                                    <input type="checkbox" 
                                           name="fields[{index}][disabled]" 
                                           value="1"
                                           id="disabled_{index}">
                                    <label for="disabled_{index}">Désactivé</label>
                                    <p class="scf-field-description">Désactive complètement le champ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Onglet Choix -->
                    <div class="scf-tab-panel" id="tab-choices" style="display:none;">
                        <div class="scf-tab-section">
                            <h4>
                                <span class="dashicons dashicons-list-view"></span>
                                Options du champ
                            </h4>
                            
                            <div class="scf-field-row">
                                <div class="scf-field-col">
                                    <label class="scf-field-label">Autoriser plusieurs sélections</label>
                                    <select name="fields[{index}][multiple]" class="scf-field-select">
                                        <option value="0">Non</option>
                                        <option value="1">Oui</option>
                                    </select>
                                    <p class="scf-field-description">Autorise la sélection de plusieurs options</p>
                                </div>
                            </div>
                            
                            <div class="scf-options-list">
                                <!-- Les options seront ajoutées dynamiquement -->
                            </div>
                            
                            <button type="button" class="button scf-add-option-btn">
                                <span class="dashicons dashicons-plus-alt2"></span>
                                Ajouter une option
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

<script>
jQuery(document).ready(function($) {
    // Gestion des onglets
    function initTabs() {
        $('.scf-tabs-nav button').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $tabItem = $button.closest('.scf-tab-nav-item');
            var tabName = $button.data('tab');
            var $tabsWrapper = $button.closest('.scf-tabs-wrapper');
            
            // Retirer la classe active de tous les onglets et panneaux
            $tabsWrapper.find('.scf-tab-nav-item').removeClass('active');
            $tabsWrapper.find('.scf-tab-panel').removeClass('active');
            
            // Ajouter la classe active à l'onglet cliqué et son panneau
            $tabItem.addClass('active');
            $('#tab-' + tabName).addClass('active');
        });
    }
    
    // Gestion de l'affichage/masquage de l'onglet Choix
    function toggleChoicesTab($fieldAccordion) {
        var fieldType = $fieldAccordion.find('.scf-field-type-select-tabs').val();
        var $choicesTab = $fieldAccordion.find('.scf-tab-choices');
        var $choicesPanel = $fieldAccordion.find('#tab-choices');
        
        if (['select', 'radio', 'checkbox'].includes(fieldType)) {
            $choicesTab.show();
            $choicesPanel.show();
        } else {
            $choicesTab.hide();
            $choicesPanel.hide();
            
            // Si l'onglet Choix était actif, basculer vers Général
            if ($choicesTab.hasClass('active')) {
                $fieldAccordion.find('[data-tab="general"]').click();
            }
        }
    }
    
    // Initialiser les onglets pour les champs existants
    $('.scf-field-accordion-item').each(function() {
        var $this = $(this);
        initTabs.call(this);
        
        // Gérer le changement de type de champ
        $this.find('.scf-field-type-select-tabs').on('change', function() {
            toggleChoicesTab($this);
        });
        
        // Initialiser l'état de l'onglet Choix
        toggleChoicesTab($this);
    });
    
    // Gestion des champs dynamiques (nouveau champ ajouté)
    $(document).on('scf-field-added', function(e, $newField) {
        // Initialiser les onglets pour le nouveau champ
        $newField.find('.scf-tabs-nav button').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $tabItem = $button.closest('.scf-tab-nav-item');
            var tabName = $button.data('tab');
            var $tabsWrapper = $button.closest('.scf-tabs-wrapper');
            
            // Retirer la classe active de tous les onglets et panneaux
            $tabsWrapper.find('.scf-tab-nav-item').removeClass('active');
            $tabsWrapper.find('.scf-tab-panel').removeClass('active');
            
            // Ajouter la classe active à l'onglet cliqué et son panneau
            $tabItem.addClass('active');
            $tabsWrapper.find('#tab-' + tabName).addClass('active');
        });
        
        // Gérer le changement de type de champ pour le nouveau champ
        $newField.find('.scf-field-type-select-tabs').on('change', function() {
            toggleChoicesTab($newField);
        });
        
        // Initialiser l'état de l'onglet Choix
        toggleChoicesTab($newField);
    });
    
    // Gestion des boutons d'ajout d'options
    $(document).on('click', '.scf-add-option-btn', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $optionsList = $button.closest('.scf-tab-section').find('.scf-options-list');
        var fieldIndex = $button.closest('.scf-field-accordion-item').data('field-index');
        var optionCount = $optionsList.find('.scf-option-item').length;
        
        var $newOption = $('<div class="scf-option-item scf-field-row">' +
            '<div class="scf-field-col">' +
                '<label class="scf-field-label">Libellé</label>' +
                '<input type="text" ' +
                       'name="fields[' + fieldIndex + '][options][' + optionCount + '][label]" ' +
                       'class="scf-field-input" ' +
                       'placeholder="Libellé">' +
            '</div>' +
            '<div class="scf-field-col">' +
                '<label class="scf-field-label">Valeur</label>' +
                '<input type="text" ' +
                       'name="fields[' + fieldIndex + '][options][' + optionCount + '][value]" ' +
                       'class="scf-field-input" ' +
                       'placeholder="Valeur">' +
            '</div>' +
            '<div class="scf-field-col" style="flex: 0 0 auto;">' +
                '<label class="scf-field-label">&nbsp;</label>' +
                '<button type="button" class="button scf-option-remove">' +
                    '<span class="dashicons dashicons-trash"></span>' +
                '</button>' +
            '</div>' +
        '</div>');
        
        $optionsList.append($newOption);
    });
    
    // Gestion des boutons de suppression d'options
    $(document).on('click', '.scf-option-remove', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $optionItem = $button.closest('.scf-option-item');
        
        // Supprimer l'option avec animation
        $optionItem.fadeOut(200, function() {
            $(this).remove();
        });
    });
    
    // Gestion de l'accordéon
    $(document).on('click', '.scf-field-toggle', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $accordionItem = $button.closest('.scf-field-accordion-item');
        var $content = $accordionItem.find('.scf-field-accordion-content');
        
        if ($content.is(':visible')) {
            $content.slideUp(200);
            $button.find('.dashicons').removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
        } else {
            $content.slideDown(200);
            $button.find('.dashicons').removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
        }
    });
    
    // Gestion de la suppression de champs
    $(document).on('click', '.scf-field-delete', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $accordionItem = $button.closest('.scf-field-accordion-item');
        
        if (confirm('Êtes-vous sûr de vouloir supprimer ce champ ?')) {
            $accordionItem.fadeOut(200, function() {
                $(this).remove();
                updateFieldsCount();
            });
        }
    });
    
    // Mettre à jour le compteur de champs
    function updateFieldsCount() {
        var fieldsCount = $('.scf-field-accordion-item').length;
        $('.scf-fields-count').text(fieldsCount);
        $('.scf-fields-count').siblings('span').text(fieldsCount > 1 ? 'champs' : 'champ');
    }
    
    // Initialiser le premier onglet comme actif pour tous les accordéons ouverts
    $('.scf-field-accordion-content').each(function() {
        if ($(this).is(':visible')) {
            $(this).find('[data-tab="general"]').click();
        }
    });
});
</script>
