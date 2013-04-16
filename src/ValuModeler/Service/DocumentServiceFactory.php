<?php
namespace ValuModeler\Service;

use ValuModeler\Service\DocumentService;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class DocumentServiceFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dm = $serviceLocator->get('ValuModelerDm');
        $serviceBroker = $serviceLocator->get('ServiceBroker');
        
        /**
         * Attach listeners to flush class meta data when ever
         * document changes
         */
        $serviceBroker->getEventManager()->attach(
            array(
                'final.modeler.document.create',
                'final.modeler.document.update',
                'final.modeler.document.remove',
                'final.modeler.document.insertfields',
                'final.modeler.document.insertembeds',
                'final.modeler.document.insertreferences',
            ),
            function($e) use ($serviceLocator, $dm){
                $injector = $serviceLocator->get('ValuModelerMetadataInjector');
                $name     = $e->getParam('name', $e->getParam(0));
                
                if($name && $injector){
                    $document = $dm->getRepository('ValuModeler\Model\Document')->findOneByName(
                        $name
                    );
                    
                    $injector->getFactory()->reloadClassMetadata(
                        $document     
                    );
                }
            }
        );
        
        $service = new DocumentService($dm);
        return $service;
    }
}