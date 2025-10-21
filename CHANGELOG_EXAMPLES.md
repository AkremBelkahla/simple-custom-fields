# Exemples d'utilisation du système de Changelog

Ce fichier contient des exemples pratiques d'utilisation du système de gestion de changelog.

## 🚀 Démarrage rapide

### Ajouter votre première entrée

```bash
composer changelog:add added "Ma première fonctionnalité"
```

### Publier votre première version

```bash
composer changelog:release 1.5.0
```

## 📝 Exemples par scénario

### Scénario 1 : Développement d'une nouvelle fonctionnalité

Vous développez un nouveau type de champ "Galerie d'images" :

```bash
# Début du développement
composer changelog:add added "Nouveau type de champ : Galerie d'images"

# Ajout de fonctionnalités supplémentaires
composer changelog:add added "Support du drag & drop pour réorganiser les images"
composer changelog:add added "Prévisualisation des images dans l'admin"

# Documentation
composer changelog:add docs "Documentation du champ Galerie d'images"
```

### Scénario 2 : Correction de bugs

Vous corrigez plusieurs bugs signalés :

```bash
composer changelog:add fixed "Correction du bug de validation des emails avec caractères spéciaux"
composer changelog:add fixed "Correction du problème d'affichage des checkboxes sur mobile"
composer changelog:add fixed "Correction de la sauvegarde des champs répétables"
```

### Scénario 3 : Amélioration de la sécurité

Vous renforcez la sécurité du plugin :

```bash
composer changelog:add security "Ajout de la validation CSRF sur toutes les actions AJAX"
composer changelog:add security "Renforcement de la sanitization des entrées utilisateur"
composer changelog:add security "Ajout du rate limiting sur les endpoints sensibles"
```

### Scénario 4 : Optimisation des performances

Vous optimisez les performances :

```bash
composer changelog:add performance "Optimisation des requêtes de base de données"
composer changelog:add performance "Mise en cache des groupes de champs"
composer changelog:add performance "Lazy loading des assets JavaScript"
```

### Scénario 5 : Refonte de l'interface

Vous améliorez l'interface utilisateur :

```bash
composer changelog:add ui "Nouvelle palette de couleurs moderne"
composer changelog:add ui "Amélioration du design des modales"
composer changelog:add ui "Ajout d'animations fluides sur les interactions"
composer changelog:add ui "Design responsive complet"
```

### Scénario 6 : Release complète

Workflow complet pour une release majeure :

```bash
# Phase 1 : Développement (plusieurs jours/semaines)
composer changelog:add added "Support des champs conditionnels"
composer changelog:add added "Nouveau type de champ : Sélecteur de couleur"
composer changelog:add changed "Amélioration de l'interface d'édition des groupes"
composer changelog:add fixed "Correction du bug de duplication des groupes"
composer changelog:add security "Ajout de la validation CSRF"
composer changelog:add performance "Optimisation du chargement des assets"
composer changelog:add ui "Refonte complète du design"
composer changelog:add docs "Documentation complète de l'API"

# Phase 2 : Tests et corrections
composer changelog:add fixed "Correction des tests unitaires"
composer changelog:add fixed "Correction de la compatibilité PHP 7.4"

# Phase 3 : Préparation de la release
composer changelog:show  # Vérifier le changelog

# Phase 4 : Publication
composer changelog:release 1.5.0

# Phase 5 : Git
git add CHANGELOG.md simple-custom-fields.php
git commit -m "Release v1.5.0"
git tag v1.5.0
git push origin main
git push origin v1.5.0
```

### Scénario 7 : Hotfix urgent

Correction rapide d'un bug critique :

```bash
# Correction du bug
composer changelog:add fixed "Correction du bug critique de perte de données"

# Release patch immédiate
composer changelog:release 1.5.1

# Publication rapide
git add .
git commit -m "Hotfix v1.5.1 - Correction bug critique"
git tag v1.5.1
git push && git push --tags
```

### Scénario 8 : Dépréciation de fonctionnalités

Vous préparez une migration vers une nouvelle API :

```bash
composer changelog:add deprecated "Fonction scf_old_get_field() dépréciée, utilisez scf_get_field()"
composer changelog:add deprecated "Classe SCF_Old_Database dépréciée, utilisez SCF\Database"
composer changelog:add migration "Guide de migration vers la nouvelle API disponible dans docs/MIGRATION.md"
composer changelog:add added "Nouvelle API moderne avec namespace SCF\\"
```

### Scénario 9 : Breaking changes (version majeure)

Vous préparez une version majeure avec des changements incompatibles :

```bash
composer changelog:add removed "Suppression des anciennes fonctions dépréciées"
composer changelog:add changed "Changement de la structure de la base de données"
composer changelog:add migration "Migration automatique de la base de données au premier lancement"
composer changelog:add migration "Script de migration manuel disponible dans bin/migrate.php"

# Release majeure
composer changelog:release 2.0.0
```

### Scénario 10 : Documentation uniquement

Mise à jour de la documentation sans changement de code :

```bash
composer changelog:add docs "Ajout d'exemples d'utilisation dans la documentation"
composer changelog:add docs "Correction des typos dans le README"
composer changelog:add docs "Ajout de diagrammes d'architecture"

# Release patch pour la documentation
composer changelog:release 1.5.1
```

## 🎯 Bonnes pratiques illustrées

### ✅ Messages clairs et descriptifs

