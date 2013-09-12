<?php
namespace ValuModeler\Doctrine\MongoDb;

use ValuModeler\FieldType\FieldTypeFactory;
use ValuModeler\Model;
use DoctrineMongoODMModule\Service\DocumentManagerFactory as BaseFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class DocumentManagerFactory 
    extends BaseFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /**
         * Configurations
         * 
         * @var array
         */
        $config = $serviceLocator->get('Configuration');
        
        // Register field types and attach factory as default
        // fieldType factory for Documents
        $map = isset($config['valu_modeler']['field_types'])
            ? $config['valu_modeler']['field_types']
            : array();
        
        $fieldTypeFactory = new FieldTypeFactory($map);
        Model\Field::setTypeFactory($fieldTypeFactory);
        
        // Create document manager instance
        $dm = parent::createService($serviceLocator);
        return $dm;
    }    
}