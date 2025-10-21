<?php
/**
 * Gestionnaire de Changelog
 * 
 * Gère l'ajout d'entrées et la publication de versions dans le changelog
 * 
 * @package SCF
 * @subpackage Utilities
 * @since 1.5.0
 */

namespace SCF\Utilities;

use Exception;

/**
 * Classe ChangelogManager
 */
class ChangelogManager {
    
    /**
     * Chemin vers le fichier CHANGELOG.md
     * 
     * @var string
     */
    private $changelogPath;
    
    /**
     * Chemin vers le fichier principal du plugin
     * 
     * @var string
     */
    private $pluginPath;
    
    /**
     * Mapping des types vers les sections du changelog
     * 
     * @var array
     */
    private $typeMapping = [
        'added' => [
            'section' => '✨ Ajouté',
            'emoji' => '✨',
            'description' => 'Nouvelles fonctionnalités'
        ],
        'changed' => [
            'section' => '🔧 Modifié',
            'emoji' => '🔧',
            'description' => 'Modifications de fonctionnalités existantes'
        ],
        'fixed' => [
            'section' => '🐛 Corrections',
            'emoji' => '🐛',
            'description' => 'Corrections de bugs'
        ],
        'security' => [
            'section' => '🔒 Sécurité',
            'emoji' => '🔒',
            'description' => 'Améliorations de sécurité'
        ],
        'performance' => [
            'section' => '⚡ Performance',
            'emoji' => '⚡',
            'description' => 'Améliorations de performance'
        ],
        'ui' => [
            'section' => '🎨 Interface',
            'emoji' => '🎨',
            'description' => 'Modifications de l\'interface utilisateur'
        ],
        'docs' => [
            'section' => '📝 Documentation',
            'emoji' => '📝',
            'description' => 'Modifications de la documentation'
        ],
        'migration' => [
            'section' => '🔄 Migration',
            'emoji' => '🔄',
            'description' => 'Informations de migration'
        ],
        'deprecated' => [
            'section' => '⚠️ Déprécié',
            'emoji' => '⚠️',
            'description' => 'Fonctionnalités dépréciées'
        ],
        'removed' => [
            'section' => '❌ Supprimé',
            'emoji' => '❌',
            'description' => 'Fonctionnalités supprimées'
        ]
    ];
    
    /**
     * Constructeur
     * 
     * @param string $changelogPath Chemin vers CHANGELOG.md
     * @param string $pluginPath Chemin vers le fichier principal du plugin
     */
    public function __construct($changelogPath, $pluginPath) {
        $this->changelogPath = $changelogPath;
        $this->pluginPath = $pluginPath;
        
        if (!file_exists($this->changelogPath)) {
            throw new Exception("Le fichier CHANGELOG.md n'existe pas: " . $this->changelogPath);
        }
        
        if (!file_exists($this->pluginPath)) {
            throw new Exception("Le fichier du plugin n'existe pas: " . $this->pluginPath);
        }
    }
    
    /**
     * Ajoute une entrée au changelog
     * 
     * @param string $type Type d'entrée (added, changed, fixed, etc.)
     * @param string $message Message de l'entrée
     * @throws Exception Si le type est invalide
     */
    public function addEntry($type, $message) {
        if (!isset($this->typeMapping[$type])) {
            throw new Exception("Type invalide: " . $type . ". Types disponibles: " . implode(', ', array_keys($this->typeMapping)));
        }
        
        $content = file_get_contents($this->changelogPath);
        $lines = explode("\n", $content);
        
        // Trouver la section "En développement"
        $devSectionIndex = $this->findDevelopmentSection($lines);
        
        if ($devSectionIndex === false) {
            throw new Exception("Section de développement non trouvée dans le changelog");
        }
        
        // Trouver ou créer la section du type
        $typeSection = $this->typeMapping[$type]['section'];
        $typeSectionIndex = $this->findTypeSection($lines, $typeSection, $devSectionIndex);
        
        if ($typeSectionIndex === false) {
            // Créer la section si elle n'existe pas
            $typeSectionIndex = $this->createTypeSection($lines, $typeSection, $devSectionIndex);
        }
        
        // Ajouter l'entrée
        $entry = "- " . $message;
        array_splice($lines, $typeSectionIndex + 1, 0, [$entry]);
        
        // Sauvegarder
        file_put_contents($this->changelogPath, implode("\n", $lines));
    }
    
