<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1 class="wp-heading-inline">Groupes de champs</h1>
    <a href="<?php echo admin_url('admin.php?page=scf-add-group'); ?>"
       class="page-title-action">Ajouter un groupe</a>
    <hr class="wp-header-end">

    <?php if (isset($_GET['message']) && $_GET['message'] === 'success'): ?>
    <div class="notice notice-success is-dismissible">
        <p>Le groupe de champs a été enregistré avec succès.</p>
    </div>
    <?php endif; ?>

    <div class="scf-wrap">
        <?php if (!empty($groups)): ?>
        <div class="scf-box">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" class="column-title">Titre</th>
                        <th scope="col" class="column-description">Description</th>
                        <th scope="col" class="column-fields">Nombre de champs</th>
                        <th scope="col" class="column-status">État</th>
                        <th scope="col" class="column-rules">Type de contenu</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groups as $group): 
                            $fields = get_post_meta($group->ID, 'scf_fields', true);
                            $rules = get_post_meta($group->ID, 'scf_rules', true);
                            $fields_count = is_array($fields) ? count($fields) : 0;
                        ?>
                    <tr>
                        <td class="column-title">
                            <strong>
                                <a href="<?php echo admin_url('admin.php?page=scf-add-group&group_id=' . $group->ID); ?>"
                                   class="row-title">
                                    <?php echo esc_html($group->post_title); ?>
                                </a>
                            </strong>
                        </td>
                        <td class="column-description">
                            <?php echo !empty($group->post_content) ? esc_html($group->post_content) : '—'; ?>
                        </td>
                        <td class="column-fields">
                            <?php echo $fields_count; ?> champ(s)
                        </td>
                        <td class="column-status">
                            <span class="status-indicator <?php echo $group->post_status === 'publish' ? 'active' : 'inactive'; ?>">
                                <?php echo $group->post_status === 'publish' ? 'Activé' : 'Désactivé'; ?>
                            </span>
                        </td>
                        <td class="column-rules">
                            <?php
                                if (is_array($rules) && !empty($rules['value'])) {
                                    $post_type_obj = get_post_type_object($rules['value']);
                                    if ($post_type_obj) {
                                        echo esc_html($post_type_obj->labels->singular_name);
                                    } else {
                                        echo esc_html($rules['value']);
                                    }
                                } else {
                                    echo '—';
                                }
                            ?>
                        </td>
                        <td>
                            <div class="row-actions">
                                <span class="edit">
                                    <a href="<?php echo admin_url('admin.php?page=scf-add-group&group_id=' . $group->ID); ?>">
                                        Modifier
                                    </a>
                                    <?php if (current_user_can('manage_options')): ?> |
                                    <a href="#"
                                       class="scf-delete-group"
                                       data-group-id="<?php echo $group->ID; ?>">
                                        Supprimer
                                    </a>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="scf-no-items">
            <p>Aucun groupe de champs n'a été créé pour le moment.</p>
            <p>
                <a href="<?php echo admin_url('admin.php?page=scf-add-group'); ?>"
                   class="button button-primary">
                    Créer un groupe de champs
                </a>
            </p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">

jQuery(document).ready(function($) {
    $('.scf-delete-group').on('click', function(e) {
        e.preventDefault();

                const message = 'Êtes-vous sûr de vouloir supprimer ce groupe de champs ? Cette action est irréversible.';
                if (!confirm(message)) {
            return;
        }

        var groupId = $(this).data('group-id');

        $.ajax({
            url: scf_vars.ajax_url,
            type: 'POST',
            dataType: 'json',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            data: {
                action: 'scf_delete_group',
                group_id: groupId,
                nonce: scf_vars.nonce
            },
            beforeSend: function(xhr) {
                if (!scf_vars || !scf_vars.nonce) {
                    console.error('Erreur : Variables de sécurité manquantes');
                    alert('Erreur de sécurité. Veuillez rafraîchir la page.');
                    return false;
                }
            },
            success: function(response) {
                console.log('Delete group response:', response);
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
                    var errorMessage = response.data ? response.data.message : 'Erreur inconnue';
                    if (scf_vars.debug && response.data && response.data.trace) {
                        console.error('Delete group trace:', response.data.trace);
                    }
                    if (response.data && response.data.debug_info) {
                        console.log('Debug info:', response.data.debug_info);
                    }
                    alert('Une erreur est survenue : ' + errorMessage);
                }
            },
            error: function(xhr, status, error) {
                console.error('Delete group error:', {xhr: xhr, status: status, error: error});
                alert('Une erreur est survenue lors de la suppression du groupe. Veuillez consulter la console pour plus de détails.');
            }
        });
    });
});
</script>
