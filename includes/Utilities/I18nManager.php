<?php
/**
 * Gestionnaire de traductions
 * 
 * Gère le chargement et la sélection automatique des traductions
 * 
 * @package SCF
 * @subpackage Utilities
 * @since 1.5.0
 */

namespace SCF\Utilities;

/**
 * Classe I18nManager
 */
class I18nManager {
    
    /**
     * Instance unique de la classe
     * 
     * @var I18nManager
     */
    private static $instance = null;
    
    /**
     * Text domain du plugin
     * 
     * @var string
     */
    private $textDomain = 'simple-custom-fields';
    
    /**
     * Chemin vers le dossier des langues
     * 
     * @var string
     */
    private $languagesPath;
    
    /**
     * Locale actuelle
     * 
     * @var string
     */
    private $currentLocale;
    
    /**
     * Langues supportées
     * 
     * @var array
     */
    private $supportedLocales = [
        'fr_FR' => 'Français',
        'en_US' => 'English'
    ];
    
    /**
     * Constructeur privé (Singleton)
     */
    private function __construct() {
        $this->languagesPath = plugin_dir_path(dirname(dirname(__FILE__))) . 'languages';
        $this->currentLocale = $this->determineLocale();
    }
    
    /**
     * Obtient l'instance unique de la classe
     * 
     * @return I18nManager
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialise le système de traduction
     */
    public function init() {
        add_action('plugins_loaded', [$this, 'loadTextdomain'], 1);
        add_filter('plugin_locale', [$this, 'filterLocale'], 10, 2);
    }
    
    /**
     * Charge le text domain
     */
    public function loadTextdomain() {
        // Charger depuis le dossier du plugin
        $loaded = load_plugin_textdomain(
            $this->textDomain,
            false,
            basename(dirname($this->languagesPath)) . '/languages'
        );
        
        if ($loaded) {
            $this->log('Traductions chargées pour: ' . $this->currentLocale);
        } else {
            $this->log('Échec du chargement des traductions pour: ' . $this->currentLocale, 'warning');
        }
        
        // Charger depuis wp-content/languages/plugins (prioritaire)
        load_plugin_textdomain(
            $this->textDomain,
            false,
            false
        );
    }
    
    /**
     * Filtre la locale pour le plugin
     * 
     * @param string $locale Locale actuelle
     * @param string $domain Text domain
     * @return string
     */
    public function filterLocale($locale, $domain) {
        if ($domain === $this->textDomain) {
            return $this->currentLocale;
        }
        return $locale;
    }
    
    /**
     * Détermine la locale à utiliser
     * 
     * Logique : Français si WordPress est en français, sinon anglais
     * 
     * @return string
     */
    private function determineLocale() {
        $wpLocale = get_locale();
        
        // Si WordPress est en français, utiliser le français
        if (strpos($wpLocale, 'fr') === 0) {
            return 'fr_FR';
        }
        
        // Si la locale WordPress est supportée, l'utiliser
        if (isset($this->supportedLocales[$wpLocale])) {
            return $wpLocale;
        }
        
        // Par défaut, utiliser l'anglais
        return 'en_US';
    }
    
    /**
     * Obtient la locale actuelle
     * 
     * @return string
     */
    public function getCurrentLocale() {
        return $this->currentLocale;
    }
    
    /**
     * Obtient le nom de la langue actuelle
     * 
     * @return string
     */
    public function getCurrentLanguageName() {
        return $this->supportedLocales[$this->currentLocale] ?? 'English';
    }
    
    /**
     * Obtient toutes les langues supportées
     * 
     * @return array
     */
    public function getSupportedLocales() {
        return $this->supportedLocales;
    }
    
    /**
     * Vérifie si une locale est supportée
     * 
     * @param string $locale
     * @return bool
     */
    public function isLocaleSupported($locale) {
        return isset($this->supportedLocales[$locale]);
    }
    
    /**
     * Obtient le chemin vers le fichier de traduction
     * 
     * @param string $locale
     * @param string $format 'po' ou 'mo'
     * @return string
     */
    public function getTranslationFilePath($locale, $format = 'mo') {
        return $this->languagesPath . '/' . $this->textDomain . '-' . $locale . '.' . $format;
    }
    
    /**
     * Vérifie si les fichiers de traduction existent
     * 
     * @param string $locale
     * @return array
     */
    public function checkTranslationFiles($locale) {
        $poFile = $this->getTranslationFilePath($locale, 'po');
        $moFile = $this->getTranslationFilePath($locale, 'mo');
        
        return [
            'po_exists' => file_exists($poFile),
            'mo_exists' => file_exists($moFile),
            'po_path' => $poFile,
            'mo_path' => $moFile
        ];
    }
    
    /**
     * Obtient les statistiques de traduction
     * 
     * @param string $locale
     * @return array|null
     */
    public function getTranslationStats($locale) {
        $poFile = $this->getTranslationFilePath($locale, 'po');
        
        if (!file_exists($poFile)) {
            return null;
        }
        
        $content = file_get_contents($poFile);
        $total = substr_count($content, 'msgid "') - 1; // -1 pour l'en-tête
        $translated = 0;
        
        // Compter les traductions non vides
        preg_match_all('/msgstr "(.+)"/U', $content, $matches);
        $translated = count($matches[1]);
        
        return [
            'locale' => $locale,
            'language' => $this->supportedLocales[$locale] ?? $locale,
            'total' => $total,
            'translated' => $translated,
            'untranslated' => $total - $translated,
            'percentage' => $total > 0 ? round(($translated / $total) * 100, 2) : 0
        ];
    }
    
    /**
     * Traduit une chaîne
     * 
     * @param string $text Texte à traduire
     * @param string $context Contexte (optionnel)
     * @return string
     */
    public function translate($text, $context = null) {
        if ($context) {
            return _x($text, $context, $this->textDomain);
        }
        return __($text, $this->textDomain);
    }
    
    /**
     * Traduit et échappe une chaîne HTML
     * 
     * @param string $text Texte à traduire
     * @return string
     */
    public function translateEsc($text) {
        return esc_html__($text, $this->textDomain);
    }
    
    /**
     * Traduit et échappe un attribut
     * 
     * @param string $text Texte à traduire
     * @return string
     */
    public function translateAttr($text) {
        return esc_attr__($text, $this->textDomain);
    }
    
    /**
     * Traduit avec pluriel
     * 
     * @param string $single Forme singulière
     * @param string $plural Forme plurielle
     * @param int $number Nombre
     * @return string
     */
    public function translatePlural($single, $plural, $number) {
        return _n($single, $plural, $number, $this->textDomain);
    }
    
    /**
     * Log un message
     * 
     * @param string $message
     * @param string $level
     */
    private function log($message, $level = 'info') {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[SCF I18n] ' . strtoupper($level) . ': ' . $message);
        }
    }
    
    /**
     * Obtient des informations de débogage
     * 
     * @return array
     */
    public function getDebugInfo() {
        $info = [
            'current_locale' => $this->currentLocale,
            'current_language' => $this->getCurrentLanguageName(),
            'wp_locale' => get_locale(),
            'supported_locales' => $this->supportedLocales,
            'languages_path' => $this->languagesPath,
            'text_domain' => $this->textDomain,
            'files' => []
        ];
        
        foreach ($this->supportedLocales as $locale => $name) {
            $info['files'][$locale] = $this->checkTranslationFiles($locale);
            $info['stats'][$locale] = $this->getTranslationStats($locale);
        }
        
        return $info;
    }
}
