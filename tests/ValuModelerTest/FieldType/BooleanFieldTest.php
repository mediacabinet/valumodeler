<?php
namespace ValuModelerTest\FieldType;


use ValuModeler\FieldType\BooleanField;

class BooleanFieldTest extends AbstractFieldTest
{
    public function setUp()
    {
        $this->fieldType = new BooleanField();
    }

    public function testGetPrimitiveType()
    {
        $this->assertEquals('boolean', $this->fieldType->getPrimitiveType());
    }
}
