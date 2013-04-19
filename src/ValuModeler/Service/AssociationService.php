<?php
namespace ValuModeler\Service;

use ValuModeler\Model;
use ValuSo\Annotation as ValuService;

class AssociationService extends AbstractEntityService
{
    /**
     * Proxy class instance
     *
     * @var AssociationService
     */
    protected $proxy;
    
    /**
     * Does document have a named association
     * 
     * @param string $document
     * @param string $name
     * @return boolean
     */
    public function exists($document, $name)
    {
        $document = $this->resolveDocument($document, true);
        return $document->getAssociation($name) !== null; 
    }
    
    /**
     * Create an association reference to document
     *  
     * @param string $document
     * @param string $name
     * @param string $refDocument
     * @param string $associationType
     * @param boolean $embedded
     * @param array $specs
     * @return boolean True on success, false otherwise
     */
    public function create($document, $name = null, $refDocument = null, $associationType = null, $embedded = false, array $specs = array())
    {
        $document = $this->resolveDocument($document, true);
        
        if (isset($name)) {
            $specs['name'] = $name;
        }
        
        if (isset($refDocument)) {
            $specs['refDocument'] = $refDocument;
        }
        
        if (isset($associationType)) {
            $specs['associationType'] = $associationType;
        }
        
        if (isset($embedded)) {
            $specs['embedded'] = $embedded;
        } elseif (!isset($specs['embedded'])) {
            $specs['embedded'] = false;
        }
        
        $association = $this->proxy->doCreate($document, $specs);
        
        if ($association) {
            $this->getDocumentManager()->flush($document);
        }
        
        return $association;
    }
    
    /**
     * Batch-create associations
     * 
     * @param string $document
     * @param array $assocs
     * @return array
     */
    public function createMany($document, array $assocs)
    {
        $document = $this->resolveDocument($document, true);
        
        $responses = array();
        foreach ($assocs as $key => $specs) {
            if (!isset($specs['embedded'])) {
                $specs['embedded'] = false;
            }
            
            $responses[$key] = $this->proxy->doCreate($document, $specs);
        }
        
        if (in_array(true, $responses, true)) {
            $this->getDocumentManager()->flush($document);
        }
        
        return $responses;
    }
    
    /**
     * Update association
     *
     * @param string|\ValuModeler\Model\Document $document
     * @param string $name
     * @param array $specs
     * @return \ValuModeler\Model\Association|NULL
     */
    public function update($document, $name, array $specs)
    {
        $document = $this->resolveDocument($document, true);
        $association = $document->getAssociation($name);
        
        if (!$association) {
            throw new Exception\AssociationNotFoundException(
                'Document %DOCUMENT% does not contain association %ASSOCIATION%', 
                ['DOCUMENT' => $document->getName(), 'ASSOCIATION' => $name]);
        }
        
        $result = $this->proxy->doUpdate($document, $association, $specs);
        $this->getDocumentManager()->flush($document);
        return $result;
    }
    
    /**
     * Create a new association or update existing
     * 
     * @param string|\ValuModeler\Model\Document $document
     * @param string $name
     * @param array $specs
     * @return \ValuModeler\Model\Association|NULL
     */
    public function upsert($document, $name, array $specs)
    {
        $document = $this->resolveDocument($document, true);
        $association = $this->doUpsert($document, $name, $specs);
        $this->getDocumentManager()->flush($document);
        
        return $association;
    }
    
    /**
     * Batch-upsert associations
     * 
     * @param string|\ValuModeler\Model\Document $document
     * @param array $associations
     * @return array
     */
    public function upsertMany($document, array $associations)
    {
        $document = $this->resolveDocument($document, true);
        
        $results = array();
        foreach ($associations as $key => $specs) {
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
     * Remove an association from document
     * 
     * @param string $document
     * @param string $name
     */
    public function remove($document, $name)
    {
        $document = $this->resolveDocument($document, true);
        $response = $this->proxy->doRemove($document, $name);
        $this->getDocumentManager()->flush($document);
        
        return $response;
    }
    
    /**
     * Batch-remove associations from document
     * 
     * @param array $assocs
     */
    public function removeMany($document, array $assocs)
    {
        $document = $this->resolveDocument($document, true);
        $responses = array();
        foreach ($assocs as $key => $name) {
            $responses[$key] = $this->proxy->doRemove($document, $name);
        }
        
        $this->getDocumentManager()->flush($document);
        return $responses;
    }
    
    /**
     * Create a new association
     * 
     * @param Model\Document $document
     * @param array $specs
     * @return boolean
     * 
     * @ValuService\Trigger({"type":"post","name":"post.<service>.create"})
     * @ValuService\Trigger({"type":"post","name":"post.valumodelerdocument.change","args":{"document"}})
     */
    protected function doCreate(Model\Document $document, $specs)
    {

        $embedded = (bool) $specs['embedded'];
        
        // Filter and validate
        $specs = $this->getEntityInputFilter('association')->filter(
                $specs, false, true);
        
        // Find reference document by its name
        $reference = $this->getDocumentRepository()->findOneByName($specs['refDocument']);
        
        if(!$reference){
            throw new Exception\DocumentNotFoundException(
                    'Unable to locate document with name %NAME%',
                    array('NAME' => $specs['refDocument'])
            );
        }
        
        $association = $document->createAssociation(
            $specs['name'], 
            $specs['associationType'], 
            $reference, 
            $embedded, 
            $specs);
        
        return $association;
    }
    
    /**
     * Perform association update
     * 
     * @param Model\Document $document
     * @param Model\Association $association
     * @param array $specs
     * @return boolean
     * 
     * @ValuService\Trigger({"type":"post","name":"post.<service>.update"})
     * @ValuService\Trigger({"type":"post","name":"post.valumodelerdocument.change","args":{"document"}})
     */
    protected function doUpdate(Model\Document $document, Model\AbstractAssociation $association, array $specs)
    {
        $specs = $this->filterAndValidate('association', $specs, true);
        
        if (isset($specs['associationType'])) {
            $association->setType($specs['associationType']);
        }
        
        if (isset($specs['refDocument'])) {
            $reference = $this->resolveDocument($specs['refDocument'], true);
            $association->setDocument($reference);
        }
        
        return true;
    }
    
    /**
     * Perform upsert
     * 
     * @param Model\Document $document
     * @param string $name
     * @param array $specs
     * @return \ValuModeler\Model\Association
     */
    protected function doUpsert(Model\Document $document, $name, array $specs)
    {
        $document = $this->resolveDocument($document, true);
        $association = $document->getAssociation($name);
        
        if ($association) {
            $this->proxy->doUpdate($document, $association, $specs);
            return $association;
        } else {
            $specs['name'] = $name;
            
            if (!isset($specs['embedded'])) {
                $specs['embedded'] = false;
            }
            
            $association = $this->doCreate($document, $specs);
            return $association;
        }
    }
    
    /**
     * Perform association removal
     * 
     * @param Model\Document $document
     * @param string $name
     * 
     * @ValuService\Trigger({"type":"post","name":"post.<service>.remove"})
     * @ValuService\Trigger({"type":"post","name":"post.valumodelerdocument.change", "args":{"document"}})
     */
    protected function doRemove(Model\Document $document, $name)
    {
        return $document->removeAssociation($name);
    }
}