# 🌍 Système de Traduction - Vue d'ensemble

## 📝 Résumé

Le plugin Simple Custom Fields dispose maintenant d'un système complet d'internationalisation (i18n) qui gère automatiquement les traductions en français et en anglais.

## 🎯 Fonctionnement

### Détection automatique

Le système détecte automatiquement la langue de WordPress et charge la traduction appropriée :

```
WordPress en français (fr_FR, fr_CA, fr_BE, etc.) → Plugin en français
Toutes les autres langues → Plugin en anglais
```

**Aucune configuration nécessaire de la part de l'utilisateur !**

## 📦 Composants du système

### 1. Fichiers de traduction

```
languages/
├── simple-custom-fields.pot          # Template (150+ chaînes)
├── simple-custom-fields-fr_FR.po     # Français (éditable)
├── simple-custom-fields-fr_FR.mo     # Français (compilé)
├── simple-custom-fields-en_US.po     # Anglais (éditable)
└── simple-custom-fields-en_US.mo     # Anglais (compilé)
```

**Statut :** 100% traduit pour les deux langues

### 2. Classe I18nManager

**Fichier :** `includes/Utilities/I18nManager.php`

**Responsabilités :**
- Détection automatique de la locale
- Chargement des traductions
- API de traduction simplifiée
- Statistiques de traduction
- Gestion des langues supportées

**Utilisation :**

```php
$i18n = SCF\Utilities\I18nManager::getInstance();

// Obtenir la locale actuelle
$locale = $i18n->getCurrentLocale(); // 'fr_FR' ou 'en_US'

// Obtenir le nom de la langue
$language = $i18n->getCurrentLanguageName(); // 'Français' ou 'English'

// Traduire
$text = $i18n->translate('Hello');

// Statistiques
$stats = $i18n->getTranslationStats('fr_FR');
```

### 3. Script CLI i18n.php

**Fichier :** `bin/i18n.php`

**Fonctionnalités :**
- Génération des fichiers MO depuis les fichiers PO
- Scan du code pour trouver les chaînes traduisibles
- Statistiques de traduction avec barre de progression
- Interface colorée et intuitive

**Commandes :**

```bash
# Générer les fichiers MO
php bin/i18n.php generate-mo [locale]

# Scanner le code
php bin/i18n.php scan

# Statistiques
php bin/i18n.php stats [locale]

# Aide
php bin/i18n.php help
```

### 4. Commandes Composer

**Ajoutées dans `composer.json` :**

```json
{
  "scripts": {
    "i18n:generate-mo": "php bin/i18n.php generate-mo",
    "i18n:scan": "php bin/i18n.php scan",
    "i18n:stats": "php bin/i18n.php stats"
  }
}
```

**Utilisation :**

```bash
composer i18n:generate-mo
composer i18n:scan
composer i18n:stats fr_FR
```

## 🔧 Architecture technique

### Flux de chargement

```
1. WordPress démarre
   ↓
2. Hook 'plugins_loaded' (priorité 1)
   ↓
3. scf_load_textdomain() appelée
   ↓
4. I18nManager::getInstance()
   ↓
5. Détection de la locale (determineLocale())
   ↓
6. Chargement du fichier MO approprié
   ↓
7. Traductions disponibles
```

### Logique de détection

```php
private function determineLocale() {
    $wpLocale = get_locale();
    
    // Si WordPress est en français
    if (strpos($wpLocale, 'fr') === 0) {
        return 'fr_FR';
    }
    
    // Si la locale est supportée
    if (isset($this->supportedLocales[$wpLocale])) {
        return $wpLocale;
    }
    
    // Par défaut : anglais
    return 'en_US';
}
```

## 📊 Statistiques

### Couverture de traduction

| Langue | Locale | Chaînes | Traduites | Pourcentage |
|--------|--------|---------|-----------|-------------|
| 🇫🇷 Français | fr_FR | 150+ | 150+ | 100% |
| 🇬🇧 Anglais | en_US | 150+ | 150+ | 100% |

