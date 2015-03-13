<?php
namespace ValuModeler\Doctrine\MongoDb;

use Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo;
use Doctrine\Instantiator\Instantiator;

class ClassMetadata extends \Doctrine\ODM\MongoDB\Mapping\ClassMetadata
{
    
    /**
     * @var \Doctrine\Instantiator\InstantiatorInterface|null
     */
    private $instantiator;
    
    public function __construct($documentName)
    {
        if (class_exists($documentName)) {
            parent::__construct($documentName);
        } else {
            ClassMetadataInfo::__construct($documentName);
            $this->instantiator = new Instantiator();
        }
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
    
    /**
     * Mimics the behavior of the parent class
     * 
     * @see \Doctrine\ODM\MongoDB\Mapping\ClassMetadata::newInstance()
     */
    public function newInstance()
    {
        $this->instantiator = $this->instantiator ?: new Instantiator();
        return $this->instantiator->instantiate($this->name);
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