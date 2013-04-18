<?php
namespace ValuModelerTest\Service;

use Zend\Mvc\Application;
use ValuModeler\Service\DocumentService;

/**
 * DocumentService test case.
 */
class DocumentServiceTest extends AbstractModelServiceTestCase
{
    const DOCUMENT_CLASS = 'ValuModeler\Model\Document';
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->service = $this->serviceBroker->getLoader()->load('ValuModelerDocument');
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
        // TODO Auto-generated DocumentServiceTest->testGetInputFilterSpecs()
        $this->markTestIncomplete("getInputFilterSpecs test not implemented");
        
        $this->service->getInputFilterSpecs(/* parameters */);
    }

    /**
     * Tests DocumentService->getInputFilter()
     */
    public function testGetInputFilter()
    {
        // TODO Auto-generated DocumentServiceTest->testGetInputFilter()
        $this->markTestIncomplete("getInputFilter test not implemented");
        
        $this->service->getInputFilter(/* parameters */);
    }
}

