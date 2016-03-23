<?php
namespace ValuModeler\Service;

use ValuModeler\Model;
use ValuModeler\Service\Exception;
use ValuSo\Annotation as ValuService;
use ValuSo\Feature;
use ValuSo\Broker\ServiceBroker;
use Valu\InputFilter\Exception\ValidationException;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Abstract service implementation for Document,
 * Association and Field services
 * 
 * @ValuService\Exclude
 * @ValuService\Context({"native", "cli"})
 */
abstract class AbstractEntityService 
    implements  Feature\ServiceBrokerAwareInterface,
                Feature\ProxyAwareInterface
{
    
    use Feature\ProxyTrait;
    
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
    
	public function __construct(DocumentManager $dm)
    {
        $this->setDocumentManager($dm);
    }
    
    /**
     * Set document manager instance
     *
     * @param DocumentManager $dm
     * @return AbstractEntityService
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
     */
    public function getDocumentManager()
    {
        return $this->dm;
    }
    
    /**
     * Retrieve service broker instance
     * 
     * @return \ValuSo\Broker\ServiceBroker
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
     */
    public function setServiceBroker(ServiceBroker $serviceBroker)
    {
        $this->serviceBroker = $serviceBroker;
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
     * @param string $entityType
     * @return \Valu\InputFilter\InputFilter
     */
    protected function getEntityInputFilter($entityType)
    {
        $entityType = strtolower($entityType);
        
        if(!isset($this->inputFilters[$entityType])){
            $this->inputFilters[$entityType] = $this->getServiceBroker()
                ->service('InputFilter')
                ->get('ValuModeler'.ucfirst($entityType));
        }
    
        return $this->inputFilters[$entityType];
    }
    
    /**
     * Filter and validate entity specs
     * 
     * @param string $entityType
     * @param array $specs
     * @param boolean $useValidationGroup
     * @throws Exception\ValidationException
     * @return mixed
     */
    protected function filterAndValidate($entityType, array $specs, $useValidationGroup = false)
    {
        try{
            // Remove such items from specs array that are not configured
            // for input filter
            $inputFilter = $this->getEntityInputFilter($entityType);
            $inputs      = $inputFilter->getInputs();
            $specs       = array_intersect_key($specs, array_fill_keys(array_keys($inputs), true));
            
            return $inputFilter->filter($specs, $useValidationGroup, true);
        } catch(ValidationException $e) {
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
     */
    protected function getDocumentRepository()
    {
        return $this->getDocumentManager()->getRepository('ValuModeler\Model\Document');
    }
}