<?php
namespace ValuModeler\InputFilter;

use \ArrayObject;
use ValuSo\Broker\ServiceBroker;
use ValuModeler\Service\Exception\DocumentNotFoundException;
use Valu\InputFilter\ConfiguratorInterface;
use Valu\InputFilter\Configurator\Delegate\DelegateInterface;
use Zend\InputFilter\InputFilterInterface;

class InputFilterConfigurator implements DelegateInterface
{
    const NS = 'modeler://';
    
    /**
     * Service broker
     * @var \ValuSo\Broker\ServiceBroker
     */
    protected $serviceBroker;
    
    public function __construct(ServiceBroker $serviceBroker)
    {
        $this->setServiceBroker($serviceBroker);
    }
    
	/**
     * {@inheritDoc}
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
            try{
                return $serviceBroker
                    ->service('Modeler.Document')
                    ->getInputFilterSpecs($documentName);
            } catch(DocumentNotFoundException $e) {
                return array();
            }
        }
        else{
            return array();
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function prepareInputFilterSpecifications(ConfiguratorInterface $configurator, $name, ArrayObject $specifications)
    {}
    
    /**
     * {@inheritDoc}
     */
    public function finalizeInputFilter(ConfiguratorInterface $configurator, $name,
            InputFilterInterface $inputFilter)
    {}
    
    /**
     * Retrieve service broker instance
     * 
     * @return \ValuSo\Broker\ServiceBroker
     */
    public function getServiceBroker()
    {
        return $this->serviceBroker;
    }
    
    /**
     * Set service broker instance
     * 
     * @param \ValuSo\Broker\ServiceBroker $serviceBroker
     */
    public function setServiceBroker(ServiceBroker $serviceBroker)
    {
        $this->serviceBroker = $serviceBroker;
    }
}