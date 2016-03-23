<?php
namespace ValuModelerTest\FieldType;

use ValuModeler\FieldType\FieldTypeFactory;

class FieldTypeFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FieldTypeFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new FieldTypeFactory([
            'email' => 'ValuModeler\FieldType\EmailField'
        ]);
    }

    /**
     * @expectedException  \ValuModeler\FieldType\Exception\UnknownFieldTypeException
     */
    public function testRegisterFieldTypeClassDoesNotExist()
    {
        $this->factory->registerFieldType(
            'x', 'ValuModelerTest\FieldType\X');
    }

    public function testIsValidFieldType()
    {
        $this->assertTrue($this->factory->isValidFieldType('email'));
    }

    public function testIsValidFieldTypeTypedWithCaps()
    {
        $this->assertTrue($this->factory->isValidFieldType('Email'));
    }
    
    public function testIsInvalidFieldType()
    {
        $this->assertFalse($this->factory->isValidFieldType('email-address'));
    }
    
    public function testCreateFieldType()
    {
        $this->assertInstanceOf('ValuModeler\FieldType\EmailField', $this->factory->createFieldType('email'));
    }

    /**
     * @expectedException  \ValuModeler\FieldType\Exception\UnknownFieldTypeException
     */
    public function testCreateFieldTypeThatHasNotBeenRegistered()
    {
        $this->factory->createFieldType('longtext');
    }

    /**
     * @expectedException  \ValuModeler\FieldType\Exception\InvalidFieldTypeException
     */
    public function testCreateFieldTypeWhenClassDoesNotImplementCorrectInterface()
    {
        $factory = new FieldTypeFactory([
            'email' => 'ValuModelerTest\FieldType\FieldTypeFactoryTest'
        ]);

        $factory->createFieldType('email');
    }
}
