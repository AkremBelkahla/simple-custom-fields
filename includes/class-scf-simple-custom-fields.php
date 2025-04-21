<?php

if (!defined('ABSPATH')) {
    exit;
}

class SCF_Simple_Custom_Fields {
    private static $instance = null;
    private $admin_page;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        error_log('SCF Simple Custom Fields construct');
        // Ajouter les actions avec les bonnes priorités
        add_action('init', array($this, 'register_field_group_post_type'), 1);
        add_action('init', array($this, 'init_plugin'), 5);
        
        // Debug hooks
        add_action('init', function() {
            error_log('=== SCF POST TYPE CHECK ===');
            error_log('Post type registered: ' . (post_type_exists('scf-field-group') ? 'yes' : 'no'));
            if ($post_type = get_post_type_object('scf-field-group')) {
                error_log('Post type details: ' . print_r($post_type, true));
            }
        }, 999);
    }

    public function init_plugin() {
        error_log('SCF Plugin initialization');
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }

    public function enqueue_admin_assets($hook) {
        // Inclure les styles sur l'édition de page/post
        if (strpos($hook, 'post.php') !== false || strpos($hook, 'post-new.php') !== false) {
            wp_enqueue_style(
                'scf-fields',
                plugins_url('assets/css/fields.css', dirname(__FILE__)),
                array(),
                filemtime(plugin_dir_path(dirname(__FILE__)) . 'assets/css/fields.css')
            );
        }

        // N'ajouter les autres assets que sur les pages du plugin
        if (!strpos($hook, 'simple-custom-fields') && !strpos($hook, 'scf-')) {
            return;
        }

        wp_enqueue_style(
            'scf-admin',
            plugins_url('assets/css/main.css', dirname(__FILE__)),
            array(),
            filemtime(plugin_dir_path(dirname(__FILE__)) . 'assets/css/main.css')
        );

        wp_enqueue_script(
            'scf-admin',
            plugins_url('assets/js/admin.js', dirname(__FILE__)),
            array('jquery', 'jquery-ui-sortable'),
            filemtime(plugin_dir_path(dirname(__FILE__)) . 'assets/js/admin.js'),
            true
        );

        wp_localize_script('scf-admin', 'scf_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('scf_nonce')
        ));
    }

    public function load_textdomain() {
        load_plugin_textdomain('simple-custom-fields', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function register_field_group_post_type() {
        error_log('Registering SCF field group post type');
        $labels = array(
            'name' => __('Groupes de champs', 'simple-custom-fields'),
            'singular_name' => __('Groupe de champs', 'simple-custom-fields'),
            'add_new' => __('Ajouter', 'simple-custom-fields'),
            'add_new_item' => __('Ajouter un groupe', 'simple-custom-fields'),
            'edit_item' => __('Modifier le groupe', 'simple-custom-fields'),
            'new_item' => __('Nouveau groupe', 'simple-custom-fields'),
            'view_item' => __('Voir le groupe', 'simple-custom-fields'),
            'search_items' => __('Rechercher des groupes', 'simple-custom-fields'),
            'not_found' => __('Aucun groupe trouvé', 'simple-custom-fields'),
            'not_found_in_trash' => __('Aucun groupe trouvé dans la corbeille', 'simple-custom-fields'),
            'menu_name' => __('Groupes de champs', 'simple-custom-fields')
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'capabilities' => array(
                'delete_post' => 'manage_options',
                'delete_posts' => 'manage_options'
            ),
            'hierarchical' => false,
            'supports' => array('title'),
            'rewrite' => false
        );

        register_post_type('scf-field-group', $args);
    }
}
