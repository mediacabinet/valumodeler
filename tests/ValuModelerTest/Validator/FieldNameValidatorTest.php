<?php
namespace ValuModelerTest\Validator;

use ValuModeler\Validator\FieldNameValidator;

class FieldNameValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FieldNameValidator
     */
    private $validator;

    public function setUp()
    {
        $this->validator = new FieldNameValidator();
    }

    public function testValidateSimpleAlphaFieldName()
    {
        $this->assertTrue(
            $this->validator->isValid('myField')
        );
    }

    public function testErrorForInvalid()
    {
        $this->validator->isValid('0');
        $this->assertEquals(
            ['invalid' => 'Invalid field name. Valid field name may contain only letters from A to Z, numbers and underscores.'],
            $this->validator->getMessages());
    }

    public function testValidateFieldNameWithAlphaNumericCharacters()
    {
        $this->assertTrue(
            $this->validator->isValid('H20')
        );
    }

    public function testValidateFieldNameWithDash()
    {
        $this->assertFalse(
            $this->validator->isValid('H-2-0')
        );
    }

    public function testValidateFieldNameWithUnderscore()
    {
        $this->assertTrue(
            $this->validator->isValid('H_2_0')
        );
    }

    public function testValidateFieldNameStartingWithNumber()
    {
        $this->assertFalse(
            $this->validator->isValid('0_temperature')
        );
    }
}
