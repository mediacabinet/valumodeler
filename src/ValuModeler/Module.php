<?php
namespace ValuModeler;

use Zend\ModuleManager\Feature;

class Module
    implements Feature\AutoloaderProviderInterface,
               Feature\ConfigProviderInterface
{
    /**
     * getAutoloaderConfig() defined by AutoloaderProvider interface.
     *
     * @see AutoloaderProvider::getAutoloaderConfig()
     * @return array
     */    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'ValuX' => 'data/valu-modeler/documents/ValuX'
                ),
            ),
        );
    }
    
    /**
     * getConfig implementation for ConfigListener
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}