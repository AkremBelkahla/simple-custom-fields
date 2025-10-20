<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap scf-settings-page">
    <div class="scf-settings-header">
        <h1>
            <span class="dashicons dashicons-admin-settings"></span>
            Paramètres
        </h1>
        <p class="scf-settings-subtitle">Gérez les paramètres et sauvegardez vos configurations</p>
    </div>

    <hr class="wp-header-end">

    <?php if (isset($_GET['import']) && $_GET['import'] === 'success'): ?>
    <div class="notice notice-success is-dismissible">
        <p><strong>Import réussi !</strong> Les groupes de champs ont été importés avec succès.</p>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['import']) && $_GET['import'] === 'error'): ?>
    <div class="notice notice-error is-dismissible">
        <p><strong>Erreur d'import :</strong> Le fichier n'est pas valide ou est corrompu.</p>
    </div>
    <?php endif; ?>

    <div class="scf-settings-container">
        
        <!-- Section Import/Export -->
        <div class="scf-settings-section">
            <div class="scf-settings-section-header">
                <h2>
                    <span class="dashicons dashicons-database-export"></span>
                    Import / Export
                </h2>
                <p>Sauvegardez et restaurez vos groupes de champs</p>
            </div>

            <div class="scf-settings-cards">
                <!-- Export -->
                <div class="scf-settings-card">
                    <div class="scf-card-icon scf-icon-export">
                        <span class="dashicons dashicons-download"></span>
                    </div>
                    <h3>Exporter les groupes de champs</h3>
                    <p>Téléchargez un fichier JSON contenant tous vos groupes de champs et leurs configurations.</p>
                    
                    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                        <input type="hidden" name="action" value="scf_export_groups">
                        <?php wp_nonce_field('scf_export_groups', 'scf_export_nonce'); ?>
                        
                        <div class="scf-export-options">
                            <label>
                                <input type="checkbox" name="export_all" value="1" checked>
                                <strong>Tous les groupes</strong>
                            </label>
                            <p class="description">
                                <?php 
                                $groups_count = wp_count_posts('scf-field-group');
                                $total = $groups_count->publish + $groups_count->draft;
                                echo $total . ' groupe' . ($total > 1 ? 's' : '') . ' seront exportés';
                                ?>
                            </p>
                        </div>
                        
                        <button type="submit" class="button button-primary button-hero scf-btn-export">
                            <span class="dashicons dashicons-download"></span>
                            Télécharger l'export (JSON)
                        </button>
                    </form>
                </div>

                <!-- Import -->
                <div class="scf-settings-card">
                    <div class="scf-card-icon scf-icon-import">
                        <span class="dashicons dashicons-upload"></span>
                    </div>
                    <h3>Importer des groupes de champs</h3>
                    <p>Importez un fichier JSON précédemment exporté pour restaurer vos groupes de champs.</p>
                    
                    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data" id="scf-import-form">
                        <input type="hidden" name="action" value="scf_import_groups">
                        <?php wp_nonce_field('scf_import_groups', 'scf_import_nonce'); ?>
                        
                        <div class="scf-file-upload">
                            <input type="file" name="import_file" id="scf-import-file" accept=".json" required>
                            <label for="scf-import-file" class="scf-file-label">
                                <span class="dashicons dashicons-cloud-upload"></span>
                                <span class="scf-file-text">Choisir un fichier JSON</span>
                            </label>
                            <span class="scf-file-name"></span>
                        </div>
                        
                        <div class="scf-import-options">
                            <label>
                                <input type="checkbox" name="overwrite" value="1">
                                <strong>Écraser les groupes existants</strong>
                            </label>
                            <p class="description">Si un groupe avec le même nom existe, il sera remplacé</p>
                        </div>
                        
                        <button type="submit" class="button button-primary button-hero scf-btn-import">
                            <span class="dashicons dashicons-upload"></span>
                            Importer le fichier
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Section Paramètres généraux -->
        <div class="scf-settings-section">
            <div class="scf-settings-section-header">
                <h2>
                    <span class="dashicons dashicons-admin-generic"></span>
                    Paramètres généraux
                </h2>
                <p>Configurez le comportement du plugin</p>
            </div>

            <div class="scf-settings-form">
                <form method="post" action="options.php">
                    <?php settings_fields('scf_settings'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="scf_hide_on_screen">Masquer les éléments de l'écran</label>
                            </th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="checkbox" name="scf_hide_editor" value="1">
                                        Masquer l'éditeur de contenu par défaut
                                    </label>
                                    <p class="description">Utile si vous utilisez uniquement des champs personnalisés</p>
                                </fieldset>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="scf_load_styles">Charger les styles</label>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" name="scf_load_styles" value="1" checked>
                                    Charger les styles CSS du plugin
                                </label>
                                <p class="description">Décochez si vous souhaitez utiliser vos propres styles</p>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <button type="submit" class="button button-primary">
                            Enregistrer les modifications
                        </button>
                    </p>
                </form>
            </div>
        </div>

        <!-- Section À propos -->
        <div class="scf-settings-section">
            <div class="scf-settings-section-header">
                <h2>
                    <span class="dashicons dashicons-info"></span>
                    À propos
                </h2>
            </div>

            <div class="scf-about-card">
                <div class="scf-about-content">
                    <h3>Simple Custom Fields</h3>
                    <p>Plugin WordPress pour créer facilement des champs personnalisés</p>
                    
                    <div class="scf-about-info">
                        <div class="scf-info-item">
                            <strong>Version :</strong> 1.4.0
                        </div>
                        <div class="scf-info-item">
                            <strong>Auteur :</strong> Akrem Belkahla
                        </div>
                        <div class="scf-info-item">
                            <strong>Site :</strong> <a href="https://infinityweb.tn" target="_blank">infinityweb.tn</a>
                        </div>
                    </div>
                    
                    <div class="scf-about-links">
                        <a href="<?php echo admin_url('admin.php?page=scf-documentation'); ?>" class="button">
                            <span class="dashicons dashicons-book"></span>
                            Documentation
                        </a>
                        <a href="https://github.com/akrembelkahla" target="_blank" class="button">
                            <span class="dashicons dashicons-github"></span>
                            GitHub
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Afficher le nom du fichier sélectionné
    $('#scf-import-file').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $('.scf-file-name').text(fileName);
    });
});
</script>

