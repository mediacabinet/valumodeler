<?php
namespace ValuModelerTest\FieldType;


use ValuModeler\FieldType\TextField;

class TextFieldTest extends AbstractFieldTest
{
    public function setUp()
    {
        $this->fieldType = new TextField();
    }

    public function testGetType()
    {
        $this->assertEquals('text', $this->fieldType->getType());
    }

    public function testGetOptions()
    {
        $this->assertEquals(['multiline' => true], $this->fieldType->getOptions());
    }

    public function testSetGetMultiline()
    {
        $this->assertTrue($this->fieldType->getMultiline());
        $this->fieldType->setMultiline(false);
        $this->assertFalse($this->fieldType->getMultiline());
        $options = $this->fieldType->getOptions();
        $this->assertFalse($options['multiline']);
    }
    
    public function testGetFiltersWhenMultilineDisabled()
    {
        $this->fieldType->setMultiline(false);
        $this->assertEquals([['name' => 'stripnewlines']], $this->fieldType->getFilters());
    }

    public function testGetPrimitiveType()
    {
        $this->assertEquals('string', $this->fieldType->getPrimitiveType());
    }
}
