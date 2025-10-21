# Documentation API - Simple Custom Fields

## 📖 Introduction

Cette documentation décrit l'API publique du plugin Simple Custom Fields pour les développeurs WordPress.

## 🔧 Fonctions publiques

### scf_get_field()

Récupère la valeur d'un champ personnalisé.

```php
scf_get_field( string $field_name, int $post_id = null ): mixed
```

**Paramètres:**
- `$field_name` (string) : Nom du champ à récupérer
- `$post_id` (int|null) : ID du post (null = post actuel)

**Retour:**
- (mixed) : Valeur du champ formatée ou null si non trouvé

**Exemple:**

```php
// Dans une boucle WordPress
$email = scf_get_field('contact_email');
if ($email) {
    echo $email; // Déjà échappé et formaté
}

// Pour un post spécifique
$phone = scf_get_field('phone_number', 123);
```

### scf_get_raw_field()

Récupère la valeur brute d'un champ (non formatée).

```php
scf_get_raw_field( string $field_name, int $post_id = null ): mixed
```

**Paramètres:**
- `$field_name` (string) : Nom du champ
- `$post_id` (int|null) : ID du post

**Retour:**
- (mixed) : Valeur brute du champ

**Exemple:**

```php
$raw_value = scf_get_raw_field('my_field');
// Traitement personnalisé de la valeur
```

### scf_update_field()

Met à jour la valeur d'un champ.

```php
scf_update_field( string $field_name, mixed $value, int $post_id = null ): bool
```

**Paramètres:**
- `$field_name` (string) : Nom du champ
- `$value` (mixed) : Nouvelle valeur
- `$post_id` (int|null) : ID du post

**Retour:**
- (bool) : True si succès, false sinon

**Exemple:**

```php
scf_update_field('contact_email', 'nouveau@email.com', 123);
```

### scf_delete_field()

Supprime un champ personnalisé.

```php
scf_delete_field( string $field_name, int $post_id = null ): bool
```

**Paramètres:**
- `$field_name` (string) : Nom du champ
- `$post_id` (int|null) : ID du post

**Retour:**
- (bool) : True si succès, false sinon

## 🎨 Shortcodes

### [scf_fields]

Affiche tous les champs d'un groupe pour le post actuel.

```php
[scf_fields]
```

**Attributs:**
- `group` (string) : Slug du groupe de champs (optionnel)
- `post_id` (int) : ID du post (optionnel)

**Exemples:**

```php
// Tous les groupes du post actuel
[scf_fields]

// Groupe spécifique
[scf_fields group="informations-contact"]

// Post spécifique
[scf_fields post_id="123"]
```

### [scf_field]

Affiche un champ spécifique.

```php
[scf_field name="field_name"]
```

**Attributs:**
- `name` (string) : Nom du champ (requis)
- `post_id` (int) : ID du post (optionnel)
- `format` (string) : Format d'affichage (optionnel)

**Exemples:**

```php
[scf_field name="contact_email"]
[scf_field name="phone" post_id="123"]
```

## 🪝 Actions (Hooks)

### scf_group_created

Déclenché après la création d'un groupe de champs.

```php
do_action( 'scf_group_created', int $group_id, array $data );
```

**Paramètres:**
- `$group_id` (int) : ID du groupe créé
- `$data` (array) : Données du groupe

**Exemple:**

```php
add_action('scf_group_created', function($group_id, $data) {
    error_log("Nouveau groupe créé : {$group_id}");
}, 10, 2);
```

### scf_group_updated

Déclenché après la mise à jour d'un groupe.

```php
do_action( 'scf_group_updated', int $group_id, array $data );
```

### scf_group_deleted

Déclenché après la suppression d'un groupe.

```php
do_action( 'scf_group_deleted', int $group_id );
```

### scf_field_saved

Déclenché après la sauvegarde d'une valeur de champ.

```php
do_action( 'scf_field_saved', int $post_id, string $field_name, mixed $value );
```

**Exemple:**

```php
add_action('scf_field_saved', function($post_id, $field_name, $value) {
    if ($field_name === 'contact_email') {
        // Envoyer une notification
        wp_mail('admin@site.com', 'Email mis à jour', $value);
    }
}, 10, 3);
```

## 🎯 Filtres (Filters)

### scf_config

Modifie la configuration du plugin.

```php
apply_filters( 'scf_config', array $config ): array
```

**Exemple:**

```php
add_filter('scf_config', function($config) {
    $config['security']['max_attempts_per_hour'] = 100;
    return $config;
});
```

### scf_validation_rules

Modifie les règles de validation des champs.

```php
apply_filters( 'scf_validation_rules', array $rules ): array
```

**Exemple:**

```php
add_filter('scf_validation_rules', function($rules) {
    $rules['custom_type'] = array(
        'sanitize' => 'my_custom_sanitize',
        'validate' => 'my_custom_validate',
    );
    return $rules;
});
```

### scf_field_types

Ajoute ou modifie des types de champs.

```php
apply_filters( 'scf_field_types', array $types ): array
```

**Exemple:**

```php
add_filter('scf_field_types', function($types) {
    $types['color'] = array(
        'label' => 'Couleur',
        'icon' => 'dashicons-art',
        'category' => 'basic',
    );
    return $types;
});
```

