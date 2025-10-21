#!/usr/bin/env php
<?php
/**
 * Script CLI pour gérer le changelog
 * 
 * Usage:
 *   php bin/changelog.php add <type> <message>
 *   php bin/changelog.php release <version>
 *   php bin/changelog.php show
 * 
 * Types disponibles:
 *   added, changed, fixed, security, performance, ui, docs, migration, deprecated, removed
 */

require_once __DIR__ . '/../vendor/autoload.php';

use SCF\Utilities\ChangelogManager;

// Couleurs pour le terminal
class Colors {
    const RESET = "\033[0m";
    const RED = "\033[31m";
    const GREEN = "\033[32m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const MAGENTA = "\033[35m";
    const CYAN = "\033[36m";
    const WHITE = "\033[37m";
    const BOLD = "\033[1m";
}

function printHelp() {
    echo Colors::BOLD . Colors::CYAN . "📝 Gestionnaire de Changelog SCF\n" . Colors::RESET;
    echo "\n";
    echo Colors::BOLD . "Usage:\n" . Colors::RESET;
    echo "  php bin/changelog.php <commande> [options]\n";
    echo "\n";
    echo Colors::BOLD . "Commandes:\n" . Colors::RESET;
    echo Colors::GREEN . "  add <type> <message>\n" . Colors::RESET;
    echo "    Ajoute une nouvelle entrée au changelog\n";
    echo "    Types: added, changed, fixed, security, performance, ui, docs, migration, deprecated, removed\n";
    echo "\n";
    echo Colors::GREEN . "  release <version> [date]\n" . Colors::RESET;
    echo "    Publie une nouvelle version (met à jour le changelog et la version du plugin)\n";
    echo "    Version: format semver (ex: 1.5.0)\n";
    echo "    Date: format YYYY-MM-DD (optionnel, par défaut aujourd'hui)\n";
    echo "\n";
    echo Colors::GREEN . "  show\n" . Colors::RESET;
    echo "    Affiche le changelog actuel\n";
    echo "\n";
    echo Colors::BOLD . "Exemples:\n" . Colors::RESET;
    echo "  php bin/changelog.php add fixed \"Correction du bug de validation des emails\"\n";
    echo "  php bin/changelog.php add added \"Ajout du support des champs répétables\"\n";
    echo "  php bin/changelog.php release 1.5.0\n";
    echo "  php bin/changelog.php release 1.5.0 2024-01-15\n";
    echo "\n";
}

function printSuccess($message) {
    echo Colors::GREEN . "✓ " . $message . Colors::RESET . "\n";
}

function printError($message) {
    echo Colors::RED . "✗ " . $message . Colors::RESET . "\n";
}

function printWarning($message) {
    echo Colors::YELLOW . "⚠ " . $message . Colors::RESET . "\n";
}

function printInfo($message) {
    echo Colors::BLUE . "ℹ " . $message . Colors::RESET . "\n";
}

// Vérifier les arguments
if ($argc < 2) {
    printHelp();
    exit(1);
}

$command = $argv[1];
$changelogPath = __DIR__ . '/../CHANGELOG.md';
$pluginPath = __DIR__ . '/../simple-custom-fields.php';

$manager = new ChangelogManager($changelogPath, $pluginPath);

try {
    switch ($command) {
        case 'add':
            if ($argc < 4) {
                printError("Arguments manquants pour la commande 'add'");
                echo "Usage: php bin/changelog.php add <type> <message>\n";
                exit(1);
            }
            
            $type = $argv[2];
            $message = $argv[3];
            
            $manager->addEntry($type, $message);
            printSuccess("Entrée ajoutée au changelog");
            printInfo("Type: " . $type);
            printInfo("Message: " . $message);
            break;
            
        case 'release':
            if ($argc < 3) {
                printError("Version manquante pour la commande 'release'");
                echo "Usage: php bin/changelog.php release <version> [date]\n";
                exit(1);
            }
            
            $version = $argv[2];
            $date = $argc >= 4 ? $argv[3] : date('Y-m-d');
            
            // Validation du format de version
            if (!preg_match('/^\d+\.\d+\.\d+$/', $version)) {
                printError("Format de version invalide. Utilisez le format semver (ex: 1.5.0)");
                exit(1);
            }
            
            // Validation du format de date
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                printError("Format de date invalide. Utilisez le format YYYY-MM-DD");
                exit(1);
            }
            
            printInfo("Préparation de la release v" . $version);
            
            // Mettre à jour le changelog
            $manager->releaseVersion($version, $date);
            printSuccess("Changelog mis à jour pour la version " . $version);
            
            // Mettre à jour la version du plugin
            $manager->updatePluginVersion($version);
            printSuccess("Version du plugin mise à jour: " . $version);
            
            echo "\n";
            printSuccess("Release v" . $version . " créée avec succès!");
            echo "\n";
            printWarning("N'oubliez pas de:");
            echo "  1. Vérifier les modifications dans CHANGELOG.md\n";
            echo "  2. Vérifier la version dans simple-custom-fields.php\n";
            echo "  3. Commiter les changements: git add . && git commit -m \"Release v" . $version . "\"\n";
            echo "  4. Créer un tag: git tag v" . $version . "\n";
            echo "  5. Pousser: git push && git push --tags\n";
            break;
            
        case 'show':
            $content = file_get_contents($changelogPath);
            echo $content;
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
