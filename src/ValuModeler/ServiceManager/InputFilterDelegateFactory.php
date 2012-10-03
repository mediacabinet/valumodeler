<?php
namespace ValuModeler\ServiceManager;

use ValuModeler\InputFilter\Configurator\Delegate\ModelerDelegate;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class InputFilterDelegateFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceBroker = $serviceLocator->get('ServiceBroker');
        
        $service = new ModelerDelegate($serviceBroker);
        return $service;
    }
}