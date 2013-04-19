<?php
namespace ValuModeler\ServiceManager;

use Zend\Authentication\Storage\StorageInterface;

use ValuModeler\Doctrine\MongoDb\ClassMetadataFactory;
use ValuModeler\Doctrine\MongoDb\MetadataInjector;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\Cache\StorageFactory;

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
        
        if(isset($cache['enabled'])
                && $cache['enabled']){

            if (isset($cache['adapter'])) {
                $cache = StorageFactory::factory($cache);
            } else {
                $cache = $serviceLocator->get('Cache');
            }
        
            if ($cache instanceof StorageInterface) {
                $factory->setCache($cache);
            }
        }
        
        $injector = new MetadataInjector($dm, $factory);
        return $injector;
    }
}