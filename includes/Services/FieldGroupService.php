<?php
/**
 * Service de gestion des groupes de champs
 *
 * Logique métier pour les groupes de champs
 *
 * @package SimpleCustomFields
 * @subpackage Services
 * @since 1.5.0
 */

namespace SCF\Services;

use SCF\Utilities\Logger;
use SCF\Utilities\ErrorHandler;
use SCF\Validators\FieldValidator;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe FieldGroupService
 *
 * Gère toute la logique métier des groupes de champs
 */
class FieldGroupService {
    /**
     * Instance unique (Singleton)
     *
     * @var FieldGroupService|null
     */
    private static $instance = null;

    /**
     * Logger
     *
     * @var Logger
     */
    private $logger;

    /**
     * Gestionnaire d'erreurs
     *
     * @var ErrorHandler
     */
    private $error_handler;

    /**
     * Validateur
     *
     * @var FieldValidator
     */
    private $validator;

    /**
     * Récupère l'instance unique
     *
     * @return FieldGroupService
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructeur privé (Singleton)
     */
    private function __construct() {
        $this->logger = Logger::get_instance();
        $this->error_handler = ErrorHandler::get_instance();
        $this->validator = FieldValidator::get_instance();
    }

    /**
     * Crée un nouveau groupe de champs
     *
     * @param array $data Données du groupe
     * @return int|false ID du groupe créé ou false
     */
    public function create_group(array $data) {
        $this->logger->info('Création d\'un nouveau groupe de champs', array('data' => $data));

        try {
            // Valider les données
            $validated = $this->validate_group_data($data);
            if (!$validated) {
                return false;
            }

            // Créer le post
            $post_data = array(
                'post_title' => $data['title'],
                'post_type' => 'scf-field-group',
                'post_status' => isset($data['status']) ? $data['status'] : 'draft',
                'post_content' => isset($data['description']) ? $data['description'] : '',
            );

            $group_id = wp_insert_post($post_data, true);

            if (is_wp_error($group_id)) {
                $this->error_handler->add_error(
                    'group_creation_failed',
                    $group_id->get_error_message(),
                    array('data' => $data),
                    'error'
                );
                return false;
            }

            // Sauvegarder les métadonnées
            if (isset($data['fields'])) {
                update_post_meta($group_id, 'scf_fields', $data['fields']);
            }

            if (isset($data['rules'])) {
                update_post_meta($group_id, 'scf_rules', $data['rules']);
            }

            $this->logger->info('Groupe de champs créé avec succès', array('group_id' => $group_id));

            do_action('scf_group_created', $group_id, $data);

            return $group_id;

        } catch (\Exception $e) {
            $this->error_handler->handle_exception($e, 'create_group');
            return false;
        }
    }

    /**
     * Met à jour un groupe de champs
     *
     * @param int $group_id ID du groupe
     * @param array $data Nouvelles données
     * @return bool
     */
    public function update_group($group_id, array $data) {
        $this->logger->info('Mise à jour du groupe de champs', array(
            'group_id' => $group_id,
            'data' => $data
        ));

        try {
            // Vérifier que le groupe existe
            $group = get_post($group_id);
            if (!$group || $group->post_type !== 'scf-field-group') {
                $this->error_handler->add_error(
                    'group_not_found',
                    __('Groupe de champs introuvable', 'simple-custom-fields'),
                    array('group_id' => $group_id),
                    'error'
                );
                return false;
            }

            // Valider les données
            $validated = $this->validate_group_data($data);
            if (!$validated) {
                return false;
            }

            // Mettre à jour le post
            $post_data = array(
                'ID' => $group_id,
                'post_title' => $data['title'],
                'post_status' => isset($data['status']) ? $data['status'] : $group->post_status,
                'post_content' => isset($data['description']) ? $data['description'] : $group->post_content,
            );

            $result = wp_update_post($post_data, true);

            if (is_wp_error($result)) {
                $this->error_handler->add_error(
                    'group_update_failed',
                    $result->get_error_message(),
                    array('group_id' => $group_id),
                    'error'
                );
                return false;
            }

            // Mettre à jour les métadonnées
            if (isset($data['fields'])) {
                update_post_meta($group_id, 'scf_fields', $data['fields']);
            }

            if (isset($data['rules'])) {
                update_post_meta($group_id, 'scf_rules', $data['rules']);
            }

            $this->logger->info('Groupe de champs mis à jour avec succès', array('group_id' => $group_id));

            do_action('scf_group_updated', $group_id, $data);

            return true;

        } catch (\Exception $e) {
            $this->error_handler->handle_exception($e, 'update_group');
            return false;
        }
    }