### Catégories de chaînes

- **Métadonnées du plugin** : 2 chaînes
- **Éléments de menu** : 7 chaînes
- **Types de champs** : 14 chaînes
- **Labels de champs** : 13 chaînes
- **Paramètres de groupe** : 10 chaînes
- **Boutons** : 10 chaînes
- **Messages** : 20 chaînes
- **Messages de sécurité** : 5 chaînes
- **Confirmations** : 4 chaînes
- **Textes d'aide** : 13 chaînes
- **Règles d'affichage** : 5 chaînes
- **Types de contenu** : 3 chaînes
- **Statuts** : 4 chaînes
- **Import/Export** : 8 chaînes
- **Documentation** : 5 chaînes
- **Informations de version** : 3 chaînes
- **Paramètres avancés** : 7 chaînes
- **Messages de validation** : 4 chaînes
- **Journalisation** : 6 chaînes
- **Notifications** : 3 chaînes
- **Accessibilité** : 7 chaînes
- **Date/Heure** : 6 chaînes
- **Divers** : 15 chaînes

**Total : 150+ chaînes**

## 🛠️ Utilisation pour les développeurs

### Dans le code PHP

```php
// Traduction simple
__('Text', 'simple-custom-fields');

// Avec échappement HTML
esc_html__('Text', 'simple-custom-fields');

// Avec échappement d'attribut
esc_attr__('Text', 'simple-custom-fields');

// Affichage direct
_e('Text', 'simple-custom-fields');
esc_html_e('Text', 'simple-custom-fields');

// Avec contexte
_x('Post', 'noun', 'simple-custom-fields');

// Pluriels
_n('One field', '%s fields', $count, 'simple-custom-fields');

// Avec sprintf
sprintf(__('Hello %s', 'simple-custom-fields'), $name);
```

### Dans les templates

```php
<h1><?php esc_html_e('Field Groups', 'simple-custom-fields'); ?></h1>

<button><?php esc_html_e('Save', 'simple-custom-fields'); ?></button>

<input 
    type="text" 
    placeholder="<?php esc_attr_e('Enter name', 'simple-custom-fields'); ?>"
>
```

## ➕ Ajouter une nouvelle langue

### Étapes

1. **Créer les fichiers PO**
   ```bash
   cp languages/simple-custom-fields.pot languages/simple-custom-fields-es_ES.po
   ```

2. **Traduire avec Poedit**
   - Ouvrir le fichier `.po`
   - Traduire les chaînes
   - Sauvegarder (génère automatiquement le `.mo`)

3. **Ajouter dans I18nManager**
   ```php
   private $supportedLocales = [
       'fr_FR' => 'Français',
       'en_US' => 'English',
       'es_ES' => 'Español'  // Nouvelle langue
   ];
   ```

4. **Mettre à jour la logique de détection (optionnel)**
   ```php
   if (strpos($wpLocale, 'es') === 0) {
       return 'es_ES';
   }
   ```

5. **Tester**

## 📚 Documentation

### Guides disponibles

1. **[I18N_README.md](I18N_README.md)**
   - Démarrage rapide
   - Commandes essentielles
   - Exemples de base

2. **[docs/I18N_GUIDE.md](docs/I18N_GUIDE.md)**
   - Guide complet et détaillé
   - Workflow de traduction
   - Bonnes pratiques
   - Débogage
   - Ressources

3. **[includes/Utilities/I18nManager.php](includes/Utilities/I18nManager.php)**
   - Documentation de la classe
   - API complète
   - Exemples d'utilisation

4. **[bin/i18n.php](bin/i18n.php)**
   - Script CLI
   - Commandes disponibles
   - Aide intégrée

## 🔍 Débogage

### Vérifier la configuration

