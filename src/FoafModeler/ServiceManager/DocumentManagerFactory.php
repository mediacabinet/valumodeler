<?php
namespace FoafModeler\ServiceManager;

use FoafModeler\FieldType\FieldTypeFactory;
use FoafModeler\Model;
use Zend\ServiceManager\ServiceLocatorInterface;

class DocumentManagerFactory 
    extends \Foaf\Doctrine\ServiceManager\DocumentManagerFactory
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
        $map = isset($config['foaf_modeler']['field_types'])
            ? $config['foaf_modeler']['field_types']
            : array();
        
        $fieldTypeFactory = new FieldTypeFactory($map);
        Model\Field::setTypeFactory($fieldTypeFactory);
        
        // Create document manager instance
        $dm = parent::createService($serviceLocator);
        return $dm;
    }    
}