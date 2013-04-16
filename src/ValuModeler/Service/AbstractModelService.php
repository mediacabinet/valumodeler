<?php
namespace ValuModeler\Service;

use ValuModeler\Model;
use ValuModeler\Service\Exception;
use ValuSo\Annotation as ValuService;
use ValuSo\Feature;
use ValuSo\Broker\ServiceBroker;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Zend\InputFilter\Factory;

/**
 * Document service
 * 
 */
abstract class AbstractModelService 
    implements  Feature\ServiceBrokerAwareInterface,
                Feature\ProxyAwareInterface
{
    
    /**
     * Document manager
     *
     * @var DocumentManager
     */
    private $dm;
    
    /**
     * Array of input filters by model name
     * 
     * @var array 
     */
    private $inputFilters;
    
    /**
     * Service broker instance
     * 
     * @var \ValuSo\Broker\ServiceBroker
     */
    private $serviceBroker;
    
    /**
     * Proxy class instance
     * 
     * @var Document
     */
    private $proxy;
    
	public function __construct(DocumentManager $dm)
    {
        $this->proxy = $this;
        $this->setDocumentManager($dm);
    }
    
    /**
     * Set document manager instance
     *
     * @param DocumentManager $dm
     * @return User
     *
     * @ValuService\Exclude
     */
    public function setDocumentManager(DocumentManager $dm)
    {
        $this->dm = $dm;
        return $this;
    }
    
    /**
     * Retrieve document manager instance
     *
     * @return DocumentManager
     *
     * @ValuService\Exclude
     */
    public function getDocumentManager()
    {
        return $this->dm;
    }
    
    /**
     * Retrieve service broker instance
     * 
     * @return \ValuSo\Broker\ServiceBroker
     * @ValuService\Exclude
     */
    public function getServiceBroker()
    {
        if (!$this->serviceBroker) {
            throw new \RuntimeException('Service broker is not set');
        }
        
        return $this->serviceBroker;
    }
    
    /**
     * @see \ValuSo\Feature\ServiceBrokerAwareInterface::setServiceBroker()
     * @ValuService\Exclude
     */
    public function setServiceBroker(ServiceBroker $serviceBroker)
    {
        $this->serviceBroker = $serviceBroker;
    }
    
    /**
     * @see \ValuSo\Feature\ProxyAwareInterface::setServiceProxy()
     */
    public function setServiceProxy($serviceProxy)
    {
        $this->proxy = $serviceProxy;
    }
    
    /**
     * Retrieve input filter instance
     *
     * @return \Valu\InputFilter\InputFilter
     */
    protected function getModelInputFilter($type)
    {
        $type = strtolower($type);
        
        if(!isset($this->inputFilters[$type])){
            $this->inputFilters[$type] = $this->getServiceBroker()
                ->service('InputFilter')
                ->get('ValuModeler'.ucfirst($type));
        }
    
        return $this->inputFilters[$type];
    }
}