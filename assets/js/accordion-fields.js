jQuery(document).ready(function($) {
    'use strict';
    
    // Toggle accordéon
    $(document).on('click', '.scf-field-accordion-header', function(e) {
        // Ne pas toggle si on clique sur un bouton
        if ($(e.target).closest('button').length) {
            return;
        }
        
        var $item = $(this).closest('.scf-field-accordion-item');
        var $content = $item.find('.scf-field-accordion-content');
        
        // Toggle l'item
        $item.toggleClass('is-open');
        
        // Animation slide
        if ($item.hasClass('is-open')) {
            $content.slideDown(300);
        } else {
            $content.slideUp(300);
        }
    });
    
    // Toggle avec le bouton
    $(document).on('click', '.scf-field-toggle', function(e) {
        e.stopPropagation();
        $(this).closest('.scf-field-accordion-header').trigger('click');
    });
    
    // Supprimer un champ
    $(document).on('click', '.scf-field-delete', function(e) {
        e.stopPropagation();
        
        if (confirm('Supprimer ce champ ?')) {
            var $item = $(this).closest('.scf-field-accordion-item');
            $item.fadeOut(300, function() {
                $(this).remove();
                updateFieldNumbers();
            });
        }
    });
    
    // Ajouter une option
    $(document).on('click', '.scf-add-option-btn', function() {
        var $list = $(this).closest('.scf-field-options-section').find('.scf-options-list');
        var fieldIndex = $(this).closest('.scf-field-accordion-item').data('field-index');
        var optionIndex = $list.find('.scf-option-item').length;
        
        var optionHtml = `
            <div class="scf-option-item">
                <input type="text" 
                       name="fields[${fieldIndex}][options][${optionIndex}][label]" 
                       placeholder="Libellé" 
                       class="scf-option-label">
                <input type="text" 
                       name="fields[${fieldIndex}][options][${optionIndex}][value]" 
                       placeholder="Valeur" 
                       class="scf-option-value">
                <button type="button" class="scf-option-remove">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
        `;
        
        $list.append(optionHtml);
    });
    
    // Supprimer une option
    $(document).on('click', '.scf-option-remove', function() {
        $(this).closest('.scf-option-item').fadeOut(200, function() {
            $(this).remove();
        });
    });
    
    // Synchronisation label -> valeur pour les options
    $(document).on('input', '.scf-option-label', function() {
        var $valueField = $(this).siblings('.scf-option-value');
        var currentValue = $valueField.val();
        var sanitizedLabel = sanitizeFieldName($(this).val());
        
        // Si valeur vide ou égale au label précédent, on synchronise
        if (currentValue === '' || currentValue === $(this).data('prev-label')) {
            $valueField.val(sanitizedLabel);
        }
        $(this).data('prev-label', sanitizedLabel);
    });
    
    // Synchronisation libellé -> nom du champ
    $(document).on('input', '.scf-field-label-input', function() {
        var $nameField = $(this).closest('.scf-field-form-grid').find('.scf-field-name-input');
        var currentName = $nameField.val();
        var sanitizedLabel = sanitizeFieldName($(this).val());
        
        // Si nom vide ou égal au libellé précédent, on synchronise
        if (currentName === '' || currentName === $(this).data('prev-label')) {
            $nameField.val(sanitizedLabel);
        }
        $(this).data('prev-label', sanitizedLabel);
    });
    
    // Afficher/masquer la section options selon le type
    $(document).on('change', '.scf-field-type-select', function() {
        var type = $(this).val();
        var $optionsSection = $(this).closest('.scf-field-accordion-content').find('.scf-field-options-section');
        
        if (type === 'select' || type === 'radio' || type === 'checkbox') {
            $optionsSection.slideDown(200);
        } else {
            $optionsSection.slideUp(200);
        }
        
        // Mettre à jour le badge de type dans le header
        updateTypeBadge($(this));
    });
    
    // Mettre à jour le badge de type
    function updateTypeBadge($select) {
        var type = $select.val();
        var typeLabel = $select.find('option:selected').text().trim();
        var $badge = $select.closest('.scf-field-accordion-item').find('.scf-field-type-badge');
        
        var icons = {
            'text': 'dashicons-editor-textcolor',
            'textarea': 'dashicons-editor-alignleft',
            'number': 'dashicons-calculator',
            'email': 'dashicons-email',
            'url': 'dashicons-admin-links',
            'date': 'dashicons-calendar-alt',
            'time': 'dashicons-clock',
            'color': 'dashicons-art',
            'select': 'dashicons-list-view',
            'radio': 'dashicons-marker',
            'checkbox': 'dashicons-yes',
            'wysiwyg': 'dashicons-editor-paste-text',
            'image': 'dashicons-format-image',
            'file': 'dashicons-media-default'
        };
        
        var icon = icons[type] || 'dashicons-admin-generic';
        
        // Remplacer complètement le contenu du badge
        $badge.empty().append(
            $('<span>').addClass('dashicons ' + icon),
            ' ' + typeLabel
        );
    }
    
    // Mettre à jour les numéros des champs
    function updateFieldNumbers() {
        $('.scf-field-accordion-item').each(function(index) {
            $(this).find('.scf-field-number').text((index + 1));
            $(this).attr('data-field-index', index);
        });
    }
    
    // Fonction pour sanitizer le nom du champ
    function sanitizeFieldName(str) {
        return str
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/^_+|_+$/g, '');
    }
    
    // Sortable pour réorganiser les champs
    if ($.fn.sortable) {
        $('.scf-fields-accordion').sortable({
            handle: '.scf-field-drag-handle',
            axis: 'y',
            opacity: 0.7,
            cursor: 'move',
            placeholder: 'scf-field-placeholder',
            start: function(e, ui) {
                ui.placeholder.height(ui.item.height());
            },
            stop: function() {
                updateFieldNumbers();
            }
        });
    }
    
    // Ouvrir le premier champ par défaut
    $('.scf-field-accordion-item:first').addClass('is-open')
        .find('.scf-field-accordion-content').show();
    
    // Ajouter un nouveau champ
    $(document).on('click', '.scf-add-field', function(e) {
        e.preventDefault(); // Empêcher le comportement par défaut
        
        var $accordion = $('.scf-fields-accordion');
        var fieldIndex = $('.scf-field-accordion-item').length;
        
        var newFieldHtml = `
            <div class="scf-field-accordion-item is-open" data-field-index="${fieldIndex}">
                <div class="scf-field-accordion-header">
                    <div class="scf-field-number">
                        <span class="scf-field-drag-handle dashicons dashicons-menu"></span>
                        ${fieldIndex + 1}
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
                
                <div class="scf-field-accordion-content" style="display:block;">
                    <div class="scf-field-form-grid">
                        <div class="scf-field-form-group">
                            <label>Libellé du champ</label>
                            <input type="text" 
                                   name="fields[${fieldIndex}][label]" 
                                   class="scf-field-label-input"
                                   value="Nouveau champ"
                                   placeholder="Ex: Titre du produit">
                        </div>
                        
                        <div class="scf-field-form-group">
                            <label>Nom du champ</label>
                            <input type="text" 
                                   name="fields[${fieldIndex}][name]" 
                                   class="scf-field-name-input"
                                   value="nouveau_champ_${fieldIndex}"
                                   placeholder="Ex: titre_produit">
                        </div>
                        
                        <div class="scf-field-form-group">
                            <label>Type de champ</label>
                            <select name="fields[${fieldIndex}][type]" class="scf-field-type-select">
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
                        </div>
                        
                        <div class="scf-field-form-group">
                            <label>Champ requis</label>
                            <select name="fields[${fieldIndex}][required]">
                                <option value="0">Non</option>
                                <option value="1">Oui</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="scf-field-options-section" style="display:none;">
                        <h4>
                            <span class="dashicons dashicons-list-view"></span>
                            Options du champ
                        </h4>
                        
                        <div class="scf-options-list"></div>
                        
                        <button type="button" class="button scf-add-option-btn">
                            <span class="dashicons dashicons-plus-alt2"></span>
                            Ajouter une option
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        $accordion.append(newFieldHtml);
        updateFieldNumbers();
        
        // Scroll vers le nouveau champ après un court délai
        setTimeout(function() {
            var $newField = $('.scf-field-accordion-item:last');
            if ($newField.length) {
                $('html, body').animate({
                    scrollTop: $newField.offset().top - 100
                }, 300);
            }
        }, 100);
    });
});
