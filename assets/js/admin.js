jQuery(document).ready(function($) {
    if (typeof scf_vars === 'undefined') {
        console.error('SCF: scf_vars is not defined!');
    } else {
        console.log('SCF Vars:', scf_vars);
    }

    console.log('SCF Admin JS loaded');
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

    // Gestion de l'ouverture de la modal d'options
    $(document).on('click', '.edit-options', function() {
        var $row = $(this).closest('.scf-field-row');
        var options = JSON.parse($row.find('.field-options').val() || '[]');
        var $modal = $('#optionsModal');
        var $optionsList = $modal.find('.options-list');
        
        // Vider et remplir la liste d'options
        $optionsList.empty();
        options.forEach(function(option) {
            var template = $('#option-template').html()
                .replace(/{label}/g, option.label)
                .replace(/{value}/g, option.value);
            $optionsList.append(template);
        });
        
        // Ouvrir la modal
        $modal.show();
        
        // Sauvegarder la référence à la ligne courante
        $modal.data('currentRow', $row);
    });

    // Gestion de l'ajout d'une option
    $(document).on('click', '.add-option', function() {
        var template = $('#option-template').html()
            .replace(/{label}/g, '')
            .replace(/{value}/g, '');
        var $newOption = $(template);
        $(this).siblings('.options-list').append($newOption);
        
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

    // Gestion de la suppression d'une option
    $(document).on('click', '.remove-option', function() {
        $(this).closest('.option-row').remove();
    });

    // Sauvegarde des options
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
        $modal.hide();
    });

    // Fermeture de la modal
    $(document).on('click', '.close-modal', function() {
        $('#optionsModal').hide();
    });

    // Mettre à jour la visibilité au chargement
    updateFieldTypeVisibility();

    // Ajout d'un nouveau champ
    $('.scf-add-field').on('click', function() {
        var template = $('#field-template').html();
        template = template.replace(/{index}/g, fieldIndex++);
        $('#the-list').append(template);
        updateFieldTypeVisibility();
    });

    // Mettre à jour la visibilité quand le type change
    $(document).on('change', 'select[name$="[type]"]', function() {
        updateFieldTypeVisibility();
    });

    // Suppression d'un champ
    $(document).on('click', '.remove-field', function() {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce champ ?')) {
            $(this).closest('tr').remove();
        }
    });

    // La gestion de la suppression des groupes a été déplacée dans templates/groups-page.php

    // ... [le reste du fichier reste inchangé]
});
