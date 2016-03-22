<?php
namespace ValuModelerTest\FieldType;


use ValuModeler\FieldType\FloatField;

class FloatFieldTest extends AbstractFieldTest
{
    public function setUp()
    {
        $this->fieldType = new FloatField();
    }

    public function testGetPrimitiveType()
    {
        $this->assertEquals('float', $this->fieldType->getPrimitiveType());
    }
}
