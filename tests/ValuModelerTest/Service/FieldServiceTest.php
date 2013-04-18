<?php
namespace ValuModelerTest\Service;

/**
 * FieldService test case.
 */
class FieldServiceTest extends AbstractModelServiceTestCase
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
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        $this->document = $this->serviceBroker->service('Modeler.Document')->create('Field\Test');
        $this->service = $this->serviceBroker->getLoader()->load('ValuModelerField');
    }
    
    /**
     * Tests FieldService->create()
     */
    public function testCreate()
    {
        $field = $this->service->create($this->document, 'createTest', 'string');
        
        $this->assertInstanceOf(self::FIELD_CLASS, $field);
        $this->assertTrue($field->getName() === 'createTest');
        $this->assertTrue($field->getType()->getPrimitiveType() === 'string');
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
        
        $field = $this->service->create($this->document->getName(), 'triggerTest', 'int');
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

