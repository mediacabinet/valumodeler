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
        
        $serviceBroker = $serviceLocator->get('ServiceBroker');
        
        // Register field types and attach factory as default
        // fieldType factory for Documents
        $map = isset($config['foaf_modeler']['field_types'])
            ? $config['foaf_modeler']['field_types']
            : array();
        
        $fieldTypeFactory = new FieldTypeFactory($map);
        Model\Field::setTypeFactory($fieldTypeFactory);
        
        // Set default input filters
        Model\Document::setDefaultInputFilter(
            $serviceBroker->service('InputFilter')->get('FoafModelerDocument')
        );
        
        Model\Field::setDefaultInputFilter(
            $serviceBroker->service('InputFilter')->get('FoafModelerField')
        );
        
        Model\Embed::setDefaultInputFilter(
            $serviceBroker->service('InputFilter')->get('FoafModelerEmbed')
        );
        
        Model\Reference::setDefaultInputFilter(
            $serviceBroker->service('InputFilter')->get('FoafModelerReference')
        );
        
        // Create document manager instance
        $dm = parent::createService($serviceLocator);
        return $dm;
    }    
}