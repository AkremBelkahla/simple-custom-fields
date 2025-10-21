# Guide d'Internationalisation (i18n)

Ce guide explique comment fonctionne le système de traduction de Simple Custom Fields et comment l'utiliser.

## 📋 Table des matières

- [Vue d'ensemble](#vue-densemble)
- [Langues supportées](#langues-supportées)
- [Fonctionnement automatique](#fonctionnement-automatique)
- [Structure des fichiers](#structure-des-fichiers)
- [Utilisation pour les développeurs](#utilisation-pour-les-développeurs)
- [Gestion des traductions](#gestion-des-traductions)
- [Ajouter une nouvelle langue](#ajouter-une-nouvelle-langue)
- [Outils CLI](#outils-cli)

## Vue d'ensemble

Simple Custom Fields dispose d'un système de traduction automatique qui :

- ✅ Détecte automatiquement la langue de WordPress
- ✅ Charge le français si WordPress est en français
- ✅ Charge l'anglais pour toutes les autres langues
- ✅ Supporte l'ajout de nouvelles langues facilement
- ✅ Fournit des outils CLI pour gérer les traductions

## Langues supportées

| Langue | Locale | Fichiers | Statut |
|--------|--------|----------|--------|
| 🇫🇷 Français | `fr_FR` | `simple-custom-fields-fr_FR.po/mo` | ✅ Complet |
| 🇬🇧 Anglais | `en_US` | `simple-custom-fields-en_US.po/mo` | ✅ Complet |

## Fonctionnement automatique

### Logique de sélection

Le plugin utilise la logique suivante pour choisir la langue :

```
Si WordPress est en français (fr_*) 
    → Utiliser le français (fr_FR)
Sinon
    → Utiliser l'anglais (en_US)
```

### Exemples

- WordPress en `fr_FR` → Plugin en français
- WordPress en `fr_CA` → Plugin en français
- WordPress en `fr_BE` → Plugin en français
- WordPress en `en_US` → Plugin en anglais
- WordPress en `es_ES` → Plugin en anglais (langue par défaut)
- WordPress en `de_DE` → Plugin en anglais (langue par défaut)

## Structure des fichiers

```
languages/
├── simple-custom-fields.pot          # Template (fichier source)
├── simple-custom-fields-fr_FR.po     # Traduction française (éditable)
├── simple-custom-fields-fr_FR.mo     # Traduction française (compilée)
├── simple-custom-fields-en_US.po     # Traduction anglaise (éditable)
└── simple-custom-fields-en_US.mo     # Traduction anglaise (compilée)
```

### Types de fichiers

- **`.pot`** : Template contenant toutes les chaînes traduisibles (fichier source)
- **`.po`** : Fichier de traduction éditable (format texte)
- **`.mo`** : Fichier de traduction compilé (format binaire, utilisé par WordPress)

## Utilisation pour les développeurs

### Dans le code PHP

#### Traduction simple

```php
// Fonction de base
__('Text to translate', 'simple-custom-fields');

// Avec échappement HTML
esc_html__('Text to translate', 'simple-custom-fields');

// Avec échappement d'attribut
esc_attr__('Text to translate', 'simple-custom-fields');
```

#### Affichage direct

```php
// Afficher directement
_e('Text to translate', 'simple-custom-fields');

// Avec échappement HTML
esc_html_e('Text to translate', 'simple-custom-fields');

// Avec échappement d'attribut
esc_attr_e('Text to translate', 'simple-custom-fields');
```

#### Traduction avec contexte

```php
// Utile pour les mots ayant plusieurs sens
_x('Post', 'noun', 'simple-custom-fields');  // Article
_x('Post', 'verb', 'simple-custom-fields');  // Publier
```

#### Pluriels

```php
// Gestion automatique du pluriel
$count = 5;
printf(
    _n(
        'One field',
        '%s fields',
        $count,
        'simple-custom-fields'
    ),
    $count
);
```

### Utilisation de I18nManager

```php
// Obtenir l'instance
$i18n = SCF\Utilities\I18nManager::getInstance();

// Obtenir la locale actuelle
$locale = $i18n->getCurrentLocale(); // 'fr_FR' ou 'en_US'

// Obtenir le nom de la langue
$language = $i18n->getCurrentLanguageName(); // 'Français' ou 'English'

// Traduire une chaîne
$translated = $i18n->translate('Hello');

// Traduire avec échappement
$safe = $i18n->translateEsc('Hello');

// Obtenir les statistiques
$stats = $i18n->getTranslationStats('fr_FR');
print_r($stats);
/*
Array (
    [locale] => fr_FR
    [language] => Français
    [total] => 150
    [translated] => 150
    [untranslated] => 0
    [percentage] => 100
)
*/
```

### Dans les templates

```php
<h1><?php esc_html_e('Field Groups', 'simple-custom-fields'); ?></h1>

<button>
    <?php esc_html_e('Add Field', 'simple-custom-fields'); ?>
</button>

<input 
    type="text" 
    placeholder="<?php esc_attr_e('Enter field name', 'simple-custom-fields'); ?>"
>
```

### Dans JavaScript

Les traductions sont passées via `wp_localize_script` :

```javascript
// Les traductions sont disponibles dans l'objet scf_vars
console.log(scf_vars.i18n.save);        // "Enregistrer" ou "Save"
console.log(scf_vars.i18n.delete);      // "Supprimer" ou "Delete"
console.log(scf_vars.i18n.confirm);     // "Êtes-vous sûr ?" ou "Are you sure?"
```

## Gestion des traductions

### Générer les fichiers MO

Les fichiers `.mo` doivent être générés à partir des fichiers `.po` :

```bash
# Générer tous les fichiers MO
composer i18n:generate-mo

# Ou via le script direct
php bin/i18n.php generate-mo

# Générer pour une langue spécifique
php bin/i18n.php generate-mo fr_FR
```

### Scanner les chaînes traduisibles

Pour trouver toutes les chaînes traduisibles dans le code :

```bash
# Scanner le code
composer i18n:scan

# Ou via le script direct
php bin/i18n.php scan
```

### Statistiques de traduction

Pour voir les statistiques d'une langue :

```bash
# Statistiques pour le français
composer i18n:stats fr_FR

# Statistiques pour l'anglais
composer i18n:stats en_US

# Ou via le script direct
php bin/i18n.php stats fr_FR
```

Exemple de sortie :

```
ℹ Statistiques pour fr_FR:

  Total: 150 chaînes
  Traduites: 150 (100%)
  Non traduites: 0

  [██████████████████████████████████████████████████] 100%
```

## Ajouter une nouvelle langue

### 1. Créer les fichiers de traduction

```bash
# Copier le template
cp languages/simple-custom-fields.pot languages/simple-custom-fields-es_ES.po

# Éditer le fichier
nano languages/simple-custom-fields-es_ES.po
```

### 2. Mettre à jour l'en-tête

```po
msgid ""
msgstr ""
"Project-Id-Version: Simple Custom Fields 1.5.0\n"
"Language: es_ES\n"
"Language-Team: Your Team\n"
...
```

### 3. Traduire les chaînes

```po
msgid "Field Groups"
msgstr "Grupos de campos"

msgid "Add Field"
msgstr "Añadir campo"
```

### 4. Générer le fichier MO

```bash
php bin/i18n.php generate-mo es_ES
```

### 5. Ajouter la langue dans I18nManager

Éditer `includes/Utilities/I18nManager.php` :

```php
private $supportedLocales = [
    'fr_FR' => 'Français',
    'en_US' => 'English',
    'es_ES' => 'Español'  // Ajouter ici
];
```

### 6. Mettre à jour la logique de détection (optionnel)

Si vous voulez une détection automatique pour cette langue :

```php
private function determineLocale() {
    $wpLocale = get_locale();
    
    // Français
    if (strpos($wpLocale, 'fr') === 0) {
        return 'fr_FR';
    }
    
    // Espagnol (nouveau)
    if (strpos($wpLocale, 'es') === 0) {
        return 'es_ES';
    }
    
    // Par défaut : anglais
    return 'en_US';
}
```

## Outils CLI

### Script i18n.php

Le script `bin/i18n.php` fournit plusieurs commandes :

```bash
# Aide
php bin/i18n.php help

# Générer le fichier POT (nécessite wp-cli)
php bin/i18n.php generate-pot

# Générer les fichiers MO
php bin/i18n.php generate-mo [locale]

# Scanner les chaînes traduisibles
php bin/i18n.php scan

# Statistiques
php bin/i18n.php stats [locale]
```

### Commandes Composer

```bash
# Générer les fichiers MO
composer i18n:generate-mo

# Scanner le code
composer i18n:scan

# Statistiques
composer i18n:stats
```

## Workflow de traduction

### Pour les développeurs

1. **Ajouter des chaînes traduisibles dans le code**
   ```php
   echo esc_html__('New feature', 'simple-custom-fields');
   ```

2. **Scanner pour vérifier**
   ```bash
   composer i18n:scan
   ```

3. **Mettre à jour le fichier POT** (avec wp-cli)
   ```bash
   wp i18n make-pot . languages/simple-custom-fields.pot
   ```

4. **Mettre à jour les fichiers PO**
   - Ouvrir chaque fichier `.po`
   - Ajouter les nouvelles traductions
   - Sauvegarder

5. **Générer les fichiers MO**
   ```bash
   composer i18n:generate-mo
   ```

6. **Tester**
   - Changer la langue de WordPress
   - Vérifier que les traductions s'affichent correctement

### Pour les traducteurs

1. **Obtenir le fichier PO**
   ```
   languages/simple-custom-fields-[locale].po
   ```

2. **Utiliser un éditeur de traduction**
   - [Poedit](https://poedit.net/) (recommandé)
   - [Loco Translate](https://wordpress.org/plugins/loco-translate/) (plugin WordPress)
   - Éditeur de texte

3. **Traduire les chaînes**
   ```po
   msgid "Field Groups"
   msgstr "Votre traduction"
   ```

4. **Sauvegarder et générer le MO**
   - Poedit génère automatiquement le `.mo`
   - Ou utiliser : `php bin/i18n.php generate-mo [locale]`

5. **Tester la traduction**

## Bonnes pratiques

### ✅ À faire

- Toujours utiliser le text domain `'simple-custom-fields'`
- Échapper les sorties avec `esc_html__()` ou `esc_attr__()`
- Utiliser des chaînes complètes, pas de concaténation
- Fournir du contexte avec `_x()` si nécessaire
- Utiliser `_n()` pour les pluriels

### ❌ À éviter

```php
// ❌ Mauvais : concaténation
echo __('Hello', 'simple-custom-fields') . ' ' . $name;

// ✅ Bon : utiliser sprintf
echo sprintf(__('Hello %s', 'simple-custom-fields'), $name);

// ❌ Mauvais : text domain incorrect
__('Text', 'wrong-domain');

// ✅ Bon : text domain correct
__('Text', 'simple-custom-fields');

// ❌ Mauvais : pas d'échappement
echo __('Text', 'simple-custom-fields');

// ✅ Bon : avec échappement
echo esc_html__('Text', 'simple-custom-fields');
```

## Débogage

### Vérifier la langue chargée

```php
// Obtenir des informations de débogage
$i18n = SCF\Utilities\I18nManager::getInstance();
$debug = $i18n->getDebugInfo();
print_r($debug);
```

### Vérifier les fichiers

```php
$i18n = SCF\Utilities\I18nManager::getInstance();
$files = $i18n->checkTranslationFiles('fr_FR');
print_r($files);
/*
Array (
    [po_exists] => true
    [mo_exists] => true
    [po_path] => /path/to/languages/simple-custom-fields-fr_FR.po
    [mo_path] => /path/to/languages/simple-custom-fields-fr_FR.mo
)
*/
```

### Activer le mode debug

Dans `wp-config.php` :

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Les messages de débogage seront dans `wp-content/debug.log` :

```
[SCF I18n] INFO: Traductions chargées pour: fr_FR
```

## Ressources

### Outils recommandés

- **[Poedit](https://poedit.net/)** - Éditeur de fichiers PO/MO
- **[Loco Translate](https://wordpress.org/plugins/loco-translate/)** - Plugin WordPress pour traduire
- **[WP-CLI](https://wp-cli.org/)** - Outil en ligne de commande WordPress

### Documentation WordPress

- [I18n for WordPress Developers](https://developer.wordpress.org/plugins/internationalization/)
- [How to Internationalize Your Plugin](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/)
- [Localization](https://developer.wordpress.org/plugins/internationalization/localization/)

### Standards

- [GNU gettext](https://www.gnu.org/software/gettext/)
- [Portable Object (PO) format](https://www.gnu.org/software/gettext/manual/html_node/PO-Files.html)

## Support

Pour toute question sur les traductions :

- 📧 Email : akrem.belkahla@infinityweb.tn
- 🌐 Site : https://infinityweb.tn
- 📝 Issues : Créez une issue sur le dépôt GitHub

## Contribuer

Vous souhaitez ajouter une traduction dans votre langue ?

1. Forkez le projet
2. Créez les fichiers de traduction
3. Testez la traduction
4. Soumettez une Pull Request

Nous acceptons avec plaisir les contributions de traduction !

---

**Créé avec ❤️ par Infinity Web**
