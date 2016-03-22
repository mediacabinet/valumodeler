<?php
namespace ValuModelerTest\FieldType;


use ValuModeler\FieldType\MapField;

class MapFieldTest extends AbstractFieldTest
{
    public function setUp()
    {
        $this->fieldType = new MapField();
    }

    public function testGetPrimitiveType()
    {
        $this->assertEquals('map', $this->fieldType->getPrimitiveType());
    }
}
