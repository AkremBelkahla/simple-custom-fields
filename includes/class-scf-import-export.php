<?php
if (!defined('ABSPATH')) exit;

class SCF_Import_Export {
    
    public function __construct() {
        add_action('admin_post_scf_export_groups', array($this, 'export_groups'));
        add_action('admin_post_scf_import_groups', array($this, 'import_groups'));
    }
    
    /**
     * Exporter tous les groupes de champs en JSON
     */
    public function export_groups() {
        // Vérifier le nonce
        if (!isset($_POST['scf_export_nonce']) || !wp_verify_nonce($_POST['scf_export_nonce'], 'scf_export_groups')) {
            wp_die('Erreur de sécurité');
        }
        
        // Vérifier les permissions
        if (!current_user_can('manage_options')) {
            wp_die('Permissions insuffisantes');
        }
        
        // Récupérer tous les groupes de champs
        $groups = get_posts(array(
            'post_type' => 'scf-field-group',
            'posts_per_page' => -1,
            'post_status' => array('publish', 'draft')
        ));
        
        $export_data = array(
            'version' => '1.4.0',
            'export_date' => current_time('mysql'),
            'groups' => array()
        );
        
        foreach ($groups as $group) {
            $group_data = array(
                'title' => $group->post_title,
                'description' => $group->post_content,
                'status' => $group->post_status,
                'fields' => get_post_meta($group->ID, 'scf_fields', true),
                'location' => get_post_meta($group->ID, 'scf_location', true),
                'settings' => array(
                    'position' => get_post_meta($group->ID, 'scf_position', true),
                    'style' => get_post_meta($group->ID, 'scf_style', true)
                )
            );
            
            $export_data['groups'][] = $group_data;
        }
        
        // Générer le nom du fichier
        $filename = 'scf-export-' . date('Y-m-d-His') . '.json';
        
        // Headers pour le téléchargement
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Envoyer le JSON
        echo json_encode($export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Importer des groupes de champs depuis un fichier JSON
     */
    public function import_groups() {
        // Vérifier le nonce
        if (!isset($_POST['scf_import_nonce']) || !wp_verify_nonce($_POST['scf_import_nonce'], 'scf_import_groups')) {
            wp_die('Erreur de sécurité');
        }
        
        // Vérifier les permissions
        if (!current_user_can('manage_options')) {
            wp_die('Permissions insuffisantes');
        }
        
        // Vérifier qu'un fichier a été uploadé
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            wp_redirect(admin_url('admin.php?page=scf-settings&import=error'));
            exit;
        }
        
        // Lire le contenu du fichier
        $file_content = file_get_contents($_FILES['import_file']['tmp_name']);
        $import_data = json_decode($file_content, true);
        
        // Vérifier que le JSON est valide
        if (json_last_error() !== JSON_ERROR_NONE || !isset($import_data['groups'])) {
            wp_redirect(admin_url('admin.php?page=scf-settings&import=error'));
            exit;
        }
        
        $overwrite = isset($_POST['overwrite']) && $_POST['overwrite'] === '1';
        $imported_count = 0;
        
        foreach ($import_data['groups'] as $group_data) {
            // Vérifier si un groupe avec le même titre existe
            $existing = get_page_by_title($group_data['title'], OBJECT, 'scf-field-group');
            
            if ($existing && !$overwrite) {
                // Ajouter un suffixe au titre
                $group_data['title'] .= ' (Importé)';
            } elseif ($existing && $overwrite) {
                // Supprimer l'ancien groupe
                wp_delete_post($existing->ID, true);
            }
            
            // Créer le nouveau groupe
            $post_id = wp_insert_post(array(
                'post_title' => $group_data['title'],
                'post_content' => isset($group_data['description']) ? $group_data['description'] : '',
                'post_status' => isset($group_data['status']) ? $group_data['status'] : 'publish',
                'post_type' => 'scf-field-group'
            ));
            
            if ($post_id && !is_wp_error($post_id)) {
                // Ajouter les métadonnées
                if (isset($group_data['fields'])) {
                    update_post_meta($post_id, 'scf_fields', $group_data['fields']);
                }
                
                if (isset($group_data['location'])) {
                    update_post_meta($post_id, 'scf_location', $group_data['location']);
                }
                
                if (isset($group_data['settings']['position'])) {
                    update_post_meta($post_id, 'scf_position', $group_data['settings']['position']);
                }
                
                if (isset($group_data['settings']['style'])) {
                    update_post_meta($post_id, 'scf_style', $group_data['settings']['style']);
                }
                
                $imported_count++;
            }
        }
        
        // Rediriger avec message de succès
        wp_redirect(admin_url('admin.php?page=scf-settings&import=success&count=' . $imported_count));
        exit;
    }
}

// Initialiser la classe
new SCF_Import_Export();
