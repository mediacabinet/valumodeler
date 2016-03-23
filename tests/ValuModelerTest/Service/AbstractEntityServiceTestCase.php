<?php
namespace ValuModelerTest\Service;

use Zend\Mvc\Application;
use PHPUnit_Framework_TestCase as TestCase;

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
     * @var \ValuSo\Broker\ServiceBroker
     */
    protected $serviceBroker;
    
    /**
     * @var Application
     */
    protected static $application;
    
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected static $sm;
    
    public static function setUpBeforeClass()
    {
        self::$application = Application::init([
            'modules' => [
                'DoctrineModule',
                'DoctrineMongoODMModule',
                'ValuCore',
                'ValuSo',
                'ValuModeler',
            ],
            'module_listener_options' => [
                'config_static_paths' => [__DIR__ . '/../../../config/tests.config.php'],
                'config_cache_enabled' => false,
                'module_paths' => [
                    'vendor/valu',
                    'vendor/doctrine',
                ]
            ]
        ]);
        
        self::$sm = self::$application->getServiceManager();
        
        $config = self::$sm->get('Configuration');
        foreach ($config['doctrine']['driver']['valux']['paths'] as $path) {
            if (!file_exists($path)) {
                mkdir($path, 0744, true);
            }
        }
    }
    
    public static function tearDownAfterClass()
    {
        self::rmDir('data');
    }
    
    /**
     * Prepares the environment before running a test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->dm = self::$sm->get('doctrine.documentmanager.valu_modeler');
        
        $config = self::$sm->get('Configuration');
        $this->dm->getConnection()->dropDatabase($config['doctrine']['configuration']['odm_default']['default_db']);
        
        $this->serviceBroker = self::$sm->get('ServiceBroker');
        $this->dm->getSchemaManager()->ensureIndexes();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->dm->clear();
        $this->serviceBroker = null;
        $this->dm = null;
        $this->service = null;
        
        gc_collect_cycles();
        
        parent::tearDown();
    }
    
    protected static function rmDir($dir, $removeSelf = true) {
        
        if (!is_dir($dir)) {
            return false;
        }
        
        $files = array_diff(scandir($dir), array('.','..'));
        
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR .$file;
            (is_dir($path)) ? self::rmDir($path) : unlink($path);
        }
        
        return $removeSelf ? rmdir($dir) : true;
    }
}