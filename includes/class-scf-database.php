<?php
if (!defined('ABSPATH')) exit;

class SCF_Database {
    private static $instance = null;
    private $table_name;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'scf_fields';
    }

    public function create_table() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            group_id bigint(20) NOT NULL,
            field_name varchar(255) NOT NULL,
            field_value longtext,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY group_id (group_id),
            KEY field_name (field_name)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function save_field($post_id, $group_id, $field_name, $field_value) {
        global $wpdb;

        // Vérifier si l'entrée existe déjà
        $existing = $this->get_field($post_id, $group_id, $field_name);

        if ($existing) {
            // Mise à jour
            return $wpdb->update(
                $this->table_name,
                array('field_value' => maybe_serialize($field_value)),
                array(
                    'post_id' => $post_id,
                    'group_id' => $group_id,
                    'field_name' => $field_name
                )
            );
        } else {
            // Insertion
            return $wpdb->insert(
                $this->table_name,
                array(
                    'post_id' => $post_id,
                    'group_id' => $group_id,
                    'field_name' => $field_name,
                    'field_value' => maybe_serialize($field_value)
                )
            );
        }
    }

    public function get_field($post_id, $group_id, $field_name) {
        global $wpdb;

        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} 
                WHERE post_id = %d AND group_id = %d AND field_name = %s",
                $post_id, $group_id, $field_name
            )
        );

        if ($result) {
            $result->field_value = maybe_unserialize($result->field_value);
        }

        return $result;
    }

    public function get_fields_by_post($post_id) {
        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name} WHERE post_id = %d",
                $post_id
            )
        );

        foreach ($results as $result) {
            $result->field_value = maybe_unserialize($result->field_value);
        }

        return $results;
    }

    public function delete_fields($post_id, $group_id = null) {
        global $wpdb;

        if ($group_id) {
            return $wpdb->delete(
                $this->table_name,
                array(
                    'post_id' => $post_id,
                    'group_id' => $group_id
                )
            );
        } else {
            return $wpdb->delete(
                $this->table_name,
                array('post_id' => $post_id)
            );
        }
    }
}
