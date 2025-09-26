<?php

use PHPUnit\Framework\TestCase;
use Brain\Monkey\Functions;

class GetFieldTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Brain\Monkey\setUp();
        
        // Simuler les fonctions WordPress
        Functions\when('get_the_ID')->justReturn(123);
        Functions\when('get_post_type')->justReturn('post');
        Functions\when('get_posts')->justReturn([]);
        Functions\when('get_post_meta')->justReturn([]);
        Functions\when('esc_html')->returnArg(1);
        Functions\when('esc_attr')->returnArg(1);
        Functions\when('nl2br')->returnArg(1);
    }

    protected function tearDown(): void
    {
        Brain\Monkey\tearDown();
        parent::tearDown();
    }

    public function testGetFieldWithNoResults()
    {
        // Inclure le fichier principal du plugin
        require_once dirname(dirname(__DIR__)) . '/simple-custom-fields.php';
        
        // Appeler la fonction à tester
        $result = scf_get_field('non_existent_field');
        
        // Vérifier que le résultat est null quand aucun champ n'est trouvé
        $this->assertNull($result);
    }
    
    public function testGetFieldWithTextValue()
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
        
        // Appeler la fonction à tester
        $result = scf_get_field('test_field');
        
        // Vérifier que le résultat est correct
        $this->assertEquals('test_value', $result);
    }
    
    public function testGetFieldWithEmailValue()
    {
        // Mock de la classe SCF_Database
        $db_mock = $this->getMockBuilder('SCF_Database')
            ->disableOriginalConstructor()
            ->addMethods(['get_field'])
            ->getMock();
            
        $db_mock->method('get_field')->willReturn((object) [
            'field_value' => 'test@example.com'
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
                'name' => 'test_email',
                'label' => 'Test Email',
                'type' => 'email'
            ]
        ];
        
        Functions\when('get_post_meta')->justReturn($rules, $fields);
        
        // Simuler la fonction sprintf pour le formatage des emails
        Functions\when('sprintf')->returnArg(1);
        
        // Inclure le fichier principal du plugin
        require_once dirname(dirname(__DIR__)) . '/simple-custom-fields.php';
        
        // Appeler la fonction à tester
        $result = scf_get_field('test_email');
        
        // Vérifier que le résultat contient un lien mailto
        $this->assertStringContainsString('mailto:', $result);
        $this->assertStringContainsString('test@example.com', $result);
    }
}
