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
{
    
    /**
     * Test whether or not a document exists
     * 
     * @param string $document
     * @return boolean
     */
    public function exists($document)
    {
        return $this->resolveDocument($document) != null;
    }
    
    /**
     * Create a new document model
     * 
     * @param string|null $name Unique name of the document
     * @param array $specs
     * @throws Exception\DocumentAlreadyExistsException
     * 
     * @ValuService\Trigger("post");
     */
    public function create($name = null, $specs = array())
    {
        if ($name) {
            $specs['name'] = $name;
        }
        
        $parent  = isset($specs['parent']) ? $specs['parent'] : null; 
        $fields  = isset($specs['fields']) ? $specs['fields'] : null; 
        $embeds  = isset($specs['embeds']) ? $specs['embeds'] : null; 
        $refs    = isset($specs['refs']) ? $specs['refs'] : null; 
        $indexes = isset($specs['indexes']) ? $specs['indexes'] : null;
        
        if (!$refs && isset($specs['references'])) {
            $refs = $specs['references'];
        }
        
        // Filter and validate
        $specs = $this->filterAndValidate('document', $specs, false);
        
        // Test that document name is not reserved
        if($this->resolveDocument($specs['name'])){
            throw new Exception\DocumentAlreadyExistsException(
                    'Document %NAME% already exists',
                    array('NAME' => $specs['name'])
            );
        }
        
        $document = new Model\Document($specs['name']);
        
        if(isset($specs['collection'])){
            $document->setCollection($specs['collection']);
        }
        
        if($parent){
            $parent = $this->resolveDocument($parent, true);
            $document->setParent($parent);
        }
        
        $this->getDocumentManager()->persist($document);
        $this->getDocumentManager()->flush($document);
        
        try{
            if ($fields) {
                $this->service('Modeler.Field')->createMany($fields);
            }
            
            if ($embeds) {
                $this->service('Modeler.Embed')->createMany($embeds);
            }
            
            if ($refs) {
                $this->service('Modeler.Reference')->createMany($refs);
            }
        } catch(\Exception $e) {
            $this->doRemove($document);
            $this->getDocumentManager()->flush();
            
            throw $e;
        }
        
        return $document;
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
        
        $result = array();
        
        foreach($documents as $key => $specs){
            
            try{
                $result[] = $this->proxy->create(null, $specs);
            }
            catch(Exception\DocumentAlreadyExistsException $e){
                if($options['skip_existing']){
                    $result[] = null;
                    continue; // Skip existing
                }
                else{
                    throw $e;
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Remove one document
     * 
     * @param string $document
     * @return boolean True if document was found and removed, false otherwise
     */
    public function remove($document)
    {
        $document = $this->resolveDocument($document, false);
        
        if (!$document) {
            return false;
        }
        
        $result = $this->proxy->doRemove($document, true);
        if ($result) {
            $this->getDocumentManager()->flush();
        }
        
        return $result;
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
        
        foreach($documents as $key => $document){
            $document = $this->resolveDocument($document);
            
            if ($document) {
                $result[$key] = $this->proxy->doRemove($document, false);                
            } else {
                $result[$key] = false;
            }
        }
        
        if (in_array(true, $result)) {
            $this->getDocumentManager()->flush();
        }
        
        return $result;
    }
    
    /**
     * Retrieve input filter specifications for document
     * 
     * @param string $document
     * @throws Exception\DocumentNotFoundException
     * @return array Specifications, compatible with Zend\InputFilter\Factory 
     */
    public function getInputFilterSpecs($document)
    {
        $document = $this->resolveDocument($document, true);
        
        return $document->getInputFilterSpecifications();
    }
    
    /**
     * Retrieve input filter instance
     *
     * @param string $document
     * @return \Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter($document)
    {
        $document = $this->resolveDocument($document, true);
        $specs = $this->proxy->getInputFilterSpecs($document);
    
        $factory = new Factory();
        return $factory->createInputFilter($specs);
    }
    
    /**
     * Remove document by name
     * 
     * @param Model\Document $document
     * @return boolean
     * @ValuService\Trigger({"type":"post","name":"post.<service>.remove"})
     */
    protected function doRemove(Model\Document $document)
    {
        $this->getDocumentManager()->remove($document);
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