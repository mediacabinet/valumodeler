<?php
namespace ValuModeler\FieldType;

use Valu\Model\ArrayAdapter;

/**
 * Abstract field type
 *
 */
abstract class AbstractFieldType
    implements FieldTypeInterface
{
    
    /**
     * Array of associated filters
     * 
     * @var array
     */
    protected $filters = array();
    
    /**
     * Array of associated validators
     * 
     * @var array
     */
    protected $validators = array();
    
    /**
     * Primitive type
     * 
     * @var string
     */
    protected $primitiveType;
    
    /**
     * Array adapter instance
     * @var ArrayAdapter
     */
    protected $arrayAdapter;
    
    /**
     * @see \ValuModeler\FieldType\FieldTypeInterface::getPrimitiveType()
     */
    public function getPrimitiveType()
    {
        return $this->primitiveType;
    }
    
    /**
     * @see \ValuModeler\FieldType\FieldTypeInterface::getOptions()
     */
    public function getOptions()
    {
        return array();
    }
    
    /**
     * @see \ValuModeler\FieldType\FieldTypeInterface::setOptions()
     */
    public function setOptions(array $options)
    {}
    
    /**
     * Retrieve array adapter
     *
     * @return ArrayAdapter
     */
    public function getArrayAdapter()
    {
        if(is_null($this->arrayAdapter)){
            $this->setArrayAdapter(new ArrayAdapter());
        }
    
        return $this->arrayAdapter;
    }
    
    /**
     * Set array adapter
     * 
     * @param ArrayAdapter $arrayAdapter
     */
    public function setArrayAdapter(ArrayAdapter $arrayAdapter)
    {
        $this->arrayAdapter = $arrayAdapter;
    }
    
    /**
     * @see \ValuModeler\FieldType\FieldTypeInterface::getFilters()
     */
	public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @see \ValuModeler\FieldType\FieldTypeInterface::getValidators()
     */
	public function getValidators()
    {
        return $this->validators;
    }
}