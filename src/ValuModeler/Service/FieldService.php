<?php
namespace ValuModeler\Service;

use ValuModeler\Model;
use ValuSo\Annotation as ValuService;

class FieldService extends AbstractModelService
{
    /**
     * Create a new field to document
     *  
     * @param string $document
     * @param string $name
     * @param string $fieldType
     * @param array $specs
     * @return boolean True on success, false otherwise
     */
    public function create($document, $name = null, $fieldType = null, array $specs = array())
    {
        $document = $this->resolveDocument($document, true);
        
        if (isset($name)) {
            $specs['name'] = $name;
        }
        
        if (isset($fieldType)) {
            $specs['fieldType'] = $fieldType;
        }
        
        $response = $this->proxy->doCreate($document, $specs);
        
        if ($response) {
            $this->getDocumentManager()->flush($document);
        }
        
        return $response;
    }
    
    /**
     * Batch-create fields
     * 
     * @param string $document
     * @param array $fields
     * @return array
     */
    public function createMany($document, $fields)
    {
        $document = $this->resolveDocument($document, true);
        
        $responses = array();
        foreach ($fields as $key => $specs) {
            $responses[$key] = $this->doCreate($document, $specs);
        }
        
        $this->getDocumentManager()->flush($document);
        
        return $responses;
    }
    
    /**
     * Remove a field from document
     * 
     * @param string $document
     * @param string $name
     * 
     * @ValuService\Trigger("post")
     */
    public function remove($document, $name)
    {
        $document = $this->resolveDocument($document, true);
        $response = $this->doRemove($document, $name);
        $this->getDocumentManager()->flush($document);
        
        return $response;
    }
    
    /**
     * Batch-remove fields from document
     * 
     * @param array $fields
     */
    public function removeMany($document, array $fields)
    {
        $document = $this->resolveDocument($document, true);
        $responses = array();
        foreach ($fields as $key => $name) {
            $responses[$key] = $this->proxy->doRemove($document, $name);
        }
        
        if (in_array(true, $responses, true)) {
            $this->getDocumentManager()->flush($document);
        }
        
        return $responses;
    }
    
    /**
     * Create a new field
     * 
     * @param Model\Document $document
     * @param array $specs
     * @return boolean
     * 
     * @ValuService\Trigger({"type":"post","name":"post.<service>.create"})
     */
    protected function doCreate(Model\Document $document, $specs)
    {
        // Filter and validate
        $specs = $this->getModelInputFilter('field')->filter(
                $specs, false, true);
        
        $field = new Model\Field($specs['name'], $specs['fieldType'], $specs);
        $document->addField($field);
        
        return true;
    }
    
    /**
     * Perform field removal
     * 
     * @param Model\Document $document
     * @param string $name
     * 
     * @ValuService\Trigger({"type":"post","name":"post.<service>.remove"})
     */
    protected function doRemove(Model\Document $document, $name)
    {
        return $document->removeField($name);
    }
}