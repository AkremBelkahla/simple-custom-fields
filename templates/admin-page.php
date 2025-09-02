<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1><?php esc_html_e('Simple Custom Fields', 'simple-custom-fields'); ?></h1>

    <?php
    // Debug - Afficher spécifiquement la table scf_fields
    global $wpdb;
    
    echo '<div class="notice notice-info"><p>Début du debug SCF</p></div>';
    
    $results = $wpdb->get_results("
        SELECT p.ID as post_id, 
               p.post_title,
               pm.meta_value as scf_fields
        FROM {$wpdb->posts} p
        JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE p.post_type = 'scf-field-group'
        AND pm.meta_key = 'scf_fields'
        ORDER BY p.post_title
    ");
    
    if (empty($results)) {
        echo '<div class="notice notice-warning"><p>Aucun groupe de champs trouvé dans la base de données</p></div>';
        error_log('SCF Debug: Aucun résultat trouvé pour la requête scf_fields');
    } else {
        error_log('SCF Debug: ' . count($results) . ' groupes trouvés');
    }
    ?>

    <div class="scf-debug-container">
        <h2>Debug - Données BDD</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID Groupe</th>
                    <th>Nom du Groupe</th>
                    <th>Champs (désérialisés)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo esc_html($row->post_id); ?></td>
                        <td><?php echo esc_html($row->post_title); ?></td>
                        <td>
                            <pre><?php 
                                $fields = maybe_unserialize($row->scf_fields);
                                echo esc_html(print_r($fields, true)); 
                            ?></pre>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <style>
        .scf-debug-container {
            margin-top: 20px;
            background: white;
            padding: 20px;
            border: 1px solid #ccd0d4;
        }
        .scf-debug-container pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</div>
