<?php
namespace ValuModeler\Service;

use ValuModeler\Model;
use ValuSo\Annotation as ValuService;

class FieldService extends AbstractEntityService
{
    /**
     * Does document have a named field
     *
     * @param string $document
     * @param string $name
     * @return boolean
     */
    public function exists($document, $name)
    {
        $document = $this->resolveDocument($document, true);
        return $document->getField($name) !== null;
    }
    
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
        
        $response = $this->proxy()->doCreate($document, $specs);
        
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
            $responses[$key] = $this->proxy()->doCreate($document, $specs);
        }
        
        $this->getDocumentManager()->flush($document);
        
        return $responses;
    }
    
    /**
     * Update field
     *
     * @param string|\ValuModeler\Model\Document $document
     * @param string $name
     * @param array $specs
     * @return \ValuModeler\Model\Field|NULL
     */
    public function update($document, $name, array $specs)
    {
        $document = $this->resolveDocument($document, true);
        $field = $document->getField($name);
        
        if (!$field) {
            throw new Exception\FieldNotFoundException(
                'Document %DOCUMENT% does not contain field %FIELD%', 
                ['DOCUMENT' => $document->getName(), 'FIELD' => $name]);
        }
        
        $result = $this->proxy()->doUpdate($document, $field, $specs);
        $this->getDocumentManager()->flush($document);
        return $result;
    }
    
    /**
     * Create a new field or update existing
     * 
     * @param string|\ValuModeler\Model\Document $document
     * @param string $name
     * @param array $specs
     * @return \ValuModeler\Model\Field|NULL
     */
    public function upsert($document, $name, array $specs)
    {
        $document = $this->resolveDocument($document, true);
        $field = $this->doUpsert($document, $name, $specs);
        $this->getDocumentManager()->flush($document);
        
        return $field;
    }
    
    /**
     * Batch-upsert fields
     * 
     * @param string|\ValuModeler\Model\Document $document
     * @param array $fields
     * @return array
     */
    public function upsertMany($document, array $fields)
    {
        $document = $this->resolveDocument($document, true);
        
        $results = array();
        foreach ($fields as $key => $specs) {
            if (isset($specs['name'])) {
                $results[$key] = $this->upsert($document, $specs['name'], $specs);
            } else {
                $results[$key] = null;
            }
        }
        
        $this->getDocumentManager()->flush($document);
        
        return $results;
    }
    
    /**
     * Remove a field from document
     * 
     * @param string $document
     * @param string $name
     */
    public function remove($document, $name)
    {
        $document = $this->resolveDocument($document, true);
        $response = $this->proxy()->doRemove($document, $name);
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
            $responses[$key] = $this->proxy()->doRemove($document, $name);
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
     * @ValuService\Trigger({"type":"post","name":"post.valumodelerdocument.change","args":{"document"}})
     */
    protected function doCreate(Model\Document $document, array $specs)
    {
        $fullSpecs = $specs;
        $specs = $this->filterAndValidate('field', $specs, false);
        
        $field = new Model\Field($specs['name'], $specs['fieldType']);
        $document->addField($field);
        
        unset($specs['type']);
        unset($specs['fieldType']);
        
        $field->setOptions(array_merge($fullSpecs, $specs));
        
        return $field;
    }
    
    /**
     * Perform field removal
     * 
     * @param Model\Document $document
     * @param string $name
     * 
     * @ValuService\Trigger({"type":"post","name":"post.<service>.remove"})
     * @ValuService\Trigger({"type":"post","name":"post.valumodelerdocument.change","args":{"document"}})
     */
    protected function doRemove(Model\Document $document, $name)
    {
        return $document->removeField($name);
    }
    
    /**
     * Perform field update
     * 
     * @param Model\Document $document
     * @param Model\Field $field
     * @param array $specs
     * @return boolean
     * 
     * @ValuService\Trigger({"type":"post","name":"post.<service>.update"})
     * @ValuService\Trigger({"type":"post","name":"post.valumodelerdocument.change","args":{"document"}})
     */
    protected function doUpdate(Model\Document $document, Model\Field $field, array $specs)
    {
        $fullSpecs = $specs;
        $specs = $this->filterAndValidate('field', $specs, true);

        $field->setOptions(array_merge($fullSpecs, $specs));
        return true;
    }
    
    /**
     * Perform upsert
     * 
     * @param Model\Document $document
     * @param string $name
     * @param array $specs
     * @return \ValuModeler\Model\Field
     */
    protected function doUpsert(Model\Document $document, $name, array $specs)
    {
        $document = $this->resolveDocument($document, true);
        $field = $document->getField($name);
        
        if ($field) {
            $this->proxy()->doUpdate($document, $field, $specs);
            return $field;
        } else {
            $specs['name'] = $name;
            $field = $this->doCreate($document, $specs);
            return $field;
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see \ValuModeler\Service\AbstractEntityService::filterAndValidate()
     */
    protected function filterAndValidate($entityType, array $specs, $useValidationGroup = false)
    {
        if ($entityType === 'field') {
            if (isset($specs['fieldType']) && !Model\Field::getTypeFactory()->isValidFieldType($specs['fieldType'])) {
                throw new Exception\UnknownFieldTypeException(
                        'Unknown field type: %TYPE%', array('TYPE' => $specs['fieldType']));
            }
            
            return parent::filterAndValidate($entityType, $specs, $useValidationGroup);
        } else {
            return parent::filterAndValidate($entityType, $specs, $useValidationGroup);
        }
    }
}