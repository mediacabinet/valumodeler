<?php
namespace ValuModelerTest\Service;

/**
 * DocumentService test case.
 */
class DocumentServiceTest extends AbstractEntityServiceTestCase
{
    const DOCUMENT_CLASS = 'ValuModeler\Model\Document';
    
    /**
     * @var \ValuModeler\Service\DocumentService
     */
    protected $service;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->service = $this->serviceBroker->service('Modeler.Document');
    }
    
    /**
     * Tests DocumentService->create()
     */
    public function testCreate()
    {
        $parent = $this->service->create('Create\Parent');
        
        $specs = [
            'collection' => 'valu_test_document',
            'parent'     => $parent
        ];
        
        $document = $this->service->create('Create\Child', $specs);
        
        $this->assertInstanceOf(self::DOCUMENT_CLASS, $document);
        $this->assertEquals($specs['collection'], $document->getCollection());
        $this->assertSame($parent, $document->getParent());
    }

    public function testCreateTriggersEvents()
    {
        $triggered = false;
        $name = 'Create\Trigger';
    
        $this->serviceBroker->getEventManager()->attach('post.valumodelerdocument.create', function($e) use(&$triggered, $name) {
            
            if ($e->getParam('name') === $name ) {
                $triggered = true;
            }
        });
    
        $doc = $this->service->create($name);
        $this->assertTrue($triggered);
    }
    
    /**
     * @expectedException \ValuModeler\Service\Exception\ValidationException
     */
    public function testCreateFailsWithoutDocumentName()
    {
        $this->service->create();
    }
    
    /**
     * @expectedException \ValuModeler\Service\Exception\DocumentAlreadyExistsException
     */
    public function testCreateFailsWithReservedDocumentName()
    {
        $this->service->create('Create\Existing');
        $this->service->create('Create\Existing');
    }
    
    /**
     * @expectedException \ValuModeler\Service\Exception\ServiceException
     */
    public function testCreateFailsWithReservedCollection()
    {
        $this->service->create('Create\CollectionTest', ['collection' => 'reserved']);
        $this->service->create('Create\ReservedCollectionTest', ['collection' => 'reserved']);
    }

    /**
     * Tests DocumentService->createMany()
     */
    public function testCreateMany()
    {
        $result = $this->service->createMany([
            ['name' => 'Create\Many1'],
            ['name' => 'Create\Many2'],
        ]);
        
        $this->assertEquals(2, sizeof($result));
        $this->assertInstanceOf(self::DOCUMENT_CLASS, $result[0]);
    }
    
    public function testCreateManyWithSkipExisting()
    {
        $result = $this->service->createMany([
            ['name' => 'Create\Skip1'],
            ['name' => 'Create\Skip2'],
            ['name' => 'Create\Skip1'],
        ], ['skip_existing' => true]);
        
        $this->assertEquals(3, sizeof($result));
        $this->assertNull($result[2]);
    }
    
    /**
     * Tests DocumentService->update()
     */
    public function testUpdate()
    {
        $parent = $this->service->create('Update\Parent');
        
        $specs = [
            'collection' => 'update_collection',
            'parent'     => $parent
        ];
        
        $document = $this->service->create('UpdateTest', $specs);
        
        $newParent = $this->service->create('Update\NewParent');
        $newSpecs = [
            'collection' => 'new_update_collection',
            'parent'     => $newParent
        ];
        
        $this->service->update($document, $newSpecs);
        
        $this->assertEquals($newSpecs['collection'], $document->getCollection());
        $this->assertSame($newParent, $document->getParent());
    }
    
    public function testUpdateTriggersEvents()
    {
        $doc = $this->service->create('UpdateTriggerTest');
        
        $triggered = 0;
        $this->serviceBroker->getEventManager()->attach(['post.valumodelerdocument.update','post.valumodelerdocument.change'], function($e) use(&$triggered) {
            $triggered++;
        });
        
        $this->service->update(
            'UpdateTriggerTest', 
            ['collection' => 'update_trigger_collection']);
        
        $this->assertEquals(2, $triggered);
    }
    
    public function testUpsert()
    {
        $create = $this->service->upsert('UpsertTest', ['collection' => 'upsert_1']);
        $update = $this->service->upsert('UpsertTest', ['collection' => 'upsert_2']);
        
        $this->assertSame($create, $update);
        $this->assertEquals('upsert_2', $update->getCollection());
    }
    
    /**
     * Tests DocumentService->exists()
     */
    public function testExists()
    {
        $this->service->create('Create\ExistsTest');
        $this->assertTrue($this->service->exists('Create\ExistsTest'));
        $this->assertFalse($this->service->exists('Create\existstest'));
    }

    /**
     * Tests DocumentService->remove()
     */
    public function testRemove()
    {
        // TODO Auto-generated DocumentServiceTest->testRemove()
        $this->markTestIncomplete("remove test not implemented");
        
        $this->service->remove(/* parameters */);
    }
    
    public function testRemoveTriggersEvents()
    {
        $triggered = false;
        $document = $this->service->create('Remove\Trigger');
        
        $this->serviceBroker->getEventManager()->attach('post.valumodelerdocument.remove', function($e) use(&$triggered, $document) {
            // Ensure that name is passed
            if ($e->getParam('document') === $document) {
                $triggered = true;
            }
        });
        
        
        $this->service->remove($document);
        $this->assertTrue($triggered); 
    }

    /**
     * Tests DocumentService->removeMany()
     */
    public function testRemoveMany()
    {
        $this->service->create('Remove\Trigger1');
        $this->service->create('Remove\Trigger2');
        
        $result = $this->service->removeMany(['Remove\Trigger1', 'Remove\Trigger2', 'Remove\Trigger3']);
        
        $this->assertEquals(3, sizeof($result));
        $this->assertTrue($result[0]);
        $this->assertFalse($result[2]);
    }

    /**
     * Tests DocumentService->getInputFilterSpecs()
     */
    public function testGetInputFilterSpecs()
    {
        $document = $this->service->create('InputFilterSpecTest');
        $this->serviceBroker->service('Modeler.Field')->create(
            'InputFilterSpecTest',
            'email',
            'email',
            ['required' => true, 'allowEmpty' => false]
        );
        
        $specs = $this->service->getInputFilterSpecs($document);
        
        $this->assertArrayHasKey('email', $specs);
        $this->assertTrue($specs['email']['required']);
        $this->assertFalse($specs['email']['allow_empty']);
    }

    /**
     * Tests DocumentService->getInputFilter()
     */
    public function testGetInputFilter()
    {
        $document = $this->service->create('InputFilterTest');
        $inputFilter = $this->service->getInputFilter($document);
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $inputFilter);
    }
}

