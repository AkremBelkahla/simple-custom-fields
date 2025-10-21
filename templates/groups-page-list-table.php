<?php 
if (!defined('ABSPATH')) exit;

// $list_table est passé depuis render_groups_page()
?>

<div class="wrap scf-groups-page">
    <h1 class="wp-heading-inline">
        <?php echo esc_html__('Groupes de champs', 'simple-custom-fields'); ?>
    </h1>
    
    <a href="<?php echo admin_url('admin.php?page=scf-add-group'); ?>" class="page-title-action">
        <?php echo esc_html__('Ajouter', 'simple-custom-fields'); ?>
    </a>
    
    <hr class="wp-header-end">
    
    <?php if (isset($_GET['message'])): ?>
        <?php if ($_GET['message'] === 'success'): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html__('Le groupe de champs a été enregistré avec succès.', 'simple-custom-fields'); ?></p>
            </div>
        <?php elseif ($_GET['message'] === 'bulk_success'): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html__('Les groupes ont été mis à jour avec succès.', 'simple-custom-fields'); ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <form method="get">
        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
        <?php 
        $list_table->views();
        $list_table->search_box(__('Rechercher des groupes', 'simple-custom-fields'), 'scf-groups');
        $list_table->display(); 
        ?>
    </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Gestion de la suppression AJAX
    $(document).on('click', '.scf-delete-group', function(e) {
        e.preventDefault();
        
        const message = '<?php echo esc_js(__('Êtes-vous sûr de vouloir supprimer ce groupe de champs ? Cette action est irréversible.', 'simple-custom-fields')); ?>';
        if (!confirm(message)) {
            return;
        }
        
        var groupId = $(this).data('group-id');
        var nonce = $(this).data('nonce');
        var $row = $(this).closest('tr');
        
        $.ajax({
            url: scf_vars.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'scf_delete_group',
                group_id: groupId,
                nonce: nonce
            },
            beforeSend: function() {
                $row.css('opacity', '0.5');
            },
            success: function(response) {
                if (response.success) {
                    $row.fadeOut(400, function() {
                        $(this).remove();
                        
                        // Recharger si plus aucun élément
                        if ($('.wp-list-table tbody tr').length === 0) {
                            location.reload();
                        }
                    });
                } else {
                    alert('<?php echo esc_js(__('Une erreur est survenue : ', 'simple-custom-fields')); ?>' + (response.data ? response.data.message : ''));
                    $row.css('opacity', '1');
                }
            },
            error: function(xhr, status, error) {
                console.error('Delete group error:', {xhr: xhr, status: status, error: error});
                alert('<?php echo esc_js(__('Une erreur est survenue lors de la suppression du groupe.', 'simple-custom-fields')); ?>');
                $row.css('opacity', '1');
            }
        });
    });
});
</script>
