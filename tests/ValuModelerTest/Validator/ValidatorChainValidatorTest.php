<?php
namespace ValuModelerTest\Validator;

use ValuModeler\Validator\ValidatorChainValidator;

class ValidatorChainValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidateValidatorChain()
    {
        $validator = new ValidatorChainValidator();
        $this->assertTrue($validator->isValid([['name' => 'emailaddress']]));
    }

    public function testValidateInvalidValidatorChain()
    {
        $validator = new ValidatorChainValidator();
        $this->assertFalse($validator->isValid(['name' => 'emailaddress']));

        $this->assertEquals(
            ['invalid' => 'Invalid validator chain configuration'],
            $validator->getMessages());
    }
}
