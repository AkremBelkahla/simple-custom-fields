# Changelog

Toutes les modifications notables de ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère au [Versioning Sémantique](https://semver.org/lang/fr/).

## [1.5.0] - 2024-01-XX (En développement)

### ✨ Ajouté

#### Architecture
- Architecture modulaire PSR-4 avec autoloader Composer
- Namespace `SCF\` pour toutes les nouvelles classes
- Structure de dossiers organisée par responsabilité
- Configuration centralisée dans `SCF\Core\Config`
- Fichier de constantes `config/constants.php`

#### Logging et Erreurs
- Système de logging centralisé avec `SCF\Utilities\Logger`
- 8 niveaux de log (DEBUG, INFO, NOTICE, WARNING, ERROR, CRITICAL, ALERT, EMERGENCY)
- Logs stockés dans `wp-content/uploads/scf-logs/`
- Rotation automatique des logs (30 jours par défaut)
- Gestionnaire d'erreurs centralisé `SCF\Utilities\ErrorHandler`
- Notifications admin pour les erreurs critiques

#### Validation et Sécurité
- Classe de validation stricte `SCF\Validators\FieldValidator`
- Validation par type de champ avec règles personnalisables
- Sanitization automatique selon le type
- Support des champs requis
- Validation de plage pour les nombres
- Validation de format pour les dates
- Validation des choix pour select/radio/checkbox

#### Services
- Service métier `SCF\Services\FieldGroupService`
- Méthodes CRUD complètes pour les groupes
- Duplication de groupes
- Gestion transactionnelle avec rollback
- Hooks d'action pour extensibilité

#### Documentation
- Documentation d'architecture complète (`docs/ARCHITECTURE.md`)
- Documentation API pour développeurs (`docs/API.md`)
- Exemples d'utilisation détaillés
- Documentation des hooks et filtres
- Diagrammes de flux de données
- Guide complet de gestion du changelog (`docs/CHANGELOG_GUIDE.md`)

#### Outils de développement
- Système de gestion de changelog automatisé (`bin/changelog.php`)
- Classe `SCF\Utilities\ChangelogManager` pour parser et mettre à jour le changelog
- Commandes Composer pour faciliter la gestion des versions
- Mise à jour automatique de la version du plugin lors des releases
- Support de 10 types d'entrées (added, changed, fixed, security, etc.)

#### Tests
- Configuration PHPUnit avec Brain Monkey
- Tests unitaires pour `FieldValidator`
- Bootstrap de tests avec mocks WordPress
- Scripts Composer pour exécuter les tests
- Support des tests d'intégration

#### Qualité de code
- Configuration PHPStan (niveau 5)
- Configuration PHPCS avec standards WordPress
- Scripts de linting dans Composer
- Autoloader optimisé

### 🔧 Modifié

#### Composer
- Métadonnées complètes du package
- Autoloader PSR-4 étendu
- Dépendances de développement ajoutées
- Scripts de test et lint configurés

#### Sécurité
- Amélioration de la classe `SCF_Security` existante
- Correction du bug de nonce dans la suppression de groupes
- Rate limiting renforcé
- Headers de sécurité HTTP

### 📝 Documentation

#### Nouveaux fichiers
- `docs/ARCHITECTURE.md` : Architecture technique
- `docs/API.md` : API publique
- `CHANGELOG.md` : Ce fichier
- `.phpcs.xml.dist` : Configuration PHPCS
- `phpstan.neon` : Configuration PHPStan
- `tests/bootstrap.php` : Bootstrap des tests

### 🔄 Migration

#### Compatibilité
- Maintien des anciennes classes pour compatibilité
- Pas de breaking changes
- Migration progressive vers la nouvelle architecture
- Dépréciation planifiée pour v2.0

### 🐛 Corrections

- **Sécurité** : Correction du bug de vérification de nonce lors de la suppression de groupes
- **Validation** : Amélioration de la validation des emails
- **Performance** : Optimisation de l'autoloader

---

## [1.4.1] - 2024-01-XX

### 🔒 Sécurité
- Ajout de la classe `SCF_Security` pour centraliser les vérifications
- Protection CSRF renforcée
- Rate limiting sur les actions AJAX
- Validation stricte des entrées
- Échappement systématique des sorties

### 🐛 Corrections
- Correction des problèmes de vérification de nonce
- Amélioration de la gestion des erreurs

---

## [1.4.0] - 2024-01-XX

### ✨ Ajouté
- Refonte complète du design avec palette moderne
- Layout en cards pour une meilleure organisation
- Animations fluides sur toutes les interactions
- Design responsive complet
- Modales avec backdrop blur
- Amélioration du drag & drop

### 🎨 Interface
- Nouvelle palette de couleurs professionnelle
- Typographie améliorée
- Icônes Dashicons intégrées
- États de focus intuitifs

---

## [1.3.0] - Date

### ⚡ Performance
- Optimisation des requêtes de base de données
- Mise en cache des groupes de champs
- Amélioration du chargement des assets

### 🐛 Corrections
- Corrections de bugs divers
- Amélioration de la stabilité

---

## [1.2.0] - Date

### ✨ Ajouté
- Support des shortcodes `[scf_fields]` et `[scf_field]`
- Affichage front-end des champs personnalisés

---

## [1.1.0] - Date

### ✨ Ajouté
- Nouveaux types de champs (Date, URL, File)
- Amélioration de l'interface d'administration
- Support des règles d'affichage conditionnelles

---

## [1.0.0] - Date

### 🎉 Initial Release
- Création de groupes de champs personnalisés
- Types de champs de base (Text, Textarea, Number, Email, Select, Radio, Checkbox)
- Table de base de données dédiée
- Interface d'administration
- Fonction `scf_get_field()` pour récupérer les valeurs

---

## Légende

- ✨ Ajouté : Nouvelles fonctionnalités
- 🔧 Modifié : Modifications de fonctionnalités existantes
- 🐛 Corrections : Corrections de bugs
- 🔒 Sécurité : Améliorations de sécurité
- ⚡ Performance : Améliorations de performance
- 🎨 Interface : Modifications de l'interface utilisateur
- 📝 Documentation : Modifications de la documentation
- 🔄 Migration : Informations de migration
- ⚠️ Déprécié : Fonctionnalités dépréciées
- ❌ Supprimé : Fonctionnalités supprimées
