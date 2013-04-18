<?php
namespace ValuModeler\Service;

use ValuSo\Broker\ServiceBroker;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\AbstractFactoryInterface;

class ServiceFactory implements AbstractFactoryInterface
{
    private $services = [
        'valumodelerdocument'  => 'ValuModeler\Service\DocumentService',
        'valumodelerembed'     => 'ValuModeler\Service\EmbedService',
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
        $brokerId = spl_object_hash($serviceBroker);
        
        if (in_array($brokerId, $this->initialized)) {
            return false;
        }
        
        $this->initialized[] = $brokerId;
        
        $serviceBroker->getEventManager()->attach(
                array(
                    'post.modeler.document.create',
                    'post.modeler.document.update',
                    'post.modeler.document.remove',
                ),
                function($e) use ($serviceLocator){
                    $document = $e->getParam('document');
                    
                    if ($document) {
                        $injector = $serviceLocator->get('ValuModelerMetadataInjector');
                        
                        $injector->getFactory()->reloadClassMetadata(
                                $document
                        );
                    }
                }
        );
        
        return true;
    }
}