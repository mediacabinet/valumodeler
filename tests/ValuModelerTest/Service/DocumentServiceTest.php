<?php
namespace ValuModelerTest\Service;

use Zend\Mvc\Application;
use ValuModeler\Service\DocumentService;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * DocumentService test case.
 */
class DocumentServiceTest extends TestCase
{

    /**
     *
     * @var DocumentService
     */
    private $documentService;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        // TODO Auto-generated DocumentServiceTest::setUp()
        
        $this->application = Application::init([
            'modules' => [
                'DoctrineModule',
                'DoctrineMongoODMModule',
                'valucore',
                'valuso',
                'valumodeler',
            ],
            'module_listener_options' => [
                'config_static_paths' => [__DIR__ . '/../../test.config.php'],
                'config_cache_enabled' => false,
                'module_paths' => [
                    'vendor/valu',
                    'vendor/doctrine',
                ]
            ]
        ]);
        
        $sm = $this->application->getServiceManager();
        $dm = $sm->get('doctrine.documentmanager.valu_modeler');
        
        //$dm->getConnection()->dropDatabase('valu_modeler_test');
        
        $this->documentService = new DocumentService($dm);
        $this->documentService->setServiceBroker($sm->get('ServiceBroker'));
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated DocumentServiceTest::tearDown()
        $this->application = null;
        $this->documentService = null;
        
        parent::tearDown();
    }

    /**
     * Tests DocumentService::version()
     */
    public function testVersion()
    {
        // TODO Auto-generated DocumentServiceTest::testVersion()
        $this->markTestIncomplete("version test not implemented");
        
        DocumentService::version(/* parameters */);
    }

    /**
     * Tests DocumentService->exists()
     */
    public function testExists()
    {
        // TODO Auto-generated DocumentServiceTest->testExists()
        $this->markTestIncomplete("exists test not implemented");
        
        $this->documentService->exists(/* parameters */);
    }

    /**
     * Tests DocumentService->create()
     */
    public function testCreate()
    {
        $id = $this->documentService->create('My\Empty\Document');
        $this->assertNotNull($id);
    }

    /**
     * Tests DocumentService->createMany()
     */
    public function testCreateMany()
    {
        // TODO Auto-generated DocumentServiceTest->testCreateMany()
        $this->markTestIncomplete("createMany test not implemented");
        
        $this->documentService->createMany(/* parameters */);
    }

    /**
     * Tests DocumentService->insertFields()
     */
    public function testInsertFields()
    {
        // TODO Auto-generated DocumentServiceTest->testInsertFields()
        $this->markTestIncomplete("insertFields test not implemented");
        
        $this->documentService->insertFields(/* parameters */);
    }

    /**
     * Tests DocumentService->insertEmbeds()
     */
    public function testInsertEmbeds()
    {
        // TODO Auto-generated DocumentServiceTest->testInsertEmbeds()
        $this->markTestIncomplete("insertEmbeds test not implemented");
        
        $this->documentService->insertEmbeds(/* parameters */);
    }

    /**
     * Tests DocumentService->insertReferences()
     */
    public function testInsertReferences()
    {
        // TODO Auto-generated DocumentServiceTest->testInsertReferences()
        $this->markTestIncomplete("insertReferences test not implemented");
        
        $this->documentService->insertReferences(/* parameters */);
    }

    /**
     * Tests DocumentService->remove()
     */
    public function testRemove()
    {
        // TODO Auto-generated DocumentServiceTest->testRemove()
        $this->markTestIncomplete("remove test not implemented");
        
        $this->documentService->remove(/* parameters */);
    }

    /**
     * Tests DocumentService->removeMany()
     */
    public function testRemoveMany()
    {
        // TODO Auto-generated DocumentServiceTest->testRemoveMany()
        $this->markTestIncomplete("removeMany test not implemented");
        
        $this->documentService->removeMany(/* parameters */);
    }

    /**
     * Tests DocumentService->getInputFilterSpecs()
     */
    public function testGetInputFilterSpecs()
    {
        // TODO Auto-generated DocumentServiceTest->testGetInputFilterSpecs()
        $this->markTestIncomplete("getInputFilterSpecs test not implemented");
        
        $this->documentService->getInputFilterSpecs(/* parameters */);
    }

    /**
     * Tests DocumentService->getInputFilter()
     */
    public function testGetInputFilter()
    {
        // TODO Auto-generated DocumentServiceTest->testGetInputFilter()
        $this->markTestIncomplete("getInputFilter test not implemented");
        
        $this->documentService->getInputFilter(/* parameters */);
    }

    /**
     * Tests DocumentService->setDocumentManager()
     */
    public function testSetDocumentManager()
    {
        // TODO Auto-generated DocumentServiceTest->testSetDocumentManager()
        $this->markTestIncomplete("setDocumentManager test not implemented");
        
        $this->documentService->setDocumentManager(/* parameters */);
    }

    /**
     * Tests DocumentService->getDocumentManager()
     */
    public function testGetDocumentManager()
    {
        // TODO Auto-generated DocumentServiceTest->testGetDocumentManager()
        $this->markTestIncomplete("getDocumentManager test not implemented");
        
        $this->documentService->getDocumentManager(/* parameters */);
    }

    /**
     * Tests DocumentService->getServiceBroker()
     */
    public function testGetServiceBroker()
    {
        // TODO Auto-generated DocumentServiceTest->testGetServiceBroker()
        $this->markTestIncomplete("getServiceBroker test not implemented");
        
        $this->documentService->getServiceBroker(/* parameters */);
    }

    /**
     * Tests DocumentService->setServiceBroker()
     */
    public function testSetServiceBroker()
    {
        // TODO Auto-generated DocumentServiceTest->testSetServiceBroker()
        $this->markTestIncomplete("setServiceBroker test not implemented");
        
        $this->documentService->setServiceBroker(/* parameters */);
    }

    /**
     * Tests DocumentService->setServiceProxy()
     */
    public function testSetServiceProxy()
    {
        // TODO Auto-generated DocumentServiceTest->testSetServiceProxy()
        $this->markTestIncomplete("setServiceProxy test not implemented");
        
        $this->documentService->setServiceProxy(/* parameters */);
    }
}

