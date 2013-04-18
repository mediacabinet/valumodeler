<?php
namespace ValuModelerTest\Service;

/**
 * EmbedService test case.
 */
class EmbedServiceTest extends AbstractModelServiceTestCase
{

    const EMBED_CLASS = 'ValuModeler\Model\Embed';
    
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
        
        $this->document = $this->serviceBroker->service('Modeler.Document')->create('TestDocument');
        $this->reference = $this->serviceBroker->service('Modeler.Document')->create('ReferenceDocument');
        $this->service = $this->serviceBroker->getLoader()->load('ValuModelerEmbed');
    }
    
    /**
     * Tests EmbedService->create()
     */
    public function testCreate()
    {
        $embed = $this->service->create($this->document->getName(), 'createTest', 'ReferenceDocument', 'embed_one');
        
        $this->assertInstanceOf(self::EMBED_CLASS, $embed);
        $this->assertTrue($embed->getName() === 'createTest');
        $this->assertTrue($embed->getType() === 'embed_one');
        $this->assertTrue($embed->getDocument() === $this->reference);
    }
    
    public function testCreateTriggersEvents()
    {
        $triggered = false;
        
        $this->serviceBroker->getEventManager()->attach('post.valumodelerembed.create', function($e) use(&$triggered) {
            $triggered = true;
        });
        
        $embed = $this->service->create($this->document->getName(), 'triggerTest', 'ReferenceDocument', 'embed_one');
        $this->assertTrue($triggered);
    }

    /**
     * Tests EmbedService->createMany()
     */
    public function testCreateMany()
    {
        $embeds = $this->service->createMany($this->document, [
            ['name' => 'many1', 'embedType' => 'embed_one', 'embedDocument' => 'ReferenceDocument'],        
            ['name' => 'many2', 'embedType' => 'embed_many', 'embedDocument' => 'ReferenceDocument'],        
        ]);
        
        $this->assertEquals(2, sizeof($embeds));
        $this->assertInstanceOf(self::EMBED_CLASS, $embeds[0]);
        $this->assertInstanceOf(self::EMBED_CLASS, $embeds[1]);
    }
    
    /**
     * Tests EmbedService->exists()
     */
    public function testExists()
    {
        $embed = $this->service->create($this->document->getName(), 'existsTest', 'ReferenceDocument', 'embed_one');
        $this->assertTrue($this->service->exists($this->document, 'existsTest'));
    }

    /**
     * Tests EmbedService->remove()
     */
    public function testRemove()
    {
        $embed = $this->service->create($this->document->getName(), 'removeTest', 'ReferenceDocument', 'embed_one');
        $this->assertTrue($this->service->remove($this->document, 'removeTest'));
        $this->assertFalse($this->service->exists($this->document, 'removeTest'));
    }
    
    public function testRemoveTriggersEvents()
    {
        $triggered = false;
        
        $this->serviceBroker->getEventManager()->attach('post.valumodelerembed.remove', function($e) use(&$triggered) {
            
            // Ensure that name is passed
            if ($e->getParam('name') === 'rmTriggerTest') {
                $triggered = true;
            }
            
        });
        
        $embed = $this->service->create($this->document->getName(), 'rmTriggerTest', 'ReferenceDocument', 'embed_one');
        $this->service->remove($this->document, 'rmTriggerTest');
        $this->assertTrue($triggered); 
    }

    /**
     * Tests EmbedService->removeMany()
     */
    public function testRemoveMany()
    {
        $embeds = $this->service->createMany($this->document, [
            ['name' => 'rm1', 'embedType' => 'embed_one', 'embedDocument' => 'ReferenceDocument'],
            ['name' => 'rm2', 'embedType' => 'embed_many', 'embedDocument' => 'ReferenceDocument'],
        ]);
        
        $result = $this->service->removeMany(
            $this->document,
            ['rm1', 'rm2', 'rm3']);
        
        $this->assertEquals(
            [true, true, false],
            $result);
    }
}

