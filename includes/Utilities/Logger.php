<?php
/**
 * Système de logging centralisé
 *
 * Gère tous les logs du plugin de manière structurée
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
 * Classe Logger
 *
 * Système de logging avec niveaux de sévérité
 */
class Logger {
    /**
     * Instance unique (Singleton)
     *
     * @var Logger|null
     */
    private static $instance = null;

    /**
     * Niveaux de log
     */
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';

    /**
     * Chemin du fichier de log
     *
     * @var string
     */
    private $log_file;

    /**
     * Activer/désactiver le logging
     *
     * @var bool
     */
    private $enabled;

    /**
     * Niveau minimum de log
     *
     * @var string
     */
    private $min_level;

    /**
     * Hiérarchie des niveaux
     *
     * @var array
     */
    private $levels = array(
        self::DEBUG     => 0,
        self::INFO      => 1,
        self::NOTICE    => 2,
        self::WARNING   => 3,
        self::ERROR     => 4,
        self::CRITICAL  => 5,
        self::ALERT     => 6,
        self::EMERGENCY => 7,
    );

    /**
     * Récupère l'instance unique
     *
     * @return Logger
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
        $upload_dir = wp_upload_dir();
        $log_dir = $upload_dir['basedir'] . '/scf-logs';

        // Créer le répertoire de logs s'il n'existe pas
        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
            // Protéger le répertoire avec .htaccess
            file_put_contents($log_dir . '/.htaccess', 'Deny from all');
        }

        $this->log_file = $log_dir . '/scf-' . date('Y-m-d') . '.log';
        $this->enabled = defined('WP_DEBUG') && WP_DEBUG;
        $this->min_level = defined('SCF_LOG_LEVEL') ? SCF_LOG_LEVEL : self::WARNING;
    }

    /**
     * Log un message
     *
     * @param string $level Niveau de log
     * @param string $message Message à logger
     * @param array $context Contexte supplémentaire
     * @return void
     */
    public function log($level, $message, array $context = array()) {
        if (!$this->enabled) {
            return;
        }

        // Vérifier le niveau minimum
        if (!isset($this->levels[$level]) || !isset($this->levels[$this->min_level])) {
            return;
        }

        if ($this->levels[$level] < $this->levels[$this->min_level]) {
            return;
        }

        $log_entry = $this->format_log_entry($level, $message, $context);

        // Écrire dans le fichier de log
        error_log($log_entry, 3, $this->log_file);

        // Également logger dans error_log de PHP si niveau critique
        if ($this->levels[$level] >= $this->levels[self::ERROR]) {
            error_log('[SCF] ' . $message);
        }
    }

    /**
     * Formate une entrée de log
     *
     * @param string $level Niveau
     * @param string $message Message
     * @param array $context Contexte
     * @return string
     */
    private function format_log_entry($level, $message, array $context) {
        $timestamp = current_time('Y-m-d H:i:s');
        $user_id = get_current_user_id();
        
        $entry = sprintf(
            "[%s] [%s] [User:%d] %s",
            $timestamp,
            strtoupper($level),
            $user_id,
            $message
        );

        if (!empty($context)) {
            $entry .= ' | Context: ' . wp_json_encode($context);
        }

        return $entry . PHP_EOL;
    }

    /**
     * Log niveau DEBUG
     *
     * @param string $message Message
     * @param array $context Contexte
     * @return void
     */
    public function debug($message, array $context = array()) {
        $this->log(self::DEBUG, $message, $context);
    }

    /**
     * Log niveau INFO
     *
     * @param string $message Message
     * @param array $context Contexte
     * @return void
     */
    public function info($message, array $context = array()) {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Log niveau NOTICE
     *
     * @param string $message Message
     * @param array $context Contexte
     * @return void
     */
    public function notice($message, array $context = array()) {
        $this->log(self::NOTICE, $message, $context);
    }

    /**
     * Log niveau WARNING
     *
     * @param string $message Message
     * @param array $context Contexte
     * @return void
     */
    public function warning($message, array $context = array()) {
        $this->log(self::WARNING, $message, $context);
    }

    /**
     * Log niveau ERROR
     *
     * @param string $message Message
     * @param array $context Contexte
     * @return void
     */
    public function error($message, array $context = array()) {
        $this->log(self::ERROR, $message, $context);
    }

    /**
     * Log niveau CRITICAL
     *
     * @param string $message Message
     * @param array $context Contexte
     * @return void
     */
    public function critical($message, array $context = array()) {
        $this->log(self::CRITICAL, $message, $context);
    }

    /**
     * Log niveau ALERT
     *
     * @param string $message Message
     * @param array $context Contexte
     * @return void
     */
    public function alert($message, array $context = array()) {
        $this->log(self::ALERT, $message, $context);
    }

    /**
     * Log niveau EMERGENCY
     *
     * @param string $message Message
     * @param array $context Contexte
     * @return void
     */
    public function emergency($message, array $context = array()) {
        $this->log(self::EMERGENCY, $message, $context);
    }

    /**
     * Nettoie les anciens fichiers de log
     *
     * @param int $days Nombre de jours à conserver
     * @return void
     */
    public function cleanup_old_logs($days = 30) {
        $upload_dir = wp_upload_dir();
        $log_dir = $upload_dir['basedir'] . '/scf-logs';

        if (!is_dir($log_dir)) {
            return;
        }

        $files = glob($log_dir . '/scf-*.log');
        $cutoff_time = time() - ($days * DAY_IN_SECONDS);

        foreach ($files as $file) {
            if (filemtime($file) < $cutoff_time) {
                unlink($file);
            }
        }
    }
}
