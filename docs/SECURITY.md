# Guide de Sécurité - Simple Custom Fields

## 🔒 Vue d'ensemble

Ce document décrit les mesures de sécurité implémentées dans Simple Custom Fields et les bonnes pratiques à suivre.

## 🛡️ Couches de sécurité

### 1. Protection CSRF (Cross-Site Request Forgery)

#### Nonces WordPress

Chaque action AJAX utilise un nonce spécifique :

```php
// Création du nonce (côté serveur)
$nonce = wp_create_nonce('scf_delete_group');

// Vérification du nonce
wp_verify_nonce($nonce, 'scf_delete_group');
```

**IMPORTANT** : L'action utilisée pour créer le nonce DOIT être exactement la même que celle utilisée pour le vérifier.

#### Nonces par action

```javascript
// Côté JavaScript
scf_vars.nonces = {
    delete_group: 'abc123...',
    get_field_settings: 'def456...',
    save_field_group: 'ghi789...'
}

// Utilisation
nonce: scf_vars.nonces.delete_group
```

### 2. Vérification des permissions

```php
// Vérifier que l'utilisateur a les droits nécessaires
if (!current_user_can('manage_options')) {
    wp_die(__('Permission refusée', 'simple-custom-fields'));
}
```

### 3. Validation des entrées

#### Validation stricte par type

```php
use SCF\Validators\FieldValidator;

$validator = FieldValidator::get_instance();

// Valider un email
$result = $validator->validate($email, 'email', array('required' => true));

if ($result !== true) {
    // $result contient les erreurs
    foreach ($result as $error) {
        // Traiter l'erreur
    }
}
```

#### Types de validation disponibles

- **text** : Texte simple avec longueur max
- **textarea** : Texte multiligne
- **email** : Adresse email valide
- **url** : URL valide
- **number** : Nombre avec min/max optionnels
- **date** : Date au format YYYY-MM-DD
- **select/radio** : Choix dans une liste
- **checkbox** : Sélections multiples
- **file** : ID d'attachement valide

### 4. Sanitization des données

#### Sanitization automatique

```php
// Le validateur sanitize automatiquement selon le type
$clean_value = $validator->sanitize($value, 'email');
```

#### Fonctions de sanitization WordPress

```php
// Texte simple
$clean = sanitize_text_field($input);

// Email
$clean = sanitize_email($input);

// URL
$clean = esc_url_raw($input);

// Textarea
$clean = sanitize_textarea_field($input);

// Clé (slug)
$clean = sanitize_key($input);

// HTML autorisé
$clean = wp_kses_post($input);
```

### 5. Échappement des sorties

**TOUJOURS** échapper les données avant affichage :

```php
// Texte simple
echo esc_html($value);

// Attribut HTML
echo '<div data-value="' . esc_attr($value) . '">';

// URL
echo '<a href="' . esc_url($value) . '">';

// JavaScript
echo '<script>var data = "' . esc_js($value) . '";</script>';

// Textarea
echo '<textarea>' . esc_textarea($value) . '</textarea>';
```

### 6. Rate Limiting

Protection contre les abus :

```php
// Limite : 50 requêtes par heure par utilisateur
if (!$this->check_rate_limit()) {
    wp_die(__('Trop de requêtes. Veuillez réessayer plus tard.', 'simple-custom-fields'));
}
```

### 7. Headers de sécurité HTTP

```php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
```

## 🔐 Bonnes pratiques

### Requêtes AJAX sécurisées

#### Côté serveur (PHP)

```php
public function ajax_handler() {
    // 1. Vérifier que c'est une requête AJAX
    if (!wp_doing_ajax()) {
        wp_die();
    }
    
    // 2. Vérifier les permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(__('Permission refusée', 'simple-custom-fields'));
    }
    
    // 3. Vérifier le nonce
    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
    if (!wp_verify_nonce($nonce, 'scf_my_action')) {
        wp_send_json_error(__('Nonce invalide', 'simple-custom-fields'));
    }
    
    // 4. Valider et sanitizer les données
    $data = isset($_POST['data']) ? sanitize_text_field($_POST['data']) : '';
    
    // 5. Traiter la requête
    // ...
    
    // 6. Retourner la réponse
    wp_send_json_success(array('message' => 'Succès'));
}
```

