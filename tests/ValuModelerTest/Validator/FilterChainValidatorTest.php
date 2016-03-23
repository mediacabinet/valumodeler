<?php
namespace ValuModelerTest\Validator;

use ValuModeler\Validator\FilterChainValidator;

class FilterChainValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidateFilterChain()
    {
        $validator = new FilterChainValidator();
        $this->assertTrue($validator->isValid([['name' => 'stripnewlines']]));
    }

    public function testValidateInvalidFilterChain()
    {
        $validator = new FilterChainValidator();
        $this->assertFalse($validator->isValid(['name' => 'stripnewlines']));

        $this->assertEquals(
            ['invalid' => 'Invalid filter chain configuration'],
            $validator->getMessages());
    }
}
