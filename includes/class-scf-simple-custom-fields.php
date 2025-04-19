<?php
/**
 * Plugin Name: Simple Custom Fields
 * Description: Un plugin simple pour créer et gérer des champs personnalisés dans WordPress.
 * Version: 1.0.0
 * Author: Votre Nom
 */

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
        add_action('init', array($this, 'register_field_group_post_type'));
    }

    public function init() {
        // Initialisation des composants
        $this->admin_page = SCF_Admin_Page::get_instance();

        // Hooks
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }

    public function enqueue_admin_assets($hook) {
        // N'ajouter les assets que sur les pages du plugin
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
            'show_ui' => false,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array('title'),
            'rewrite' => false
        );

        register_post_type('scf-field-group', $args);
    }
}
