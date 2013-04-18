<?php
namespace ValuModeler\Doctrine\MongoDb;

use ValuModeler\Model;
use ValuModeler\Utils;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;

class Driver implements MappingDriver
{
    private $documents = null;
    
    /**
     * Array that maps primitive types to
     * corresponding MongoDB types
     * 
     * @var array
     */
    private $typeMap = array(
        'map' => 'hash',
        'integer' => 'int'     
    );
    
    /**
     * Retrieve registered document by name
     * 
     * @param unknown_type $name
     * @return \ValuModeler\Model\Document
     */
    public function getDocument($name)
    {
        return isset($this->documents[$name])
            ? $this->documents[$name]
            : null;
    }
    
    /**
     * Register new document
     * 
     * @param string $name
     * @param Model\Document $document
     */
    public function addDocument(Model\Document $document)
    {
        $this->documents[$document->getName()] = $document;
    }
    
    /**
     * Retrieve all registered documents
     * 
     * @return array
     */
    public function getDocuments()
    {
        return $this->documents;
    }
    
    /**
     * Loads the metadata for the specified class into the provided container.
     *
     * @param string $className
     * @param ClassMetadata $metadata
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        $documentName = Utils::classToDocName($className);
        
        if(!$documentName){
            throw new \InvalidArgumentException(
                'Class name is not in namespace: '.$this->classNs
            );
        }
        
        $document = $this->getDocument($documentName);
        
        if(!$document){
            throw new \InvalidArgumentException(
                "Class name ".$className." doesn't match with any known document"
            );
        }
        
        // Define explicit collection
        if($document->getCollection()){
            $metadata->setCollection($document->getCollection());
        }
        // No collection, define as embedded document
        else{
            $metadata->isEmbeddedDocument = true;
        }
        
        // Indexes
        /*
        foreach($document->getIndexes() as $index){
            $metadata->addIndex($index);
        }
        */
        
        // Embeds
        foreach($document->getEmbeds() as $embed){
            
            $mapping = array(
                'name'         => $embed->getName(),
                'strategy'     => 'pushAll',
                'embedded'     => true,
                'targetDocument' => Utils::docNameToClass($embed->getDocument()->getName())
            );
            
            if($embed->getType() == Model\AbstractAssociation::REFERENCE_ONE){
                $mapping['type'] = 'one';
            }
            else if($embed->getType() == Model\Embed::REFERENCE_MANY){
                $mapping['type'] = 'many';
            }
            else{
                throw new \InvalidArgumentException('Invalid embed type defined for '.$embed->getName());
            }
            
            $this->addFieldMapping($metadata, $mapping);
        }
        
        // Fields
        foreach($document->getFields() as $field){
            
            $type = $field->getType()->getPrimitiveType();
            
            if(isset($this->typeMap[$type])){
                $type = $this->typeMap[$type];
            }
            
            $mapping = array(
                'name' => $field->getName(),
                'type' => $type        
            );
            
            $this->addFieldMapping($metadata, $mapping);
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see \Doctrine\Common\Persistence\Mapping\Driver\MappingDriver::getAllClassNames()
     */
    public function getAllClassNames()
    {
        $names = array();
        
        foreach($this->getDocuments() as $document){
            $names[] = Utils::docNameToClass($document->getName()); 
        }
        
        return $names;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Doctrine\Common\Persistence\Mapping\Driver\MappingDriver::isTransient()
     */
    public function isTransient($className)
    {
        return false;
    }
    
    /**
     * Add field mapping
     * 
     * @param ClassMetadata $class
     * @param array $mapping
     * @throws \InvalidArgumentException
     */
    private function addFieldMapping(ClassMetadata $class, $mapping)
    {
        $keys = null;

        if (isset($mapping['name'])) {
            $name = $mapping['name'];
        }
        else {
            throw new \InvalidArgumentException("Mapping doesn't contain name information");
        }

        if (isset($mapping['type']) && $mapping['type'] === 'collection') {
            $mapping['strategy'] = isset($mapping['strategy']) ? $mapping['strategy'] : 'pushAll';
        }
        
        if (isset($mapping['index'])) {
            $keys = array(
                $name => isset($mapping['order']) ? $mapping['order'] : 'asc'
            );
        }
        
        if (isset($mapping['unique'])) {
            $keys = array(
                $name => isset($mapping['order']) ? $mapping['order'] : 'asc'
            );
        }
        
        if ($keys !== null) {
            
            $options = array();
            
            if (isset($mapping['index-name'])) {
                $options['name'] = (string) $mapping['index-name'];
            }
            if (isset($mapping['drop-dups'])) {
                $options['dropDups'] = (boolean) $mapping['drop-dups'];
            }
            if (isset($mapping['background'])) {
                $options['background'] = (boolean) $mapping['background'];
            }
            if (isset($mapping['safe'])) {
                $options['safe'] = (boolean) $mapping['safe'];
            }
            if (isset($mapping['unique'])) {
                $options['unique'] = (boolean) $mapping['unique'];
            }
            if (isset($mapping['sparse'])) {
                $options['sparse'] = (boolean) $mapping['sparse'];
            }
            
            $class->addIndex($keys, $options);
        }
        
        $class->mapField($mapping);
    }
}