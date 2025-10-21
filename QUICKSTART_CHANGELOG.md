# 🚀 Démarrage Rapide - Système de Changelog

Guide ultra-rapide pour commencer à utiliser le système de changelog.

## ⚡ En 30 secondes

### 1. Ajouter une modification

```bash
composer changelog:add fixed "Correction d'un bug"
```

### 2. Publier une version

```bash
composer changelog:release 1.5.0
```

C'est tout ! 🎉

## 📝 Commandes essentielles

| Commande | Description | Exemple |
|----------|-------------|---------|
| `composer changelog:add <type> <message>` | Ajoute une entrée | `composer changelog:add added "Nouvelle fonctionnalité"` |
| `composer changelog:release <version>` | Publie une version | `composer changelog:release 1.5.0` |
| `composer changelog:show` | Affiche le changelog | `composer changelog:show` |
| `composer changelog:help` | Affiche l'aide | `composer changelog:help` |

## 🏷️ Types disponibles

| Type | Utilisation |
|------|-------------|
| `added` | Nouvelle fonctionnalité |
| `changed` | Modification d'une fonctionnalité |
| `fixed` | Correction de bug |
| `security` | Amélioration de sécurité |
| `performance` | Optimisation |
| `ui` | Changement d'interface |
| `docs` | Documentation |
| `migration` | Guide de migration |
| `deprecated` | Fonctionnalité dépréciée |
| `removed` | Fonctionnalité supprimée |

## 💡 Exemples rapides

### Correction de bug

```bash
composer changelog:add fixed "Correction du problème de sauvegarde"
```

### Nouvelle fonctionnalité

```bash
composer changelog:add added "Support des champs répétables"
```

### Amélioration de sécurité

```bash
composer changelog:add security "Renforcement de la validation CSRF"
```

### Publier une version

```bash
# Version patch (correction de bugs)
composer changelog:release 1.5.1

# Version minor (nouvelles fonctionnalités)
composer changelog:release 1.6.0

# Version major (breaking changes)
composer changelog:release 2.0.0
```

## 🔄 Workflow complet

```bash
# 1. Développer et ajouter au changelog
composer changelog:add added "Ma nouvelle fonctionnalité"

# 2. Vérifier le changelog
composer changelog:show

# 3. Publier la version
composer changelog:release 1.5.0

# 4. Commiter et pousser
git add .
git commit -m "Release v1.5.0"
git tag v1.5.0
git push && git push --tags
```

## 📚 Documentation complète

- 📖 [Guide complet](docs/CHANGELOG_GUIDE.md) - Documentation détaillée
- 💡 [Exemples](CHANGELOG_EXAMPLES.md) - Exemples pratiques par scénario
- 📝 [Changelog](CHANGELOG.md) - Historique des versions

## ❓ Besoin d'aide ?

```bash
composer changelog:help
```

---

**Astuce :** Ajoutez une entrée au changelog à chaque modification importante !
