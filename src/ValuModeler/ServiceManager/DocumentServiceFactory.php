<?php
namespace ValuModeler\ServiceManager;

use ValuModeler\Service\Document;
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
                'final.Modeler.Document.create',
                'final.Modeler.Document.update',
                'final.Modeler.Document.remove',
                'final.Modeler.Document.insertFields',
                'final.Modeler.Document.insertEmbeds',
                'final.Modeler.Document.insertReferences',
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
        
        $service = new Document($dm);
        return $service;
    }
}