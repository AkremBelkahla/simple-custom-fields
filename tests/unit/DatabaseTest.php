<?php

use PHPUnit\Framework\TestCase;
use Brain\Monkey\Functions;

class DatabaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Brain\Monkey\setUp();
        
        // Simuler les fonctions WordPress globales
        Functions\when('get_option')->justReturn(null);
    }

    protected function tearDown(): void
    {
        Brain\Monkey\tearDown();
        parent::tearDown();
    }

    public function testGetInstance()
    {
        // Mock de la classe wpdb
        $wpdb = $this->getMockBuilder('stdClass')
            ->addMethods(['get_var', 'get_row', 'get_results', 'prepare', 'update', 'insert', 'delete'])
            ->getMock();
        
        $wpdb->prefix = 'wp_';
        $wpdb->method('prepare')->willReturn('');
        
        // Définir la variable globale wpdb
        global $wpdb;
        $GLOBALS['wpdb'] = $wpdb;
        
        // Inclure la classe à tester
        require_once dirname(dirname(__DIR__)) . '/includes/class-scf-database.php';
        
        // Tester la méthode get_instance
        $instance1 = SCF_Database::get_instance();
        $instance2 = SCF_Database::get_instance();
        
        $this->assertInstanceOf('SCF_Database', $instance1);
        $this->assertSame($instance1, $instance2, 'Les instances devraient être identiques (singleton)');
    }
    
    public function testGetField()
    {
        // Mock de la classe wpdb
        $wpdb = $this->getMockBuilder('stdClass')
            ->addMethods(['get_var', 'get_row', 'get_results', 'prepare', 'update', 'insert', 'delete'])
            ->getMock();
        
        $wpdb->prefix = 'wp_';
        
        // Configurer le mock pour retourner un résultat attendu
        $expected_result = (object) [
            'id' => 1,
            'post_id' => 123,
            'group_id' => 456,
            'field_name' => 'test_field',
            'field_value' => 'test_value'
        ];
        
        $wpdb->method('get_row')->willReturn($expected_result);
        $wpdb->method('prepare')->willReturn('');
        
        // Définir la variable globale wpdb
        global $wpdb;
        $GLOBALS['wpdb'] = $wpdb;
        
        // Inclure la classe à tester
        require_once dirname(dirname(__DIR__)) . '/includes/class-scf-database.php';
        
        // Tester la méthode get_field
        $db = SCF_Database::get_instance();
        $result = $db->get_field(123, 456, 'test_field');
        
        $this->assertEquals($expected_result, $result);
    }
}
