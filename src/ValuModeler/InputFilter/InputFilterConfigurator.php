<?php
namespace ValuModeler\InputFilter;

use \ArrayObject;
use ValuModeler\Utils;
use ValuSo\Broker\ServiceBroker;
use ValuModeler\Service\Exception\DocumentNotFoundException;
use Valu\InputFilter\ConfiguratorInterface;
use Valu\InputFilter\Configurator\Delegate\DelegateInterface;
use Zend\InputFilter\InputFilterInterface;

class InputFilterConfigurator implements DelegateInterface
{
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
        $documentName = Utils::inputFilterUrlToDocName($name);

        if($documentName !== false){
            try{
                return $this->getServiceBroker()
                    ->service('Modeler.Document')
                    ->getInputFilterSpecs($documentName);
            } catch(DocumentNotFoundException $e) {
                return [];
            }
        }
        else{
            return [];
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