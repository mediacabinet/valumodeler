<?php
namespace ValuModeler\Service;

use ValuModeler\Model;
use ValuModeler\Service\Exception;
use ValuSo\Annotation as ValuService;
use Zend\InputFilter\Factory;

/**
 * Document service
 * 
 */
class DocumentService extends AbstractEntityService
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
     * Retrieve all documents
     * 
     * @return \ArrayAccess
     */
    public function findAll()
    {
        return $this->getDocumentRepository()->findAll();
    }
    
    /**
     * Create a new document model
     * 
     * @param string|null $name Unique name of the document
     * @param array $specs
     * @throws Exception\DocumentAlreadyExistsException
     * 
     * @ValuService\Trigger("post")
     */
    public function create($name = null, $specs = array())
    {
        if ($name) {
            $specs['name'] = $name;
        }
        
        $indexes = isset($specs['indexes']) ? $specs['indexes'] : null;
        
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
            $this->assertCollectionIsUnique($specs['collection']);
            $document->setCollection($specs['collection']);
        }
        
        if(isset($specs['parent'])){
            $parent = $this->resolveDocument($specs['parent'], true);
            $document->setParent($parent);
        }
        
        $this->getDocumentManager()->persist($document);
        $this->getDocumentManager()->flush($document);
        
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
                $result[] = $this->proxy()->create(null, $specs);
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
     * Update document
     * 
     * @param string|\ValuModeler\Model\Document $document
     * @param array $specs
     */
    public function update($document, array $specs = array())
    {
        $document = $this->resolveDocument($document, true);
        
        $result = $this->proxy()->doUpdate($document, $specs);
        $this->getDocumentManager()->flush($document);
        
        return true;
    }
    
    /**
     * Creates a new document or updates existing
     * 
     * @param string|\ValuModeler\Model\Document $document
     * @param array $specs
     * @return \ValuModeler\Model\Document
     */
    public function upsert($document, $specs = array())
    {
        $resolved = $this->resolveDocument($document, false);
        
        if ($resolved) {
            $this->proxy()->doUpdate($resolved, $specs);
            $this->getDocumentManager()->flush($resolved);
            return $resolved;
        } else {
            return $this->proxy()->create($document, $specs);
        }
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
        
        $result = $this->proxy()->doRemove($document, true);
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
                $result[$key] = $this->proxy()->doRemove($document, false);                
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
        $specs = $this->proxy()->getInputFilterSpecs($document);
    
        $factory = new Factory();
        return $factory->createInputFilter($specs);
    }
    
    /**
     * Perform update to document
     *
     * @param Model\Document $document
     * @param array $specs
     * @return boolean
     * 
     * @ValuService\Trigger({"type":"post","name":"post.<service>.update"})
     * @ValuService\Trigger({"type":"post","name":"post.<service>.change"})
     */
    protected function doUpdate(Model\Document $document, array $specs)
    {
        // Filter and validate
        $specs = $this->filterAndValidate('document', $specs, true);
        
        if (isset($specs['collection']) && $specs['collection'] !== $document->getCollection()) {
            $this->assertCollectionIsUnique($specs['collection']);
            $document->setCollection($specs['collection']);
        }
        
        if (isset($specs['parent'])) {
            $parent = $this->resolveDocument($specs['parent'], true);
            $document->setParent($parent);
        }
        
        return true;
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
     * Assert that collection is unique
     * @param string $collection
     * @throws Exception\ServiceException
     */
    private function assertCollectionIsUnique($collection)
    {
        if (is_null($collection)) {
            return;
        } else {
            if ($this->getDocumentRepository()->findOneByCollection($collection)) {
                throw new Exception\ServiceException(
                    'Collection %COLLECTION% is reserved', array('COLLECTION' => $collection));
            }
        }
    }
}