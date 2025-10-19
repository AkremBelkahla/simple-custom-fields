jQuery(document).ready(function($) {
    if (typeof scf_vars === 'undefined') {
        console.error('SCF: scf_vars is not defined!');
    } else {
        console.log('SCF Vars:', scf_vars);
    }

    console.log('SCF Admin JS loaded');
    var fieldIndex = $('.scf-field-row').length;
    
    // Initialiser le drag and drop avec animation
    if ($('#the-list').length) {
        $('#the-list').sortable({
            handle: '.column-order',
            placeholder: 'ui-sortable-placeholder',
            helper: function(e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function(index) {
                    $(this).width($originals.eq(index).width());
                });
                return $helper;
            },
            start: function(e, ui) {
                ui.placeholder.height(ui.item.height());
                ui.item.addClass('dragging');
            },
            stop: function(e, ui) {
                ui.item.removeClass('dragging');
            }
        });
    }

    // Fonction pour sanitizer le libellé en nom de champ
    function sanitizeFieldName(label) {
        return label.toLowerCase()
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/^_+|_+$/g, '')
            .replace(/_+/g, '_');
    }

    // Mise à jour automatique du nom lors de la saisie du libellé
    $(document).on('input', '.scf-field-row input[name$="[label]"]', function() {
        var $row = $(this).closest('.scf-field-row');
        var $nameInput = $row.find('input[name$="[name]"]');
        
        if (!$nameInput.val() || $nameInput.val() === sanitizeFieldName($(this).attr('data-previous-label'))) {
            $nameInput.val(sanitizeFieldName($(this).val()));
        }
        
        $(this).attr('data-previous-label', $(this).val());
    });

    // Fonction pour mettre à jour la visibilité des options selon le type de champ
    function updateFieldTypeVisibility() {
        $('.scf-field-row').each(function() {
            var $row = $(this);
            var fieldType = $row.find('select[name$="[type]"]').val();
            var showOptions = fieldType === 'select' || fieldType === 'radio' || fieldType === 'checkbox';
            
            // Masquer/montrer le bouton d'options
            $row.find('.edit-options').toggle(showOptions);
        });
    }

    // Gestion de l'ouverture de la modal d'options avec animation
    $(document).on('click', '.edit-options', function() {
        var $row = $(this).closest('.scf-field-row');
        var options = JSON.parse($row.find('.field-options').val() || '[]');
        var $modal = $('#optionsModal');
        var $optionsList = $modal.find('.options-list');
        
        // Vider et remplir la liste d'options
        $optionsList.empty();
        
        if (options.length > 0) {
            options.forEach(function(option) {
                var template = $('#option-template').html()
                    .replace(/{label}/g, option.label)
                    .replace(/{value}/g, option.value);
                $optionsList.append(template);
            });
        } else {
            // Ajouter 2 options vides par défaut
            for (var i = 0; i < 2; i++) {
                var template = $('#option-template').html()
                    .replace(/{label}/g, '')
                    .replace(/{value}/g, '');
                $optionsList.append(template);
            }
        }
        
        // Ouvrir la modal avec animation
        $modal.css('display', 'flex').hide().fadeIn(200);
        
        // Sauvegarder la référence à la ligne courante
        $modal.data('currentRow', $row);
    });

    // Gestion de l'ajout d'une option
    $(document).on('click', '.add-option', function() {
        var templateHtml = $('#option-template').html();
        
        if (!templateHtml) {
            console.error('Template #option-template non trouvé');
            return;
        }
        
        var template = templateHtml
            .replace(/{label}/g, '')
            .replace(/{value}/g, '');
        var $newOption = $(template);
        var $optionsList = $(this).closest('.scf-modal-body').find('.options-list');
        
        if ($optionsList.length === 0) {
            console.error('.options-list non trouvé');
            return;
        }
        
        $optionsList.append($newOption);
        
        // Synchronisation automatique label -> valeur
        $newOption.find('.option-label').on('input', function() {
            var $valueField = $(this).closest('.option-row').find('.option-value');
            var currentValue = $valueField.val();
            var sanitizedLabel = sanitizeFieldName($(this).val());
            
            // Si valeur vide ou égale au label précédent, on synchronise
            if (currentValue === '' || currentValue === $(this).data('prev-label')) {
                $valueField.val(sanitizedLabel);
            }
            $(this).data('prev-label', sanitizedLabel);
        });
    });

    // Gestion de la suppression d'une option avec animation
    $(document).on('click', '.remove-option', function() {
        var $optionRow = $(this).closest('.option-row');
        $optionRow.css('background', '#fef2f2');
        $optionRow.animate({
            opacity: 0,
            marginBottom: 0,
            height: 0,
            padding: 0
        }, 300, function() {
            $optionRow.remove();
        });
    });

    // Sauvegarde des options avec animation
    $(document).on('click', '.save-options', function() {
        var $modal = $('#optionsModal');
        var $row = $modal.data('currentRow');
        var options = [];
        
        $modal.find('.option-row').each(function() {
            var $option = $(this);
            var label = $option.find('.option-label').val();
            var value = $option.find('.option-value').val();
            
            // On sauvegarde même si la valeur est vide
            options.push({
                label: label,
                value: value
            });
        });
        
        // Sauvegarde brute sans modification
        $row.find('.field-options').val(JSON.stringify(options));
        
        // Animation de succès
        $row.css('background', '#e7f5ec');
        setTimeout(function() {
            $row.css('background', '');
        }, 600);
        
        $modal.fadeOut(200);
    });

    // Fermeture de la modal avec animation
    $(document).on('click', '.close-modal', function() {
        $('#optionsModal').fadeOut(200);
    });
    
    // Fermer la modal en cliquant sur le fond
    $(document).on('click', '.scf-modal', function(e) {
        if ($(e.target).hasClass('scf-modal')) {
            $(this).fadeOut(200);
        }
    });

    // Mettre à jour la visibilité au chargement
    updateFieldTypeVisibility();

    // Ajout d'un nouveau champ avec animation
    $('.scf-add-field').on('click', function() {
        var template = $('#field-template').html();
        template = template.replace(/{index}/g, fieldIndex++);
        var $newRow = $(template);
        $newRow.css('opacity', '0');
        $('#the-list').append($newRow);
        $newRow.animate({opacity: 1}, 300);
        updateFieldTypeVisibility();
        
        // Scroll vers le nouveau champ
        $('html, body').animate({
            scrollTop: $newRow.offset().top - 100
        }, 400);
    });

    // Mettre à jour la visibilité quand le type change
    $(document).on('change', 'select[name$="[type]"]', function() {
        updateFieldTypeVisibility();
    });

    // Suppression d'un champ avec animation
    $(document).on('click', '.remove-field', function() {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce champ ?')) {
            var $row = $(this).closest('tr');
            $row.css('background', '#fef2f2');
            $row.animate({
                opacity: 0,
                height: 0,
                padding: 0
            }, 300, function() {
                $row.remove();
            });
        }
    });

    // La gestion de la suppression des groupes a été déplacée dans templates/groups-page.php

    // ... [le reste du fichier reste inchangé]
});
