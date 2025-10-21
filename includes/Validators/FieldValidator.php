<?php
/**
 * Validateur de champs
 *
 * Valide les données des champs personnalisés
 *
 * @package SimpleCustomFields
 * @subpackage Validators
 * @since 1.5.0
 */

namespace SCF\Validators;

use SCF\Utilities\ErrorHandler;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe FieldValidator
 *
 * Validation stricte des champs selon leur type
 */
class FieldValidator {
    /**
     * Instance unique (Singleton)
     *
     * @var FieldValidator|null
     */
    private static $instance = null;

    /**
     * Gestionnaire d'erreurs
     *
     * @var ErrorHandler
     */
    private $error_handler;

    /**
     * Règles de validation par type
     *
     * @var array
     */
    private $validation_rules = array();

    /**
     * Récupère l'instance unique
     *
     * @return FieldValidator
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
        $this->error_handler = ErrorHandler::get_instance();
        $this->init_validation_rules();
    }

    /**
     * Initialise les règles de validation
     *
     * @return void
     */
    private function init_validation_rules() {
        $this->validation_rules = array(
            'text' => array(
                'max_length' => 255,
                'sanitize' => 'sanitize_text_field',
            ),
            'textarea' => array(
                'max_length' => 65535,
                'sanitize' => 'sanitize_textarea_field',
            ),
            'email' => array(
                'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'sanitize' => 'sanitize_email',
            ),
            'url' => array(
                'sanitize' => 'esc_url_raw',
            ),
            'number' => array(
                'type' => 'numeric',
                'sanitize' => 'floatval',
            ),
            'date' => array(
                'pattern' => '/^\d{4}-\d{2}-\d{2}$/',
                'sanitize' => 'sanitize_text_field',
            ),
            'checkbox' => array(
                'type' => 'array',
                'sanitize' => 'sanitize_text_field',
            ),
            'select' => array(
                'sanitize' => 'sanitize_text_field',
            ),
            'radio' => array(
                'sanitize' => 'sanitize_text_field',
            ),
            'file' => array(
                'type' => 'file',
                'sanitize' => 'absint',
            ),
        );

        $this->validation_rules = apply_filters('scf_validation_rules', $this->validation_rules);
    }

