jQuery(document).ready(function($) {
    var fieldIndex = $('.scf-field-row').length;

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

    // Ajout d'un nouveau champ
    $('.scf-add-field').on('click', function() {
        var template = $('#field-template').html();
        template = template.replace(/{index}/g, fieldIndex++);
        $('#the-list').append(template);
        updateFieldTypeVisibility();
    });

    // Suppression d'un champ
    $(document).on('click', '.remove-field', function() {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce champ ?')) {
            $(this).closest('tr').remove();
        }
    });

    // Gestion de la visibilité du bouton d'options
    function updateFieldTypeVisibility() {
        $('.field-type-select').each(function() {
            var $select = $(this);
            var $row = $select.closest('tr');
            var $editOptions = $row.find('.edit-options');
            
            if ($select.val() === 'select' || $select.val() === 'radio' || $select.val() === 'checkbox') {
                $editOptions.show();
            } else {
                $editOptions.hide();
            }
        });
    }

    // Mise à jour de la visibilité lors du changement de type
    $(document).on('change', '.field-type-select', function() {
        updateFieldTypeVisibility();
    });

    // Initialisation de la visibilité
    updateFieldTypeVisibility();

    // Gestion du modal des options
    var $modal = $('#optionsModal');
    var $currentOptionsField;

    // Ouverture du modal
    $(document).on('click', '.edit-options', function(e) {
        e.preventDefault();
        $currentOptionsField = $(this).siblings('.field-options');
        var options = [];
        
        try {
            var optionsValue = $currentOptionsField.val();
            if (optionsValue) {
                options = JSON.parse(optionsValue);
            }
        } catch (e) {
            console.error('Erreur lors du parsing des options:', e);
        }
        
        $('.options-list').empty();
        
        if (options.length === 0) {
            // Ajouter une option vide par défaut
            var template = $('#option-template').html()
                .replace(/{label}/g, '')
                .replace(/{value}/g, '');
            $('.options-list').append(template);
        } else {
            options.forEach(function(option) {
                var template = $('#option-template').html()
                    .replace(/{label}/g, option.label || '')
                    .replace(/{value}/g, option.value || '');
                $('.options-list').append(template);
            });
        }
        
        $modal.show();
    });

    // Fermeture du modal
    $('.close-modal').on('click', function() {
        $modal.hide();
    });

    // Ajout d'une option
    $('.add-option').on('click', function() {
        var template = $('#option-template').html()
            .replace(/{label}/g, '')
            .replace(/{value}/g, '');
        $('.options-list').append(template);
    });

    // Suppression d'une option
    $(document).on('click', '.remove-option', function() {
        var $optionsList = $('.options-list');
        $(this).closest('.option-row').remove();
        
        // S'il n'y a plus d'options, en ajouter une vide
        if ($optionsList.children().length === 0) {
            var template = $('#option-template').html()
                .replace(/{label}/g, '')
                .replace(/{value}/g, '');
            $optionsList.append(template);
        }
    });

    // Sauvegarde des options
    $('.save-options').on('click', function() {
        var options = [];
        $('.options-list .option-row').each(function() {
            var $row = $(this);
            var label = $row.find('.option-label').val().trim();
            var value = $row.find('.option-value').val().trim();
            
            console.log('Option trouvée:', { label: label, value: value });
            
            if (label) {
                options.push({
                    label: label,
                    value: value || label.toLowerCase()
                        .replace(/[^a-z0-9]+/g, '_')
                        .replace(/^_+|_+$/g, '')
                        .replace(/_+/g, '_')
                });
            }
        });
        
        console.log('Options à sauvegarder:', options);
        
        if (options.length === 0) {
            alert('Vous devez ajouter au moins une option avec un libellé.');
            return;
        }

        var jsonString = JSON.stringify(options);
        console.log('JSON des options:', jsonString);
        
        // On s'assure que le JSON est valide
        try {
            JSON.parse(jsonString);
            $currentOptionsField.val(jsonString);
            $modal.hide();
        } catch (e) {
            console.error('Erreur lors de la création du JSON:', e);
            alert('Une erreur est survenue lors de la sauvegarde des options.');
        }
    });

    // Validation du formulaire
    $('#scf-form').on('submit', function(e) {
        var title = $('#title').val();
        if (!title) {
            e.preventDefault();
            alert('Le titre du groupe est obligatoire.');
            return false;
        }

        var hasFields = $('.scf-field-row').length > 0;
        if (!hasFields) {
            e.preventDefault();
            alert('Vous devez ajouter au moins un champ.');
            return false;
        }

        // Validation des champs
        var isValid = true;
        var usedNames = {};
        
        $('.scf-field-row').each(function() {
            var $row = $(this);
            var name = $row.find('input[name$="[name]"]').val();
            var label = $row.find('input[name$="[label]"]').val();
            var type = $row.find('select[name$="[type]"]').val();
            
            if (!label) {
                alert('Le libellé est obligatoire pour tous les champs.');
                isValid = false;
                return false;
            }
            
            if (!name) {
                alert('Le nom est obligatoire pour le champ "' + label + '"');
                isValid = false;
                return false;
            }
            
            if (usedNames[name]) {
                alert('Le nom "' + name + '" est utilisé plusieurs fois. Chaque nom doit être unique.');
                isValid = false;
                return false;
            }
            
            usedNames[name] = true;

            // Vérification des options pour les champs qui en nécessitent
            if (type === 'select' || type === 'radio' || type === 'checkbox') {
                var options = $row.find('.field-options').val();
                if (!options) {
                    alert('Vous devez ajouter des options pour le champ "' + label + '"');
                    isValid = false;
                    return false;
                }
                
                try {
                    var parsedOptions = JSON.parse(options);
                    if (!Array.isArray(parsedOptions) || parsedOptions.length === 0) {
                        alert('Vous devez ajouter au moins une option pour le champ "' + label + '"');
                        isValid = false;
                        return false;
                    }
                } catch (e) {
                    alert('Erreur dans les options du champ "' + label + '"');
                    isValid = false;
                    return false;
                }
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }

        return true;
    });
});
