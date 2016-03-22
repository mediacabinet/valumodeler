<?php
namespace ValuModelerTest\FieldType;


use ValuModeler\FieldType\EmailField;

class EmailFieldTest extends AbstractFieldTest
{
    public function setUp()
    {
        $this->fieldType = new EmailField();
    }

    public function testGetType()
    {
        $this->assertEquals('email', $this->fieldType->getType());
    }

    public function testGetPrimitiveType()
    {
        $this->assertEquals('string', $this->fieldType->getPrimitiveType());
    }

    public function testGetValidators()
    {
        $this->assertEquals([['name' => 'emailaddress']], $this->fieldType->getValidators());
    }

    public function testIsValidEmail()
    {
        $this->assertIsValidFieldValue('developer@valu.fi');
    }

    public function testIsInvalidEmail()
    {
        $this->assertIsInvalidFieldValue('@valu.fi');
    }
}