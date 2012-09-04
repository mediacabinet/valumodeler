<?php
namespace FoafModeler\ServiceManager;

use Zend\Cache\StorageFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CacheAdapterFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        
        if(isset($config['foaf_modeler']['cache'])){
            $cache = StorageFactory::factory($config['foaf_modeler']['cache']);
            return $cache;
        }
        else{
            return null;
        }
    }
}