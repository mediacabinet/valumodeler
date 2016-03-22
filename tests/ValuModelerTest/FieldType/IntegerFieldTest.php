<?php
namespace ValuModelerTest\FieldType;


use ValuModeler\FieldType\IntegerField;

class IntegerFieldTest extends AbstractFieldTest
{
    public function setUp()
    {
        $this->fieldType = new IntegerField();
    }

    public function testGetPrimitiveType()
    {
        $this->assertEquals('integer', $this->fieldType->getPrimitiveType());
    }
}
