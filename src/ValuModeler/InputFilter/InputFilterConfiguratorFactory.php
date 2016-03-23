<?php
namespace ValuModeler\InputFilter;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class InputFilterConfiguratorFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceBroker = $serviceLocator->get('ServiceBroker');
        
        $service = new InputFilterConfigurator($serviceBroker);
        return $service;
    }
}