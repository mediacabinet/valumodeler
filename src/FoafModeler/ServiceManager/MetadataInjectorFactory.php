<?php
namespace FoafModeler\ServiceManager;

use FoafModeler\Doctrine\MongoDb\ClassMetadataFactory;
use FoafModeler\Doctrine\MongoDb\MetadataInjector;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class MetadataInjectorFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dm     = $serviceLocator->get('FoafModelerDm');
        $cache  = $serviceLocator->get('FoafModelerCache');
        $config = $serviceLocator->get('Configuration');
        $dir    = isset($config['foaf_modeler']['class_dir'])
            ? $config['foaf_modeler']['class_dir']
            : null;
        
        $factory = new ClassMetadataFactory();
        $factory->setDirectory($dir);
        
        if($cache){
            $factory->setCache($cache);
        }
        
        $injector = new MetadataInjector($dm, $factory);
        return $injector;
    }
}