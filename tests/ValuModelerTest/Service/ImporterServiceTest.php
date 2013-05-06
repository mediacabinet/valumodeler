<?php
namespace ValuModelerTest\Service;

use Zend\Mvc\Application;
use ValuModeler\Service\DocumentService;

/**
 * DocumentService test case.
 */
class DocumentServiceTest extends AbstractEntityServiceTestCase
{
    const DOCUMENT_CLASS = 'ValuModeler\Model\Document';
    
    /**
     * @var \ValuModeler\Service\ImporterService
     */
    protected $service;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->service = $this->serviceBroker->getLoader()->load('ValuModelerImporter');
    }
    
    public function testImport()
    {
        $specs = [
            'import1' => [
                'name' => 'Import1',
                'fields' => [
                    [
                        'name' => 'field1',
                        'required' => true,
                        'allowEmpty' => false,
                        'fieldType' => 'string',
                        'filters' => [
                            ['name' => 'string_trim']
                        ],
                        'validators' => [
                            ['name' => 'emailaddress']
                        ]
                    ]
                ]
            ],
            'import2' => [
                'name' => 'Import2',
                'fields' => [
                    [
                        'name' => 'field1',
                        'required' => true,
                        'allowEmpty' => false,
                        'fieldType' => 'string',
                        'filters' => [
                            ['name' => 'string_trim']
                        ],
                        'validators' => [
                            ['name' => 'emailaddress']
                        ]
                    ]
                ],
                'associations' => [
                    [
                        'name' => 'ref1', 
                        'associationType' => 'reference_one', 
                        'refDocument' => 'Import1'
                    ]
                ]
            ]
        ];
        
        $documents = $this->service->import($specs);
        $this->assertEquals(sizeof($specs), sizeof($documents));
        $this->assertArrayHasKey('import1', $documents);
        $this->assertArrayHasKey('import2', $documents);
        
        $this->assertInstanceOf('ValuModeler\Model\Field', $documents['import1']->getField('field1'));
        $this->assertInstanceOf('ValuModeler\Model\Field', $documents['import2']->getField('field1'));
        
        $this->assertInstanceOf('ValuModeler\Model\Reference', $documents['import2']->getAssociation('ref1'));
    }
}