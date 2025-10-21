<?php
/**
 * Gestionnaire d'erreurs centralisé
 *
 * Gère toutes les erreurs du plugin de manière structurée
 *
 * @package SimpleCustomFields
 * @subpackage Utilities
 * @since 1.5.0
 */

namespace SCF\Utilities;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe ErrorHandler
 *
 * Gestion centralisée des erreurs avec logging et notifications
 */
class ErrorHandler {
    /**
     * Instance unique (Singleton)
     *
     * @var ErrorHandler|null
     */
    private static $instance = null;

    /**
     * Logger
     *
     * @var Logger
     */
    private $logger;

    /**
     * Erreurs collectées
     *
     * @var array
     */
    private $errors = array();

    /**
     * Récupère l'instance unique
     *
     * @return ErrorHandler
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
        $this->logger = Logger::get_instance();
        
        // Enregistrer les hooks WordPress pour les erreurs
        add_action('admin_notices', array($this, 'display_admin_notices'));
    }

    /**
     * Enregistre une erreur
     *
     * @param string $code Code d'erreur
     * @param string $message Message d'erreur
     * @param array $context Contexte supplémentaire
     * @param string $severity Sévérité (error, warning, info)
     * @return void
     */
    public function add_error($code, $message, array $context = array(), $severity = 'error') {
        $error = array(
            'code' => $code,
            'message' => $message,
            'context' => $context,
            'severity' => $severity,
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id(),
        );

        $this->errors[] = $error;

        // Logger l'erreur
        switch ($severity) {
            case 'critical':
                $this->logger->critical($message, array_merge($context, array('code' => $code)));
                break;
            case 'error':
                $this->logger->error($message, array_merge($context, array('code' => $code)));
                break;
            case 'warning':
                $this->logger->warning($message, array_merge($context, array('code' => $code)));
                break;
            default:
                $this->logger->info($message, array_merge($context, array('code' => $code)));
        }

        // Stocker en transient pour affichage admin
        if (is_admin()) {
            $this->store_admin_notice($error);
        }
    }

    /**
     * Stocke une notice admin
     *
     * @param array $error Erreur à stocker
     * @return void
     */
    private function store_admin_notice($error) {
        $user_id = get_current_user_id();
        $transient_key = 'scf_admin_notices_' . $user_id;
        
        $notices = get_transient($transient_key);
        if (!is_array($notices)) {
            $notices = array();
        }

        $notices[] = $error;
        set_transient($transient_key, $notices, HOUR_IN_SECONDS);
    }

    /**
     * Affiche les notices admin
     *
     * @return void
     */
    public function display_admin_notices() {
        $user_id = get_current_user_id();
        $transient_key = 'scf_admin_notices_' . $user_id;
        
        $notices = get_transient($transient_key);
        if (!is_array($notices) || empty($notices)) {
            return;
        }

        foreach ($notices as $notice) {
            $class = 'notice notice-' . $this->get_notice_class($notice['severity']);
            printf(
                '<div class="%s"><p><strong>Simple Custom Fields:</strong> %s</p></div>',
                esc_attr($class),
                esc_html($notice['message'])
            );
        }

        // Supprimer les notices affichées
        delete_transient($transient_key);
    }

    /**
     * Convertit la sévérité en classe CSS WordPress
     *
     * @param string $severity Sévérité
     * @return string
     */
    private function get_notice_class($severity) {
        $map = array(
            'critical' => 'error',
            'error' => 'error',
            'warning' => 'warning',
            'info' => 'info',
            'success' => 'success',
        );

        return isset($map[$severity]) ? $map[$severity] : 'info';
    }

    /**
     * Récupère toutes les erreurs
     *
     * @return array
     */
    public function get_errors() {
        return $this->errors;
    }

    /**
     * Récupère les erreurs par code
     *
     * @param string $code Code d'erreur
     * @return array
     */
    public function get_errors_by_code($code) {
        return array_filter($this->errors, function($error) use ($code) {
            return $error['code'] === $code;
        });
    }

    /**
     * Vérifie s'il y a des erreurs
     *
     * @return bool
     */
    public function has_errors() {
        return !empty($this->errors);
    }

    /**
     * Efface toutes les erreurs
     *
     * @return void
     */
    public function clear_errors() {
        $this->errors = array();
    }

    /**
     * Gère une exception
     *
     * @param \Exception $exception Exception à gérer
     * @param string $context Contexte de l'exception
     * @return void
     */
    public function handle_exception(\Exception $exception, $context = '') {
        $this->add_error(
            'exception_' . get_class($exception),
            $exception->getMessage(),
            array(
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
                'context' => $context,
            ),
            'critical'
        );
    }

    /**
     * Crée une erreur de validation
     *
     * @param string $field Champ concerné
     * @param string $message Message d'erreur
     * @return void
     */
    public function validation_error($field, $message) {
        $this->add_error(
            'validation_error',
            sprintf(__('Erreur de validation pour le champ "%s": %s', 'simple-custom-fields'), $field, $message),
            array('field' => $field),
            'warning'
        );
    }

    /**
     * Crée une erreur de sécurité
     *
     * @param string $type Type de violation
     * @param array $context Contexte
     * @return void
     */
    public function security_error($type, array $context = array()) {
        $this->add_error(
            'security_violation',
            sprintf(__('Violation de sécurité détectée: %s', 'simple-custom-fields'), $type),
            $context,
            'critical'
        );
    }

    /**
     * Crée une erreur de base de données
     *
     * @param string $query Requête SQL
     * @param string $error Message d'erreur
     * @return void
     */
    public function database_error($query, $error) {
        global $wpdb;
        
        $this->add_error(
            'database_error',
            __('Erreur de base de données', 'simple-custom-fields'),
            array(
                'query' => $query,
                'error' => $error,
                'last_error' => $wpdb->last_error,
            ),
            'critical'
        );
    }
}
