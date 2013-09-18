<?php
namespace ValuModelerTest\Service;

/**
 * FieldService test case.
 */
class FieldServiceTest extends AbstractEntityServiceTestCase
{

    const FIELD_CLASS = 'ValuModeler\Model\Field';
    
    /**
     * @var \ValuModeler\Model\Document
     */
    private $document;
    
    /**
     * @var \ValuModeler\Model\Document
     */
    private $reference;
    
    /**
     * @var \ValuModeler\Service\FieldService
     */
    protected $service;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        $this->document = $this->serviceBroker->service('Modeler.Document')->create('Field\Test');
        $this->service = $this->serviceBroker->service('Modeler.Field');
    }
    
    /**
     * Tests FieldService->create()
     */
    public function testCreate()
    {
        $specs = [
            'required' => true,
            'allowEmpty' => false,
            'filters' => [
                ['name' => 'string_trim']
            ],
            'validators' => [
                ['name' => 'emailaddress']
            ]
        ];
        
        $field = $this->service->create($this->document, 'createTest', 'string', $specs);
        
        $this->assertInstanceOf(self::FIELD_CLASS, $field);
        $this->assertEquals('createTest', $field->getName());
        $this->assertEquals('string', $field->getType()->getPrimitiveType());
        $this->assertTrue($field->getRequired());
        $this->assertFalse($field->getAllowEmpty());
        $this->assertEquals($specs['validators'], $field->getValidators());
        $this->assertEquals($specs['filters'], $field->getFilters());
    }
    
    /**
     * @expectedException \ValuModeler\Service\Exception\ValidationException
     */
    public function testCreateFailsWithInvalidFieldType()
    {
        $field = $this->service->create($this->document, 'createTest', 'invalid');
    }
    
    public function testCreateTriggersEvents()
    {
        $triggered = false;
        
        $this->serviceBroker->getEventManager()->attach('post.valumodelerfield.create', function($e) use(&$triggered) {
            $triggered = true;
        });
        
        $field = $this->service->create($this->document->getName(), 'triggerTest', 'integer');
        $this->assertTrue($triggered);
    }

    /**
     * Tests FieldService->createMany()
     */
    public function testCreateMany()
    {
        $fields = $this->service->createMany($this->document, [
            ['name' => 'many1', 'fieldType' => 'string'],        
            ['name' => 'many2', 'fieldType' => 'string'],        
        ]);
        
        $this->assertEquals(2, sizeof($fields));
        $this->assertInstanceOf(self::FIELD_CLASS, $fields[0]);
        $this->assertInstanceOf(self::FIELD_CLASS, $fields[1]);
    }
    
    /**
     * Tests FieldService->update()
     */
    public function testUpdate()
    {
        $specs = [
            'fieldType' => 'integer',
            'required' => true,
            'allowEmpty' => false,
            'filters' => [
                ['name' => 'string_trim']
            ],
            'validators' => [
                ['name' => 'emailaddress']
            ]
        ];
        
        $field = $this->service->create($this->document, 'updateTest', 'string');
        $this->service->update($this->document, 'updateTest', $specs);
        
        $this->assertEquals('updateTest', $field->getName());
        $this->assertEquals('integer', $field->getType()->getPrimitiveType());
        $this->assertTrue($field->getRequired());
        $this->assertFalse($field->getAllowEmpty());
        $this->assertEquals($specs['validators'], $field->getValidators());
        $this->assertEquals($specs['filters'], $field->getFilters());
    }
    
    /**
     * Tests FieldService->testUpsert()
     */
    public function testUpsert()
    {
        $field = $this->service->upsert($this->document, 'upsertTest', ['fieldType' => 'string']);
        $this->assertInstanceOf(self::FIELD_CLASS, $field);
        
        $field = $this->service->upsert($this->document, 'upsertTest', ['fieldType' => 'integer']);
        $this->assertEquals('integer', $field->getType()->getPrimitiveType());
    }
    
    public function testUpsertMany()
    {
        $this->service->create($this->document, 'upsert1', 'string');
        
        $result = $this->service->upsertMany($this->document, [
            ['name' => 'upsert1', 'fieldType' => 'integer'],
            ['name' => 'upsert2', 'fieldType' => 'string'],
        ]);
        
        $this->assertEquals(2, sizeof($result));
        $this->assertEquals('integer', $result[0]->getType()->getPrimitiveType());
        $this->assertEquals('string', $result[1]->getType()->getPrimitiveType());
    }
    
    /**
     * Tests FieldService->exists()
     */
    public function testExists()
    {
        $field = $this->service->create($this->document, 'existsTest', 'string');
        $this->assertTrue($this->service->exists($this->document, 'existsTest'));
    }

    /**
     * Tests FieldService->remove()
     */
    public function testRemove()
    {
        $field = $this->service->create($this->document->getName(), 'removeTest', 'string');
        $this->assertTrue($this->service->remove($this->document, 'removeTest'));
        $this->assertFalse($this->service->exists($this->document, 'removeTest'));
    }
    
    public function testRemoveTriggersEvents()
    {
        $triggered = false;
        
        $this->serviceBroker->getEventManager()->attach('post.valumodelerfield.remove', function($e) use(&$triggered) {
            
            // Ensure that name is passed
            if ($e->getParam('name') === 'rmTriggerTest') {
                $triggered = true;
            }
            
        });
        
        $field = $this->service->create($this->document->getName(), 'rmTriggerTest', 'integer');
        $this->service->remove($this->document, 'rmTriggerTest');
        $this->assertTrue($triggered); 
    }

    /**
     * Tests FieldService->removeMany()
     */
    public function testRemoveMany()
    {
        $fields = $this->service->createMany($this->document, [
            ['name' => 'rm1', 'fieldType' => 'string'],
            ['name' => 'rm2', 'fieldType' => 'string'],
        ]);
        
        $result = $this->service->removeMany(
            $this->document,
            ['rm1', 'rm2', 'rm3']);
        
        $this->assertEquals(
            [true, true, false],
            $result);
    }
}

