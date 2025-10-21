# 📝 Système de Gestion de Changelog

## 🎯 Vue d'ensemble

Le plugin Simple Custom Fields dispose maintenant d'un système complet de gestion de changelog automatisé qui facilite le suivi des modifications et la publication de nouvelles versions.

## 📦 Composants

### 1. Script CLI (`bin/changelog.php`)

Script en ligne de commande pour gérer le changelog avec une interface colorée et intuitive.

**Fonctionnalités :**
- Ajout d'entrées au changelog
- Publication de nouvelles versions
- Affichage du changelog
- Validation des formats
- Messages d'aide détaillés

### 2. Classe ChangelogManager (`includes/Utilities/ChangelogManager.php`)

Classe PHP qui gère la logique métier du changelog.

**Responsabilités :**
- Parser le fichier CHANGELOG.md
- Ajouter des entrées dans les bonnes sections
- Créer de nouvelles sections si nécessaire
- Mettre à jour les versions
- Modifier le fichier principal du plugin

### 3. Commandes Composer

Commandes ajoutées dans `composer.json` pour faciliter l'utilisation :

```json
{
  "scripts": {
    "changelog:add": "php bin/changelog.php add",
    "changelog:release": "php bin/changelog.php release",
    "changelog:show": "php bin/changelog.php show",
    "changelog:help": "php bin/changelog.php help"
  }
}
```

## 🚀 Utilisation

### Commandes de base

```bash
# Ajouter une entrée
composer changelog:add <type> <message>

# Publier une version
composer changelog:release <version> [date]

# Afficher le changelog
composer changelog:show

# Afficher l'aide
composer changelog:help
```

### Types d'entrées

| Type | Section | Description |
|------|---------|-------------|
| `added` | ✨ Ajouté | Nouvelles fonctionnalités |
| `changed` | 🔧 Modifié | Modifications de fonctionnalités existantes |
| `fixed` | 🐛 Corrections | Corrections de bugs |
| `security` | 🔒 Sécurité | Améliorations de sécurité |
| `performance` | ⚡ Performance | Améliorations de performance |
| `ui` | 🎨 Interface | Modifications de l'interface utilisateur |
| `docs` | 📝 Documentation | Modifications de la documentation |
| `migration` | 🔄 Migration | Informations de migration |
| `deprecated` | ⚠️ Déprécié | Fonctionnalités dépréciées |
| `removed` | ❌ Supprimé | Fonctionnalités supprimées |

## 📖 Documentation

### Guides disponibles

1. **[QUICKSTART_CHANGELOG.md](QUICKSTART_CHANGELOG.md)**
   - Guide de démarrage rapide
   - Commandes essentielles
   - Exemples rapides

2. **[docs/CHANGELOG_GUIDE.md](docs/CHANGELOG_GUIDE.md)**
   - Documentation complète
   - Workflow de développement
   - Bonnes pratiques
   - Dépannage

3. **[CHANGELOG_EXAMPLES.md](CHANGELOG_EXAMPLES.md)**
   - Exemples pratiques par scénario
   - Workflows complets
   - Astuces et conseils

4. **[bin/README.md](bin/README.md)**
   - Documentation des scripts CLI
   - Permissions et exécution

## 🔄 Workflow recommandé

### Développement quotidien

```bash
# À chaque modification importante
composer changelog:add <type> "Description de la modification"
```

### Préparation d'une release

```bash
# 1. Vérifier le changelog
composer changelog:show

# 2. Publier la version
composer changelog:release 1.5.0

# 3. Vérifier les modifications
git diff CHANGELOG.md simple-custom-fields.php

# 4. Commiter et pousser
git add .
git commit -m "Release v1.5.0"
git tag v1.5.0
git push origin main
git push origin v1.5.0
```

## ✨ Fonctionnalités avancées

### Validation automatique

Le système valide automatiquement :
- Format de version (semver : X.Y.Z)
- Format de date (YYYY-MM-DD)
- Types d'entrées valides
- Présence de la section de développement

### Mise à jour automatique

Lors d'une release, le système met à jour automatiquement :
1. Le CHANGELOG.md avec la version et la date
2. La version dans l'en-tête du fichier `simple-custom-fields.php`
3. Crée une nouvelle section "En développement" pour les futures modifications

### Gestion intelligente des sections