```php
$i18n = SCF\Utilities\I18nManager::getInstance();
$debug = $i18n->getDebugInfo();

print_r($debug);
/*
Array (
    [current_locale] => fr_FR
    [current_language] => Français
    [wp_locale] => fr_FR
    [supported_locales] => Array (...)
    [languages_path] => /path/to/languages
    [text_domain] => simple-custom-fields
    [files] => Array (
        [fr_FR] => Array (
            [po_exists] => true
            [mo_exists] => true
            [po_path] => /path/to/simple-custom-fields-fr_FR.po
            [mo_path] => /path/to/simple-custom-fields-fr_FR.mo
        )
    )
    [stats] => Array (...)
)
*/
```

### Activer les logs

Dans `wp-config.php` :

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Les logs apparaîtront dans `wp-content/debug.log` :

```
[SCF I18n] INFO: Traductions chargées pour: fr_FR
```

## ✅ Tests

### Tester le changement de langue

1. Aller dans **Réglages → Général**
2. Changer la **Langue du site**
3. Sauvegarder
4. Vérifier que le plugin affiche la bonne langue

### Tester les traductions

1. Activer le mode debug
2. Vérifier les logs
3. Tester chaque section du plugin
4. Vérifier que toutes les chaînes sont traduites

## 🎯 Bonnes pratiques

### ✅ À faire

- Toujours utiliser le text domain `'simple-custom-fields'`
- Échapper les sorties avec `esc_html__()` ou `esc_attr__()`
- Utiliser des chaînes complètes, pas de concaténation
- Utiliser `sprintf()` pour les variables
- Fournir du contexte avec `_x()` si nécessaire
- Utiliser `_n()` pour les pluriels

### ❌ À éviter

- Concaténer des chaînes traduites
- Oublier le text domain
- Ne pas échapper les sorties
- Utiliser des chaînes fragmentées
- Hard-coder des textes sans traduction

## 🚀 Améliorations futures

### Fonctionnalités potentielles

- [ ] Support de plus de langues (espagnol, allemand, italien, etc.)
- [ ] Interface admin pour gérer les traductions
- [ ] Traduction des métadonnées du plugin sur WordPress.org
- [ ] Export/Import de traductions
- [ ] Traduction collaborative en ligne
- [ ] Génération automatique du fichier POT via WP-CLI
- [ ] Validation automatique des traductions
- [ ] Détection des chaînes non traduites

## 📞 Support

Pour toute question sur les traductions :

- 📧 Email : akrem.belkahla@infinityweb.tn
- 🌐 Site : https://infinityweb.tn
- 📝 Issues : Créez une issue sur le dépôt GitHub

## 🤝 Contribuer

Vous souhaitez contribuer aux traductions ?

1. Forkez le projet
2. Créez ou améliorez une traduction
3. Testez la traduction
4. Soumettez une Pull Request

Les contributions sont les bienvenues !

## 📈 Historique

### Version 1.5.0 (2025-01-21)

- ✅ Système de traduction complet implémenté
- ✅ Support du français et de l'anglais
- ✅ Détection automatique de la langue
- ✅ Classe I18nManager créée
- ✅ Script CLI i18n.php créé
- ✅ Documentation complète
- ✅ 150+ chaînes traduites

## 🔗 Ressources

### Outils

- [Poedit](https://poedit.net/) - Éditeur de fichiers PO/MO
- [Loco Translate](https://wordpress.org/plugins/loco-translate/) - Plugin WordPress
- [WP-CLI](https://wp-cli.org/) - Outils en ligne de commande

### Documentation WordPress

- [I18n for WordPress Developers](https://developer.wordpress.org/plugins/internationalization/)
- [How to Internationalize Your Plugin](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/)
- [Localization](https://developer.wordpress.org/plugins/internationalization/localization/)

### Standards

- [GNU gettext](https://www.gnu.org/software/gettext/)
- [Portable Object (PO) format](https://www.gnu.org/software/gettext/manual/html_node/PO-Files.html)

---

**Simple Custom Fields** - Système de traduction créé avec ❤️ par Infinity Web
