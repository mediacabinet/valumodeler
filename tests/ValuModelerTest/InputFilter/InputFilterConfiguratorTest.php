<?php
namespace ValuModelerTest\InputFilter;

use ValuModeler\InputFilter\InputFilterConfigurator;
use ValuModelerTest\Mock\MockConfigurator;
use ValuModelerTest\Service\AbstractEntityServiceTestCase;

class InputFilterConfiguratorTest extends AbstractEntityServiceTestCase
{
    /**
     * @var InputFilterConfigurator
     */
    private $configurator;

    public function setUp()
    {
        parent::setUp();
        $this->configurator = new InputFilterConfigurator($this->serviceBroker);
    }

    public function testGetInputFilterSpecifications()
    {
        $mockConfigurator = new MockConfigurator();

        $specs = [
            'TestDocument' => [
                'name' => 'TestDocument',
                'fields' => [
                    [
                        'name' => 'email',
                        'required' => true,
                        'allowEmpty' => false,
                        'fieldType' => 'email'
                    ]
                ]
            ]
        ];

        $this->serviceBroker
            ->service('Modeler.Importer')
            ->import($specs);

        $specs = $this->configurator
                ->getInputFilterSpecifications($mockConfigurator, 'modeler://TestDocument');

        $this->assertEquals([
            'email' => [
                'name' => 'email',
                'required' => true,
                'allow_empty' => false,
                'validators' => [
                    ['name' => 'emailaddress']
                ],
                'filters' => []
            ]
        ], $specs);
    }

    public function testGetInputFilterSpecificationsWhenDocumentIsNotFound()
    {
        $mockConfigurator = new MockConfigurator();

        $specs = $this->configurator
            ->getInputFilterSpecifications($mockConfigurator, 'modeler://Foo');

        $this->assertEquals([], $specs);
    }
}
