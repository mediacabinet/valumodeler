<?php
namespace ValuModelerTest\FieldType;


use ValuModeler\FieldType\DateField;

class DateFieldTest extends AbstractFieldTest
{
    public function setUp()
    {
        $this->fieldType = new DateField();
    }

    public function testGetPrimitiveType()
    {
        $this->assertEquals('date', $this->fieldType->getPrimitiveType());
    }
}