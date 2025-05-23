<?php
if (!defined('ABSPATH')) exit;

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
    <h1 class="wp-heading-inline"><?php echo $group_id ? 'Modifier le groupe' : 'Ajouter un groupe'; ?></h1>
    <a href="<?php echo admin_url('admin.php?page=simple-custom-fields'); ?>"
       class="page-title-action">Retour à la liste</a>
    <hr class="wp-header-end">

    <form id="scf-form"
          method="post"
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

        <div id="poststuff">
            <div id="post-body"
                 class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div id="titlediv">
                        <div id="titlewrap">
                            <input type="text"
                                   name="title"
                                   size="30"
                                   id="title"
                                   value="<?php echo $group ? esc_attr($group->post_title) : ''; ?>"
                                   placeholder="Titre du groupe de champs"
                                   spellcheck="true"
                                   autocomplete="off">
                        </div>
                    </div>
                </div>

                <div id="postbox-container-2"
                     class="postbox-container">
                    <div id="normal-sortables"
                         class="meta-box-sortables ui-sortable">
                        <div id="scf-fields"
                             class="postbox">
                            <div class="postbox-header">
                                <h2 class="hndle ui-sortable-handle">
                                    <span>Champs</span>
                                </h2>
                                <div class="handle-actions hide-if-no-js">
                                    <button type="button"
                                            class="handlediv"
                                            aria-expanded="true">
                                        <span class="screen-reader-text">Afficher/Masquer le panneau</span>
                                        <span class="toggle-indicator"
                                              aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="inside">
                                <table class="wp-list-table widefat fixed striped">
                                    <thead>
                                        <tr>
                                            <th scope="col"
                                                class="manage-column column-order"></th>
                                            <th scope="col"
                                                class="manage-column column-type">Type</th>
                                            <th scope="col"
                                                class="manage-column column-label">Libellé</th>
                                            <th scope="col"
                                                class="manage-column column-name">Nom</th>
                                            <th scope="col"
                                                class="manage-column column-actions">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="the-list">
                                        <?php if (!empty($fields)): ?>
                                        <?php foreach ($fields as $index => $field): ?>
                                        <tr class="scf-field-row">
                                            <td class="column-order">
                                                <span class="dashicons dashicons-menu"></span>
                                            </td>
                                            <td class="column-type">
                                                <select name="fields[<?php echo $index; ?>][type]"
                                                        class="field-type-select">
                                                    <option value="text"
                                                            <?php selected($field['type'], 'text'); ?>>Texte</option>
                                                    <option value="textarea"
                                                            <?php selected($field['type'], 'textarea'); ?>>Zone de texte
                                                    </option>
                                                    <option value="number"
                                                            <?php selected($field['type'], 'number'); ?>>Nombre</option>
                                                    <option value="email"
                                                            <?php selected($field['type'], 'email'); ?>>Email</option>
                                                    <option value="select"
                                                            <?php selected($field['type'], 'select'); ?>>Liste
                                                        déroulante</option>
                                                    <option value="radio"
                                                            <?php selected($field['type'], 'radio'); ?>>Boutons radio
                                                    </option>
                                                    <option value="checkbox"
                                                            <?php selected($field['type'], 'checkbox'); ?>>Cases à
                                                        cocher</option>
                                                </select>
                                            </td>
                                            <td class="column-label">
                                                <input type="text"
                                                       name="fields[<?php echo $index; ?>][label]"
                                                       class="widefat"
                                                       placeholder="Libellé du champ"
                                                       value="<?php echo esc_attr($field['label']); ?>">
                                            </td>
                                            <td class="column-name">
                                                <input type="text"
                                                       name="fields[<?php echo $index; ?>][name]"
                                                       class="widefat"
                                                       placeholder="Nom du champ"
                                                       value="<?php echo esc_attr($field['name']); ?>">
                                            </td>
                                            <td class="column-actions">
                                                <?php $show_options = in_array($field['type'], array('select', 'radio', 'checkbox')); ?>
                                                <button type="button"
                                                        class="button button-primary edit-options"
                                                        <?php echo !$show_options ? 'style="display: none;"' : ''; ?>>
                                                    <span class="dashicons dashicons-list-view"
                                                          style="margin-top: 5px;"
                                                          title="Options"></span>
                                                </button>
                                                <button type="button"
                                                        class="button button-primary remove-field">
                                                    <span class="dashicons dashicons-trash"
                                                          style="margin-top: 5px;"
                                                          title="Supprimer"></span>
                                                </button>
                                                <?php 
                                                        $options = isset($field['options']) ? $field['options'] : array();
                                                        $options_json = wp_json_encode($options);
                                                        ?>
                                                <input type="hidden"
                                                       name="fields[<?php echo $index; ?>][options]"
                                                       class="field-options"
                                                       value="<?php echo esc_attr($options_json); ?>">
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="scf-add-field-wrapper">
                                <button type="button"
                                        class="button button-primary scf-add-field">
                                    <span class="dashicons dashicons-plus-alt2"
                                          style="margin-top: 5px;"></span>
                                    Ajouter un champ
                                </button>
                            </div>
                        </div>

                        <div id="scf-description"
                             class="postbox">
                            <div class="postbox-header">
                                <h2 class="hndle ui-sortable-handle">
                                    <span>Description du groupe</span>
                                </h2>
                            </div>
                            <div class="inside">
                                <textarea name="description"
                                          class="widefat"
                                          rows="4"
                                          placeholder="Description du groupe de champs"><?php echo $group ? esc_textarea($group->post_content) : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="postbox-container-1"
                     class="postbox-container">
                    <div id="side-sortables"
                         class="meta-box-sortables ui-sortable">
                        <div id="submitdiv"
                             class="postbox">
                            <div class="postbox-header">
                                <h2 class="hndle ui-sortable-handle">
                                    <span>Publier</span>
                                </h2>
                            </div>
                            <div class="inside">
                                <div class="submitbox"
                                     id="submitpost">
                                    <div id="major-publishing-actions">
                                        <div id="publishing-action">
                                            <span class="spinner"></span>
                                            <input type="submit"
                                                   class="button button-primary button-large"
                                                   value="<?php echo $group_id ? 'Mettre à jour' : 'Publier'; ?>"
                                                   id="publish"
                                                   name="publish">
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="scf-rules"
                             class="postbox">
                            <div class="postbox-header">
                                <h2 class="hndle ui-sortable-handle">
                                    <span>Type de contenu</span>
                                </h2>
                            </div>
                            <div class="inside">
                                <p><label>Sélectionnez le type de contenu :</label></p>
                                <select name="rules[value]" class="widefat">
                                    <?php 
                                    $post_types = get_post_types(array('public' => true), 'objects');
                                    foreach ($post_types as $post_type) {
                                        $selected = '';
                                        if (isset($rules['value']) && $rules['value'] === $post_type->name) {
                                            $selected = 'selected';
                                        }
                                        echo '<option value="' . esc_attr($post_type->name) . '" ' . $selected . '>';
                                        echo esc_html($post_type->labels->singular_name);
                                        echo '</option>';
                                    }
                                    ?>
                                </select>
                                <input type="hidden" name="rules[type]" value="post_type">
                                <input type="hidden" name="rules[operator]" value="=">
                            </div>
                        </div>

                        <div id="scf-activation"
                             class="postbox">
                            <div class="postbox-header">
                                <h2 class="hndle ui-sortable-handle">
                                    <span>Activation du groupe</span>
                                </h2>
                            </div>
                            <div class="inside">
                                <div class="">
                                    <label for="group_status">Statut du groupe</label>
                                    <select name="status"
                                            id="group_status"
                                            class="widefat">
                                        <option value="publish"
                                                <?php echo (!$group || $group->post_status === 'publish') ? 'selected' : ''; ?>>
                                            Activé
                                        </option>
                                        <option value="draft"
                                                <?php echo ($group && $group->post_status === 'draft') ? 'selected' : ''; ?>>
                                            Désactivé
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template pour un nouveau champ -->
<script type="text/template" id="field-template">
    <tr class="scf-field-row">
        <td class="column-order">
            <span class="dashicons dashicons-menu"></span>
        </td>
        <td class="column-type">
            <select name="fields[{index}][type]" class="field-type-select">
                <option value="text">Texte</option>
                <option value="textarea">Zone de texte</option>
                <option value="number">Nombre</option>
                <option value="email">Email</option>
                <option value="select">Liste déroulante</option>
                <option value="radio">Boutons radio</option>
                <option value="checkbox">Cases à cocher</option>
            </select>
        </td>
        <td class="column-label">
            <input type="text" name="fields[{index}][label]" class="widefat" placeholder="Libellé du champ">
        </td>
        <td class="column-name">
            <input type="text" name="fields[{index}][name]" class="widefat" placeholder="Nom du champ">
        </td>
        <td class="column-actions">
            <button type="button" class="button button-primary edit-options" style="display: none;">
                <span class="dashicons dashicons-list-view" title="Options"></span>
            </button>
            <button type="button" class="button button-primary remove-field">
                <span class="dashicons dashicons-trash" title="Supprimer"></span>
            </button>
            <input type="hidden" name="fields[{index}][options]" class="field-options" value="[]">
        </td>
    </tr>
