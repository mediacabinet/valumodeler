<?php
namespace ValuModelerTest\Service;

use Zend\Mvc\Application;
use ValuModeler\Service\DocumentService;
use ValuSo\Broker\ServiceBroker;
use PHPUnit_Framework_TestCase as TestCase;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * DocumentService test case.
 */
class AbstractEntityServiceTestCase extends TestCase
{
    
    /**
     * @var \ValuModeler\Service\AbstractEntityService
     */
    protected $service;
    
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;
    
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $sm;
    
    /**
     * @var \ValuSo\Broker\ServiceBroker
     */
    protected $serviceBroker;
    
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
        
        $this->sm = $this->application->getServiceManager();
        
        $this->dm = $this->sm->get('doctrine.documentmanager.valu_modeler');
        $this->dm->getConnection()->dropDatabase('valu_modeler_test');
        
        $this->serviceBroker = $this->sm->get('ServiceBroker');
        
        $this->dm->getSchemaManager()->ensureIndexes();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated DocumentServiceTest::tearDown()
        $this->application = null;
        $this->service = null;
        
        parent::tearDown();
    }
}