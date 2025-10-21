#!/usr/bin/env php
<?php
/**
 * Script CLI pour gérer les traductions
 * 
 * Usage:
 *   php bin/i18n.php generate-pot
 *   php bin/i18n.php generate-mo
 *   php bin/i18n.php generate-mo fr_FR
 *   php bin/i18n.php scan
 * 
 * @package SCF
 * @subpackage i18n
 */

// Couleurs pour le terminal
class Colors {
    const RESET = "\033[0m";
    const RED = "\033[31m";
    const GREEN = "\033[32m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const CYAN = "\033[36m";
    const BOLD = "\033[1m";
}

function printSuccess($message) {
    echo Colors::GREEN . "✓ " . $message . Colors::RESET . "\n";
}

function printError($message) {
    echo Colors::RED . "✗ " . $message . Colors::RESET . "\n";
}

function printInfo($message) {
    echo Colors::BLUE . "ℹ " . $message . Colors::RESET . "\n";
}

function printWarning($message) {
    echo Colors::YELLOW . "⚠ " . $message . Colors::RESET . "\n";
}

function printHelp() {
    echo Colors::BOLD . Colors::CYAN . "🌍 Gestionnaire de traductions SCF\n" . Colors::RESET;
    echo "\n";
    echo Colors::BOLD . "Usage:\n" . Colors::RESET;
    echo "  php bin/i18n.php <commande> [options]\n";
    echo "\n";
    echo Colors::BOLD . "Commandes:\n" . Colors::RESET;
    echo Colors::GREEN . "  generate-pot\n" . Colors::RESET;
    echo "    Génère le fichier POT (template) depuis le code source\n";
    echo "\n";
    echo Colors::GREEN . "  generate-mo [locale]\n" . Colors::RESET;
    echo "    Génère les fichiers MO depuis les fichiers PO\n";
    echo "    locale: fr_FR, en_US (optionnel, génère tous si omis)\n";
    echo "\n";
    echo Colors::GREEN . "  scan\n" . Colors::RESET;
    echo "    Scanne le code pour trouver les chaînes traduisibles\n";
    echo "\n";
    echo Colors::GREEN . "  stats [locale]\n" . Colors::RESET;
    echo "    Affiche les statistiques de traduction\n";
    echo "\n";
    echo Colors::BOLD . "Exemples:\n" . Colors::RESET;
    echo "  php bin/i18n.php generate-pot\n";
    echo "  php bin/i18n.php generate-mo\n";
    echo "  php bin/i18n.php generate-mo fr_FR\n";
    echo "  php bin/i18n.php scan\n";
    echo "  php bin/i18n.php stats fr_FR\n";
    echo "\n";
}

/**
 * Génère un fichier MO depuis un fichier PO
 */
function generateMO($poFile, $moFile) {
    if (!file_exists($poFile)) {
        printError("Fichier PO introuvable: " . $poFile);
        return false;
    }

    // Lire le fichier PO
    $poContent = file_get_contents($poFile);
    $entries = parsePO($poContent);

    // Créer le fichier MO
    $mo = createMO($entries);
    
    if (file_put_contents($moFile, $mo) !== false) {
        printSuccess("Fichier MO généré: " . basename($moFile));
        return true;
    }

    printError("Échec de la génération du fichier MO");
    return false;
}

/**
 * Parse un fichier PO
 */
function parsePO($content) {
    $entries = [];
    $lines = explode("\n", $content);
    $currentEntry = null;

    foreach ($lines as $line) {
        $line = trim($line);

        if (empty($line) || $line[0] === '#') {
            continue;
        }

        if (strpos($line, 'msgid ') === 0) {
            if ($currentEntry !== null && !empty($currentEntry['msgid'])) {
                $entries[] = $currentEntry;
            }
            $currentEntry = [
                'msgid' => parseString($line),
                'msgstr' => ''
            ];
        } elseif (strpos($line, 'msgstr ') === 0 && $currentEntry !== null) {
            $currentEntry['msgstr'] = parseString($line);
        } elseif ($line[0] === '"' && $currentEntry !== null) {
            // Continuation de la ligne précédente
            $str = parseString($line);
            if (isset($currentEntry['msgstr']) && $currentEntry['msgstr'] !== '') {
                $currentEntry['msgstr'] .= $str;
            } else {
                $currentEntry['msgid'] .= $str;
            }
        }
    }

    if ($currentEntry !== null && !empty($currentEntry['msgid'])) {
        $entries[] = $currentEntry;
    }

    return $entries;
}

/**
 * Parse une chaîne entre guillemets
 */
function parseString($line) {
    if (preg_match('/"(.*)"/s', $line, $matches)) {
        return stripcslashes($matches[1]);
    }
    return '';
}

/**
 * Crée un fichier MO binaire
 */
function createMO($entries) {
    $originals = '';
    $translations = '';
    $origOffsets = [];
    $transOffsets = [];

    foreach ($entries as $entry) {
        if (empty($entry['msgid'])) {
            continue;
        }

        $origOffsets[] = [strlen($originals), strlen($entry['msgid'])];
        $originals .= $entry['msgid'] . "\0";

        $transOffsets[] = [strlen($translations), strlen($entry['msgstr'])];
        $translations .= $entry['msgstr'] . "\0";
    }

    $count = count($origOffsets);
    $origOffset = 28 + ($count * 16);
    $transOffset = $origOffset + strlen($originals);

    // Magic number
    $mo = pack('L', 0x950412de);
    // Revision
    $mo .= pack('L', 0);
    // Number of strings
    $mo .= pack('L', $count);
    // Offset of original strings
    $mo .= pack('L', 28);
    // Offset of translated strings
    $mo .= pack('L', 28 + ($count * 8));
    // Hash table size
    $mo .= pack('L', 0);
    // Hash table offset
    $mo .= pack('L', 0);

    // Original strings offsets
    foreach ($origOffsets as $offset) {
        $mo .= pack('L', $offset[1]);
        $mo .= pack('L', $origOffset + $offset[0]);
    }

    // Translated strings offsets
    foreach ($transOffsets as $offset) {
        $mo .= pack('L', $offset[1]);
        $mo .= pack('L', $transOffset + $offset[0]);
    }

    // Original strings
    $mo .= $originals;
    // Translated strings
    $mo .= $translations;

    return $mo;
}

/**
 * Scanne le code pour les chaînes traduisibles
 */
function scanTranslatableStrings($dir) {
    $strings = [];
    $functions = ['__', '_e', '_x', '_n', 'esc_html__', 'esc_html_e', 'esc_attr__', 'esc_attr_e'];
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            
            foreach ($functions as $func) {
                $pattern = '/' . preg_quote($func) . '\s*\(\s*[\'"]([^\'"]+)[\'"]/';
                if (preg_match_all($pattern, $content, $matches)) {
                    foreach ($matches[1] as $string) {
                        if (!isset($strings[$string])) {
                            $strings[$string] = [];
                        }
                        $strings[$string][] = str_replace($dir . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    }
                }
            }
        }
    }

