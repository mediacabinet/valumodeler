<?php
namespace FoafModeler;

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
                    'FoafX' => 'data/foaf-modeler/documents/FoafX'
                ),
            ),
        );
    }
    
    /**
     * getConfig implementation for ConfigListener
     *
     * @return \Zend\Config\Ini
     */
    public function getConfig()
    {
        return \Zend\Config\Factory::fromFile(__DIR__ . '/config/module.ini');
    }
}