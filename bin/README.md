# Scripts CLI

Ce dossier contient les scripts en ligne de commande pour gérer le plugin.

## 📝 changelog.php

Script pour gérer le changelog et les versions du plugin.

### Utilisation rapide

```bash
# Ajouter une entrée
php bin/changelog.php add fixed "Correction d'un bug"

# Publier une version
php bin/changelog.php release 1.5.0

# Afficher le changelog
php bin/changelog.php show

# Aide
php bin/changelog.php help
```

### Via Composer

```bash
composer changelog:add fixed "Correction d'un bug"
composer changelog:release 1.5.0
composer changelog:show
composer changelog:help
```

### Documentation complète

Consultez le guide complet : [docs/CHANGELOG_GUIDE.md](../docs/CHANGELOG_GUIDE.md)

## Permissions

Sur les systèmes Unix/Linux, assurez-vous que le script est exécutable :

```bash
chmod +x bin/changelog.php
```

Vous pourrez alors l'exécuter directement :

```bash
./bin/changelog.php help
```
