<?php

use PHPUnit\Framework\TestCase;
use SCF\Fields\Text;

class FieldsTest extends TestCase
{
    public function testFieldCreation()
    {
        $field = new Text('test_field', 'Test Field');
        $this->assertEquals('test_field', $field->getName());
        $this->assertEquals('Test Field', $field->getLabel());
    }

    public function testFieldValidation()
    {
        $field = new Text('test_field', 'Test Field');
        $this->assertTrue($field->validate('valid value'));
    }
}