#### Côté client (JavaScript)

```javascript
jQuery.ajax({
    url: scf_vars.ajax_url,
    type: 'POST',
    data: {
        action: 'scf_my_action',
        nonce: scf_vars.nonces.my_action,
        data: myData
    },
    success: function(response) {
        if (response.success) {
            // Traiter le succès
        } else {
            // Traiter l'erreur
        }
    },
    error: function(xhr, status, error) {
        console.error('Erreur AJAX:', error);
    }
});
```

### Validation de formulaire

```php
public function save_field_group() {
    // 1. Vérifier le nonce
    if (!isset($_POST['scf_nonce']) || !wp_verify_nonce($_POST['scf_nonce'], 'scf_save_field_group')) {
        wp_die(__('Échec de la vérification de sécurité', 'simple-custom-fields'));
    }
    
    // 2. Vérifier les permissions
    if (!current_user_can('manage_options')) {
        wp_die(__('Permission refusée', 'simple-custom-fields'));
    }
    
    // 3. Valider les données requises
    if (empty($_POST['title'])) {
        wp_die(__('Le titre est requis', 'simple-custom-fields'));
    }
    
    // 4. Sanitizer toutes les entrées
    $title = sanitize_text_field($_POST['title']);
    $description = sanitize_textarea_field($_POST['description']);
    
    // 5. Valider les données
    $validator = FieldValidator::get_instance();
    // ...
    
    // 6. Sauvegarder
    // ...
}
```

### Requêtes de base de données

**TOUJOURS** utiliser des requêtes préparées :

```php
global $wpdb;

// ✅ BON - Requête préparée
$results = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}scf_fields WHERE post_id = %d AND field_name = %s",
    $post_id,
    $field_name
));

// ❌ MAUVAIS - Injection SQL possible
$results = $wpdb->get_results(
    "SELECT * FROM {$wpdb->prefix}scf_fields WHERE post_id = $post_id"
);
```

## 🚨 Gestion des erreurs

### Logging sécurisé

```php
use SCF\Utilities\Logger;

$logger = Logger::get_instance();

// Ne jamais logger de données sensibles (mots de passe, tokens, etc.)
$logger->error('Échec de connexion', array(
    'user_id' => $user_id,
    'ip' => $ip_address,
    // ❌ Ne PAS logger : 'password' => $password
));
```

### Messages d'erreur

```php
// ✅ BON - Message générique pour l'utilisateur
wp_send_json_error(__('Une erreur est survenue', 'simple-custom-fields'));

// ❌ MAUVAIS - Révèle des informations système
wp_send_json_error('MySQL Error: ' . $wpdb->last_error);
```

## 🔍 Audit de sécurité

### Checklist

- [ ] Tous les nonces sont vérifiés
- [ ] Les permissions sont vérifiées
- [ ] Les entrées sont validées
- [ ] Les entrées sont sanitizées
- [ ] Les sorties sont échappées
- [ ] Les requêtes SQL sont préparées
- [ ] Le rate limiting est actif
- [ ] Les erreurs sont loggées de manière sécurisée
- [ ] Les données sensibles ne sont pas exposées
- [ ] Les headers de sécurité sont définis

### Outils de test

```bash
# Vérifier le code avec PHPCS
composer phpcs

# Analyse statique avec PHPStan
composer phpstan

# Tests de sécurité
composer test
```

## 📚 Ressources

- [WordPress Nonces](https://developer.wordpress.org/plugins/security/nonces/)
- [Data Validation](https://developer.wordpress.org/plugins/security/data-validation/)
- [Securing Input](https://developer.wordpress.org/plugins/security/securing-input/)
- [Securing Output](https://developer.wordpress.org/plugins/security/securing-output/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)

## 🐛 Signaler une vulnérabilité

Si vous découvrez une vulnérabilité de sécurité, veuillez l'envoyer par email à :
**akrem.belkahla@infinityweb.tn**

**Ne créez PAS d'issue publique pour les problèmes de sécurité.**
