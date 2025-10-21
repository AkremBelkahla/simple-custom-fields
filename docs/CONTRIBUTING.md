# Guide de Contribution - Simple Custom Fields

## 🎯 Bienvenue

Merci de votre intérêt pour contribuer à Simple Custom Fields ! Ce guide vous aidera à contribuer efficacement au projet.

## 📋 Table des matières

- [Code de conduite](#code-de-conduite)
- [Comment contribuer](#comment-contribuer)
- [Standards de code](#standards-de-code)
- [Processus de développement](#processus-de-développement)
- [Tests](#tests)
- [Documentation](#documentation)

## 🤝 Code de conduite

### Nos engagements

- Respecter tous les contributeurs
- Accepter les critiques constructives
- Se concentrer sur ce qui est meilleur pour la communauté
- Faire preuve d'empathie envers les autres

## 💡 Comment contribuer

### Signaler un bug

1. Vérifiez que le bug n'a pas déjà été signalé
2. Créez une issue avec le template "Bug Report"
3. Incluez :
   - Description détaillée du problème
   - Étapes pour reproduire
   - Comportement attendu vs actuel
   - Version de WordPress et PHP
   - Captures d'écran si pertinent

### Proposer une fonctionnalité

1. Créez une issue avec le template "Feature Request"
2. Décrivez clairement :
   - Le problème que cela résout
   - La solution proposée
   - Les alternatives considérées

### Soumettre une Pull Request

1. **Fork** le projet
2. **Créez une branche** : `git checkout -b feature/ma-fonctionnalite`
3. **Commitez** vos changements : `git commit -m 'Ajout de ma fonctionnalité'`
4. **Pushez** : `git push origin feature/ma-fonctionnalite`
5. **Ouvrez une Pull Request**

## 📝 Standards de code

### PHP

#### Standards WordPress

Nous suivons les [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/).

```bash
# Vérifier le code
composer phpcs

# Corriger automatiquement
composer phpcbf
```

#### Conventions de nommage

```php
// Classes : PascalCase
class FieldValidator {}

// Méthodes : snake_case
public function validate_field() {}

// Variables : snake_case
$field_value = '';

// Constantes : UPPER_SNAKE_CASE
define('SCF_VERSION', '1.5.0');
```

#### Documentation PHPDoc

```php
/**
 * Description courte de la fonction
 *
 * Description détaillée si nécessaire.
 *
 * @since 1.5.0
 *
 * @param string $param1 Description du paramètre
 * @param int    $param2 Description du paramètre
 * @return bool True si succès, false sinon
 */
public function my_function($param1, $param2) {
    // Code
}
```

#### Sécurité

**TOUJOURS** :
- Vérifier les nonces
- Vérifier les permissions
- Sanitizer les entrées
- Échapper les sorties
- Utiliser des requêtes préparées

```php
// ✅ BON
$value = sanitize_text_field($_POST['value']);
echo esc_html($value);

// ❌ MAUVAIS
echo $_POST['value'];
```

### JavaScript

#### Standards

```javascript
// Utiliser 'use strict'
'use strict';

// Variables : camelCase
var fieldValue = '';

// Constantes : UPPER_SNAKE_CASE
const MAX_LENGTH = 255;

// Fonctions : camelCase
function validateField() {}
```

#### jQuery

```javascript
// Préfixer les variables jQuery avec $
var $element = jQuery('#my-element');

// Utiliser jQuery au lieu de $
jQuery(document).ready(function($) {
    // Code
});
```

### CSS

#### Conventions BEM

```css
/* Block */
.scf-field {}

/* Element */
.scf-field__label {}

/* Modifier */
.scf-field--required {}
```

#### Organisation

```css
/* 1. Positionnement */
position: relative;
top: 0;
left: 0;

/* 2. Box model */
display: block;
width: 100%;
margin: 0;
padding: 10px;

/* 3. Typographie */
font-size: 14px;
line-height: 1.5;

/* 4. Visuel */
background: #fff;
border: 1px solid #ddd;
color: #333;

/* 5. Autres */
cursor: pointer;
```

## 🔄 Processus de développement

### Configuration de l'environnement

```bash
# Cloner le repository
git clone https://github.com/infinityweb/simple-custom-fields.git
cd simple-custom-fields

# Installer les dépendances
composer install
npm install

# Créer une branche
git checkout -b feature/ma-fonctionnalite
```

### Workflow Git

#### Branches

- `main` : Production stable
- `develop` : Développement actif
- `feature/*` : Nouvelles fonctionnalités
- `bugfix/*` : Corrections de bugs
- `hotfix/*` : Corrections urgentes

#### Messages de commit

Format : `type(scope): description`

Types :
- `feat` : Nouvelle fonctionnalité
- `fix` : Correction de bug
- `docs` : Documentation
- `style` : Formatage
- `refactor` : Refactorisation
- `test` : Tests
- `chore` : Maintenance

Exemples :
```
feat(validator): ajout validation pour type color
fix(security): correction bug nonce delete_group
docs(api): mise à jour documentation hooks
```

### Développement

1. **Écrire les tests** en premier (TDD)
2. **Implémenter** la fonctionnalité
3. **Vérifier** le code avec les linters
4. **Tester** manuellement
5. **Documenter** les changements

## 🧪 Tests

### Tests unitaires

```bash
# Tous les tests
composer test

# Tests unitaires uniquement
composer test-unit

# Tests d'intégration
composer test-integration

# Avec couverture
composer test -- --coverage-html coverage/
```

### Écrire un test

```php
namespace SCF\Tests\Unit\Validators;

use PHPUnit\Framework\TestCase;
use SCF\Validators\FieldValidator;

class FieldValidatorTest extends TestCase {
    private $validator;
    
    protected function setUp(): void {
        parent::setUp();
        $this->validator = FieldValidator::get_instance();
    }
    
    public function test_validate_email() {
        $result = $this->validator->validate('test@example.com', 'email');
        $this->assertTrue($result);
    }
}
```

### Couverture de code

Objectif : **80% minimum**

## 📚 Documentation

### Code

- Documenter toutes les classes publiques
- Documenter toutes les méthodes publiques
- Ajouter des exemples pour les fonctions complexes
- Maintenir les PHPDoc à jour

### Fichiers

- `README.md` : Vue d'ensemble
- `CHANGELOG.md` : Historique des versions
- `docs/ARCHITECTURE.md` : Architecture technique
- `docs/API.md` : API publique
- `docs/SECURITY.md` : Guide de sécurité

### Mise à jour

Lors de l'ajout d'une fonctionnalité :

1. Mettre à jour `CHANGELOG.md`
2. Mettre à jour `docs/API.md` si API publique
3. Ajouter des exemples d'utilisation
4. Mettre à jour `README.md` si nécessaire

## 🎨 Style Guide

### PHP

```php
<?php
/**
 * Description du fichier
 *
 * @package SimpleCustomFields
 * @subpackage Validators
 * @since 1.5.0
 */

namespace SCF\Validators;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe de validation
 */
class FieldValidator {
    /**
     * Instance unique
     *
     * @var FieldValidator|null
     */
    private static $instance = null;
    
    /**
     * Récupère l'instance
     *
     * @return FieldValidator
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
```

### JavaScript

```javascript
/**
 * Module de validation
 *
 * @since 1.5.0
 */
(function($) {
    'use strict';
    
    /**
     * Valide un champ
     *
     * @param {string} value - Valeur à valider
     * @param {string} type - Type de champ
     * @return {boolean} True si valide
     */
    function validateField(value, type) {
        // Code
    }
    
})(jQuery);
```

## ✅ Checklist avant PR

- [ ] Le code suit les standards WordPress
- [ ] Tous les tests passent
- [ ] La couverture de code est maintenue
- [ ] La documentation est à jour
- [ ] Le CHANGELOG est mis à jour
- [ ] Pas de console.log ou error_log oubliés
- [ ] Les traductions sont à jour
- [ ] Testé sur différentes versions de WordPress
- [ ] Testé sur différentes versions de PHP

## 🏷️ Versioning

Nous suivons le [Semantic Versioning](https://semver.org/) :

- **MAJOR** : Changements incompatibles
- **MINOR** : Nouvelles fonctionnalités compatibles
- **PATCH** : Corrections de bugs

Exemple : `1.5.2`
- 1 : Version majeure
- 5 : Version mineure
- 2 : Patch

## 📞 Contact

- **Email** : akrem.belkahla@infinityweb.tn
- **Site** : https://infinityweb.tn
- **GitHub** : https://github.com/infinityweb

## 📄 Licence

En contribuant, vous acceptez que vos contributions soient sous licence GPL-2.0-or-later.

---

**Merci de contribuer à Simple Custom Fields ! 🎉**
