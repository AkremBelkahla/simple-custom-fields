<?php
if (!defined('ABSPATH')) exit;

// Charger WP_List_Table si nécessaire
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Classe pour afficher la liste des groupes de champs avec WP_List_Table
 */
class SCF_Groups_List_Table extends WP_List_Table {
    
    /**
     * Constructeur
     */
    public function __construct() {
        parent::__construct(array(
            'singular' => __('Groupe de champs', 'simple-custom-fields'),
            'plural'   => __('Groupes de champs', 'simple-custom-fields'),
            'ajax'     => false
        ));
    }
    
    /**
     * Définir les colonnes de la table
     */
    public function get_columns() {
        return array(
            'cb'          => '<input type="checkbox" />',
            'title'       => __('Titre', 'simple-custom-fields'),
            'description' => __('Description', 'simple-custom-fields'),
            'fields'      => __('Champs', 'simple-custom-fields'),
            'type'        => __('Type de contenu', 'simple-custom-fields'),
            'status'      => __('Statut', 'simple-custom-fields')
        );
    }
    
    /**
     * Définir les colonnes triables
     */
    public function get_sortable_columns() {
        return array(
            'title'  => array('title', false),
            'status' => array('status', false)
        );
    }
    
    /**
     * Définir les colonnes cachées
     */
    public function get_hidden_columns() {
        return array();
    }
    
