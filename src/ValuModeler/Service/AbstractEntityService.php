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
 * @ValuService\Exclude
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
    protected $dm;
    
    /**
     * Array of input filters by model name
     * 
     * @var array 
     */
    protected $inputFilters;
    
    /**
     * Service broker instance
     * 
     * @var \ValuSo\Broker\ServiceBroker
     */
    protected $serviceBroker;
    
    /**
     * Proxy class instance
     * 
     * @var AbstractModelService
     */
    protected $proxy;
    
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
     * Provides convenient access to service
     * 
     * @param string $name
     * @return \ValuSo\Broker\Worker
     */
    public function service($name)
    {
        return $this->getServiceBroker()->service($name);
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
    
    /**
     * Filter and validate entity specs
     * 
     * @param string $modelType
     * @param array $specs
     * @param boolean $useValidationGroup
     * @throws Exception\ValidationException
     * @return mixed
     */
    protected function filterAndValidate($modelType, array $specs, $useValidationGroup = false)
    {
        try{
            return $this->getModelInputFilter($modelType)->filter($specs, $useValidationGroup, true);
        } catch(\Valu\InputFilter\Exception\ValidationException $e) {
            throw new Exception\ValidationException(
                $e->getRawMessage(), $e->getVars());
        }
    }
    
    /**
     * Resolve document by its name
     * 
     * @param string|Model\Document $document
     * @param boolean $require
     * @throws Exception\DocumentNotFoundException
     * @return \ValuModeler\Model\Document
     */
    protected function resolveDocument($document, $require = false)
    {
        if($document instanceof Model\Document){
            return $document;
        }
        else{
            $doc = $this->getDocumentRepository()->findOneByName($document);
            
            if(!$doc && $require){
                throw new Exception\DocumentNotFoundException(
                    'Document %NAME% not found',
                    array('NAME' => $document)
                );
            }
            
            return $doc;
        }
    }
    
    /**
     * Retrieve document repository instance
     * 
     * @return \Doctrine\ODM\MongoDb\DocumentRepository
     * @ValuService\Exclude
     */
    protected function getDocumentRepository()
    {
        return $this->getDocumentManager()->getRepository('ValuModeler\Model\Document');
    }
}