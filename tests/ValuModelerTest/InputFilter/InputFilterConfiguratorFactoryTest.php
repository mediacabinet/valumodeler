<?php
namespace ValuModelerTest\InputFilter;

use ValuModeler\InputFilter\InputFilterConfiguratorFactory;
use ValuModelerTest\Service\AbstractEntityServiceTestCase;

class InputFilterConfiguratorFactoryTest extends AbstractEntityServiceTestCase
{
    public function testCreateService()
    {
        $factory = new InputFilterConfiguratorFactory();
        $service = $factory->createService(self::$sm);

        $this->assertInstanceOf(
            'ValuModeler\InputFilter\InputFilterConfigurator', $service);
    }
}
