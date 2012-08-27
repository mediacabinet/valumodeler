<?php
namespace FoafModeler\ServiceManager;

use FoafModeler\Doctrine\MongoDb\MetadataInjector;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class MetadataInjectorFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dm     = $serviceLocator->get('FoafModelerDm');
        $config = $serviceLocator->get('Configuration');
        $dir    = isset($config['foaf_modeler']['class_dir'])
            ? $config['foaf_modeler']['class_dir']
            : null;
        
        $injector = new MetadataInjector($dm);
        $injector->setDirectory($dir);
        return $injector;
    }
}