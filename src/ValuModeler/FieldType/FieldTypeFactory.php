<?php
namespace ValuModeler\FieldType;

use ValuModeler\FieldType\Exception\InvalidFieldTypeException;
use ValuModeler\FieldType\Exception\UnknownFieldTypeException;

/**
 * Factory class for creating different field type instances
 */
class FieldTypeFactory
{
    /**
     * Field type class map
     * 
     * Maps field types to corresponding class names.
     * 
     * @var array
     */
    protected $classMap = array();
    
    /**
     * Initialize with class map
     * 
     * @param array $map
     */
    public function __construct(array $map)
    {
        $this->registerFieldTypes($map);
    }
    
    /**
     * Is the field type recognized?
     * 
     * @param string $type
     * @return boolean
     */
    public function isValidFieldType($type)
    {
        return $this->getClassName($this->canonicalizeType($type)) !== null;
    }
    
    /**
     * Create field type instance by name
     * 
     * @param string $type
     * @throws UnknownFieldTypeException
     * @throws InvalidFieldTypeException
     * @return FieldTypeInterface
     */
    public function createFieldType($type)
    {
        $class = $this->getClassName($this->canonicalizeType($type));
        if(!$class){
            throw new UnknownFieldTypeException(
                sprintf('Unknown field type "%s". No matching key has been registered.', $type));
        }

        $fieldType = new $class;
        if(! $fieldType instanceof FieldTypeInterface){
            throw new InvalidFieldTypeException(
                sprintf('Class "%s" registered for field type "%s" does not implement FieldTypeInterface', $class, $type));
        }

        return $fieldType;
    }    
    
    /**
     * Register a new field type
     * 
     * @param string $type
     * @param string $class
     * @throws \Exception
     */
    public function registerFieldType($type, $class)
    {
        $type = $this->canonicalizeType($type);
        
        if(!class_exists($class)){
            throw new UnknownFieldTypeException(
                sprintf('Class "%s" registered for field type "%s" does not exist', $class, $type));
        }
    
        $this->classMap[$type] = $class;
    }
    
    /**
     * Batch-register field types
     * 
     * @param array $map
     * @throws \InvalidArgumentException
     */
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

    /**
     * Retrieve class name for field type
     * 
     * @param string $type
     * @return string|null Class name or null if not found
     */
    protected function getClassName($type)
    {
        return isset($this->classMap[$type]) ? $this->classMap[$type] : null; 
    }
    
    /**
     * Retrieve type name in normal form
     * 
     * @param string $type
     * @return string
     */
    protected function canonicalizeType($type)
    {
        return strtolower($type);
    }
}