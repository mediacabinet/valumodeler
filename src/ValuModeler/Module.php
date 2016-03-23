<?php
namespace ValuModeler;

use ValuModeler\Odm\DocumentManagerFactory;
use Zend\ModuleManager\Feature;
use Zend\Loader\AutoloaderFactory;
use Zend\Loader\StandardAutoloader;

class Module
    implements Feature\AutoloaderProviderInterface,
               Feature\ConfigProviderInterface,
               Feature\ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */    
    public function getAutoloaderConfig()
    {
        return array(
            AutoloaderFactory::STANDARD_AUTOLOADER => array(
                StandardAutoloader::LOAD_NS => array(
                    __NAMESPACE__ => __DIR__,
                    'ValuX' => realpath(__DIR__ . '/../../../../../data/valumodeler/documents/ValuX')
                ),
            ),
        );
    }
    
    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
    
    /**
     * {@inheritDoc}
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'doctrine.documentmanager.valu_modeler' => new DocumentManagerFactory('odm_default'),
            )
        );
    }
}