### scf_field_value

Modifie la valeur d'un champ avant affichage.

```php
apply_filters( 'scf_field_value', mixed $value, string $field_name, int $post_id ): mixed
```

**Exemple:**

```php
add_filter('scf_field_value', function($value, $field_name, $post_id) {
    if ($field_name === 'price') {
        return number_format($value, 2) . ' €';
    }
    return $value;
}, 10, 3);
```

### scf_sanitize_field_value

Personnalise la sanitization d'un champ.

```php
apply_filters( 'scf_sanitize_field_value', mixed $value, string $type ): mixed
```

## 🔌 Classes publiques

### SCF\Services\FieldGroupService

Service de gestion des groupes de champs.

```php
use SCF\Services\FieldGroupService;

$service = FieldGroupService::get_instance();

// Créer un groupe
$group_id = $service->create_group(array(
    'title' => 'Mon groupe',
    'fields' => array(/* ... */),
    'rules' => array(/* ... */),
));

// Récupérer un groupe
$group = $service->get_group($group_id);

// Mettre à jour un groupe
$service->update_group($group_id, array(
    'title' => 'Nouveau titre',
));

// Supprimer un groupe
$service->delete_group($group_id);

// Dupliquer un groupe
$new_id = $service->duplicate_group($group_id);
```

### SCF\Utilities\Logger

Système de logging.

```php
use SCF\Utilities\Logger;

$logger = Logger::get_instance();

$logger->debug('Message de debug');
$logger->info('Information');
$logger->warning('Avertissement');
$logger->error('Erreur');
$logger->critical('Critique');
```

### SCF\Validators\FieldValidator

Validation des champs.

```php
use SCF\Validators\FieldValidator;

$validator = FieldValidator::get_instance();

// Valider une valeur
$result = $validator->validate($value, 'email', array(
    'required' => true,
));

if ($result === true) {
    // Valide
} else {
    // $result contient les erreurs
    foreach ($result as $error) {
        echo $error;
    }
}

// Sanitizer une valeur
$clean = $validator->sanitize($value, 'email');
```

## 📊 Exemples d'utilisation

### Afficher des champs dans un template

```php
<?php
// single.php ou page.php

$email = scf_get_field('contact_email');
$phone = scf_get_field('contact_phone');
$address = scf_get_field('contact_address');
?>

<div class="contact-info">
    <?php if ($email): ?>
        <p><strong>Email:</strong> <?php echo $email; ?></p>
    <?php endif; ?>
    
    <?php if ($phone): ?>
        <p><strong>Téléphone:</strong> <?php echo esc_html($phone); ?></p>
    <?php endif; ?>
    
    <?php if ($address): ?>
        <p><strong>Adresse:</strong> <?php echo $address; ?></p>
    <?php endif; ?>
</div>
```

### Créer un groupe de champs par code

```php
use SCF\Services\FieldGroupService;

add_action('init', function() {
    $service = FieldGroupService::get_instance();
    
    $service->create_group(array(
        'title' => 'Informations produit',
        'status' => 'publish',
        'fields' => array(
            array(
                'name' => 'product_price',
                'label' => 'Prix',
                'type' => 'number',
                'settings' => array(
                    'required' => true,
                    'min' => 0,
                ),
            ),
            array(
                'name' => 'product_sku',
                'label' => 'SKU',
                'type' => 'text',
            ),
        ),
        'rules' => array(
            'type' => 'post_type',
            'operator' => '=',
            'value' => 'product',
        ),
    ));
});
```

### Personnaliser l'affichage d'un type de champ

```php
add_filter('scf_field_value', function($value, $field_name, $post_id) {
    // Récupérer les métadonnées du champ
    $groups = get_posts(array(
        'post_type' => 'scf-field-group',
        'posts_per_page' => -1,
    ));
    
    foreach ($groups as $group) {
        $fields = get_post_meta($group->ID, 'scf_fields', true);
        foreach ($fields as $field) {
            if ($field['name'] === $field_name && $field['type'] === 'number') {
                // Formater les nombres avec séparateur de milliers
                return number_format($value, 0, ',', ' ');
            }
        }
    }
    
    return $value;
}, 10, 3);
```

## 🔒 Sécurité

### Vérification des permissions

```php
if (current_user_can('manage_options')) {
    // L'utilisateur peut gérer les groupes de champs
}
```

### Validation et sanitization

Toujours valider et nettoyer les données :

```php
use SCF\Validators\FieldValidator;

$validator = FieldValidator::get_instance();
$clean_value = $validator->sanitize($_POST['field_value'], 'email');
```

### Échappement des sorties

```php
// Pour du texte simple
echo esc_html($value);

// Pour des attributs HTML
echo '<div data-value="' . esc_attr($value) . '">';

// Pour des URLs
echo '<a href="' . esc_url($value) . '">';
```

## 📝 Notes importantes

1. **Toujours échapper les sorties** même si `scf_get_field()` retourne des données formatées
2. **Utiliser les services** plutôt que d'accéder directement à la base de données
3. **Respecter les hooks** pour étendre les fonctionnalités
4. **Valider les entrées** avant de sauvegarder
5. **Logger les erreurs** pour faciliter le débogage
