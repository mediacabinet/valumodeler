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
class DocumentService extends AbstractModelService
    implements  Feature\ServiceBrokerAwareInterface,
                Feature\ProxyAwareInterface
{
    /**
     * Array of input filters by model name
     * 
     * @var array 
     */
    private $inputFilters;
    
    /**
     * Test whether or not a document exists
     * 
     * @param string $name
     * @return boolean
     */
    public function exists($name)
    {
        return $this->resolveDocument($name) != null;
    }
    
    /**
     * Create a new document model
     * 
     * @param string $name Unique name of the document
     * @param array $specs
     * @param array $options
     * @throws Exception\DocumentAlreadyExistsException
     * @ValuService\Trigger("post");
     */
    public function create($name, $specs = array(), array $options = array())
    {
        // Test that URI is not reserved
        if($this->getDocumentRepository()->findOneByName($name)){
            throw new Exception\DocumentAlreadyExistsException(
                'Document %NAME% already exists',
                array('NAME' => $name)
            );
        }

        $specs['name'] = $name;
        
        $fields  = isset($specs['fields']) ? $specs['fields'] : null; 
        $embeds  = isset($specs['embeds']) ? $specs['embeds'] : null; 
        $refs    = isset($specs['refs']) ? $specs['refs'] : null; 
        $indexes = isset($specs['indexes']) ? $specs['indexes'] : null; 
        
        // Filter and validate
        $specs = $this->getModelInputFilter('document')->filter(
            $specs, false, true);
        
        $document = new Model\Document($specs['name']);
        
        if(isset($specs['collection'])){
            $document->setCollection($specs['collection']);
        }
        
        if(isset($specs['parent'])){
            $parent = $this->resolveDocument($specs['parent'], true);
            $document->setParent($parent);
        }
        
        // add fields, embeds and references
        $this->populateDocument($document, $fields, $embeds, $refs);
        
        $this->getDocumentManager()->persist($document);
        $this->getDocumentManager()->flush($document);
        
        return $document->getId();
    }
    
    /**
     * Batch create documents
     * 
     * @param array $documents
     * @return array Document IDs
     */
    public function createMany(array $documents, array $options = array())
    {
        $options = array_merge(
            array('skip_existing' => false),
            $options        
        );
        
        $ids = array();
        
        foreach($documents as $key => $specs){
            
            $name = isset($specs['name'])
                ? $specs['name']
                : $key;
            
            $fields = isset($specs['fields'])
                ? (array) $specs['fields']
                : array();
            
            $embeds = isset($specs['embeds'])
                ? (array) $specs['embeds']
                : array();
            
            $refs = isset($specs['refs'])
                ? (array) $specs['refs']
                : array();
            
            $indexes = isset($specs['indexes'])
                ? (array) $specs['indexes']
                : array();
            
            if(!isset($specs['refs']) && isset($specs['references'])){
                $refs = $specs['references'];
            }
            
            unset($specs['name']);
            unset($specs['fields']);
            unset($specs['embeds']);
            unset($specs['refs']);
            unset($specs['references']);
            unset($specs['indexes']);
            
            try{
                $ids[] = $this->proxy->create($name, $fields, $embeds, $refs, $indexes, $specs);
            }
            catch(Exception\DocumentAlreadyExistsException $e){
                if($options['skip_existing']){
                    continue; // Skip existing
                }
                else{
                    throw $e;
                }
            }
        }
        
        return $ids;
    }
    
    /**
     * Insert new fields to document
     * 
     * @param string $name Document name
     * @param array $fields
     * @return boolean True on success
     * @ValuService\Trigger("post");
     */
    public function insertFields($name, array $fields, array $options = array())
    {
        $document = $this->resolveDocument($name, true);
        
        $this->populateDocument($document, $fields, null, null, $options);
        $this->getDocumentManager()->flush($document);
        
        return true;
    }
    
    /**
     * Insert new embeds to document
     *
     * @param string $name Document name
     * @param array $embeds
     * @return boolean True on success
     * @ValuService\Trigger("post");
     */
    public function insertEmbeds($name, array $embeds, array $options = array())
    {
        $document = $this->resolveDocument($name, true);
        
        $this->populateDocument($document, null, $embeds, null, $options);
        $this->getDocumentManager()->flush($document);
        
        return true;
    }
    
    /**
     * Insert new references for document
     *
     * @param string $name Document name
     * @param array $references
     * @return boolean True on success
     * @ValuService\Trigger("post");
     */
    public function insertReferences($name, array $references, array $options = array())
    {
        $document = $this->resolveDocument($name, true);
        
        $this->populateDocument($document, null, null, $references, $options);
        $this->getDocumentManager()->flush($document);
        
        return true;
    }
    
    /**
     * Remove one document
     * 
     * @param string $name
     * @return boolean True if document was found and removed, false otherwise
     */
    public function remove($name)
    {
        $document = $this->getDocumentRepository()->findOneByName($name);
        
        if (!$document) {
            return false;
        }
        
        return $this->doRemove($document, true);
    }
    
    /**
     * Batch remove documents
     * 
     * @param array $documents
     * @return array Result array
     */
    public function removeMany(array $documents)
    {
        $result = array();
        
        foreach($documents as $name){
            $document = $this->getDocumentRepository()->findOneByName($name);
            
            if (!$document) {
                continue;
            }
            
            $result[$document->getId()] = $this->doRemove($document, false);
        }
        
        $this->getDocumentManager()->flush();
        return $result;
    }
    
    /**
     * Retrieve input filter specifications for document
     * 
     * @param string $name
     * @throws Exception\DocumentNotFoundException
     * @return array Specifications, compatible with Zend\InputFilter\Factory 
     */
    public function getInputFilterSpecs($name)
    {
        $document = $this->resolveDocument($name, true);
        
        return $document->getInputFilterSpecifications();
    }
    
    /**
     * Retrieve input filter instance
     *
     * @param string $name
     * @return \Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter($name)
    {
        $document = $this->resolveDocument($name, true);
        
        $specs = $this->getServiceBroker()
            ->service('Modeler.Document')
            ->getInputFilterSpecs($document->getName());
    
        $factory = new Factory();
        return $factory->createInputFilter($specs);
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
     * Populate document with fields, embeds and references
     * 
     * @param Model\Document $document
     * @param array $fields
     * @param array $embeds
     * @param array $refs
     * @throws Exception\DocumentNotFoundException
     */
    protected function populateDocument(Model\Document $document, array $fields = null, array $embeds = null, array $refs = null, $options = array())
    {
        $options = array_merge(
            array(
                'skip_existing' => false      
            ),
            $options
        );
        
        // Insert fields
        if($fields && sizeof($fields)){
        
            foreach($fields as $key => $specs){
                
                if (!isset($specs['name'])) {
                    $specs['name'] = $key;
                }
                
                if (isset($specs['type'])) {
                    $specs['fieldType'] = $specs['type'];
                }
                
                // Filter and validate
                $specs = $this->getModelInputFilter('field')->filter(
                    $specs, false, true);
                
                // Skip, if desired
                if($document->getField($specs['name']) && $options['skip_existing']){
                    continue;
                }
        
                $field = new Model\Field($specs['name'], $specs['fieldType'], $specs);
                $document->addField($field);
            }
        }
        
        // Insert embeds
        if($embeds && sizeof($embeds)){
            foreach($embeds as $key => $specs){
                
                if (!isset($specs['name'])) {
                    $specs['name'] = $key;
                }
                
                if(isset($specs['type'])){
                    $specs['embedType'] = $specs['type'];
                }
                
                // Filter and validate
                $specs = $this->getModelInputFilter('embed')->filter(
                        $specs, false, true);
        
                // Find reference document by its name
                $reference = $this->getDocumentRepository()->findOneByName($specs['document']);
        
                if(!$reference){
                    throw new Exception\DocumentNotFoundException(
                        'Unable to locate document with name %NAME%',
                        array('NAME' => $specs['document'])
                    );
                }
                
                // Skip, if desired
                if($document->getEmbed($specs['name']) && $options['skip_existing']){
                    continue;
                }
                
                $embed = new Model\Embed($specs['name'], $specs['embedType'], $reference);
                $document->addEmbed($embed);
            }
        }
        
        // Insert references
        if($refs && sizeof($refs)){
            foreach($refs as $key => $specs){

                if (!isset($specs['name'])) {
                    $specs['name'] = $key;
                }
                
                if(isset($specs['type'])){
                    $specs['refType'] = $specs['type'];
                }
                
                // Filter and validate
                $specs = $this->getModelInputFilter('reference')->filter(
                    $specs, false, true);
                
                // Find reference document by its name
                $reference = $this->getDocumentRepository()->findOneByName($specs['document']);
        
                if(!$reference){
                    throw new Exception\DocumentNotFoundException(
                        'Unable to locate document with name %NAME%',
                        array('NAME' => $specs['document'])
                    );
                }
                
                // Skip, if desired
                if($document->getReference($specs['name']) && $options['skip_existing']){
                    continue;
                }
                
                $ref = new Model\Reference($specs['name'], $specs['refType'], $reference);
                $document->addReference($ref);
            }
        }
    }
    
    /**
     * Remove document by name
     * 
     * @param Model\Document $document
     * @param boolean $flush
     * @return boolean
     */
    protected function doRemove(Model\Document $document, $flush = true)
    {
        $this->getDocumentManager()->remove($document);
        
        if($flush){
            $this->getDocumentManager()->flush($document);
        }
        
        return true;
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