    return $strings;
}

/**
 * Calcule les statistiques de traduction
 */
function getTranslationStats($poFile) {
    if (!file_exists($poFile)) {
        return null;
    }

    $content = file_get_contents($poFile);
    $entries = parsePO($content);

    $total = 0;
    $translated = 0;

    foreach ($entries as $entry) {
        if (!empty($entry['msgid'])) {
            $total++;
            if (!empty($entry['msgstr'])) {
                $translated++;
            }
        }
    }

    return [
        'total' => $total,
        'translated' => $translated,
        'untranslated' => $total - $translated,
        'percentage' => $total > 0 ? round(($translated / $total) * 100, 2) : 0
    ];
}

// Main
if ($argc < 2) {
    printHelp();
    exit(1);
}

$command = $argv[1];
$pluginDir = dirname(__DIR__);
$languagesDir = $pluginDir . '/languages';

try {
    switch ($command) {
        case 'generate-pot':
            printInfo("Génération du fichier POT...");
            printWarning("Cette fonctionnalité nécessite wp-cli ou xgettext");
            printInfo("Utilisez: wp i18n make-pot " . $pluginDir . " " . $languagesDir . "/simple-custom-fields.pot");
            break;

        case 'generate-mo':
            $locale = isset($argv[2]) ? $argv[2] : null;
            
            if ($locale) {
                $poFile = $languagesDir . "/simple-custom-fields-{$locale}.po";
                $moFile = $languagesDir . "/simple-custom-fields-{$locale}.mo";
                
                printInfo("Génération du fichier MO pour {$locale}...");
                generateMO($poFile, $moFile);
            } else {
                printInfo("Génération de tous les fichiers MO...");
                
                $locales = ['fr_FR', 'en_US'];
                foreach ($locales as $loc) {
                    $poFile = $languagesDir . "/simple-custom-fields-{$loc}.po";
                    $moFile = $languagesDir . "/simple-custom-fields-{$loc}.mo";
                    
                    if (file_exists($poFile)) {
                        generateMO($poFile, $moFile);
                    }
                }
            }
            
            printSuccess("Génération terminée!");
            break;

        case 'scan':
            printInfo("Scan des chaînes traduisibles...");
            
            $strings = scanTranslatableStrings($pluginDir . '/includes');
            $strings = array_merge($strings, scanTranslatableStrings($pluginDir . '/templates'));
            
            printSuccess("Trouvé " . count($strings) . " chaînes traduisibles");
            
            echo "\n";
            foreach ($strings as $string => $files) {
                echo Colors::CYAN . $string . Colors::RESET . "\n";
                foreach (array_unique($files) as $file) {
                    echo "  → " . $file . "\n";
                }
            }
            break;

        case 'stats':
            $locale = isset($argv[2]) ? $argv[2] : 'fr_FR';
            $poFile = $languagesDir . "/simple-custom-fields-{$locale}.po";
            
            printInfo("Statistiques pour {$locale}:");
            
            $stats = getTranslationStats($poFile);
            
            if ($stats) {
                echo "\n";
                echo "  Total: " . $stats['total'] . " chaînes\n";
                echo "  Traduites: " . Colors::GREEN . $stats['translated'] . Colors::RESET . " (" . $stats['percentage'] . "%)\n";
                echo "  Non traduites: " . Colors::RED . $stats['untranslated'] . Colors::RESET . "\n";
                echo "\n";
                
                // Barre de progression
                $barLength = 50;
                $filled = round(($stats['percentage'] / 100) * $barLength);
                $empty = $barLength - $filled;
                
                echo "  [" . Colors::GREEN . str_repeat("█", $filled) . Colors::RESET;
                echo Colors::RED . str_repeat("░", $empty) . Colors::RESET . "] ";
                echo $stats['percentage'] . "%\n";
            } else {
                printError("Fichier PO introuvable");
            }
            break;

        case 'help':
        case '--help':
        case '-h':
            printHelp();
            break;

        default:
            printError("Commande inconnue: " . $command);
            printHelp();
            exit(1);
    }
} catch (Exception $e) {
    printError("Erreur: " . $e->getMessage());
    exit(1);
}
