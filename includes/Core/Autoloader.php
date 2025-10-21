<?php
/**
 * Autoloader PSR-4 pour Simple Custom Fields
 *
 * Charge automatiquement les classes selon le standard PSR-4
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
 * Classe Autoloader
 *
 * Gère le chargement automatique des classes du plugin
 */
class Autoloader {
    /**
     * Namespace de base du plugin
     *
     * @var string
     */
    private $namespace = 'SCF\\';

    /**
     * Répertoire de base des classes
     *
     * @var string
     */
    private $base_dir;

    /**
     * Constructeur
     *
     * @param string $base_dir Répertoire de base des classes
     */
    public function __construct($base_dir) {
        $this->base_dir = rtrim($base_dir, '/') . '/';
    }

    /**
     * Enregistre l'autoloader
     *
     * @return void
     */
    public function register() {
        spl_autoload_register(array($this, 'load_class'));
    }

    /**
     * Charge une classe
     *
     * @param string $class Nom complet de la classe
     * @return void
     */
    private function load_class($class) {
        // Vérifier si la classe appartient à notre namespace
        if (strpos($class, $this->namespace) !== 0) {
            return;
        }

        // Retirer le namespace de base
        $relative_class = substr($class, strlen($this->namespace));

        // Convertir le namespace en chemin de fichier
        $file = $this->base_dir . str_replace('\\', '/', $relative_class) . '.php';

        // Charger le fichier s'il existe
        if (file_exists($file)) {
            require_once $file;
        }
    }

    /**
     * Charge les fichiers de compatibilité
     *
     * @return void
     */
    public function load_compatibility_files() {
        $compat_files = array(
            'class-scf-database.php',
            'class-scf-security.php',
            'class-scf-fields.php',
            'class-scf-meta-boxes.php',
            'class-scf-admin-page.php',
            'class-scf-import-export.php',
        );

        foreach ($compat_files as $file) {
            $file_path = $this->base_dir . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }
}
