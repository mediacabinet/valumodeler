<?php
namespace ValuModelerTest\FieldType;


use ValuModeler\FieldType\StringField;

class StringFieldTest extends AbstractFieldTest
{
    public function setUp()
    {
        $this->fieldType = new StringField();
    }

    public function testGetPrimitiveType()
    {
        $this->assertEquals('string', $this->fieldType->getPrimitiveType());
    }
}
