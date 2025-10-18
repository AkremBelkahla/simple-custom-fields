<?php
/**
 * Classe de sécurité centralisée pour Simple Custom Fields
 * 
 * Gère toutes les vérifications de sécurité du plugin
 * 
 * @package SimpleCustomFields
 * @since 1.4.1
 */

if (!defined('ABSPATH')) {
    exit;
}

class SCF_Security {
    private static $instance = null;
    
    /**
     * Nombre maximum de tentatives par heure
     */
    const MAX_ATTEMPTS_PER_HOUR = 50;
    
    /**
     * Durée de vie du nonce en secondes (12 heures)
     */
    const NONCE_LIFETIME = 43200;
    
    /**
     * Actions autorisées pour les requêtes AJAX
     */
    private $allowed_ajax_actions = array(
        'scf_delete_group',
        'scf_get_field_settings',
        'scf_save_field_group'
    );
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Modifier la durée de vie des nonces
        add_filter('nonce_life', array($this, 'set_nonce_lifetime'));
        
        // Ajouter des headers de sécurité
        add_action('admin_init', array($this, 'add_security_headers'));
    }
    
    /**
     * Définir la durée de vie des nonces
     */
    public function set_nonce_lifetime() {
        return self::NONCE_LIFETIME;
    }
    
    /**
     * Ajouter des headers de sécurité HTTP
     */
    public function add_security_headers() {
        if (!headers_sent()) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: SAMEORIGIN');
            header('X-XSS-Protection: 1; mode=block');
            header('Referrer-Policy: strict-origin-when-cross-origin');
        }
    }
    
    /**
     * Vérifier les permissions utilisateur
     * 
     * @param string $capability Capability WordPress requise
     * @return bool
     */
    public function check_permissions($capability = 'manage_options') {
        if (!is_user_logged_in()) {
            return false;
        }
        
        if (!current_user_can($capability)) {
            $this->log_security_event('permission_denied', array(
                'user_id' => get_current_user_id(),
                'capability' => $capability
            ));
            return false;
        }
        
        return true;
    }
    
    /**
     * Vérifier le nonce de sécurité
     * 
     * @param string $nonce Nonce à vérifier
     * @param string $action Action associée au nonce
     * @return bool
     */
    public function verify_nonce($nonce, $action = 'scf_nonce') {
        if (empty($nonce)) {
            $this->log_security_event('nonce_missing', array(
                'action' => $action
            ));
            return false;
        }
        
        $verified = wp_verify_nonce($nonce, $action);
        
        if (!$verified) {
            $this->log_security_event('nonce_invalid', array(
                'action' => $action,
                'user_id' => get_current_user_id()
            ));
            return false;
        }
        
        return true;
    }
    
    /**
     * Vérifier une requête AJAX
     * 
     * @param string $action Action AJAX
     * @param string $nonce Nonce à vérifier
     * @param string $capability Capability requise
     * @return bool
     */
    public function verify_ajax_request($action, $nonce, $capability = 'manage_options') {
        // Vérifier que c'est bien une requête AJAX
        if (!wp_doing_ajax()) {
            $this->log_security_event('not_ajax_request', array('action' => $action));
            return false;
        }
        
        // Vérifier que l'action est autorisée
        if (!in_array($action, $this->allowed_ajax_actions)) {
            $this->log_security_event('unauthorized_ajax_action', array('action' => $action));
            return false;
        }
        
        // Vérifier les permissions
        if (!$this->check_permissions($capability)) {
            return false;
        }
        
        // Vérifier le nonce
        if (!$this->verify_nonce($nonce)) {
            return false;
        }
        
        // Vérifier le referer
        if (!$this->verify_referer()) {
            return false;
        }
        
        // Vérifier le rate limiting
        if (!$this->check_rate_limit()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Vérifier le referer HTTP
     * 
     * @return bool
     */
    public function verify_referer() {
        $referer = wp_get_referer();
        
        if (!$referer) {
            $this->log_security_event('missing_referer');
            return false;
        }
        
        // Vérifier que le referer vient bien de l'admin WordPress
        if (strpos($referer, admin_url()) !== 0) {
            $this->log_security_event('invalid_referer', array(
                'referer' => $referer
            ));
            return false;
        }
        
        return true;
    }
    
    /**
     * Vérifier le rate limiting (limitation du nombre de requêtes)
     * 
     * @return bool
     */
    public function check_rate_limit() {
        $user_id = get_current_user_id();
        $transient_key = 'scf_rate_limit_' . $user_id;
        $attempts = get_transient($transient_key);
        
        if ($attempts === false) {
            // Première tentative de l'heure
            set_transient($transient_key, 1, HOUR_IN_SECONDS);
            return true;
        }
        
        if ($attempts >= self::MAX_ATTEMPTS_PER_HOUR) {
            $this->log_security_event('rate_limit_exceeded', array(
                'user_id' => $user_id,
                'attempts' => $attempts
            ));
            return false;
        }
        
        // Incrémenter le compteur
        set_transient($transient_key, $attempts + 1, HOUR_IN_SECONDS);
        return true;
    }
    
    /**
     * Sanitize un tableau de manière récursive
     * 
     * @param array $data Données à nettoyer
     * @param string $type Type de sanitization (text, email, url, key, etc.)
     * @return array
     */
    public function sanitize_array($data, $type = 'text') {
        if (!is_array($data)) {
            return $this->sanitize_value($data, $type);
        }
        
        $sanitized = array();
        foreach ($data as $key => $value) {
            $sanitized_key = sanitize_key($key);
            
            if (is_array($value)) {
                $sanitized[$sanitized_key] = $this->sanitize_array($value, $type);
            } else {
                $sanitized[$sanitized_key] = $this->sanitize_value($value, $type);
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize une valeur selon son type
     * 
     * @param mixed $value Valeur à nettoyer
     * @param string $type Type de sanitization
     * @return mixed
     */
    public function sanitize_value($value, $type = 'text') {
        switch ($type) {
            case 'email':
                return sanitize_email($value);
                
            case 'url':
                return esc_url_raw($value);
                
            case 'key':
                return sanitize_key($value);
                
            case 'textarea':
                return sanitize_textarea_field($value);
                
            case 'html':
                return wp_kses_post($value);
                
            case 'int':
                return intval($value);
                
            case 'float':
                return floatval($value);
                
            case 'bool':
                return (bool) $value;
                
            case 'text':
            default:
                return sanitize_text_field($value);
        }
    }
    
    /**
     * Valider un ID de post
     * 
     * @param int $post_id ID du post
     * @param string $post_type Type de post attendu
     * @return bool|WP_Post
     */
    public function validate_post_id($post_id, $post_type = 'scf-field-group') {
        $post_id = intval($post_id);
        
        if ($post_id <= 0) {
            $this->log_security_event('invalid_post_id', array('post_id' => $post_id));
            return false;
        }
        
        $post = get_post($post_id);
        
        if (!$post) {
            $this->log_security_event('post_not_found', array('post_id' => $post_id));
            return false;
        }
        
        if ($post->post_type !== $post_type) {
            $this->log_security_event('invalid_post_type', array(
                'post_id' => $post_id,
                'expected' => $post_type,
                'actual' => $post->post_type
            ));
            return false;
        }
        
        return $post;
    }
    
    /**
     * Échapper les données pour l'affichage
     * 
     * @param mixed $data Données à échapper
     * @param string $context Contexte (html, attr, js, url)
     * @return mixed
     */
    public function escape_output($data, $context = 'html') {
        if (is_array($data)) {
            return array_map(function($item) use ($context) {
                return $this->escape_output($item, $context);
            }, $data);
        }
        
        switch ($context) {
            case 'attr':
                return esc_attr($data);
                
            case 'js':
                return esc_js($data);
                
            case 'url':
                return esc_url($data);
                
            case 'textarea':
                return esc_textarea($data);
                
            case 'html':
            default:
                return esc_html($data);
        }
    }
    
    /**
     * Logger un événement de sécurité
     * 
     * @param string $event Type d'événement
     * @param array $data Données supplémentaires
     */
    private function log_security_event($event, $data = array()) {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }
        
        $log_data = array(
            'event' => $event,
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id(),
            'ip' => $this->get_client_ip(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '',
            'data' => $data
        );
        
        error_log('[SCF Security] ' . wp_json_encode($log_data));
    }
    
    /**
     * Obtenir l'IP du client de manière sécurisée
     * 
     * @return string
     */
    private function get_client_ip() {
        $ip_keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        
        foreach ($ip_keys as $key) {
            if (isset($_SERVER[$key]) && filter_var($_SERVER[$key], FILTER_VALIDATE_IP)) {
                return sanitize_text_field($_SERVER[$key]);
            }
        }
        
        return 'unknown';
    }
    
    /**
     * Générer un nonce sécurisé
     * 
     * @param string $action Action pour le nonce
     * @return string
     */
    public function create_nonce($action = 'scf_nonce') {
        return wp_create_nonce($action);
    }
    
    /**
     * Vérifier si une requête POST est valide
     * 
     * @param string $nonce_field Nom du champ nonce
     * @param string $nonce_action Action du nonce
     * @param string $capability Capability requise
     * @return bool
     */
    public function verify_post_request($nonce_field = 'scf_nonce', $nonce_action = 'scf_save_field_group', $capability = 'manage_options') {
        // Vérifier la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->log_security_event('invalid_http_method', array(
                'method' => $_SERVER['REQUEST_METHOD']
            ));
            return false;
        }
        
        // Vérifier les permissions
        if (!$this->check_permissions($capability)) {
            return false;
        }
        
        // Vérifier le nonce
        if (!isset($_POST[$nonce_field]) || !$this->verify_nonce($_POST[$nonce_field], $nonce_action)) {
            return false;
        }
        
        return true;
    }
}
