<?php
namespace ValuModeler\Service;

use ValuModeler\Model;
use ValuSo\Annotation as ValuService;

class EmbedService extends AbstractModelService
{
    /**
     * Does document have a named embed
     * 
     * @param string $document
     * @param string $name
     * @return boolean
     */
    public function exists($document, $name)
    {
        $document = $this->resolveDocument($document, true);
        return $document->getEmbed($name) !== null; 
    }
    
    /**
     * Create an embed reference to document
     *  
     * @param string $document
     * @param string $name
     * @param string $embedDocument
     * @param string $embedType
     * @param array $specs
     * @return boolean True on success, false otherwise
     */
    public function create($document, $name = null, $embedDocument = null, $embedType = null, array $specs = array())
    {
        $document = $this->resolveDocument($document, true);
        
        if (isset($name)) {
            $specs['name'] = $name;
        }
        
        if (isset($embedDocument)) {
            $specs['embedDocument'] = $embedDocument;
        }
        
        if (isset($embedType)) {
            $specs['embedType'] = $embedType;
        }
        
        $embed = $this->proxy->doCreate($document, $specs);
        
        if ($embed) {
            $this->getDocumentManager()->flush($document);
        }
        
        return $embed;
    }
    
    /**
     * Batch-create embedded references
     * 
     * @param string $document
     * @param array $embeds
     * @return array
     */
    public function createMany($document, $embeds)
    {
        $document = $this->resolveDocument($document, true);
        
        $responses = array();
        foreach ($embeds as $key => $specs) {
            $responses[$key] = $this->doCreate($document, $specs);
        }
        
        if (in_array(true, $responses, true)) {
            $this->getDocumentManager()->flush($document);
        }
        
        return $responses;
    }
    
    /**
     * Remove an embed from document
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
     * Batch-remove embeds from document
     * 
     * @param array $embeds
     */
    public function removeMany($document, array $embeds)
    {
        $document = $this->resolveDocument($document, true);
        $responses = array();
        foreach ($embeds as $key => $name) {
            $responses[$key] = $this->proxy->doRemove($document, $name);
        }
        
        $this->getDocumentManager()->flush($document);
        return $responses;
    }
    
    /**
     * Create a new embed
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
        $specs = $this->getModelInputFilter('embed')->filter(
                $specs, false, true);
        
        // Find reference document by its name
        $reference = $this->getDocumentRepository()->findOneByName($specs['embedDocument']);
        
        if(!$reference){
            throw new Exception\DocumentNotFoundException(
                    'Unable to locate document with name %NAME%',
                    array('NAME' => $specs['document'])
            );
        }
        
        $embed = new Model\Embed($specs['name'], $specs['embedType'], $reference);
        $document->addEmbed($embed);
        
        return $embed;
    }
    
    /**
     * Perform embed removal
     * 
     * @param Model\Document $document
     * @param string $name
     * 
     * @ValuService\Trigger({"type":"post","name":"post.<service>.remove"})
     */
    protected function doRemove(Model\Document $document, $name)
    {
        return $document->removeEmbed($name);
    }
}