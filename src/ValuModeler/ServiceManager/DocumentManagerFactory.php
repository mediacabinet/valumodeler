<?php
namespace ValuModeler\ServiceManager;

use ValuModeler\FieldType\FieldTypeFactory;
use ValuModeler\Model;
use Zend\ServiceManager\ServiceLocatorInterface;

class DocumentManagerFactory 
    extends \Valu\Doctrine\ServiceManager\DocumentManagerFactory
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
        $map = isset($config['valu_modeler']['field_types'])
            ? $config['valu_modeler']['field_types']
            : array();
        
        $fieldTypeFactory = new FieldTypeFactory($map);
        Model\Field::setTypeFactory($fieldTypeFactory);
        
        // Set default input filters
        Model\Document::setDefaultInputFilter(
            $serviceBroker->service('InputFilter')->get('ValuModelerDocument')
        );
        
        Model\Field::setDefaultInputFilter(
            $serviceBroker->service('InputFilter')->get('ValuModelerField')
        );
        
        Model\Embed::setDefaultInputFilter(
            $serviceBroker->service('InputFilter')->get('ValuModelerEmbed')
        );
        
        Model\Reference::setDefaultInputFilter(
            $serviceBroker->service('InputFilter')->get('ValuModelerReference')
        );
        
        // Create document manager instance
        $dm = parent::createService($serviceLocator);
        return $dm;
    }    
}