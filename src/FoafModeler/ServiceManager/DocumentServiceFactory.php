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
        
        $service = new Document($dm);
        return $service;
    }
}