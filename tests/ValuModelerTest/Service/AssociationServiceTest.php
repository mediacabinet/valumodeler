<?php
namespace ValuModelerTest\Service;

use ValuModeler\Service\AssociationService;

/**
 * AssociationService test case.
 */
class AssociationServiceTest extends AbstractModelServiceTestCase
{

    const ASSOC_CLASS = 'ValuModeler\Model\AbstractAssociation';
    const EMBED_CLASS = 'ValuModeler\Model\Embed';
    const REFERENCE_CLASS = 'ValuModeler\Model\Reference';
    
    /**
     * @var \ValuModeler\Model\Document
     */
    private $document;
    
    /**
     * @var \ValuModeler\Model\Document
     */
    private $reference;
    
    /**
     * @var AssociationService
     */
    protected $service;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        $this->document = $this->serviceBroker->service('Modeler.Document')->create('TestDocument');
        $this->reference = $this->serviceBroker->service('Modeler.Document')->create('ReferenceDocument');
        $this->service = $this->serviceBroker->getLoader()->load('ValuModelerAssociation');
    }
    
    /**
     * Tests AssociationService->create()
     */
    public function testCreate()
    {
        $assoc = $this->service->create($this->document->getName(), 'createTest', 'ReferenceDocument', 'reference_one');
    
        $this->assertInstanceOf(self::REFERENCE_CLASS, $assoc);
        $this->assertTrue($assoc->getName() === 'createTest');
        $this->assertTrue($assoc->getType() === 'reference_one');
        $this->assertTrue($assoc->getDocument() === $this->reference);
    }
    
    /**
     * Tests AssociationService->create()
     */
    public function testCreateEmbedded()
    {
        $embed = $this->service->create($this->document->getName(), 'createEmbedTest', 'ReferenceDocument', 'reference_one', true);
        
        $this->assertInstanceOf(self::EMBED_CLASS, $embed);
        $this->assertTrue($embed->getName() === 'createEmbedTest');
        $this->assertTrue($embed->getType() === 'reference_one');
        $this->assertTrue($embed->getDocument() === $this->reference);
    }
    
    public function testCreateTriggersEvents()
    {
        $triggered = false;
        
        $this->serviceBroker->getEventManager()->attach('post.valumodelerassociation.create', function($e) use(&$triggered) {
            $triggered = true;
        });
        
        $this->service->create($this->document->getName(), 'triggerTest', 'ReferenceDocument', 'reference_one');
        $this->assertTrue($triggered);
    }

    /**
     * Tests AssociationService->createMany()
     */
    public function testCreateMany()
    {
        $assocs = $this->service->createMany($this->document, [
            ['name' => 'many1', 'associationType' => 'reference_one', 'refDocument' => 'ReferenceDocument'],        
            ['name' => 'many2', 'associationType' => 'reference_many', 'refDocument' => 'ReferenceDocument'],        
        ]);
        
        $this->assertEquals(2, sizeof($assocs));
        $this->assertInstanceOf(self::ASSOC_CLASS, $assocs[0]);
        $this->assertInstanceOf(self::ASSOC_CLASS, $assocs[1]);
    }
    
    /**
     * Tests AssociationService->exists()
     */
    public function testExists()
    {
        $this->service->create($this->document->getName(), 'existsTest', 'ReferenceDocument', 'reference_one');
        $this->assertTrue($this->service->exists($this->document, 'existsTest'));
    }

    /**
     * Tests AssociationService->remove()
     */
    public function testRemove()
    {
        $this->service->create($this->document->getName(), 'removeTest', 'ReferenceDocument', 'reference_one');
        $this->assertTrue($this->service->remove($this->document, 'removeTest'));
        $this->assertFalse($this->service->exists($this->document, 'removeTest'));
    }
    
    public function testRemoveTriggersEvents()
    {
        $triggered = false;
        
        $this->serviceBroker->getEventManager()->attach('post.valumodelerassociation.remove', function($e) use(&$triggered) {
            
            // Ensure that name is passed
            if ($e->getParam('name') === 'rmTriggerTest') {
                $triggered = true;
            }
            
        });
        
        $this->service->create($this->document->getName(), 'rmTriggerTest', 'ReferenceDocument', 'reference_one');
        $this->service->remove($this->document, 'rmTriggerTest');
        $this->assertTrue($triggered); 
    }

    /**
     * Tests AssociationService->removeMany()
     */
    public function testRemoveMany()
    {
        $assocs = $this->service->createMany($this->document, [
            ['name' => 'rm1', 'associationType' => 'reference_one', 'refDocument' => 'ReferenceDocument'],
            ['name' => 'rm2', 'associationType' => 'reference_many', 'refDocument' => 'ReferenceDocument'],
        ]);
        
        $result = $this->service->removeMany(
            $this->document,
            ['rm1', 'rm2', 'rm3']);
        
        $this->assertEquals(
            [true, true, false],
            $result);
    }
}

