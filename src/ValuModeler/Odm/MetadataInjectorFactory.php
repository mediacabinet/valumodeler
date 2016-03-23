<?php
namespace ValuModeler\Odm;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\StorageInterface;

class MetadataInjectorFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dm     = $serviceLocator->get('doctrine.documentmanager.valu_modeler');
        $config = $serviceLocator->get('Configuration');
        $dir    = isset($config['valu_modeler']['class_dir'])
                  ? $config['valu_modeler']['class_dir'] : null;
        $cache  = isset($config['valu_modeler']['cache'])
                  ? $config['valu_modeler']['cache'] : null;
        
        $factory = new ClassMetadataFactory();
        $factory->setDirectory($dir);
        
        if ($cache && isset($cache['adapter'])){
            $cache = StorageFactory::factory($cache);
        } elseif ($serviceLocator->has('ObjectCache')) {
            $cache = $serviceLocator->get('ObjectCache');
        }
        
        if ($cache instanceof StorageInterface) {
            $factory->setCache($cache);
        }
        
        $injector = new MetadataInjector($dm, $factory);
        return $injector;
    }
}