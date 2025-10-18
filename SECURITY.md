# Sécurité - Simple Custom Fields

## 🔒 Mesures de sécurité implémentées

### 1. Protection CSRF (Cross-Site Request Forgery)
- **Nonces WordPress** : Tous les formulaires et requêtes AJAX utilisent des nonces uniques
- **Durée de vie** : 12 heures (43200 secondes)
- **Vérification stricte** : Validation obligatoire avant toute action sensible

### 2. Validation des permissions
- **Capability check** : `manage_options` requis pour toutes les actions admin
- **Vérification utilisateur** : Authentification obligatoire
- **Logging** : Tentatives d'accès non autorisées enregistrées

### 3. Sanitization des données
Toutes les entrées utilisateur sont nettoyées selon leur type :
- `sanitize_text_field()` pour les textes simples
- `sanitize_textarea_field()` pour les zones de texte
- `sanitize_email()` pour les emails
- `esc_url_raw()` pour les URLs
- `sanitize_key()` pour les clés/identifiants
- `intval()` / `floatval()` pour les nombres

### 4. Échappement des sorties
Toutes les données affichées sont échappées :
- `esc_html()` pour le HTML
- `esc_attr()` pour les attributs
- `esc_url()` pour les URLs
- `esc_js()` pour le JavaScript
- `wp_kses_post()` pour le contenu HTML autorisé

### 5. Protection AJAX
- Vérification de `DOING_AJAX`
- Validation du referer HTTP
- Actions autorisées en liste blanche
- Rate limiting (50 requêtes/heure/utilisateur)

### 6. Rate Limiting
- **Limite** : 50 requêtes par heure et par utilisateur
- **Stockage** : Transients WordPress
- **Logging** : Dépassements enregistrés

### 7. Headers de sécurité HTTP
```
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
```

### 8. Protection des fichiers
- `.htaccess` pour bloquer l'accès aux fichiers sensibles
- Désactivation de l'exécution PHP dans les dossiers uploads/assets
- Blocage de l'indexation des répertoires

### 9. Validation des IDs
- Vérification de l'existence des posts
- Validation du type de post
- Protection contre les injections

### 10. Logging de sécurité
Événements enregistrés (en mode WP_DEBUG) :
- Tentatives d'accès non autorisées
- Nonces invalides
- Rate limit dépassé
- Actions suspectes

## 🛡️ Classe SCF_Security

La classe centralisée `SCF_Security` gère toutes les vérifications :

```php
$security = SCF_Security::get_instance();

// Vérifier les permissions
$security->check_permissions('manage_options');

// Vérifier un nonce
$security->verify_nonce($nonce, 'action_name');

// Vérifier une requête AJAX complète
$security->verify_ajax_request('action', $nonce);

// Sanitize des données
$clean_data = $security->sanitize_array($data, 'text');

// Échapper pour l'affichage
$safe_output = $security->escape_output($data, 'html');

// Valider un ID de post
$post = $security->validate_post_id($id, 'scf-field-group');
```

## 🔐 Bonnes pratiques

### Pour les développeurs

1. **Toujours vérifier les permissions**
```php
if (!$this->security->check_permissions('manage_options')) {
    wp_die('Accès refusé');
}
```

2. **Toujours valider les nonces**
```php
if (!$this->security->verify_nonce($_POST['nonce'], 'action')) {
    wp_die('Nonce invalide');
}
```

3. **Toujours sanitize les entrées**
```php
$value = $this->security->sanitize_value($_POST['field'], 'text');
```

4. **Toujours échapper les sorties**
```php
echo $this->security->escape_output($value, 'html');
```

5. **Utiliser la validation complète pour AJAX**
```php
if (!$this->security->verify_ajax_request('action', $nonce)) {
    wp_send_json_error('Sécurité échouée');
}
```

### Pour les utilisateurs

1. **Gardez WordPress à jour**
2. **Utilisez des mots de passe forts**
3. **Limitez les utilisateurs avec capability `manage_options`**
4. **Activez HTTPS sur votre site**
5. **Faites des sauvegardes régulières**

## 🚨 Signaler une vulnérabilité

Si vous découvrez une faille de sécurité :

1. **NE PAS** créer d'issue publique
2. Envoyer un email à : akrem.belkahla@infinityweb.tn
3. Inclure :
   - Description détaillée
   - Steps to reproduce
   - Impact potentiel
   - Version affectée

Nous nous engageons à :
- Répondre sous 48h
- Corriger sous 7 jours (vulnérabilités critiques)
- Créditer le découvreur (si souhaité)

## 📋 Checklist de sécurité

- [x] Protection CSRF avec nonces
- [x] Validation des permissions utilisateur
- [x] Sanitization de toutes les entrées
- [x] Échappement de toutes les sorties
- [x] Protection contre les injections SQL (prepared statements)
- [x] Rate limiting sur les requêtes AJAX
- [x] Headers de sécurité HTTP
- [x] Protection des fichiers sensibles
- [x] Validation des IDs et types de posts
- [x] Logging des événements de sécurité
- [x] Vérification du referer HTTP
- [x] Protection contre XSS
- [x] Protection contre CSRF
- [x] Protection contre les injections
- [x] Classe de sécurité centralisée

## 📚 Ressources

- [WordPress Security Handbook](https://developer.wordpress.org/plugins/security/)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [WordPress Nonces](https://developer.wordpress.org/plugins/security/nonces/)
- [Data Validation](https://developer.wordpress.org/plugins/security/data-validation/)

## 🔄 Mises à jour

### Version 1.4.1 (Octobre 2025)
- ✅ Ajout de la classe `SCF_Security` centralisée
- ✅ Implémentation du rate limiting
- ✅ Headers de sécurité HTTP
- ✅ Protection .htaccess
- ✅ Logging des événements de sécurité
- ✅ Validation stricte des requêtes AJAX

---

**Auteur** : Akrem Belkahla  
**Agence** : Infinity Web  
**Email** : akrem.belkahla@infinityweb.tn  
**Version** : 1.4.1