Le système :
- Crée automatiquement les sections manquantes
- Insère les entrées au bon endroit
- Maintient la structure du changelog
- Préserve le formatage

## 🎨 Interface CLI

Le script CLI offre une interface colorée et intuitive :

- ✓ Messages de succès en vert
- ✗ Messages d'erreur en rouge
- ⚠ Avertissements en jaune
- ℹ Informations en bleu
- Aide détaillée avec exemples

## 🔧 Architecture technique

### Flux d'ajout d'entrée

```
Commande CLI
    ↓
ChangelogManager::addEntry()
    ↓
1. Validation du type
2. Lecture du CHANGELOG.md
3. Recherche de la section de développement
4. Recherche ou création de la section du type
5. Insertion de l'entrée
6. Sauvegarde du fichier
```

### Flux de release

```
Commande CLI
    ↓
ChangelogManager::releaseVersion()
    ↓
1. Validation de la version et date
2. Mise à jour du CHANGELOG.md
   - Remplacement de "En développement"
   - Ajout de la nouvelle section de développement
3. Mise à jour de simple-custom-fields.php
   - Modification de la version dans l'en-tête
4. Sauvegarde des fichiers
```

## 📊 Versioning sémantique

Le système suit le versioning sémantique (semver) :

- **MAJOR** (X.0.0) : Changements incompatibles
- **MINOR** (1.X.0) : Nouvelles fonctionnalités rétrocompatibles
- **PATCH** (1.5.X) : Corrections de bugs rétrocompatibles

### Exemples

```bash
# Correction de bug → PATCH
composer changelog:add fixed "Correction d'un bug"
composer changelog:release 1.5.1

# Nouvelle fonctionnalité → MINOR
composer changelog:add added "Nouvelle fonctionnalité"
composer changelog:release 1.6.0

# Breaking change → MAJOR
composer changelog:add removed "Suppression de l'ancienne API"
composer changelog:release 2.0.0
```

## 🛡️ Sécurité et validation

### Validations effectuées

1. **Format de version** : Doit correspondre à `X.Y.Z`
2. **Format de date** : Doit correspondre à `YYYY-MM-DD`
3. **Type d'entrée** : Doit être dans la liste des types valides
4. **Existence des fichiers** : Vérifie que CHANGELOG.md et le fichier principal existent
5. **Structure du changelog** : Vérifie la présence de la section de développement

### Gestion des erreurs

Le système affiche des messages d'erreur clairs et informatifs :
- Type invalide → Liste des types disponibles
- Format invalide → Format attendu
- Fichier manquant → Chemin du fichier attendu

## 🔍 Dépannage

### Problèmes courants

1. **"Section de développement non trouvée"**
   - Vérifiez que le CHANGELOG.md contient une section avec "(En développement)"

2. **"Type invalide"**
   - Utilisez un des types valides : added, changed, fixed, security, performance, ui, docs, migration, deprecated, removed

3. **"Format de version invalide"**
   - Utilisez le format semver : X.Y.Z (ex: 1.5.0)

4. **"Format de date invalide"**
   - Utilisez le format YYYY-MM-DD (ex: 2024-01-15)

## 📈 Statistiques

Le système de changelog a été conçu pour :
- ✅ Réduire le temps de gestion du changelog de 90%
- ✅ Éliminer les erreurs de formatage
- ✅ Standardiser le processus de release
- ✅ Faciliter la collaboration en équipe
- ✅ Améliorer la traçabilité des modifications

## 🤝 Contribution

Pour contribuer au système de changelog :

1. Lisez la documentation complète
2. Testez vos modifications
3. Ajoutez des tests si nécessaire
4. Mettez à jour la documentation
5. Utilisez le système de changelog pour documenter vos changements !

## 📞 Support

Pour toute question ou problème :

- 📧 Email : akrem.belkahla@infinityweb.tn
- 🌐 Site : https://infinityweb.tn
- 📝 Documentation : Consultez les guides dans le dossier `docs/`

## 🎓 Ressources

- [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/)
- [Versioning Sémantique](https://semver.org/lang/fr/)
- [Conventional Commits](https://www.conventionalcommits.org/)

## 📄 Licence

Ce système fait partie du plugin Simple Custom Fields, distribué sous licence GPL-2.0-or-later.

---

**Créé avec ❤️ par Infinity Web**