    /**
     * Supprime un groupe de champs
     *
     * @param int $group_id ID du groupe
     * @param bool $force Forcer la suppression définitive
     * @return bool
     */
    public function delete_group($group_id, $force = true) {
        $this->logger->info('Suppression du groupe de champs', array(
            'group_id' => $group_id,
            'force' => $force
        ));

        try {
            // Vérifier que le groupe existe
            $group = get_post($group_id);
            if (!$group || $group->post_type !== 'scf-field-group') {
                $this->error_handler->add_error(
                    'group_not_found',
                    __('Groupe de champs introuvable', 'simple-custom-fields'),
                    array('group_id' => $group_id),
                    'error'
                );
                return false;
            }

            // Supprimer les métadonnées
            delete_post_meta($group_id, 'scf_fields');
            delete_post_meta($group_id, 'scf_rules');

            // Supprimer les valeurs des champs associés
            $this->delete_group_field_values($group_id);

            // Supprimer le post
            $result = wp_delete_post($group_id, $force);

            if (!$result) {
                $this->error_handler->add_error(
                    'group_deletion_failed',
                    __('Échec de la suppression du groupe', 'simple-custom-fields'),
                    array('group_id' => $group_id),
                    'error'
                );
                return false;
            }

            $this->logger->info('Groupe de champs supprimé avec succès', array('group_id' => $group_id));

            do_action('scf_group_deleted', $group_id);

            return true;

        } catch (\Exception $e) {
            $this->error_handler->handle_exception($e, 'delete_group');
            return false;
        }
    }

    /**
     * Récupère un groupe de champs
     *
     * @param int $group_id ID du groupe
     * @return array|false
     */
    public function get_group($group_id) {
        $group = get_post($group_id);
        
        if (!$group || $group->post_type !== 'scf-field-group') {
            return false;
        }

        return array(
            'id' => $group->ID,
            'title' => $group->post_title,
            'description' => $group->post_content,
            'status' => $group->post_status,
            'fields' => get_post_meta($group_id, 'scf_fields', true) ?: array(),
            'rules' => get_post_meta($group_id, 'scf_rules', true) ?: array(),
            'created' => $group->post_date,
            'modified' => $group->post_modified,
        );
    }

    /**
     * Récupère tous les groupes de champs
     *
     * @param array $args Arguments de requête
     * @return array
     */
    public function get_groups(array $args = array()) {
        $defaults = array(
            'post_type' => 'scf-field-group',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_status' => array('publish', 'draft'),
        );

        $args = wp_parse_args($args, $defaults);
        $posts = get_posts($args);

        $groups = array();
        foreach ($posts as $post) {
            $groups[] = $this->get_group($post->ID);
        }

        return $groups;
    }

    /**
     * Valide les données d'un groupe
     *
     * @param array $data Données à valider
     * @return bool
     */
    private function validate_group_data(array $data) {
        // Titre requis
        if (empty($data['title'])) {
            $this->error_handler->validation_error('title', __('Le titre est requis', 'simple-custom-fields'));
            return false;
        }

        // Valider les champs
        if (isset($data['fields']) && is_array($data['fields'])) {
            foreach ($data['fields'] as $field) {
                if (empty($field['name']) || empty($field['type'])) {
                    $this->error_handler->validation_error(
                        'fields',
                        __('Chaque champ doit avoir un nom et un type', 'simple-custom-fields')
                    );
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Supprime les valeurs des champs d'un groupe
     *
     * @param int $group_id ID du groupe
     * @return void
     */
    private function delete_group_field_values($group_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'scf_fields';

        $wpdb->delete(
            $table_name,
            array('group_id' => $group_id),
            array('%d')
        );
    }

    /**
     * Duplique un groupe de champs
     *
     * @param int $group_id ID du groupe à dupliquer
     * @return int|false ID du nouveau groupe ou false
     */
    public function duplicate_group($group_id) {
        $group = $this->get_group($group_id);
        
        if (!$group) {
            return false;
        }

        $group['title'] = $group['title'] . ' (Copie)';
        $group['status'] = 'draft';
        unset($group['id'], $group['created'], $group['modified']);

        return $this->create_group($group);
    }
}
