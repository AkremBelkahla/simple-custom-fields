<?php

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;

class ShortcodeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        
        // Simuler les fonctions WordPress
        Functions\when('get_the_ID')->justReturn(123);
        Functions\when('get_post_type')->justReturn('post');
        Functions\when('get_posts')->justReturn([]);
        Functions\when('get_post_meta')->justReturn([]);
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function testShortcodeRegistration()
    {
        // Vérifier que le shortcode est enregistré
        Monkey\Functions\expect('add_shortcode')
            ->once()
            ->with('scf_fields', Monkey\Functions\type('callable'));
        
        // Inclure le fichier principal du plugin pour tester l'enregistrement du shortcode
        require_once dirname(dirname(__DIR__)) . '/simple-custom-fields.php';
        
        // Vérifier que la fonction scf_display_custom_fields_shortcode existe
        $this->assertTrue(function_exists('scf_display_custom_fields_shortcode'));
    }
    
    public function testShortcodeOutput()
    {
        // Mock de la classe SCF_Database
        $db_mock = $this->getMockBuilder('SCF_Database')
            ->disableOriginalConstructor()
            ->addMethods(['get_field'])
            ->getMock();
            
        $db_mock->method('get_field')->willReturn((object) [
            'field_value' => 'test_value'
        ]);
        
        // Définir une fonction globale pour simuler SCF_Database::get_instance()
        Functions\when('SCF_Database::get_instance')->justReturn($db_mock);
        
        // Simuler get_posts pour retourner un groupe de champs
        $group = (object) [
            'ID' => 456,
            'post_title' => 'Test Group'
        ];
        
        Functions\when('get_posts')->justReturn([$group]);
        
        // Simuler get_post_meta pour retourner des règles et des champs
        $rules = [
            'type' => 'post_type',
            'value' => 'post'
        ];
        
        $fields = [
            [
                'name' => 'test_field',
                'label' => 'Test Field',
                'type' => 'text'
            ]
        ];
        
        Functions\when('get_post_meta')->justReturn($rules, $fields);
        
        // Inclure le fichier principal du plugin
        require_once dirname(dirname(__DIR__)) . '/simple-custom-fields.php';
        
        // Appeler la fonction de shortcode directement
        $output = scf_display_custom_fields_shortcode([]);
        
        // Vérifier que la sortie contient des éléments attendus
        $this->assertStringContainsString('scf-frontend-container', $output);
        $this->assertStringContainsString('Test Group', $output);
    }
}
