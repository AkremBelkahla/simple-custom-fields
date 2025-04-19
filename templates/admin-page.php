<?php if (!defined('ABSPATH')) exit;

// Récupérer le groupe si on est en mode édition
$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
$group = null;
$fields = array();
$rules = array();

if ($group_id) {
    $group = get_post($group_id);
    if ($group) {
        $fields = get_post_meta($group_id, 'scf_fields', true) ?: array();
        $rules = get_post_meta($group_id, 'scf_rules', true) ?: array();
    }
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo $group ? 'Modifier le groupe' : 'Ajouter un groupe'; ?></h1>
    <hr class="wp-header-end">

    <div class="scf-wrap <?php echo !$group ? 'scf-add-group-page' : ''; ?>">
        <form method="post"
              action="<?php echo admin_url('admin-post.php'); ?>">
            <?php wp_nonce_field('scf_save_field_group', 'scf_nonce'); ?>
            <input type="hidden"
                   name="action"
                   value="scf_save_field_group">
            <?php if ($group_id): ?>
            <input type="hidden"
                   name="group_id"
                   value="<?php echo $group_id; ?>">
            <?php endif; ?>

            <div class="scf-box-container">
                <!-- Titre du groupe -->
                <div class="scf-box">
                    <div class="inner">
                        <div id="titlediv">
                            <h2>
                                <label class="screen-reader-text"
                                       for="title">Titre du groupe</label>
                                <input type="text"
                                       id="title"
                                       name="title"
                                       class="regular-text"
                                       placeholder="Saisir le titre"
                                       value="<?php echo $group ? esc_attr($group->post_title) : ''; ?>"
                                       required>
                            </h2>
                        </div>
                    </div>
                </div>

                <!-- Réglages -->
                <div class="scf-box">
                    <div class="inner">
                        <h2 class="title">
                            <h3>Réglages</h3>
                            <p>Définissez où ce groupe de champs doit apparaître</p>
                        </h2>
                        <div class="content">
                            <div class="scf-field">
                                <div class="scf-label">
                                    <label>Afficher ce groupe de champs si</label>
                                </div>
                                <div class="rule">
                                    <select name="rules[0][param]">
                                        <option value="post_type">Type de contenu</option>
                                    </select>
                                    <select name="rules[0][operator]">
                                        <option value="==">est égal à</option>
                                    </select>
                                    <select name="rules[0][value]">
                                        <?php 
                                        $post_types = get_post_types(['public' => true], 'objects');
                                        foreach ($post_types as $post_type) {
                                            $selected = !empty($rules[0]['value']) && $rules[0]['value'] === $post_type->name ? 'selected' : '';
                                            echo '<option value="' . esc_attr($post_type->name) . '" ' . $selected . '>' . 
                                                 esc_html($post_type->label) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des champs -->
                <div class="scf-box">
                    <div class="inner">
                        <h2 class="title">
                            <h3>Champs</h3>
                            <p>Ajoutez et configurez vos champs personnalisés</p>
                        </h2>
                        <div class="content">
                            <div class="scf-field-list-wrap">
                                <table class="scf-field-list">
                                    <tbody>
                                        <?php if (!empty($fields)): ?>
                                        <?php foreach ($fields as $index => $field): ?>
                                        <tr class="scf-field-row">
                                            <td class="scf-field-order">
                                                <span class="dashicons dashicons-menu"></span>
                                            </td>
                                            <td class="scf-field-settings">
                                                <div class="scf-field-settings-main">
                                                    <div class="scf-field-type">
                                                        <select name="fields[<?php echo $index; ?>][type]">
                                                            <option value="text"
                                                                    <?php selected($field['type'], 'text'); ?>>Texte
                                                            </option>
                                                            <option value="textarea"
                                                                    <?php selected($field['type'], 'textarea'); ?>>Zone
                                                                de texte</option>
                                                            <option value="number"
                                                                    <?php selected($field['type'], 'number'); ?>>Nombre
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="scf-field-label">
                                                        <input type="text"
                                                               name="fields[<?php echo $index; ?>][label]"
                                                               placeholder="Libellé"
                                                               value="<?php echo esc_attr($field['label']); ?>">
                                                    </div>
                                                    <div class="scf-field-name">
                                                        <input type="text"
                                                               name="fields[<?php echo $index; ?>][name]"
                                                               placeholder="Nom"
                                                               value="<?php echo esc_attr($field['name']); ?>">
                                                    </div>
                                                    <div class="scf-field-actions">
                                                        <button type="button"
                                                                class="button button-primary scf-remove-field">
                                                            <span class="dashicons dashicons-trash"
                                                                  style="margin-top: 5px;"></span>
                                                            Supprimer
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php else: ?>
                                        <tr class="no-fields">
                                            <td colspan="2">Aucun champ ajouté</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="scf-actions">
                                <button type="button"
                                        class="button button-primary scf-add-field">
                                    <span class="dashicons dashicons-plus"
                                          style="margin-top: 5px;"></span> Ajouter un champ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="scf-actions">
                <button type="submit"
                        class="button button-primary">
                    <span class="dashicons dashicons-update"
                          style="margin-top: 5px;"></span>
                    <?php echo $group ? 'Mettre à jour' : 'Publier'; ?>
                </button>
            </div>
        </form>
    </div>
</div>