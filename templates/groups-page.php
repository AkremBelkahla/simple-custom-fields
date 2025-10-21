<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap scf-groups-page">
    <!-- Barre d'en-tête avec titre et bouton Options -->
    <div class="scf-top-bar">
        <h1 class="wp-heading-inline">
            Groupes de champs
        </h1>
        <a href="<?php echo admin_url('admin.php?page=scf-add-group'); ?>"
           class="page-title-action scf-btn-add">
            <span class="dashicons dashicons-plus-alt2"></span>
            Ajouter
        </a>
        <button type="button" class="scf-screen-options-toggle" id="scf-screen-options-toggle">
            Options de l'écran
            <span class="dashicons dashicons-arrow-down-alt2"></span>
        </button>
    </div>
    
    <hr class="wp-header-end">
    
    <!-- Panneau Options de l'écran -->
    <div class="scf-screen-options" id="scf-screen-options" style="display: none;">
        <div class="scf-screen-options-content">
            <h5>Afficher les colonnes</h5>
            <div class="scf-screen-options-columns">
                <label>
                    <input type="checkbox" class="scf-column-toggle" data-column="description" checked>
                    Description
                </label>
                <label>
                    <input type="checkbox" class="scf-column-toggle" data-column="fields" checked>
                    Champs
                </label>
                <label>
                    <input type="checkbox" class="scf-column-toggle" data-column="type" checked>
                    Type
                </label>
                <label>
                    <input type="checkbox" class="scf-column-toggle" data-column="status" checked>
                    Statut
                </label>
            </div>
        </div>
    </div>
    
    <!-- Barre de filtres -->
    <div class="scf-filters-bar">
        <div class="scf-filters-left">
            <span class="scf-filter-link scf-filter-active" data-filter="all">
                Tous (<?php echo $total_groups; ?>)
            </span>
            <span class="scf-filter-separator">|</span>
            <span class="scf-filter-link" data-filter="active">
                <?php 
                $total_groups = count($groups);
                $active_groups = 0;
                $total_fields = 0;
                foreach ($groups as $group) {
                    if ($group->post_status === 'publish') $active_groups++;
                    $fields = get_post_meta($group->ID, 'scf_fields', true);
                    $total_fields += is_array($fields) ? count($fields) : 0;
                }
                ?>
                Actifs (<?php echo $active_groups; ?>)
            </span>
            <span class="scf-filter-separator">|</span>
            <span class="scf-filter-link" data-filter="inactive">
                Inactifs (<?php echo $total_groups - $active_groups; ?>)
            </span>
        </div>
        <div class="scf-filters-right">
            <input type="search" 
                   class="scf-search-input" 
                   placeholder="Rechercher des groupes de champs"
                   id="scf-search-groups">
            <button type="button" class="button scf-search-btn">
                <span class="dashicons dashicons-search"></span>
            </button>
        </div>
    </div>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'success'): ?>
    <div class="notice notice-success is-dismissible">
        <p>Le groupe de champs a été enregistré avec succès.</p>
    </div>
    <?php endif; ?>

    <div class="scf-wrap">
        <?php if (!empty($groups)): ?>
        <table class="wp-list-table widefat fixed striped scf-groups-table">
            <thead>
                <tr>
                    <th scope="col" class="column-title">Titre</th>
                    <th scope="col" class="column-fields">Champs</th>
                    <th scope="col" class="column-type">Type de contenu</th>
                    <th scope="col" class="column-status">Statut</th>
                    <th scope="col" class="column-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($groups as $group): 
                    $fields = get_post_meta($group->ID, 'scf_fields', true);
                    $rules = get_post_meta($group->ID, 'scf_rules', true);
                    $fields_count = is_array($fields) ? count($fields) : 0;
                    
                    // Récupérer le type de contenu
                    $content_type = '—';
                    if (is_array($rules) && !empty($rules['value'])) {
                        $post_type_obj = get_post_type_object($rules['value']);
                        if ($post_type_obj) {
                            $content_type = esc_html($post_type_obj->labels->singular_name);
                        } else {
                            $content_type = esc_html($rules['value']);
                        }
                    }
                ?>
            <tr data-group-id="<?php echo $group->ID; ?>">
                <td class="column-title">
                    <strong>
                        <a href="<?php echo admin_url('admin.php?page=scf-add-group&group_id=' . $group->ID); ?>">
                            <?php echo esc_html($group->post_title); ?>
                        </a>
                    </strong>
                    <?php if (!empty($group->post_content)): ?>
                    <p class="description">
                        <?php echo esc_html($group->post_content); ?>
                    </p>
                    <?php endif; ?>
                </td>
                
                <td class="column-fields">
                    <span class="scf-badge scf-badge-info">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <?php echo $fields_count; ?> champ<?php echo $fields_count > 1 ? 's' : ''; ?>
                    </span>
                </td>
                
                <td class="column-type">
                    <span class="scf-badge scf-badge-secondary">
                        <span class="dashicons dashicons-admin-post"></span>
                        <?php echo $content_type; ?>
                    </span>
                </td>
                
                <td class="column-status">
                    <span class="scf-badge <?php echo $group->post_status === 'publish' ? 'scf-badge-success' : 'scf-badge-warning'; ?>">
                        <?php echo $group->post_status === 'publish' ? '● Activé' : '○ Désactivé'; ?>
                    </span>
                </td>
                
                <td class="column-actions">
                    <a href="<?php echo admin_url('admin.php?page=scf-add-group&group_id=' . $group->ID); ?>" 
                       class="button button-small scf-btn-edit" 
                       title="Modifier">
                        <span class="dashicons dashicons-edit"></span>
                        Modifier
                    </a>
                    <?php if (current_user_can('manage_options')): ?>
                    <button type="button" 
                            class="button button-small scf-btn-delete scf-delete-group" 
                            data-group-id="<?php echo $group->ID; ?>"
                            title="Supprimer">
                        <span class="dashicons dashicons-trash"></span>
                        Supprimer
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="scf-no-items">
            <span class="dashicons dashicons-admin-generic" style="font-size: 64px; color: var(--scf-text-light); margin-bottom: 16px;"></span>
            <p style="font-size: 18px; font-weight: 500; color: var(--scf-text-primary);">Aucun groupe de champs</p>
            <p>Commencez par créer votre premier groupe de champs personnalisés.</p>
            <p>
                <a href="<?php echo admin_url('admin.php?page=scf-add-group'); ?>"
                   class="button button-primary button-large">
                    <span class="dashicons dashicons-plus-alt2" style="margin-top: 3px;"></span>
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
                // Utiliser le nonce spécifique pour l'action delete_group
                nonce: scf_vars.nonces && scf_vars.nonces.delete_group ? scf_vars.nonces.delete_group : scf_vars.nonce
            },
            beforeSend: function(xhr) {
                // Vérifier que le nonce est disponible
                var nonce = scf_vars.nonces && scf_vars.nonces.delete_group ? scf_vars.nonces.delete_group : scf_vars.nonce;
                if (!scf_vars || !nonce) {
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
