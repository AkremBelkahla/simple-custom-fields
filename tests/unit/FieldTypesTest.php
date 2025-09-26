<?php

use PHPUnit\Framework\TestCase;
use SCF\Fields\Text;

class FieldTypesTest extends TestCase
{
    public function testTextFieldCreation()
    {
        // Inclure la classe à tester
        require_once dirname(dirname(__DIR__)) . '/includes/Fields/Text.php';
        
        // Créer un champ texte
        $field = new Text('test_field', 'Test Field');
        
        // Vérifier les propriétés
        $this->assertEquals('test_field', $field->getName());
        $this->assertEquals('Test Field', $field->getLabel());
    }
    
    public function testTextFieldValidation()
    {
        // Inclure la classe à tester
        require_once dirname(dirname(__DIR__)) . '/includes/Fields/Text.php';
        
        // Créer un champ texte
        $field = new Text('test_field', 'Test Field');
        
        // Vérifier la validation
        $this->assertTrue($field->validate('test value'));
        $this->assertFalse($field->validate(''));
    }
    
    /**
     * @dataProvider provideTextValues
     */
    public function testTextFieldWithDifferentValues($value, $expected)
    {
        // Inclure la classe à tester
        require_once dirname(dirname(__DIR__)) . '/includes/Fields/Text.php';
        
        // Créer un champ texte
        $field = new Text('test_field', 'Test Field');
        
        // Vérifier la validation
        $this->assertEquals($expected, $field->validate($value));
    }
    
    public function provideTextValues()
    {
        return [
            'empty string' => ['', false],
            'null' => [null, false],
            'zero' => ['0', true],
            'space' => [' ', true],
            'text' => ['test', true],
            'special chars' => ['!@#$%^&*()', true],
        ];
    }
}