```bash
# BON
composer changelog:add fixed "Correction du bug de validation des emails avec caractères spéciaux Unicode"
composer changelog:add added "Support du champ 'Sélecteur de couleur' avec palette personnalisable"

# MAUVAIS
composer changelog:add fixed "fix bug"
composer changelog:add added "new feature"
```

### ✅ Catégorisation appropriée

```bash
# Nouvelle fonctionnalité
composer changelog:add added "Ajout du support des champs répétables"

# Modification d'une fonctionnalité existante
composer changelog:add changed "Amélioration de l'interface de sélection des types de contenu"

# Correction de bug
composer changelog:add fixed "Correction du problème de sauvegarde des métadonnées"

# Sécurité
composer changelog:add security "Correction de la faille XSS dans l'affichage des champs"
```

### ✅ Regroupement logique

```bash
# Regrouper les modifications liées
composer changelog:add added "Support des champs conditionnels"
composer changelog:add added "Interface de configuration des règles conditionnelles"
composer changelog:add docs "Documentation des champs conditionnels"
```

## 📊 Versioning sémantique

### Version PATCH (1.5.X)

Corrections de bugs uniquement :

```bash
composer changelog:add fixed "Correction du bug de validation"
composer changelog:release 1.5.1
```

### Version MINOR (1.X.0)

Nouvelles fonctionnalités rétrocompatibles :

```bash
composer changelog:add added "Nouveau type de champ : Galerie"
composer changelog:add added "Support des champs répétables"
composer changelog:release 1.6.0
```

### Version MAJOR (X.0.0)

Changements incompatibles :

```bash
composer changelog:add removed "Suppression des anciennes fonctions dépréciées"
composer changelog:add changed "Nouvelle structure de base de données"
composer changelog:add migration "Migration automatique requise"
composer changelog:release 2.0.0
```

## 🔄 Workflow Git complet

### Workflow standard

```bash
# 1. Créer une branche
git checkout -b feature/new-field-type

# 2. Développer et ajouter au changelog
composer changelog:add added "Nouveau type de champ : Sélecteur de fichiers"

# 3. Commiter
git add .
git commit -m "feat: Ajout du type de champ Sélecteur de fichiers"

# 4. Merger dans main
git checkout main
git merge feature/new-field-type

# 5. Préparer la release
composer changelog:release 1.6.0

# 6. Publier
git add .
git commit -m "Release v1.6.0"
git tag v1.6.0
git push origin main
git push origin v1.6.0
```

### Workflow avec Pull Request

```bash
# 1. Créer une branche et développer
git checkout -b feature/gallery-field
# ... développement ...
composer changelog:add added "Support du type de champ Galerie"

# 2. Pousser la branche
git push origin feature/gallery-field

# 3. Créer une PR sur GitHub/GitLab

# 4. Après merge de la PR, préparer la release
git checkout main
git pull
composer changelog:release 1.6.0

# 5. Publier
git push && git push --tags
```

## 🛠️ Commandes utiles

### Vérifier le changelog avant une release

```bash
composer changelog:show | head -n 50
```

### Voir l'aide

```bash
composer changelog:help
```

### Utilisation directe du script PHP

```bash
# Si vous préférez utiliser directement le script
php bin/changelog.php add fixed "Correction d'un bug"
php bin/changelog.php release 1.5.0
php bin/changelog.php show
```

## 📚 Ressources

- [Guide complet](docs/CHANGELOG_GUIDE.md)
- [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/)
- [Versioning Sémantique](https://semver.org/lang/fr/)

## 💡 Astuces

### Astuce 1 : Alias Git

Ajoutez des alias Git pour faciliter l'utilisation :

```bash
git config --global alias.changelog '!composer changelog:add'
git config --global alias.release '!composer changelog:release'
```

Utilisation :
```bash
git changelog fixed "Correction d'un bug"
git release 1.5.0
```

### Astuce 2 : Script de release automatique

Créez un script `release.sh` :

```bash
#!/bin/bash
VERSION=$1
composer changelog:release $VERSION
git add .
git commit -m "Release v$VERSION"
git tag v$VERSION
git push origin main
git push origin v$VERSION
echo "✓ Release v$VERSION publiée avec succès!"
```

Utilisation :
```bash
chmod +x release.sh
./release.sh 1.5.0
```

### Astuce 3 : Hook pre-commit

Ajoutez un hook Git pour vérifier le changelog avant chaque commit :

```bash
# .git/hooks/pre-commit
#!/bin/bash
if ! grep -q "(En développement)" CHANGELOG.md; then
    echo "⚠️  Attention : Aucune section 'En développement' dans le changelog"
fi
```

## 🎓 Exercices pratiques

### Exercice 1 : Première entrée

Ajoutez votre première entrée au changelog :

```bash
composer changelog:add added "Ma première contribution au changelog"
```

### Exercice 2 : Release complète

Simulez une release complète :

```bash
composer changelog:add added "Fonctionnalité de test"
composer changelog:add fixed "Bug de test"
composer changelog:release 1.5.0
```

### Exercice 3 : Workflow complet

Suivez le workflow complet de développement à publication :

```bash
# Développement
composer changelog:add added "Nouvelle fonctionnalité"
composer changelog:add docs "Documentation de la fonctionnalité"

# Vérification
composer changelog:show

# Release
composer changelog:release 1.6.0

# Git
git add .
git commit -m "Release v1.6.0"
git tag v1.6.0
```

---

**Note :** Ces exemples sont fournis à titre d'illustration. Adaptez-les à vos besoins spécifiques.