</script>

<!-- Modal des options -->
<div id="optionsModal" class="scf-modal" style="display: none;">
    <div class="scf-modal-content">
        <div class="scf-modal-header">
            <h2>Options</h2>
            <button type="button" class="close-modal">×</button>
        </div>
        <div class="scf-modal-body">
            <div class="options-list">
                <!-- Les options seront ajoutées ici dynamiquement -->
            </div>
            <button type="button" class="button add-option">
                <span class="dashicons dashicons-plus-alt2"></span>
                Ajouter une option
            </button>
        </div>
        <div class="scf-modal-footer">
            <button type="button" class="button button-secondary close-modal">Annuler</button>
            <button type="button" class="button button-primary save-options">Enregistrer</button>
        </div>
    </div>
</div>

<!-- Template pour une option -->
<script type="text/template" id="option-template">
    <div class="option-row">
        <div class="option-field">
            <label>Libellé</label>
            <input type="text" class="option-label" placeholder="Libellé" value="{label}">
        </div>
        <div class="option-field">
            <label>Valeur</label>
            <input type="text" class="option-value" placeholder="Valeur" value="{value}">
        </div>
        <button type="button" class="button remove-option">
            <span class="dashicons dashicons-trash"></span>
        </button>
    </div>
</script>

<style>
.scf-modal {
    display: none;
    position: fixed;
    z-index: 100000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.scf-modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    width: 50%;
    max-width: 500px;
    position: relative;
    border-radius: 4px;
}

.scf-modal-header {
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.scf-modal-header h2 {
    margin: 0;
    padding: 0;
}

.scf-modal-body {
    margin-bottom: 20px;
}

.scf-modal-footer {
    padding-top: 10px;
    border-top: 1px solid #ddd;
    text-align: right;
}

.option-row {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
    align-items: flex-end;
}

.option-field {
    flex: 1;
}

.option-field label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    font-size: 13px;
}

.option-field input {
    width: 100%;
}

.option-row input {
    flex: 1;
}

.option-row .remove-option {
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.options-list {
    margin-bottom: 15px;
}

.add-option {
    margin-bottom: 15px;
}
</style>
