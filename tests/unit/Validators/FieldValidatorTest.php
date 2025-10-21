<?php
/**
 * Tests unitaires pour FieldValidator
 *
 * @package SimpleCustomFields
 * @subpackage Tests
 */

namespace SCF\Tests\Unit\Validators;

use PHPUnit\Framework\TestCase;
use SCF\Validators\FieldValidator;
use Brain\Monkey;
use Brain\Monkey\Functions;

/**
 * Classe de test pour FieldValidator
 */
class FieldValidatorTest extends TestCase {
    /**
     * Instance du validateur
     *
     * @var FieldValidator
     */
    private $validator;

    /**
     * Configuration avant chaque test
     */
    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();
        
        // Mock des fonctions WordPress
        Functions\when('__')->returnArg(1);
        Functions\when('esc_html')->returnArg(1);
        Functions\when('sanitize_text_field')->returnArg(1);
        Functions\when('sanitize_email')->returnArg(1);
        Functions\when('esc_url_raw')->returnArg(1);
        Functions\when('sanitize_textarea_field')->returnArg(1);
        Functions\when('is_email')->alias(function($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        });
        
        $this->validator = FieldValidator::get_instance();
    }

    /**
     * Nettoyage après chaque test
     */
    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * Test de validation d'email valide
     */
    public function test_validate_valid_email() {
        $result = $this->validator->validate('test@example.com', 'email');
        $this->assertTrue($result);
    }

    /**
     * Test de validation d'email invalide
     */
    public function test_validate_invalid_email() {
        $result = $this->validator->validate('invalid-email', 'email');
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test de validation de nombre valide
     */
    public function test_validate_valid_number() {
        $result = $this->validator->validate('42', 'number');
        $this->assertTrue($result);
    }

    /**
     * Test de validation de nombre avec min/max
     */
    public function test_validate_number_with_range() {
        $settings = array(
            'min' => 10,
            'max' => 100,
        );
        
        $this->assertTrue($this->validator->validate('50', 'number', $settings));
        $this->assertIsArray($this->validator->validate('5', 'number', $settings));
        $this->assertIsArray($this->validator->validate('150', 'number', $settings));
    }

    /**
     * Test de validation de date valide
     */
    public function test_validate_valid_date() {
        $result = $this->validator->validate('2024-01-15', 'date');
        $this->assertTrue($result);
    }

    /**
     * Test de validation de date invalide
     */
    public function test_validate_invalid_date() {
        $result = $this->validator->validate('2024-13-45', 'date');
        $this->assertIsArray($result);
    }

    /**
     * Test de validation de texte avec longueur max
     */
    public function test_validate_text_max_length() {
        $settings = array('max_length' => 10);
        
        $this->assertTrue($this->validator->validate('short', 'text', $settings));
        $this->assertIsArray($this->validator->validate('very long text that exceeds limit', 'text', $settings));
    }

    /**
     * Test de champ requis vide
     */
    public function test_validate_required_empty() {
        $settings = array('required' => true);
        
        $result = $this->validator->validate('', 'text', $settings);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test de champ requis rempli
     */
    public function test_validate_required_filled() {
        $settings = array('required' => true);
        
        $result = $this->validator->validate('value', 'text', $settings);
        $this->assertTrue($result);
    }

    /**
     * Test de sanitization de texte
     */
    public function test_sanitize_text() {
        $value = $this->validator->sanitize('test value', 'text');
        $this->assertEquals('test value', $value);
    }

    /**
     * Test de sanitization d'email
     */
    public function test_sanitize_email() {
        $value = $this->validator->sanitize('test@example.com', 'email');
        $this->assertEquals('test@example.com', $value);
    }

    /**
     * Test de sanitization de nombre
     */
    public function test_sanitize_number() {
        $value = $this->validator->sanitize('42.5', 'number');
        $this->assertEquals(42.5, $value);
    }

    /**
     * Test de validation de checkbox
     */
    public function test_validate_checkbox() {
        $settings = array(
            'options' => array(
                array('value' => 'option1', 'label' => 'Option 1'),
                array('value' => 'option2', 'label' => 'Option 2'),
            ),
        );
        
        $this->assertTrue($this->validator->validate(array('option1'), 'checkbox', $settings));
        $this->assertIsArray($this->validator->validate(array('invalid'), 'checkbox', $settings));
    }

    /**
     * Test de validation de select
     */
    public function test_validate_select() {
        $settings = array(
            'options' => array(
                array('value' => 'option1', 'label' => 'Option 1'),
                array('value' => 'option2', 'label' => 'Option 2'),
            ),
        );
        
        $this->assertTrue($this->validator->validate('option1', 'select', $settings));
        $this->assertIsArray($this->validator->validate('invalid', 'select', $settings));
    }

    /**
     * Test de validation d'URL valide
     */
    public function test_validate_valid_url() {
        $result = $this->validator->validate('https://example.com', 'url');
        $this->assertTrue($result);
    }

    /**
     * Test de validation d'URL invalide
     */
    public function test_validate_invalid_url() {
        $result = $this->validator->validate('not-a-url', 'url');
        $this->assertIsArray($result);
    }
}
