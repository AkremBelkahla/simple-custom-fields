# Architecture du Plugin Simple Custom Fields

## 📋 Vue d'ensemble

Simple Custom Fields est un plugin WordPress moderne qui suit les standards PSR-4 et les meilleures pratiques de développement.

## 🏗️ Structure des dossiers

```
simple-custom-fields/
├── assets/                 # Ressources front-end
│   ├── css/               # Feuilles de style
│   └── js/                # Scripts JavaScript
├── config/                # Fichiers de configuration
│   └── constants.php      # Constantes du plugin
├── docs/                  # Documentation
│   ├── ARCHITECTURE.md    # Ce fichier
│   ├── API.md            # Documentation API
│   └── HOOKS.md          # Hooks et filtres
├── includes/              # Code source PHP
│   ├── Admin/            # Classes d'administration
│   ├── Core/             # Noyau du plugin
│   ├── Fields/           # Types de champs
│   ├── Services/         # Logique métier
│   ├── Utilities/        # Utilitaires
│   └── Validators/       # Validation des données
├── languages/             # Fichiers de traduction
├── templates/             # Templates PHP
├── tests/                # Tests unitaires et d'intégration
│   ├── Unit/             # Tests unitaires
│   └── Integration/      # Tests d'intégration
└── vendor/               # Dépendances Composer

```

## 🔧 Composants principaux

### Core (Noyau)

**Namespace:** `SCF\Core`

- **Autoloader.php** : Chargement automatique des classes PSR-4
- **Config.php** : Configuration centralisée du plugin
- **Plugin.php** : Classe principale du plugin (à créer)

### Utilities (Utilitaires)

**Namespace:** `SCF\Utilities`

- **Logger.php** : Système de logging avec niveaux de sévérité
- **ErrorHandler.php** : Gestion centralisée des erreurs
- **Cache.php** : Gestion du cache (à créer)

### Services (Logique métier)

**Namespace:** `SCF\Services`

- **FieldGroupService.php** : Gestion des groupes de champs
- **FieldValueService.php** : Gestion des valeurs de champs (à créer)
- **ImportExportService.php** : Import/Export (à créer)

### Validators (Validation)

**Namespace:** `SCF\Validators`

- **FieldValidator.php** : Validation des champs selon leur type
- **SecurityValidator.php** : Validation de sécurité (à créer)

### Admin (Administration)

**Namespace:** `SCF\Admin`

- Classes pour l'interface d'administration (à migrer)

### Fields (Types de champs)

**Namespace:** `SCF\Fields`

- Classes pour chaque type de champ (Text, Textarea, etc.)

## 🔄 Flux de données

### Création d'un groupe de champs

```
User Input → Admin Page → Security Check → FieldGroupService
                                              ↓
                                         Validation
                                              ↓
                                         Database
                                              ↓
                                         Logger
```

### Affichage d'un champ

```
Template → scf_get_field() → FieldValueService → Database
                                    ↓
                              Validation
                                    ↓
                              Formatting
                                    ↓
                              Output
```

## 🔒 Sécurité

### Couches de sécurité

1. **Vérification des permissions** : `current_user_can()`
2. **Validation des nonces** : Protection CSRF
3. **Sanitization des entrées** : Nettoyage des données
4. **Validation des données** : Vérification de la cohérence
5. **Échappement des sorties** : Protection XSS
6. **Rate limiting** : Protection contre les abus

### Classes de sécurité

- `SCF_Security` : Vérifications de sécurité (existante)
- `SCF\Validators\FieldValidator` : Validation des champs
- `SCF\Utilities\ErrorHandler` : Gestion des erreurs de sécurité

## 📊 Base de données

### Table personnalisée : `wp_scf_fields`

```sql
CREATE TABLE wp_scf_fields (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    post_id bigint(20) NOT NULL,
    group_id bigint(20) NOT NULL,
    field_name varchar(255) NOT NULL,
    field_value longtext,
    created_at datetime NOT NULL,
    updated_at datetime NOT NULL,
    PRIMARY KEY (id),
    KEY post_id (post_id),
    KEY group_id (group_id),
    KEY field_name (field_name)
);
```

