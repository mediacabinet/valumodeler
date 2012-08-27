<?php
namespace FoafModeler\FieldType;

use FoafModeler\FieldType\FieldTypeInterface;
use Zend\Loader\PluginClassLoader;

class FieldTypeFactory
{
    
    protected $classMap = array();
    
    public function __construct(array $map)
    {
        $this->registerFieldTypes($map);
    }
    
    public function createFieldType($type)
    {
        $class = $this->getClass($type);
        $fieldType = new $class;
        
        if(!($fieldType instanceof FieldTypeInterface)){
            throw new \Exception('FieldType class '.$class.' does not implement FieldTypeInterface');
        }
        
        return $fieldType;
    }    
    
    public function registerFieldType($type, $class)
    {
        if(!class_exists($class)){
            throw new \Exception('Invalid configuration for fieldType '.$type);
        }
    
        $this->classMap[$type] = $class;
    }
    
    public function registerFieldTypes(array $map)
    {
        foreach($map as $type => $specs){
            
            $class = null;
            
            if(is_string($specs)){
                $class = $specs;
            }
            else if(isset($specs['class'])){
                $class = $specs['class'];
            }
            
            if(!$class){
                throw new \InvalidArgumentException(
                    sprintf("No class defined for field type %s", $type)        
                );
            }
            
            $this->registerFieldType($type, $class);
        }
    }

    protected function getClass($type)
    {
        return isset($this->classMap[$type]) ? $this->classMap[$type] : null; 
    }
}