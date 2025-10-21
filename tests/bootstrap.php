<?php
/**
 * Bootstrap des tests PHPUnit
 *
 * @package SimpleCustomFields
 * @subpackage Tests
 */

// Définir les constantes de test
define('SCF_TESTS_DIR', __DIR__);
define('SCF_PLUGIN_DIR', dirname(__DIR__) . '/');

// Charger Composer autoloader
require_once SCF_PLUGIN_DIR . 'vendor/autoload.php';

// Initialiser Brain Monkey
\Brain\Monkey\setUp();

// Définir les constantes WordPress si nécessaire
if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wordpress/');
}

if (!defined('WP_DEBUG')) {
    define('WP_DEBUG', true);
}

// Charger les constantes du plugin
require_once SCF_PLUGIN_DIR . 'config/constants.php';

// Mock des fonctions WordPress courantes
if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url) {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return strip_tags($str);
    }
}

if (!function_exists('wp_json_encode')) {
    function wp_json_encode($data, $options = 0, $depth = 512) {
        return json_encode($data, $options, $depth);
    }
}

if (!function_exists('current_time')) {
    function current_time($type, $gmt = 0) {
        return date($type === 'mysql' ? 'Y-m-d H:i:s' : 'U');
    }
}

if (!function_exists('apply_filters')) {
    function apply_filters($tag, $value) {
        return $value;
    }
}

if (!function_exists('do_action')) {
    function do_action($tag) {
        return null;
    }
}

echo "Bootstrap des tests chargé\n";
