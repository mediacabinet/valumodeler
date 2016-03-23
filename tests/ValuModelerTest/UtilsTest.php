<?php
namespace ValuModelerTest;

use ValuModeler\Utils;

class UtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testDocNameToClass()
    {
        $this->assertEquals(
            'ValuX\Test\Class',
            Utils::docNameToClass('Test\Class')
        );
    }

    public function testValidClassNameToDocName()
    {
        $this->assertEquals(
            'Test\Class',
            Utils::classToDocName('ValuX\Test\Class')
        );
    }

    public function testInvalidClassNameToDocName()
    {
        $this->assertFalse(
            Utils::classToDocName('ValuY\Test\Class')
        );
    }
}
