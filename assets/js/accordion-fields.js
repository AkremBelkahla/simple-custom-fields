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
            // Initialiser le premier onglet comme actif
            setTimeout(function() {
                $content.find('.scf-tabs-nav button[data-tab^="general"]').first().click();
            }, 100);
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
    
    // Gestion des onglets
    $(document).on('click', '.scf-tabs-nav button', function(e) {
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
    
    // Gestion de l'affichage/masquage de l'onglet Choix
    function toggleChoicesTab($fieldAccordion) {
        var fieldType = $fieldAccordion.find('.scf-field-type-select-tabs').val();
        var $choicesTab = $fieldAccordion.find('.scf-tab-choices');
        var $choicesPanel = $fieldAccordion.find('.scf-tab-panel[id^="tab-choices"]');
        
        if (['select', 'radio', 'checkbox'].includes(fieldType)) {
            $choicesTab.show();
        } else {
            $choicesTab.hide();
            
            // Si l'onglet Choix était actif, basculer vers Général
            if ($choicesTab.hasClass('active')) {
                $fieldAccordion.find('.scf-tabs-nav button[data-tab^="general"]').click();
            }
        }
    }
    
    // Afficher/masquer la section options selon le type (pour les onglets)
    $(document).on('change', '.scf-field-type-select-tabs', function() {
        toggleChoicesTab($(this).closest('.scf-field-accordion-item'));
        updateTypeBadge($(this));
    });
    
    // Ajouter une option (pour les onglets)
    $(document).on('click', '.scf-add-option-btn', function() {
        var $list = $(this).closest('.scf-tab-section').find('.scf-options-list');
        var fieldIndex = $(this).closest('.scf-field-accordion-item').data('field-index');
        var optionIndex = $list.find('.scf-option-item').length;
        
        var optionHtml = `
            <div class="scf-option-item scf-field-row">
                <div class="scf-field-col">
                    <label class="scf-field-label">Libellé</label>
                    <input type="text" 
                           name="fields[${fieldIndex}][options][${optionIndex}][label]" 
                           class="scf-field-input"
                           placeholder="Libellé">
                </div>
                <div class="scf-field-col">
                    <label class="scf-field-label">Valeur</label>
                    <input type="text" 
                           name="fields[${fieldIndex}][options][${optionIndex}][value]" 
                           class="scf-field-input"
                           placeholder="Valeur">
                </div>
                <div class="scf-field-col" style="flex: 0 0 auto;">
                    <label class="scf-field-label">&nbsp;</label>
                    <button type="button" class="button scf-option-remove">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                </div>
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
    $(document).on('input', '.scf-option-item input[name*="[label]"]', function() {
        var $valueField = $(this).closest('.scf-option-item').find('input[name*="[value]"]');
        var currentValue = $valueField.val();
        var sanitizedLabel = sanitizeFieldName($(this).val());
        
        // Si valeur vide ou égale au label précédent, on synchronise
        if (currentValue === '' || currentValue === $(this).data('prev-label')) {
            $valueField.val(sanitizedLabel);
        }
        $(this).data('prev-label', sanitizedLabel);
    });
    
    // Synchronisation libellé -> nom du champ
    $(document).on('input', '.scf-field-input[name*="[label]"]', function() {
        var $nameField = $(this).closest('.scf-field-accordion-item').find('input[name*="[name]"]');
        var currentName = $nameField.val();
        var sanitizedLabel = sanitizeFieldName($(this).val());
        
        // Si nom vide ou égal au libellé précédent, on synchronise
        if (currentName === '' || currentName === $(this).data('prev-label')) {
            $nameField.val(sanitizedLabel);
        }
        $(this).data('prev-label', sanitizedLabel);
        
        // Mettre à jour l'affichage dans le header
        var $labelDisplay = $(this).closest('.scf-field-accordion-item').find('.scf-field-label-display');
        var labelValue = $(this).val() || 'Nouveau champ';
        $labelDisplay.text(labelValue);
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
            $(this).find('.scf-field-number').contents().filter(function() {
                return this.nodeType === 3;
            }).replaceWith((index + 1).toString());
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
        e.preventDefault();
        
        var $accordion = $('.scf-fields-accordion');
        var fieldIndex = $('.scf-field-accordion-item').length;
        var template = $('#field-template').html();
        
        // Remplacer les placeholders dans le template
        var newFieldHtml = template
            .replace(/{index}/g, fieldIndex)
            .replace(/{index_display}/g, fieldIndex + 1);
        
        var $newField = $(newFieldHtml);
        $accordion.append($newField);
        
        // Ouvrir le nouveau champ
        $newField.addClass('is-open').find('.scf-field-accordion-content').show();
        
        // Initialiser le premier onglet
        setTimeout(function() {
            $newField.find('.scf-tabs-nav button[data-tab^="general"]').first().click();
        }, 100);
        
        updateFieldNumbers();
        
        // Scroll vers le nouveau champ après un court délai
        setTimeout(function() {
            $('html, body').animate({
                scrollTop: $newField.offset().top - 100
            }, 300);
        }, 200);
        
        // Déclencher l'événement pour l'initialisation des onglets
        $(document).trigger('scf-field-added', [$newField]);
    });
    
    // Initialiser les champs existants
    $('.scf-field-accordion-item').each(function() {
        var $this = $(this);
        
        // Initialiser l'état de l'onglet Choix
        toggleChoicesTab($this);
        
        // Initialiser le premier onglet comme actif si le champ est ouvert
        if ($this.hasClass('is-open') || $this.find('.scf-field-accordion-content').is(':visible')) {
            setTimeout(function() {
                $this.find('.scf-tabs-nav button[data-tab^="general"]').first().click();
            }, 100);
        }
    });
});
