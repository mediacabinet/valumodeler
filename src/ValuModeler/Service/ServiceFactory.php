<?php
namespace ValuModeler\Service;

use ValuModeler\Model\Document;
use ValuSo\Broker\ServiceBroker;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\AbstractFactoryInterface;

class ServiceFactory implements AbstractFactoryInterface
{
    private $services = [
        'valumodelerdocument'  => 'ValuModeler\Service\DocumentService',
        'valumodelerassociation'     => 'ValuModeler\Service\AssociationService',
        'valumodelerfield'     => 'ValuModeler\Service\FieldService',
    ];
    
    private $initialized = [];
    
    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return isset($this->services[$name]);
    }
    
    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
    */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $dm = $serviceLocator->get('doctrine.documentmanager.valu_modeler');
        $serviceBroker = $serviceLocator->get('ServiceBroker');
        
        $this->initEvents($serviceLocator, $serviceBroker);
        
        $service = new $this->services[$name]($dm);
        return $service;
    }
    
    private function initEvents(ServiceLocatorInterface $serviceLocator, ServiceBroker $serviceBroker)
    {
        $evmId = spl_object_hash($serviceBroker->getEventManager());
        
        if (in_array($evmId, $this->initialized)) {
            return false;
        }
        
        $this->initialized[] = $evmId;
        
        $serviceBroker->getEventManager()->attach(
            array(
                'post.valumodelerdocument.create',
                'post.valumodelerdocument.remove',
                'post.valumodelerdocument.change',
            ),
            function($e) use ($serviceLocator, $serviceBroker){
                $document = $e->getParam('document');
                
                if ($document instanceof Document) {
                    $injector = $serviceLocator->get('valu_modeler.metadata_injector');
                    
                    $injector->getFactory()->reloadClassMetadata(
                        $document
                    );
                    
                    $serviceBroker->service('InputFilter')->reloadAll();
                }
            }
        );
        
        return true;
    }
}