    /**
     * Publie une nouvelle version
     * 
     * @param string $version Numéro de version (ex: 1.5.0)
     * @param string $date Date de publication (format: YYYY-MM-DD)
     */
    public function releaseVersion($version, $date) {
        $content = file_get_contents($this->changelogPath);
        
        // Remplacer "En développement" par la version et la date
        $pattern = '/## \[[\d.]+\] - \d{4}-\d{2}-\d{2} \(En développement\)/';
        $replacement = "## [$version] - $date";
        
        $content = preg_replace($pattern, $replacement, $content);
        
        // Ajouter une nouvelle section "En développement" en haut
        $newDevSection = "\n## [" . $this->getNextVersion($version) . "] - " . date('Y-m-d') . " (En développement)\n\n### ✨ Ajouté\n\n### 🔧 Modifié\n\n### 🐛 Corrections\n\n---\n";
        
        // Insérer après l'en-tête du changelog
        $headerEnd = strpos($content, "## [");
        if ($headerEnd !== false) {
            $content = substr_replace($content, $newDevSection, $headerEnd, 0);
        }
        
        file_put_contents($this->changelogPath, $content);
    }
    
    /**
     * Met à jour la version dans le fichier principal du plugin
     * 
     * @param string $version Numéro de version
     */
    public function updatePluginVersion($version) {
        $content = file_get_contents($this->pluginPath);
        
        // Mettre à jour la version dans l'en-tête du plugin
        $pattern = '/(Version:\s*)[\d.]+/';
        $replacement = '${1}' . $version;
        
        $content = preg_replace($pattern, $replacement, $content);
        
        file_put_contents($this->pluginPath, $content);
    }
    
    /**
     * Trouve la section de développement dans le changelog
     * 
     * @param array $lines Lignes du changelog
     * @return int|false Index de la ligne ou false si non trouvée
     */
    private function findDevelopmentSection($lines) {
        foreach ($lines as $index => $line) {
            if (strpos($line, '(En développement)') !== false) {
                return $index;
            }
        }
        return false;
    }
    
    /**
     * Trouve une section de type dans le changelog
     * 
     * @param array $lines Lignes du changelog
     * @param string $typeSection Nom de la section (ex: "✨ Ajouté")
     * @param int $startIndex Index de début de recherche
     * @return int|false Index de la ligne ou false si non trouvée
     */
    private function findTypeSection($lines, $typeSection, $startIndex) {
        $nextVersionIndex = $this->findNextVersion($lines, $startIndex);
        
        for ($i = $startIndex; $i < $nextVersionIndex; $i++) {
            if (strpos($lines[$i], '### ' . $typeSection) !== false) {
                return $i;
            }
        }
        
        return false;
    }
    
    /**
     * Crée une nouvelle section de type dans le changelog
     * 
     * @param array &$lines Lignes du changelog (passé par référence)
     * @param string $typeSection Nom de la section
     * @param int $startIndex Index de début
     * @return int Index de la nouvelle section
     */
    private function createTypeSection(&$lines, $typeSection, $startIndex) {
        $nextVersionIndex = $this->findNextVersion($lines, $startIndex);
        
        // Insérer avant la prochaine version
        $insertIndex = $nextVersionIndex - 1;
        
        array_splice($lines, $insertIndex, 0, [
            "",
            "### " . $typeSection,
            ""
        ]);
        
        return $insertIndex + 1;
    }
    
    /**
     * Trouve l'index de la prochaine version dans le changelog
     * 
     * @param array $lines Lignes du changelog
     * @param int $startIndex Index de début de recherche
     * @return int Index de la prochaine version
     */
    private function findNextVersion($lines, $startIndex) {
        for ($i = $startIndex + 1; $i < count($lines); $i++) {
            if (preg_match('/^## \[[\d.]+\]/', $lines[$i])) {
                return $i;
            }
            if (strpos($lines[$i], '---') !== false) {
                return $i;
            }
        }
        return count($lines);
    }
    
    /**
     * Calcule la prochaine version de développement
     * 
     * @param string $currentVersion Version actuelle (ex: 1.5.0)
     * @return string Prochaine version (ex: 1.5.1)
     */
    private function getNextVersion($currentVersion) {
        $parts = explode('.', $currentVersion);
        $parts[2] = (int)$parts[2] + 1;
        return implode('.', $parts);
    }
    
    /**
     * Obtient la version actuelle depuis le changelog
     * 
     * @return string|null Version actuelle ou null si non trouvée
     */
    public function getCurrentVersion() {
        $content = file_get_contents($this->changelogPath);
        
        if (preg_match('/## \[([\d.]+)\] - \d{4}-\d{2}-\d{2}(?! \(En développement\))/', $content, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Obtient la liste des types disponibles
     * 
     * @return array Liste des types avec leurs descriptions
     */
    public function getAvailableTypes() {
        return $this->typeMapping;
    }
}
