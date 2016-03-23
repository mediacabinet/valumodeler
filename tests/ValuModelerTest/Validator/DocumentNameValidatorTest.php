<?php
namespace ValuModelerTest\Validator;


use ValuModeler\Validator\DocumentNameValidator;

class DocumentNameValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DocumentNameValidator
     */
    private $validator;

    public function setUp()
    {
        $this->validator = new DocumentNameValidator();
    }

    public function testValidateSimpleAlphaDocumentName()
    {
        $this->assertTrue(
            $this->validator->isValid('MyDocument')
        );
    }

    public function testErrorForInvalid()
    {
        $this->validator->isValid('0');
        $this->assertEquals(
            ['invalid' => 'Invalid document name. Valid document name may contain only letters from A to Z, numbers, underscores and backslashes.'],
            $this->validator->getMessages());
    }

    public function testValidateDocumentNameWithNamespace()
    {
        $this->assertTrue(
            $this->validator->isValid('My\Document')
        );
    }

    public function testValidateDocumentNameWithAlphaNumericCharacters()
    {
        $this->assertTrue(
            $this->validator->isValid('Water\H20')
        );
    }

    public function testValidateDocumentNameWithDash()
    {
        $this->assertFalse(
            $this->validator->isValid('Water\H-2-0')
        );
    }

    public function testValidateDocumentNameWithUnderscore()
    {
        $this->assertTrue(
            $this->validator->isValid('Water\H_2_0')
        );
    }

    public function testValidateDocumentNameStartingWithNumber()
    {
        $this->assertFalse(
            $this->validator->isValid('0Temperature')
        );
    }

    public function testValidateDocumentNameStartingWithNumberAfterNamespaceDelimiter()
    {
        $this->assertFalse(
            $this->validator->isValid('Temperature/0_weather')
        );
    }
}
