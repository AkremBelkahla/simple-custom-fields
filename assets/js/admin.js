jQuery(document).ready(function($) {
    console.log('SCF Admin JS loaded');
    console.log('SCF Vars:', typeof scf_vars !== 'undefined' ? scf_vars : 'undefined');
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

    // Gestion de la suppression des groupes
    $(document).on('click', '.scf-delete-group', function(e) {
        e.preventDefault();

        if (!confirm('Êtes-vous sûr de vouloir supprimer ce groupe de champs ?')) {
            return;
        }

        // Utilisation directe de admin-ajax.php comme fallback
        var ajaxurl = typeof scf_vars !== 'undefined' && scf_vars.ajaxurl 
            ? scf_vars.ajaxurl 
            : ajaxurl || 'https://preprod.wordifysites.com/wp-admin/admin-ajax.php';

        var groupId = $(this).data('group-id');
        var nonce = typeof scf_vars !== 'undefined' ? scf_vars.nonce : '';

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'scf_delete_group',
                group_id: groupId,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    var row = $('[data-group-id="' + response.data.group_id + '"]').closest('tr');
                    row.fadeOut(400, function() {
                        row.remove();
                        if ($('table tbody tr').length === 0) {
                            location.reload();
                        }
                    });
                } else {
                    alert('Erreur: ' + (response.data.message || 'Erreur inconnue'));
                }
            },
            error: function(xhr) {
                console.error('Erreur AJAX:', xhr.responseText);
                alert('Une erreur est survenue lors de la suppression');
            }
        });
    });

    // ... [le reste du fichier reste inchangé]
});
