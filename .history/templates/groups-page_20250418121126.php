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
                        <th scope="col" class="column-rules">Règles</th>
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
                                    if (is_array($rules) && !empty($rules)) {
                                        $post_type_obj = get_post_type_object($rules['value']);
                                        $post_type_label = $post_type_obj ? $post_type_obj->labels->singular_name : $rules['value'];
                                        $operator_label = $rules['operator'] === '=' ? 'est' : 'n\'est pas';
                                        echo esc_html(sprintf('Type de contenu %s %s', $operator_label, $post_type_label));
                                    } else {
                                        echo '—';
                                    }
                                    ?>
                        </td>
                        <td>
                            <div class="row-actions">
                                <span class="edit">
                                    <a
                                       href="<?php echo admin_url('admin.php?page=scf-add-group&group_id=' . $group->ID); ?>">
                                        Modifier
                                    </a> |
                                </span>
                                <span class="delete">
                                    <a href="#"
                                       class="scf-delete-group"
                                       data-group-id="<?php echo $group->ID; ?>">
                                        Supprimer
                                    </a>
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

        if (!confirm('Êtes-vous sûr de vouloir supprimer ce groupe de champs ?')) {
            return;
        }

        var groupId = $(this).data('group-id');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'scf_delete_group',
                group_id: groupId,
                nonce: scf_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Une erreur est survenue lors de la suppression du groupe.');
                }
            }
        });
    });
});
