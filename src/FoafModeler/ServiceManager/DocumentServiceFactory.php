<?php
namespace FoafModeler\ServiceManager;

use FoafModeler\Service\Document;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class DocumentServiceFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $dm = $serviceLocator->get('FoafModelerDm');
        $serviceBroker = $serviceLocator->get('ServiceBroker');
        
        /**
         * Attach listeners to flush class meta data when ever
         * document changes
         */
        $serviceBroker->getEventManager()->attach(
            array(
                'post.Modeler.Document.create',
                'post.Modeler.Document.update',
                'post.Modeler.Document.remove',
            ),
            function($e) use ($serviceLocator, $dm){
                $injector = $serviceLocator->get('FoafModelerMetadataInjector');
                $name     = $e->getParam('name');
                
                if($name && $injector){
                    $document = $dm->getRepository('FoafModeler\Model\Document')->findOneByName(
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