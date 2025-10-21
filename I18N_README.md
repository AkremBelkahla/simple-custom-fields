# 🌍 Système de Traduction - Simple Custom Fields

## 🚀 Démarrage Rapide

### Langues disponibles

- 🇫🇷 **Français** - Automatique si WordPress est en français
- 🇬🇧 **Anglais** - Langue par défaut pour les autres langues

### Fonctionnement automatique

Le plugin détecte automatiquement la langue de WordPress :

- WordPress en français → Plugin en français
- Autres langues → Plugin en anglais

**Aucune configuration nécessaire !**

## 📁 Fichiers de traduction

```
languages/
├── simple-custom-fields.pot          # Template
├── simple-custom-fields-fr_FR.po     # Français (source)
├── simple-custom-fields-fr_FR.mo     # Français (compilé)
├── simple-custom-fields-en_US.po     # Anglais (source)
└── simple-custom-fields-en_US.mo     # Anglais (compilé)
```

## 🛠️ Commandes disponibles

### Générer les fichiers MO

```bash
# Via Composer
composer i18n:generate-mo

# Via le script
php bin/i18n.php generate-mo
```

### Scanner les chaînes traduisibles

```bash
# Via Composer
composer i18n:scan

# Via le script
php bin/i18n.php scan
```

### Statistiques de traduction

```bash
# Via Composer
composer i18n:stats fr_FR

# Via le script
php bin/i18n.php stats fr_FR
```

## 💻 Utilisation dans le code

### PHP

```php
// Traduction simple
__('Text', 'simple-custom-fields');

// Avec échappement HTML
esc_html__('Text', 'simple-custom-fields');

// Affichage direct
esc_html_e('Text', 'simple-custom-fields');

// Avec contexte
_x('Post', 'noun', 'simple-custom-fields');

// Pluriels
_n('One field', '%s fields', $count, 'simple-custom-fields');
```

### Utilisation de I18nManager

```php
$i18n = SCF\Utilities\I18nManager::getInstance();

// Obtenir la locale actuelle
$locale = $i18n->getCurrentLocale(); // 'fr_FR' ou 'en_US'

// Obtenir le nom de la langue
$language = $i18n->getCurrentLanguageName(); // 'Français' ou 'English'

// Statistiques
$stats = $i18n->getTranslationStats('fr_FR');
```

## ➕ Ajouter une nouvelle langue

### 1. Créer les fichiers

```bash
# Copier le template
cp languages/simple-custom-fields.pot languages/simple-custom-fields-es_ES.po
```

### 2. Traduire

Utilisez [Poedit](https://poedit.net/) ou un éditeur de texte pour traduire les chaînes.

### 3. Générer le fichier MO

```bash
php bin/i18n.php generate-mo es_ES
```

### 4. Ajouter dans I18nManager

Éditez `includes/Utilities/I18nManager.php` :

```php
private $supportedLocales = [
    'fr_FR' => 'Français',
    'en_US' => 'English',
    'es_ES' => 'Español'  // Nouvelle langue
];
```

## 📊 Statistiques actuelles

### Français (fr_FR)
- ✅ 100% traduit
- 150+ chaînes

### Anglais (en_US)
- ✅ 100% traduit
- 150+ chaînes

## 📚 Documentation complète

Pour plus de détails, consultez :

- 📖 [Guide complet d'internationalisation](docs/I18N_GUIDE.md)
- 🔧 [Documentation de I18nManager](includes/Utilities/I18nManager.php)
- 🛠️ [Script CLI i18n](bin/i18n.php)

## 🎯 Workflow de traduction

### Pour les développeurs

1. Ajouter des chaînes traduisibles dans le code
2. Scanner : `composer i18n:scan`
3. Mettre à jour les fichiers PO
4. Générer les MO : `composer i18n:generate-mo`
5. Tester

### Pour les traducteurs

1. Ouvrir le fichier `.po` avec Poedit
2. Traduire les chaînes
3. Sauvegarder (génère automatiquement le `.mo`)
4. Tester dans WordPress

## ✅ Bonnes pratiques

### À faire

- ✅ Toujours utiliser `'simple-custom-fields'` comme text domain
- ✅ Échapper les sorties avec `esc_html__()` ou `esc_attr__()`
- ✅ Utiliser des chaînes complètes
- ✅ Utiliser `sprintf()` pour les variables

### À éviter

- ❌ Concaténer des chaînes traduites
- ❌ Oublier le text domain
- ❌ Ne pas échapper les sorties
- ❌ Utiliser des chaînes fragmentées

## 🔍 Débogage

### Vérifier la langue chargée

```php
$i18n = SCF\Utilities\I18nManager::getInstance();
$debug = $i18n->getDebugInfo();
print_r($debug);
```

### Activer les logs

Dans `wp-config.php` :

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Les logs seront dans `wp-content/debug.log`.

## 🤝 Contribuer

Vous souhaitez ajouter une traduction ?

1. Forkez le projet
2. Créez les fichiers de traduction
3. Testez
4. Soumettez une Pull Request

## 📞 Support

- 📧 Email : akrem.belkahla@infinityweb.tn
- 🌐 Site : https://infinityweb.tn

## 🔗 Ressources

- [Poedit](https://poedit.net/) - Éditeur de traductions
- [Loco Translate](https://wordpress.org/plugins/loco-translate/) - Plugin WordPress
- [WP-CLI](https://wp-cli.org/) - Outils en ligne de commande
- [WordPress I18n](https://developer.wordpress.org/plugins/internationalization/)

---

**Simple Custom Fields** - Traductions gérées avec ❤️ par Infinity Web
