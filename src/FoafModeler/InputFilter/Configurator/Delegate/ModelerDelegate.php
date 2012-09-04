<?php
namespace FoafModeler\InputFilter\Configurator\Delegate;

use \ArrayObject;
use Foaf\Service\Broker;
use Foaf\InputFilter\ConfiguratorInterface;
use Foaf\InputFilter\Configurator\Delegate\DelegateInterface;
use Zend\InputFilter\InputFilterInterface;

class ModelerDelegate implements DelegateInterface
{
    const NS = 'modeler://';
    
    /**
     * Service broker
     * @var \Foaf\Service\Broker
     */
    protected $serviceBroker;
    
    public function __construct(Broker $serviceBroker)
    {
        $this->setServiceBroker($serviceBroker);
    }
    
	/* (non-PHPdoc)
     * @see \Foaf\InputFilter\InputFilterLocator\Delegate\DelegateInterface::getInputFilterSpecifications()
     */
    public function getInputFilterSpecifications(ConfiguratorInterface $configurator, $name)
    {
        if(strpos($name, self::NS) === 0){
            $documentName = substr($name, strlen(self::NS));
            $serviceBroker = $this->getServiceBroker();
            
            if(!$serviceBroker){
                throw new \RuntimeException('Unable to load input filter specifications; service broker is not set');
            }
            
            // Fetch specs
            return $serviceBroker
                ->service('Modeler.Document')
                ->getInputFilterSpecs($documentName);
        }
        else{
            return array();
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see \Foaf\InputFilter\Configurator\Delegate\DelegateInterface::prepareInputFilterSpecifications()
     */
    public function prepareInputFilterSpecifications(ConfiguratorInterface $configurator, $name, ArrayObject $specifications)
    {}
    
    /* (non-PHPdoc)
     * @see \Foaf\InputFilter\InputFilterLocator\Delegate\DelegateInterface::finalizeInputFilter()
    */
    public function finalizeInputFilter(ConfiguratorInterface $configurator, $name,
            InputFilterInterface $inputFilter)
    {}
    
    /**
     * Retrieve service broker instance
     * 
     * @return Broker
     */
    public function getServiceBroker()
    {
        return $this->serviceBroker;
    }
    
    /**
     * Set service broker instance
     * 
     * @param Broker $serviceBroker
     */
    public function setServiceBroker(Broker $serviceBroker)
    {
        $this->serviceBroker = $serviceBroker;
    }
}