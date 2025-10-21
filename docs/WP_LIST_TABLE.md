# WP_List_Table - Liste WordPress Native

## Vue d'ensemble

La liste des groupes de champs utilise maintenant la classe native WordPress `WP_List_Table` pour un affichage professionnel et cohérent avec l'interface d'administration de WordPress.

## Architecture

### Fichiers créés

1. **`includes/Admin/SCF_Groups_List_Table.php`**
   - Classe personnalisée étendant `WP_List_Table`
   - Gère l'affichage, le tri, la recherche et les actions groupées

2. **`templates/groups-page-list-table.php`**
   - Template utilisant `WP_List_Table`
   - Remplace l'ancien template `groups-page.php`

3. **`assets/css/list-table.css`**
   - Styles personnalisés pour WP_List_Table
   - Conserve l'apparence du plugin

## Fonctionnalités

### Colonnes affichées

- **Checkbox** : Sélection multiple pour actions groupées
- **Titre** : Nom du groupe avec actions (Modifier, Supprimer)
- **Description** : Description du groupe
- **Champs** : Nombre de champs avec badge
- **Type de contenu** : Type de post associé avec badge
- **Statut** : Actif/Inactif avec badge coloré

### Actions disponibles

#### Actions de ligne
- **Modifier** : Éditer le groupe
- **Supprimer** : Supprimer le groupe (AJAX)

#### Actions groupées
- **Supprimer** : Supprimer plusieurs groupes
- **Activer** : Activer plusieurs groupes
- **Désactiver** : Désactiver plusieurs groupes

### Filtres et recherche

#### Vues (filtres de statut)
- **Tous** : Afficher tous les groupes
- **Actifs** : Groupes publiés uniquement
- **Inactifs** : Groupes en brouillon uniquement

#### Recherche
- Recherche en temps réel dans les titres de groupes
- Intégration native avec WP_List_Table

### Tri

Colonnes triables :
- **Titre** : Ordre alphabétique
- **Statut** : Par statut (actif/inactif)

## Utilisation

### Dans le code

```php
// Créer une instance de la table
require_once SCF_PLUGIN_DIR . 'includes/Admin/SCF_Groups_List_Table.php';
$list_table = new SCF_Groups_List_Table();
$list_table->prepare_items();

// Afficher la table dans le template
$list_table->display();
```

### Personnalisation des colonnes

Pour modifier les colonnes affichées, éditer la méthode `get_columns()` :

```php
public function get_columns() {
    return array(
        'cb'          => '<input type="checkbox" />',
        'title'       => __('Titre', 'simple-custom-fields'),
        'description' => __('Description', 'simple-custom-fields'),
        // Ajouter ou supprimer des colonnes ici
    );
}
```

### Personnalisation des actions

Pour ajouter des actions de ligne, modifier la méthode `column_title()` :

```php
$actions = array(
    'edit' => sprintf('<a href="%s">%s</a>', $edit_url, __('Modifier')),
    'duplicate' => sprintf('<a href="%s">%s</a>', $duplicate_url, __('Dupliquer')),
    // Ajouter des actions ici
);
```

Pour ajouter des actions groupées, modifier `get_bulk_actions()` :

```php
public function get_bulk_actions() {
    return array(
        'delete' => __('Supprimer'),
        'export' => __('Exporter'),
        // Ajouter des actions ici
    );
}
```

## Avantages de WP_List_Table

### Conformité WordPress
- ✅ Interface native et familière
- ✅ Cohérence avec l'administration WordPress
- ✅ Accessibilité intégrée

### Fonctionnalités intégrées
- ✅ Tri automatique des colonnes
- ✅ Pagination native
- ✅ Actions groupées
- ✅ Recherche intégrée
- ✅ Filtres de statut

### Maintenance
- ✅ Code standardisé
- ✅ Compatible avec les futures versions de WordPress
- ✅ Moins de code personnalisé à maintenir

## Styles

Les styles sont organisés en trois fichiers :

1. **`list-table.css`** : Styles spécifiques à WP_List_Table
2. **`table.css`** : Styles des badges et éléments communs
3. **`groups-page.css`** : Styles de la page (conservés pour compatibilité)

## Sécurité

### Nonces
- Chaque action utilise un nonce spécifique
- Vérification côté serveur dans `delete_group()`
- Nonces générés dans `column_title()` pour les actions de ligne

### Permissions
- Actions groupées réservées aux utilisateurs avec `manage_options`
- Vérification des capacités avant chaque action

### Validation
- Sanitisation des entrées utilisateur
- Validation des IDs de groupes
- Échappement des sorties

## Migration depuis l'ancien système

L'ancien template `groups-page.php` est conservé pour référence mais n'est plus utilisé. Pour revenir à l'ancien système :

```php
// Dans class-scf-admin-page.php, méthode render_groups_page()
require_once SCF_PLUGIN_DIR . 'templates/groups-page.php'; // Ancien
// Au lieu de
require_once SCF_PLUGIN_DIR . 'templates/groups-page-list-table.php'; // Nouveau
```

## Responsive

Le tableau s'adapte automatiquement aux petits écrans :
- Colonnes secondaires masquées sur mobile
- Actions regroupées dans un menu déroulant
- Recherche adaptée à la largeur de l'écran

## Performance

### Optimisations
- Requêtes optimisées avec `prepare_items()`
- Pagination pour limiter les résultats
- Cache des métadonnées

### Recommandations
- Pour plus de 100 groupes, activer la pagination
- Utiliser les filtres de statut pour réduire les résultats
- Indexer les métadonnées si nécessaire

## Dépannage

### La table ne s'affiche pas
1. Vérifier que `SCF_Groups_List_Table.php` est chargé
2. Vérifier les permissions utilisateur
3. Vérifier les erreurs PHP dans les logs

### Les styles ne sont pas appliqués
1. Vider le cache du navigateur
2. Vérifier que `list-table.css` est chargé
3. Vérifier les conflits avec d'autres plugins

### Les actions groupées ne fonctionnent pas
1. Vérifier les nonces
2. Vérifier les permissions utilisateur
3. Vérifier les logs d'erreurs PHP

## Références

- [WP_List_Table Documentation](https://developer.wordpress.org/reference/classes/wp_list_table/)
- [Creating Admin Tables](https://developer.wordpress.org/plugins/administration-menus/admin-tables/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
