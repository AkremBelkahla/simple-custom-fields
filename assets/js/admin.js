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

    // La gestion de la suppression des groupes a été déplacée dans templates/groups-page.php

    // ... [le reste du fichier reste inchangé]
});
