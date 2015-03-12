<?php
namespace ValuModeler\Doctrine\MongoDb;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo;

class ClassMetadata extends \Doctrine\ODM\MongoDB\Mapping\ClassMetadata
{
    
    public function __construct($documentName)
    {
        parent::__construct($documentName);
    }
    
    public function mapField(array $mapping)
    {
        if(!$this->reflClass){
            return ClassMetadataInfo::mapField($mapping);
        }
        else{
            parent::mapField($mapping);
        }
    }
    
    public function loadReflClass()
    {
        $this->setReflClass(new \ReflectionClass($this->name));
        return $this;
    }
    
    public function setReflClass(\ReflectionClass $reflClass)
    {
        $this->reflClass = $reflClass;
        $this->namespace = $this->reflClass->getNamespaceName();
        $this->collection = $this->reflClass->getShortName();
        
        $this->resetReflFields();
        return $this;
    }
    
    private function resetReflFields()
    {
        foreach($this->fieldMappings as $fieldName => $mapping){
            if ($this->reflClass->hasProperty($fieldName)) {
                $reflProp = $this->reflClass->getProperty($fieldName);
                $reflProp->setAccessible(true);
                $this->reflFields[$fieldName] = $reflProp;
            }
        }
    }
}