    /**
     * Colonne par défaut
     */
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'description':
            case 'fields':
            case 'type':
            case 'status':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }
    
    /**
     * Colonne checkbox
     */
    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="group[]" value="%s" />',
            $item['ID']
        );
    }
    
    /**
     * Colonne titre avec actions
     */
    public function column_title($item) {
        $edit_url = admin_url('admin.php?page=scf-add-group&group_id=' . $item['ID']);
        $delete_url = wp_nonce_url(
            admin_url('admin.php?page=simple-custom-fields&action=delete&group_id=' . $item['ID']),
            'scf_delete_group_' . $item['ID']
        );
        
        $actions = array(
            'edit' => sprintf(
                '<a href="%s">%s</a>',
                $edit_url,
                __('Modifier', 'simple-custom-fields')
            )
        );
        
        if (current_user_can('manage_options')) {
            $actions['delete'] = sprintf(
                '<a href="#" class="scf-delete-group" data-group-id="%s" data-nonce="%s">%s</a>',
                $item['ID'],
                wp_create_nonce('scf_delete_group'),
                __('Supprimer', 'simple-custom-fields')
            );
        }
        
        return sprintf(
            '<strong><a href="%s">%s</a></strong>%s',
            $edit_url,
            $item['title'],
            $this->row_actions($actions)
        );
    }
    
    /**
     * Colonne description
     */
    public function column_description($item) {
        return !empty($item['description']) 
            ? '<span class="description">' . esc_html($item['description']) . '</span>'
            : '—';
    }
    
    /**
     * Colonne champs
     */
    public function column_fields($item) {
        $count = $item['fields_count'];
        $label = sprintf(
            _n('%d champ', '%d champs', $count, 'simple-custom-fields'),
            $count
        );
        
        return sprintf(
            '<span class="scf-badge scf-badge-info"><span class="dashicons dashicons-admin-settings"></span> %s</span>',
            $label
        );
    }
    
    /**
     * Colonne type de contenu
     */
    public function column_type($item) {
        return sprintf(
            '<span class="scf-badge scf-badge-secondary"><span class="dashicons dashicons-admin-post"></span> %s</span>',
            esc_html($item['type'])
        );
    }
    
    /**
     * Colonne statut
     */
    public function column_status($item) {
        $is_active = $item['status'] === 'publish';
        $badge_class = $is_active ? 'scf-badge-success' : 'scf-badge-warning';
        $status_text = $is_active 
            ? '● ' . __('Activé', 'simple-custom-fields')
            : '○ ' . __('Désactivé', 'simple-custom-fields');
        
        return sprintf(
            '<span class="scf-badge %s">%s</span>',
            $badge_class,
            $status_text
        );
    }
    
    /**
     * Actions groupées
     */
    public function get_bulk_actions() {
        $actions = array();
        
        if (current_user_can('manage_options')) {
            $actions['delete'] = __('Supprimer', 'simple-custom-fields');
            $actions['activate'] = __('Activer', 'simple-custom-fields');
            $actions['deactivate'] = __('Désactiver', 'simple-custom-fields');
        }
        
        return $actions;
    }
    
    /**
     * Traiter les actions groupées
     */
    public function process_bulk_action() {
        // Vérifier le nonce
        if (isset($_REQUEST['_wpnonce']) && !empty($_REQUEST['_wpnonce'])) {
            $nonce = sanitize_text_field($_REQUEST['_wpnonce']);
            $action = $this->current_action();
            
            if (!wp_verify_nonce($nonce, 'bulk-' . $this->_args['plural'])) {
                wp_die(__('Échec de la vérification de sécurité', 'simple-custom-fields'));
            }
            
            if (isset($_REQUEST['group']) && is_array($_REQUEST['group'])) {
                $group_ids = array_map('intval', $_REQUEST['group']);
                
                switch ($action) {
                    case 'delete':
                        foreach ($group_ids as $group_id) {
                            wp_delete_post($group_id, true);
                            delete_post_meta($group_id, 'scf_fields');
                            delete_post_meta($group_id, 'scf_rules');
                        }
                        break;
                        
                    case 'activate':
                        foreach ($group_ids as $group_id) {
                            wp_update_post(array(
                                'ID' => $group_id,
                                'post_status' => 'publish'
                            ));
                        }
                        break;
                        
                    case 'deactivate':
                        foreach ($group_ids as $group_id) {
                            wp_update_post(array(
                                'ID' => $group_id,
                                'post_status' => 'draft'
                            ));
                        }
                        break;
                }
                
                // Rediriger après l'action
                wp_redirect(admin_url('admin.php?page=simple-custom-fields&message=bulk_success'));
                exit;
            }
        }
    }
    
    /**
     * Vues supplémentaires (filtres)
     */
    protected function get_views() {
        $status_links = array();
        $current = isset($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : 'all';
        
        // Compter les groupes par statut
        $all_count = wp_count_posts('scf-field-group');
        $total = $all_count->publish + $all_count->draft;
        $active = $all_count->publish;
        $inactive = $all_count->draft;
        
        // Lien "Tous"
        $class = ($current === 'all') ? ' class="current"' : '';
        $status_links['all'] = sprintf(
            '<a href="%s"%s>%s <span class="count">(%d)</span></a>',
            admin_url('admin.php?page=simple-custom-fields'),
            $class,
            __('Tous', 'simple-custom-fields'),
            $total
        );
        
        // Lien "Actifs"
        $class = ($current === 'active') ? ' class="current"' : '';
        $status_links['active'] = sprintf(
            '<a href="%s"%s>%s <span class="count">(%d)</span></a>',
            admin_url('admin.php?page=simple-custom-fields&status=active'),
            $class,
            __('Actifs', 'simple-custom-fields'),
            $active
        );
        
        // Lien "Inactifs"
        $class = ($current === 'inactive') ? ' class="current"' : '';
        $status_links['inactive'] = sprintf(
            '<a href="%s"%s>%s <span class="count">(%d)</span></a>',
            admin_url('admin.php?page=simple-custom-fields&status=inactive'),
            $class,
            __('Inactifs', 'simple-custom-fields'),
            $inactive
        );
        
        return $status_links;
    }
    
    /**
     * Message quand aucun élément n'est trouvé
     */
    public function no_items() {
        echo '<div class="scf-no-items">';
        echo '<span class="dashicons dashicons-admin-generic" style="font-size: 64px; color: #ccc; margin-bottom: 16px;"></span>';
        echo '<p style="font-size: 18px; font-weight: 500;">' . __('Aucun groupe de champs', 'simple-custom-fields') . '</p>';
        echo '<p>' . __('Commencez par créer votre premier groupe de champs personnalisés.', 'simple-custom-fields') . '</p>';
        echo '<p><a href="' . admin_url('admin.php?page=scf-add-group') . '" class="button button-primary button-large">';
        echo '<span class="dashicons dashicons-plus-alt2" style="margin-top: 3px;"></span> ';
        echo __('Créer un groupe de champs', 'simple-custom-fields');
        echo '</a></p>';
        echo '</div>';
    }
    
    /**
     * Préparer les éléments à afficher
     */
    public function prepare_items() {
        // Traiter les actions groupées
        $this->process_bulk_action();
        
        // Définir les colonnes
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        // Récupérer les paramètres de recherche et de filtre
        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
        $status_filter = isset($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : 'all';
        $orderby = isset($_REQUEST['orderby']) ? sanitize_text_field($_REQUEST['orderby']) : 'title';
        $order = isset($_REQUEST['order']) ? sanitize_text_field($_REQUEST['order']) : 'ASC';
        
        // Construire les arguments de la requête
        $args = array(
            'post_type' => 'scf-field-group',
            'posts_per_page' => -1,
            'orderby' => $orderby,
            'order' => $order
        );
        
        // Filtrer par statut
        if ($status_filter === 'active') {
            $args['post_status'] = 'publish';
        } elseif ($status_filter === 'inactive') {
            $args['post_status'] = 'draft';
        } else {
            $args['post_status'] = array('publish', 'draft');
        }
        
        // Recherche
        if (!empty($search)) {
            $args['s'] = $search;
        }
        
        // Récupérer les groupes
        $groups = get_posts($args);
        
        // Préparer les données
        $data = array();
        foreach ($groups as $group) {
            $fields = get_post_meta($group->ID, 'scf_fields', true);
            $rules = get_post_meta($group->ID, 'scf_rules', true);
            $fields_count = is_array($fields) ? count($fields) : 0;
            
            // Récupérer le type de contenu
            $content_type = '—';
            if (is_array($rules) && !empty($rules['value'])) {
                $post_type_obj = get_post_type_object($rules['value']);
                if ($post_type_obj) {
                    $content_type = $post_type_obj->labels->singular_name;
                } else {
                    $content_type = $rules['value'];
                }
            }
            
            $data[] = array(
                'ID' => $group->ID,
                'title' => $group->post_title,
                'description' => $group->post_content,
                'fields_count' => $fields_count,
                'fields' => $fields_count,
                'type' => $content_type,
                'status' => $group->post_status
            );
        }
        
        $this->items = $data;
        
        // Pagination (si nécessaire)
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }
}
