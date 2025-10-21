<?php
/**
 * Configuration centralisée du plugin
 *
 * Gère toutes les configurations et constantes du plugin
 *
 * @package SimpleCustomFields
 * @subpackage Core
 * @since 1.5.0
 */

namespace SCF\Core;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe Config
 *
 * Centralise toutes les configurations du plugin
 */
class Config {
    /**
     * Instance unique (Singleton)
     *
     * @var Config|null
     */
    private static $instance = null;

    /**
     * Configuration du plugin
     *
     * @var array
     */
    private $config = array();

    /**
     * Récupère l'instance unique
     *
     * @return Config
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructeur privé (Singleton)
     */
    private function __construct() {
        $this->init_config();
    }

    /**
     * Initialise la configuration
     *
     * @return void
     */
    private function init_config() {
        $this->config = array(
            // Informations du plugin
            'plugin' => array(
                'name' => 'Simple Custom Fields',
                'slug' => 'simple-custom-fields',
                'version' => '1.5.0',
                'text_domain' => 'simple-custom-fields',
                'author' => 'Akrem Belkahla',
                'author_uri' => 'https://infinityweb.tn',
                'min_php_version' => '7.4',
                'min_wp_version' => '5.0',
            ),

            // Base de données
            'database' => array(
                'table_name' => 'scf_fields',
                'charset' => 'utf8mb4',
                'collate' => 'utf8mb4_unicode_ci',
                'version' => '1.0',
            ),

            // Sécurité
            'security' => array(
                'nonce_lifetime' => 43200, // 12 heures
                'max_attempts_per_hour' => 50,
                'allowed_ajax_actions' => array(
                    'scf_delete_group',
                    'scf_get_field_settings',
                    'scf_save_field_group',
                ),
                'allowed_file_types' => array(
                    'jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx',
                ),
                'max_file_size' => 5242880, // 5MB
            ),

            // Types de champs supportés
            'field_types' => array(
                'text' => array(
                    'label' => 'Texte',
                    'icon' => 'dashicons-editor-textcolor',
                    'category' => 'basic',
                ),
                'textarea' => array(
                    'label' => 'Zone de texte',
                    'icon' => 'dashicons-editor-alignleft',
                    'category' => 'basic',
                ),
                'number' => array(
                    'label' => 'Nombre',
                    'icon' => 'dashicons-calculator',
                    'category' => 'basic',
                ),
                'email' => array(
                    'label' => 'Email',
                    'icon' => 'dashicons-email',
                    'category' => 'basic',
                ),
                'url' => array(
                    'label' => 'URL',
                    'icon' => 'dashicons-admin-links',
                    'category' => 'basic',
                ),
                'date' => array(
                    'label' => 'Date',
                    'icon' => 'dashicons-calendar-alt',
                    'category' => 'basic',
                ),
                'select' => array(
                    'label' => 'Liste déroulante',
                    'icon' => 'dashicons-menu-alt',
                    'category' => 'choice',
                ),
                'radio' => array(
                    'label' => 'Boutons radio',
                    'icon' => 'dashicons-marker',
                    'category' => 'choice',
                ),
                'checkbox' => array(
                    'label' => 'Cases à cocher',
                    'icon' => 'dashicons-yes',
                    'category' => 'choice',
                ),
                'file' => array(
                    'label' => 'Fichier',
                    'icon' => 'dashicons-media-default',
                    'category' => 'content',
                ),
            ),

            // Capacités requises
            'capabilities' => array(
                'manage_field_groups' => 'manage_options',
                'edit_field_groups' => 'manage_options',
                'delete_field_groups' => 'manage_options',
            ),

            // Chemins et URLs
            'paths' => array(
                'plugin_dir' => SCF_PLUGIN_DIR,
                'plugin_url' => SCF_PLUGIN_URL,
                'includes_dir' => SCF_PLUGIN_DIR . 'includes/',
                'assets_dir' => SCF_PLUGIN_DIR . 'assets/',
                'templates_dir' => SCF_PLUGIN_DIR . 'templates/',
                'languages_dir' => SCF_PLUGIN_DIR . 'languages/',
            ),

            // Options par défaut
            'defaults' => array(
                'field_group_status' => 'draft',
                'field_group_position' => 'normal',
                'field_group_priority' => 'default',
                'log_retention_days' => 30,
            ),

            // Performances
            'performance' => array(
                'cache_enabled' => true,
                'cache_expiration' => 3600, // 1 heure
                'transient_prefix' => 'scf_',
            ),
        );

        // Permettre la modification via filtres
        $this->config = apply_filters('scf_config', $this->config);
    }

    /**
     * Récupère une valeur de configuration
     *
     * @param string $key Clé de configuration (notation pointée)
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    public function get($key, $default = null) {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Définit une valeur de configuration
     *
     * @param string $key Clé de configuration
     * @param mixed $value Valeur
     * @return void
     */
    public function set($key, $value) {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = array();
            }
            $config = &$config[$k];
        }

        $config = $value;
    }

    /**
     * Récupère toute la configuration
     *
     * @return array
     */
    public function get_all() {
        return $this->config;
    }

    /**
     * Vérifie si une clé existe
     *
     * @param string $key Clé de configuration
     * @return bool
     */
    public function has($key) {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return false;
            }
            $value = $value[$k];
        }

        return true;
    }

    /**
     * Récupère la version du plugin
     *
     * @return string
     */
    public function get_version() {
        return $this->get('plugin.version');
    }

    /**
     * Récupère le nom du plugin
     *
     * @return string
     */
    public function get_plugin_name() {
        return $this->get('plugin.name');
    }

    /**
     * Récupère le slug du plugin
     *
     * @return string
     */
    public function get_plugin_slug() {
        return $this->get('plugin.slug');
    }

    /**
     * Récupère le text domain
     *
     * @return string
     */
    public function get_text_domain() {
        return $this->get('plugin.text_domain');
    }
}
