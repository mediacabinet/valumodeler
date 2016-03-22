<?php
namespace ValuModelerTest\FieldType;


use ValuModeler\FieldType\CollectionField;

class CollectionFieldTest extends AbstractFieldTest
{
    public function setUp()
    {
        $this->fieldType = new CollectionField();
    }

    public function testGetPrimitiveType()
    {
        $this->assertEquals('collection', $this->fieldType->getPrimitiveType());
    }
}