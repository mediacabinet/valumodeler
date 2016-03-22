<?php
namespace ValuModelerTest\FieldType;

use ValuModeler\FieldType\FieldTypeInterface;
use ValuModeler\Model\Field;
use Zend\InputFilter\Factory;

abstract class AbstractFieldTest extends  \PHPUnit_Framework_TestCase
{
    /**
     * @var FieldTypeInterface
     */
    protected $fieldType;

    protected $testFieldName = 'test';

    public function testGetType()
    {
        $this->assertEquals($this->fieldType->getPrimitiveType(), $this->fieldType->getType());
    }

    public function testGetPrimitiveType()
    {
        throw new \Exception("Test not implemented");
    }

    public function testGetOptions()
    {
        $this->assertEquals([], $this->fieldType->getOptions());
    }

    public function testGetFilters()
    {
        $this->assertEquals([], $this->fieldType->getFilters());
    }

    public function testGetValidators()
    {
        $this->assertEquals([], $this->fieldType->getValidators());
    }

    public function assertIsValidFieldValue($value)
    {
        $inputFilter = $this->createInputFilter($this->fieldType);
        $inputFilter->setData([$this->testFieldName => $value]);

        $this->assertTrue($inputFilter->isValid());
    }

    public function assertIsInvalidFieldValue($value)
    {
        $inputFilter = $this->createInputFilter($this->fieldType);
        $inputFilter->setData([$this->testFieldName => $value]);

        $this->assertFalse($inputFilter->isValid());
    }

    protected function createInputFilter(FieldTypeInterface $type)
    {
        $field = $this->createTestField($type);
        $factory = new Factory();

        $config = [];
        $config[$this->testFieldName] = $field->getInputFilterSpecifications();

        return $factory->createInputFilter($config);
    }

    protected function createTestField(FieldTypeInterface $type, $options = [])
    {
        return new Field($this->testFieldName, $type, $options);
    }
}