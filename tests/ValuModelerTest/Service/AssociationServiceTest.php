<?php
namespace ValuModelerTest\Service;

use ValuModeler\Service\AssociationService;

/**
 * AssociationService test case.
 */
class AssociationServiceTest extends AbstractEntityServiceTestCase
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
        $this->service = $this->serviceBroker->service('Modeler.Association');
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->document = null;
        $this->reference = null;
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
     * @expectedException \ValuModeler\Service\Exception\DocumentNotFoundException
     */
    public function testCreateFailsWithInvalidRefDocument()
    {
        $assoc = $this->service->create($this->document->getName(), 'createTest', 'InvalidDocument', 'reference_one');
    }

    /**
     * Tests AssociationService->create()
     */
    public function testCreateEmbedded()
    {
        $embed = $this->service->create(
            $this->document->getName(), 'createEmbedTest', 'ReferenceDocument', 'reference_one', true);

        $this->assertInstanceOf(self::EMBED_CLASS, $embed);
        $this->assertTrue($embed->getName() === 'createEmbedTest');
        $this->assertTrue($embed->getType() === 'reference_one');
        $this->assertTrue($embed->getDocument() === $this->reference);
    }

    public function testCreateTriggersEvents()
    {
        $triggered = 0;

        $this->serviceBroker->getEventManager()->attach(['post.valumodelerassociation.create', 'post.valumodelerdocument.change'], function($e) use(&$triggered) {
            $triggered++;
        });

        $this->service->create($this->document->getName(), 'triggerTest', 'ReferenceDocument', 'reference_one');
        $this->assertEquals(2, $triggered);
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
     * Tests AssociationService->update()
     */
    public function testUpdate()
    {
        $newRef = $this->serviceBroker->service('Modeler.Document')->create('UpdateReference');
        $assoc = $this->service->create($this->document->getName(), 'updateTest', 'ReferenceDocument', 'reference_one');

        $this->service->update($this->document, 'updateTest', ['refDocument' => 'UpdateReference', 'associationType' => 'reference_many']);

        $this->assertTrue($assoc->getDocument() === $newRef);
        $this->assertTrue($assoc->getType() === 'reference_many');
    }

    public function testUpdateTriggersEvents()
    {

        $this->service->create($this->document, 'triggerTest', 'ReferenceDocument', 'reference_one');

        $triggered = 0;
        $this->serviceBroker->getEventManager()->attach(['post.valumodelerassociation.update', 'post.valumodelerdocument.change'], function($e) use(&$triggered) {
            $triggered++;
        });

        $this->service->update($this->document, 'triggerTest', ['associationType' => 'reference_many']);
        $this->assertEquals(2, $triggered);
    }

    /**
     * Tests AssociationService->testUpsert()
     */
    public function testUpsert()
    {
        $newRef = $this->serviceBroker->service('Modeler.Document')->create('UpsertReference');

        $assoc = $this->service->upsert($this->document, 'upsertTest', ['associationType' => 'reference_many', 'refDocument' => 'ReferenceDocument']);
        $this->assertInstanceOf(self::ASSOC_CLASS, $assoc);

        $assoc = $this->service->upsert($this->document, 'upsertTest', ['associationType' => 'reference_one', 'refDocument' => 'UpsertReference']);
        $this->assertSame($newRef, $assoc->getDocument());
        $this->assertEquals('reference_one', $assoc->getType());
    }

    public function testUpsertMany()
    {
        $this->service->create($this->document, 'upsert1', 'ReferenceDocument', 'reference_one');

        $result = $this->service->upsertMany($this->document, [
                ['name' => 'upsert1', 'associationType' => 'reference_many'],
                ['name' => 'upsert2', 'associationType' => 'reference_one', 'refDocument' => 'ReferenceDocument'],
                ]);

        $this->assertEquals(2, sizeof($result));
        $this->assertEquals('reference_many', $result[0]->getType());
        $this->assertEquals('reference_one', $result[1]->getType());
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
        $this->service->create($this->document->getName(), 'rmTriggerTest', 'ReferenceDocument', 'reference_one');

        $triggered = 0;
        $this->serviceBroker->getEventManager()->attach(['post.valumodelerassociation.remove', 'post.valumodelerdocument.change'], function($e) use(&$triggered) {
            $triggered++;
        });

        $this->service->remove($this->document, 'rmTriggerTest');
        $this->assertEquals(2, $triggered);
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