### Custom Post Type : `scf-field-group`

Stocke les définitions des groupes de champs avec leurs métadonnées :
- `scf_fields` : Configuration des champs
- `scf_rules` : Règles d'affichage

## 🎯 Patterns de conception

### Singleton

Utilisé pour les classes de service et utilitaires :
- `Logger`
- `ErrorHandler`
- `Config`
- `FieldValidator`
- `FieldGroupService`

### Factory (à implémenter)

Pour créer les instances de champs selon leur type.

### Observer

Utilisation des hooks WordPress :
- `do_action('scf_group_created', $group_id, $data)`
- `do_action('scf_group_updated', $group_id, $data)`
- `do_action('scf_group_deleted', $group_id)`

## 📝 Logging

### Niveaux de log

1. **DEBUG** : Informations de débogage détaillées
2. **INFO** : Événements informatifs
3. **NOTICE** : Événements normaux mais significatifs
4. **WARNING** : Avertissements
5. **ERROR** : Erreurs d'exécution
6. **CRITICAL** : Conditions critiques
7. **ALERT** : Actions immédiates requises
8. **EMERGENCY** : Système inutilisable

### Emplacement des logs

`wp-content/uploads/scf-logs/scf-YYYY-MM-DD.log`

## 🧪 Tests

### Structure des tests

```
tests/
├── Unit/                    # Tests unitaires
│   ├── Core/
│   ├── Services/
│   ├── Validators/
│   └── Utilities/
├── Integration/             # Tests d'intégration
│   ├── Admin/
│   └── Fields/
└── bootstrap.php           # Bootstrap des tests
```

### Commandes

```bash
# Tous les tests
composer test

# Tests unitaires uniquement
composer test-unit

# Tests d'intégration uniquement
composer test-integration
```

## 🔌 Hooks et filtres

### Actions

- `scf_group_created` : Après création d'un groupe
- `scf_group_updated` : Après mise à jour d'un groupe
- `scf_group_deleted` : Après suppression d'un groupe
- `scf_field_saved` : Après sauvegarde d'une valeur de champ

### Filtres

- `scf_config` : Modifier la configuration
- `scf_validation_rules` : Modifier les règles de validation
- `scf_field_types` : Ajouter/modifier des types de champs
- `scf_field_value` : Modifier la valeur d'un champ avant affichage

## 🚀 Performance

### Optimisations

1. **Autoloader optimisé** : PSR-4 avec Composer
2. **Cache** : Mise en cache des requêtes fréquentes
3. **Index de base de données** : Sur post_id, group_id, field_name
4. **Lazy loading** : Chargement à la demande des composants

### Cache

- Durée par défaut : 1 heure
- Préfixe : `scf_`
- Invalidation automatique lors des modifications

## 📦 Dépendances

### Production

- PHP >= 7.4
- WordPress >= 5.0

### Développement

- PHPUnit ^9.5
- Brain Monkey ^2.6
- Mockery ^1.4
- WordPress Coding Standards ^3.0
- PHPStan ^1.10

## 🔄 Migration depuis l'ancienne architecture

### Compatibilité

Les anciennes classes sont maintenues pour la compatibilité :
- `SCF_Database`
- `SCF_Security`
- `SCF_Fields`
- `SCF_Admin_Page`
- `SCF_Meta_Boxes`

### Plan de migration

1. ✅ Créer la nouvelle architecture
2. ⏳ Migrer progressivement les fonctionnalités
3. ⏳ Maintenir la compatibilité ascendante
4. ⏳ Déprécier les anciennes classes
5. ⏳ Supprimer le code obsolète (version 2.0)

## 📚 Documentation

- **ARCHITECTURE.md** : Architecture du plugin (ce fichier)
- **API.md** : Documentation de l'API publique
- **HOOKS.md** : Liste complète des hooks et filtres
- **CONTRIBUTING.md** : Guide de contribution
- **CHANGELOG.md** : Historique des versions
