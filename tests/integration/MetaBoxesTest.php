<?php

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;
use Brain\Monkey\Actions;

class MetaBoxesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        
        // Simuler les fonctions WordPress
        Functions\when('post_type_exists')->justReturn(true);
        Functions\when('get_posts')->justReturn([]);
        Functions\when('get_post_meta')->justReturn([]);
        Functions\when('wp_nonce_field')->justReturn('');
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function testMetaBoxRegistration()
    {
        // Vérifier que le hook admin_init est utilisé
        Actions\expectAdded('admin_init')
            ->once()
            ->with(Monkey\Functions\type('callable'));
        
        // Vérifier que la fonction add_meta_boxes est appelée
        Actions\expectAdded('add_meta_boxes')
            ->once()
            ->with(Monkey\Functions\type('callable'));
        
        // Vérifier que la fonction save_post est utilisée
        Actions\expectAdded('save_post')
            ->once()
            ->with(Monkey\Functions\type('callable'));
        
        // Inclure la classe à tester
        require_once dirname(dirname(__DIR__)) . '/includes/class-scf-meta-boxes.php';
        
        // Instancier la classe
        $meta_boxes = SCF_Meta_Boxes::get_instance();
        
        // Vérifier que l'instance est créée correctement
        $this->assertInstanceOf('SCF_Meta_Boxes', $meta_boxes);
    }
    
    public function testAddCustomFieldsMetaBox()
    {
        // Simuler la fonction add_meta_box
        Functions\expect('add_meta_box')
            ->once()
            ->with(
                Monkey\Functions\type('string'),
                Monkey\Functions\type('string'),
                Monkey\Functions\type('callable'),
                'page',
                'normal',
                'high',
                Monkey\Functions\type('array')
            );
        
        // Simuler un groupe de champs
        $group = (object) [
            'ID' => 456,
            'post_title' => 'Test Group'
        ];
        
        // Simuler get_posts pour retourner un groupe de champs
        Functions\when('get_posts')->justReturn([$group]);
        
        // Simuler get_post_meta pour retourner des règles et des champs
        $rules = [
            'type' => 'post_type',
            'value' => 'page'
        ];
        
        $fields = [
            [
                'name' => 'test_field',
                'label' => 'Test Field',
                'type' => 'text'
            ]
        ];
        
        Functions\when('get_post_meta')->justReturn($rules, $fields);
        
        // Inclure la classe à tester
        require_once dirname(dirname(__DIR__)) . '/includes/class-scf-meta-boxes.php';
        
        // Créer un mock pour la classe SCF_Database
        $db_mock = $this->getMockBuilder('SCF_Database')
            ->disableOriginalConstructor()
            ->getMock();
        
        // Définir une fonction globale pour simuler SCF_Database::get_instance()
        Functions\when('SCF_Database::get_instance')->justReturn($db_mock);
        
        // Instancier la classe
        $meta_boxes = SCF_Meta_Boxes::get_instance();
        
        // Créer un objet post pour le test
        $post = (object) [
            'ID' => 123,
            'post_type' => 'page'
        ];
        
        // Appeler la méthode à tester
        $meta_boxes->add_custom_fields_meta_box('page', $post);
    }
}