    /**
     * Valide une valeur de champ
     *
     * @param mixed $value Valeur à valider
     * @param string $type Type de champ
     * @param array $settings Paramètres du champ
     * @return bool|array True si valide, array d'erreurs sinon
     */
    public function validate($value, $type, array $settings = array()) {
        $errors = array();

        // Vérifier si le type est supporté
        if (!isset($this->validation_rules[$type])) {
            $errors[] = sprintf(__('Type de champ non supporté: %s', 'simple-custom-fields'), $type);
            return $errors;
        }

        $rules = $this->validation_rules[$type];

        // Validation requis
        if (!empty($settings['required']) && $this->is_empty($value)) {
            $errors[] = __('Ce champ est requis', 'simple-custom-fields');
            return $errors;
        }

        // Si vide et non requis, pas besoin de valider plus
        if ($this->is_empty($value)) {
            return true;
        }

        // Validation selon le type
        switch ($type) {
            case 'email':
                if (!$this->validate_email($value)) {
                    $errors[] = __('Adresse email invalide', 'simple-custom-fields');
                }
                break;

            case 'url':
                if (!$this->validate_url($value)) {
                    $errors[] = __('URL invalide', 'simple-custom-fields');
                }
                break;

            case 'number':
                if (!$this->validate_number($value, $settings)) {
                    $errors[] = __('Nombre invalide', 'simple-custom-fields');
                }
                break;

            case 'date':
                if (!$this->validate_date($value)) {
                    $errors[] = __('Date invalide', 'simple-custom-fields');
                }
                break;

            case 'text':
            case 'textarea':
                if (!$this->validate_text($value, $rules, $settings)) {
                    $errors[] = sprintf(
                        __('Texte invalide (max %d caractères)', 'simple-custom-fields'),
                        isset($settings['max_length']) ? $settings['max_length'] : $rules['max_length']
                    );
                }
                break;

            case 'select':
            case 'radio':
                if (!$this->validate_choice($value, $settings)) {
                    $errors[] = __('Choix invalide', 'simple-custom-fields');
                }
                break;

            case 'checkbox':
                if (!$this->validate_checkbox($value, $settings)) {
                    $errors[] = __('Sélection invalide', 'simple-custom-fields');
                }
                break;

            case 'file':
                if (!$this->validate_file($value, $settings)) {
                    $errors[] = __('Fichier invalide', 'simple-custom-fields');
                }
                break;
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Vérifie si une valeur est vide
     *
     * @param mixed $value Valeur
     * @return bool
     */
    private function is_empty($value) {
        if (is_array($value)) {
            return empty($value);
        }
        return $value === '' || $value === null;
    }

    /**
     * Valide une adresse email
     *
     * @param string $value Email
     * @return bool
     */
    private function validate_email($value) {
        return is_email($value) !== false;
    }

    /**
     * Valide une URL
     *
     * @param string $value URL
     * @return bool
     */
    private function validate_url($value) {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Valide un nombre
     *
     * @param mixed $value Valeur
     * @param array $settings Paramètres
     * @return bool
     */
    private function validate_number($value, array $settings) {
        if (!is_numeric($value)) {
            return false;
        }

        $number = floatval($value);

        // Vérifier min
        if (isset($settings['min']) && $number < floatval($settings['min'])) {
            return false;
        }

        // Vérifier max
        if (isset($settings['max']) && $number > floatval($settings['max'])) {
            return false;
        }

        return true;
    }

    /**
     * Valide une date
     *
     * @param string $value Date
     * @return bool
     */
    private function validate_date($value) {
        $date = \DateTime::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value;
    }

    /**
     * Valide un texte
     *
     * @param string $value Texte
     * @param array $rules Règles
     * @param array $settings Paramètres
     * @return bool
     */
    private function validate_text($value, array $rules, array $settings) {
        $max_length = isset($settings['max_length']) ? $settings['max_length'] : $rules['max_length'];
        
        if (strlen($value) > $max_length) {
            return false;
        }

        // Pattern personnalisé
        if (isset($settings['pattern']) && !preg_match($settings['pattern'], $value)) {
            return false;
        }

        return true;
    }

    /**
     * Valide un choix (select/radio)
     *
     * @param string $value Valeur
     * @param array $settings Paramètres
     * @return bool
     */
    private function validate_choice($value, array $settings) {
        if (empty($settings['options'])) {
            return true;
        }

        $valid_values = array_column($settings['options'], 'value');
        return in_array($value, $valid_values, true);
    }

    /**
     * Valide des cases à cocher
     *
     * @param array $value Valeurs
     * @param array $settings Paramètres
     * @return bool
     */
    private function validate_checkbox($value, array $settings) {
        if (!is_array($value)) {
            return false;
        }

        if (empty($settings['options'])) {
            return true;
        }

        $valid_values = array_column($settings['options'], 'value');
        
        foreach ($value as $v) {
            if (!in_array($v, $valid_values, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valide un fichier
     *
     * @param int $value ID de l'attachement
     * @param array $settings Paramètres
     * @return bool
     */
    private function validate_file($value, array $settings) {
        if (!is_numeric($value)) {
            return false;
        }

        $attachment = get_post(intval($value));
        
        if (!$attachment || $attachment->post_type !== 'attachment') {
            return false;
        }

        // Vérifier le type de fichier
        if (!empty($settings['allowed_types'])) {
            $file_type = get_post_mime_type($attachment);
            if (!in_array($file_type, $settings['allowed_types'], true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sanitize une valeur selon son type
     *
     * @param mixed $value Valeur
     * @param string $type Type
     * @return mixed
     */
    public function sanitize($value, $type) {
        if (!isset($this->validation_rules[$type])) {
            return sanitize_text_field($value);
        }

        $rules = $this->validation_rules[$type];
        
        if (isset($rules['sanitize'])) {
            $sanitize_func = $rules['sanitize'];
            
            if ($type === 'checkbox' && is_array($value)) {
                return array_map($sanitize_func, $value);
            }
            
            return call_user_func($sanitize_func, $value);
        }

        return sanitize_text_field($value);
    }
}
