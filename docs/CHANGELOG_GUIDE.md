# Guide de Gestion du Changelog

Ce guide explique comment utiliser le système de gestion de changelog pour Simple Custom Fields.

## 📋 Table des matières

- [Introduction](#introduction)
- [Installation](#installation)
- [Utilisation](#utilisation)
- [Types d'entrées](#types-dentrées)
- [Workflow de développement](#workflow-de-développement)
- [Exemples](#exemples)
- [Bonnes pratiques](#bonnes-pratiques)

## Introduction

Le système de changelog automatise la gestion des modifications du plugin et la mise à jour des versions. Il suit le format [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/) et le [Versioning Sémantique](https://semver.org/lang/fr/).

### Fonctionnalités

- ✅ Ajout automatique d'entrées au changelog
- ✅ Publication de nouvelles versions
- ✅ Mise à jour automatique de la version du plugin
- ✅ Support de tous les types d'entrées (ajouté, corrigé, sécurité, etc.)
- ✅ Validation des formats de version et de date
- ✅ Interface CLI colorée et intuitive

## Installation

Le système est déjà installé avec le plugin. Assurez-vous d'avoir Composer installé :

```bash
composer install
```

## Utilisation

### Commandes disponibles

#### 1. Ajouter une entrée au changelog

```bash
# Via le script PHP
php bin/changelog.php add <type> <message>

# Via Composer
composer changelog:add <type> <message>
```

**Paramètres :**
- `<type>` : Type d'entrée (voir section [Types d'entrées](#types-dentrées))
- `<message>` : Description de la modification

**Exemple :**
```bash
composer changelog:add fixed "Correction du bug de validation des emails"
```

#### 2. Publier une nouvelle version

```bash
# Via le script PHP
php bin/changelog.php release <version> [date]

# Via Composer
composer changelog:release <version> [date]
```

**Paramètres :**
- `<version>` : Numéro de version au format semver (ex: 1.5.0)
- `[date]` : Date de publication au format YYYY-MM-DD (optionnel, par défaut aujourd'hui)

**Exemple :**
```bash
composer changelog:release 1.5.0
composer changelog:release 1.5.0 2024-01-15
```

**Cette commande :**
1. Met à jour le changelog avec la version et la date
2. Crée une nouvelle section "En développement" pour les futures modifications
3. Met à jour la version dans `simple-custom-fields.php`

#### 3. Afficher le changelog

```bash
# Via le script PHP
php bin/changelog.php show

# Via Composer
composer changelog:show
```

#### 4. Afficher l'aide

```bash
# Via le script PHP
php bin/changelog.php help

# Via Composer
composer changelog:help
```

## Types d'entrées

Le système supporte 10 types d'entrées différents :

| Type | Section | Emoji | Description |
|------|---------|-------|-------------|
| `added` | ✨ Ajouté | ✨ | Nouvelles fonctionnalités |
| `changed` | 🔧 Modifié | 🔧 | Modifications de fonctionnalités existantes |
| `fixed` | 🐛 Corrections | 🐛 | Corrections de bugs |
| `security` | 🔒 Sécurité | 🔒 | Améliorations de sécurité |
| `performance` | ⚡ Performance | ⚡ | Améliorations de performance |
| `ui` | 🎨 Interface | 🎨 | Modifications de l'interface utilisateur |
| `docs` | 📝 Documentation | 📝 | Modifications de la documentation |
| `migration` | 🔄 Migration | 🔄 | Informations de migration |
| `deprecated` | ⚠️ Déprécié | ⚠️ | Fonctionnalités dépréciées |
| `removed` | ❌ Supprimé | ❌ | Fonctionnalités supprimées |

## Workflow de développement

### 1. Pendant le développement

À chaque modification importante, ajoutez une entrée au changelog :

```bash
# Nouvelle fonctionnalité
composer changelog:add added "Ajout du support des champs répétables"

# Correction de bug
composer changelog:add fixed "Correction du problème de sauvegarde des métadonnées"

# Amélioration de sécurité
composer changelog:add security "Renforcement de la validation des nonces"

# Amélioration de performance
composer changelog:add performance "Optimisation des requêtes de base de données"
```

### 2. Préparation d'une release

Lorsque vous êtes prêt à publier une nouvelle version :

```bash
# Publier la version
composer changelog:release 1.5.0

# Vérifier les modifications
git diff CHANGELOG.md simple-custom-fields.php
```

### 3. Publication

Après avoir vérifié les modifications :

```bash
# Commiter les changements
git add CHANGELOG.md simple-custom-fields.php
git commit -m "Release v1.5.0"

# Créer un tag
git tag v1.5.0

# Pousser vers le dépôt
git push origin main
git push origin v1.5.0
```

## Exemples

### Exemple 1 : Ajout d'une nouvelle fonctionnalité

```bash
composer changelog:add added "Support des champs de type 'Galerie d'images'"
```

Résultat dans `CHANGELOG.md` :
```markdown
### ✨ Ajouté
- Support des champs de type 'Galerie d'images'
```

### Exemple 2 : Correction de plusieurs bugs

```bash
composer changelog:add fixed "Correction du bug de validation des emails"
composer changelog:add fixed "Correction du problème d'affichage des checkboxes"
composer changelog:add fixed "Correction de la sauvegarde des champs répétables"
```

### Exemple 3 : Release complète

```bash
# Ajouter des modifications pendant le développement
composer changelog:add added "Nouveau type de champ : Sélecteur de couleur"
composer changelog:add changed "Amélioration de l'interface d'édition des groupes"
composer changelog:add fixed "Correction du bug de duplication des groupes"
composer changelog:add security "Ajout de la validation CSRF sur toutes les actions AJAX"

# Publier la version
composer changelog:release 1.5.0

# Vérifier et commiter
git diff
git add .
git commit -m "Release v1.5.0"
git tag v1.5.0
git push && git push --tags
```

## Bonnes pratiques

### 1. Messages clairs et concis

✅ **Bon :**
```bash
composer changelog:add fixed "Correction du bug de validation des emails avec caractères spéciaux"
```

❌ **Mauvais :**
```bash
composer changelog:add fixed "fix bug"
```

### 2. Catégorisation appropriée

Choisissez le bon type pour chaque modification :

- Utilisez `added` pour les nouvelles fonctionnalités
- Utilisez `changed` pour les modifications de fonctionnalités existantes
- Utilisez `fixed` pour les corrections de bugs
- Utilisez `security` pour les problèmes de sécurité

### 3. Fréquence des entrées

- Ajoutez une entrée pour chaque modification significative
- Regroupez les petites modifications similaires
- N'ajoutez pas d'entrées pour les modifications triviales (typos, formatage)

### 4. Versioning sémantique

Suivez les règles du versioning sémantique :

- **MAJOR** (X.0.0) : Changements incompatibles avec les versions précédentes
- **MINOR** (1.X.0) : Ajout de fonctionnalités rétrocompatibles
- **PATCH** (1.5.X) : Corrections de bugs rétrocompatibles

Exemples :
```bash
# Nouvelle fonctionnalité majeure
composer changelog:release 2.0.0

# Nouvelle fonctionnalité mineure
composer changelog:release 1.5.0

# Correction de bug
composer changelog:release 1.4.2
```

### 5. Documentation des breaking changes

Pour les changements incompatibles, ajoutez des détails dans la section Migration :

```bash
composer changelog:add migration "Les anciennes fonctions scf_old_*() sont dépréciées. Utilisez scf_new_*() à la place."
composer changelog:add deprecated "Fonction scf_old_get_field() dépréciée, utilisez scf_get_field()"
```

## Structure du fichier CHANGELOG.md

Le changelog suit cette structure :

```markdown
# Changelog

## [1.5.1] - 2024-01-XX (En développement)

### ✨ Ajouté
- Nouvelles fonctionnalités en cours de développement

### 🐛 Corrections
- Corrections en cours

---

## [1.5.0] - 2024-01-15

### ✨ Ajouté
- Fonctionnalités ajoutées dans cette version

### 🔧 Modifié
- Modifications apportées

### 🐛 Corrections
- Bugs corrigés

---

## [1.4.0] - 2024-01-01
...
```

## Dépannage

### Erreur : "Section de développement non trouvée"

Assurez-vous que le fichier `CHANGELOG.md` contient une section avec "(En développement)" :

```markdown
## [X.X.X] - YYYY-MM-DD (En développement)
```

### Erreur : "Type invalide"

Vérifiez que vous utilisez un des types valides :
`added`, `changed`, `fixed`, `security`, `performance`, `ui`, `docs`, `migration`, `deprecated`, `removed`

### Erreur : "Format de version invalide"

La version doit suivre le format semver : `X.Y.Z` (ex: 1.5.0)

## Support

Pour toute question ou problème :

- 📧 Email : akrem.belkahla@infinityweb.tn
- 🌐 Site : https://infinityweb.tn
- 📝 Issues : Créez une issue sur le dépôt GitHub

## Licence

Ce système fait partie du plugin Simple Custom Fields, distribué sous licence GPL-2.0-or-later.
