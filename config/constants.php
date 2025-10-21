<?php
/**
 * Constantes de configuration du plugin
 *
 * Définit toutes les constantes utilisées par le plugin
 *
 * @package SimpleCustomFields
 * @since 1.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Version du plugin
if (!defined('SCF_VERSION')) {
    define('SCF_VERSION', '1.5.0');
}

// Version de la base de données
if (!defined('SCF_DB_VERSION')) {
    define('SCF_DB_VERSION', '1.0');
}

// Niveau de log par défaut (debug, info, notice, warning, error, critical, alert, emergency)
if (!defined('SCF_LOG_LEVEL')) {
    define('SCF_LOG_LEVEL', 'warning');
}

// Durée de rétention des logs en jours
if (!defined('SCF_LOG_RETENTION_DAYS')) {
    define('SCF_LOG_RETENTION_DAYS', 30);
}

// Activer le cache
if (!defined('SCF_CACHE_ENABLED')) {
    define('SCF_CACHE_ENABLED', true);
}

// Durée du cache en secondes
if (!defined('SCF_CACHE_DURATION')) {
    define('SCF_CACHE_DURATION', 3600);
}

// Taille maximale des fichiers uploadés (en octets)
if (!defined('SCF_MAX_FILE_SIZE')) {
    define('SCF_MAX_FILE_SIZE', 5242880); // 5MB
}

// Types de fichiers autorisés
if (!defined('SCF_ALLOWED_FILE_TYPES')) {
    define('SCF_ALLOWED_FILE_TYPES', 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx');
}

// Nombre maximum de tentatives par heure
if (!defined('SCF_MAX_ATTEMPTS_PER_HOUR')) {
    define('SCF_MAX_ATTEMPTS_PER_HOUR', 50);
}

// Durée de vie des nonces en secondes
if (!defined('SCF_NONCE_LIFETIME')) {
    define('SCF_NONCE_LIFETIME', 43200); // 12 heures
}

// Activer le mode debug
if (!defined('SCF_DEBUG')) {
    define('SCF_DEBUG', defined('WP_DEBUG') && WP_DEBUG);
}

// Préfixe pour les transients
if (!defined('SCF_TRANSIENT_PREFIX')) {
    define('SCF_TRANSIENT_PREFIX', 'scf_');
}

// Capacité requise pour gérer les groupes de champs
if (!defined('SCF_CAPABILITY')) {
    define('SCF_CAPABILITY', 'manage_options');
}
