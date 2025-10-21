# 🔧 Simple Custom Fields

[![Version](https://img.shields.io/badge/version-1.5.0-blue.svg)](https://github.com)
[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-green.svg)](LICENSE)

> 🌍 **[English version](README.md)** | **Version française**

Un plugin WordPress moderne et sécurisé pour créer et gérer des champs personnalisés avec une architecture PSR-4.

## ⚠️ Avertissement

Ce plugin est en développement actif. La version 1.5.0 apporte une refactorisation majeure avec de nouvelles fonctionnalités.

## 📝 Description

Simple Custom Fields est un plugin WordPress qui vous permet de créer et gérer des champs personnalisés pour différents types de contenu 📄.

**Interface moderne et professionnelle** avec :
- 🎨 Palette de couleurs moderne
- 📦 Layout en cards
- ✨ Animations fluides
- 📱 Design responsive
- 🎯 Focus states intuitifs

Le plugin utilise une table de base de données dédiée (`wp_scf_fields`) pour stocker toutes les valeurs des champs personnalisés, offrant de meilleures performances et une meilleure évolutivité par rapport aux postmeta WordPress. Cette table est automatiquement créée lors de l'activation du plugin et inclut :
- Index optimisés pour des requêtes rapides
- Suivi des timestamps pour tous les changements
- Stockage sérialisé des valeurs de champs
- Suivi des relations entre posts, groupes de champs et champs

## ✨ Fonctionnalités

- 📁 Créer des groupes de champs personnalisés
- 📝 Types de champs supportés :
  - ✅ Texte
  - ✅ Zone de texte
  - ✅ Nombre
  - ✅ Email
  - ✅ Liste déroulante
  - ✅ Boutons radio
  - ✅ Cases à cocher
  - ✅ Date
  - ✅ URL
  - ✅ Téléchargement de fichier
  - 🔜 Téléchargement d'image
  - 🔜 Éditeur WYSIWYG
  - 🔜 Onglets
  - 🔜 Champs répétables
  - 🔜 Bouton Vrai/Faux
  - 🔜 Champ lien
  - 🔜 Relation d'objet post
  - 🔜 Sélecteur de taxonomie
- 🔍 Configurer les règles d'affichage par type de contenu
- 🔄 Activer/désactiver les groupes de champs
- 🗃️ Stocker les champs personnalisés dans une table de base de données dédiée pour de meilleures performances
- 🌐 Afficher les champs personnalisés sur le front-end avec une fonction dédiée

## 🚀 Installation

1. 📥 Télécharger le dossier du plugin
2. 📦 Décompresser le dossier dans le répertoire `wp-content/plugins` de votre installation WordPress
3. ✅ Activer le plugin via le menu "Extensions" dans WordPress

## 📚 Utilisation

1. 🔗 Accéder au menu "Simple Custom Fields" dans l'interface d'administration WordPress
2. 🛠️ Créer un nouveau groupe de champs ou modifier un groupe existant
3. ➕ Ajouter des champs personnalisés selon vos besoins
4. 🔧 Configurer les règles d'affichage pour le groupe de champs
5. 💻 Afficher les champs sur le front-end en utilisant :

### Utilisation de base

```php
<?php 
$value = scf_get_field('nom_du_champ'); 
if ($value) {
    echo $value; 
}
?>
```

### Avec ID de post

```php 
<?php
$value = scf_get_field('nom_du_champ', 123); // 123 = ID du post
echo $value ?: 'Aucune valeur';
?>
```

### Dans les fichiers de template

```php
<div class="custom-field">
    <h3><?php echo esc_html__('Libellé du champ', 'text-domain'); ?></h3>
    <p><?php echo esc_html(scf_get_field('nom_du_champ')); ?></p>
</div>
```

### Notes de sécurité

- Toujours échapper la sortie avec `esc_html()` pour les champs texte
- Utiliser `wp_kses_post()` pour le contenu HTML
- Pour les emails, utiliser la fonction `antispambot()`

## 🆕 Nouveautés Version 1.5.0

### Architecture & Code

- ✅ **Architecture PSR-4** avec autoloader Composer
- ✅ **Namespaces modernes** (`SCF\Core`, `SCF\Services`, etc.)
- ✅ **Système de logging** centralisé avec 8 niveaux
- ✅ **Gestion d'erreurs** structurée avec notifications admin
- ✅ **Configuration centralisée** dans `SCF\Core\Config`

### Sécurité

- ✅ **Validation stricte** par type de champ
- ✅ **Correction du bug de nonce** lors de la suppression
- ✅ **Nonces spécifiques** par action AJAX
- ✅ **Rate limiting** renforcé
- ✅ **Headers de sécurité** HTTP

### Développement

- ✅ **Tests unitaires** avec PHPUnit et Brain Monkey
- ✅ **PHPStan** (niveau 5) pour analyse statique
- ✅ **PHPCS** avec standards WordPress
- ✅ **Documentation complète** (Architecture, API, Sécurité)

### Services

- ✅ `FieldGroupService` : Logique métier isolée
- ✅ `FieldValidator` : Validation par type
- ✅ `Logger` : Logs structurés avec rotation
- ✅ `ErrorHandler` : Gestion centralisée des erreurs

## 📚 Documentation

- 📖 [Architecture](docs/ARCHITECTURE.md) - Architecture technique du plugin
- 🔌 [API](docs/API.md) - Documentation de l'API publique
- 🔒 [Sécurité](docs/SECURITY.md) - Guide de sécurité
- 🤝 [Contribution](docs/CONTRIBUTING.md) - Guide de contribution
- 📝 [Changelog](CHANGELOG.md) - Historique complet des versions
- 🌍 [Internationalisation](docs/I18N_GUIDE.md) - Guide de traduction

## 🛠️ Développement

### Installation

```bash
# Cloner le repository
git clone https://github.com/infinityweb/simple-custom-fields.git
cd simple-custom-fields

# Installer les dépendances
composer install
```

### Tests

```bash
# Tous les tests
composer test

# Tests unitaires
composer test-unit

# Tests d'intégration
composer test-integration
```

### Qualité de code

```bash
# Vérifier le code
composer phpcs

# Analyse statique
composer phpstan

# Tout vérifier
composer lint
```

### Gestion du Changelog

```bash
# Ajouter une entrée au changelog
composer changelog:add <type> <message>

# Exemples
composer changelog:add added "Nouvelle fonctionnalité"
composer changelog:add fixed "Correction d'un bug"
composer changelog:add security "Amélioration de sécurité"

# Publier une nouvelle version
composer changelog:release <version>

# Exemple
composer changelog:release 1.5.0

# Afficher le changelog
composer changelog:show

# Aide
composer changelog:help
```

**Types disponibles :** `added`, `changed`, `fixed`, `security`, `performance`, `ui`, `docs`, `migration`, `deprecated`, `removed`

📖 **Documentation complète :** [Guide de gestion du changelog](docs/CHANGELOG_GUIDE.md)  
💡 **Exemples pratiques :** [Exemples d'utilisation](CHANGELOG_EXAMPLES.md)

### Gestion des traductions

```bash
# Générer les fichiers MO
composer i18n:generate-mo

# Scanner les chaînes traduisibles
composer i18n:scan

# Statistiques de traduction
composer i18n:stats fr_FR
```

**Langues supportées :** Français (fr_FR), Anglais (en_US)

📖 **Guide de traduction :** [Guide d'internationalisation](docs/I18N_GUIDE.md)

## 📜 Changelog

Voir [CHANGELOG.md](CHANGELOG.md) pour l'historique complet des versions.

## 🌍 Internationalisation

Le plugin détecte automatiquement la langue de WordPress :
- 🇫🇷 Français si WordPress est en français
- 🇬🇧 Anglais pour toutes les autres langues

**Aucune configuration nécessaire !**

150+ chaînes entièrement traduites en français et en anglais.

📖 **Guide de traduction :** [Guide I18N](docs/I18N_GUIDE.md)

## 👨‍💻 Auteur

**Akrem Belkahla**
- Email : akrem.belkahla@infinityweb.tn
- Site : [Infinity Web](https://infinityweb.tn)
- GitHub : [@AkremBelkahla](https://github.com/AkremBelkahla)

## 📄 Licence

GPL-2.0-or-later - Voir le fichier LICENSE pour plus de détails.
