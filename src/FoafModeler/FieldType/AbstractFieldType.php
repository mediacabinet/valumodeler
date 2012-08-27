<?php
namespace FoafModeler\FieldType;

use Foaf\Model\ArrayAdapter;

abstract class AbstractFieldType
    implements FieldTypeInterface
{
    
    protected $filters = array();
    
    protected $validators = array();
    
    protected $primitiveType;
    
    protected $arrayAdapter;
    
    public function getPrimitiveType()
    {
        return $this->primitiveType;
    }
    
    public function setOptions(array $options)
    {
        $adapter = $this->getArrayAdapter();
        $adapter->fromArray($this, $options);
    }
    
    /**
     * Retrieve array adapter instance
     *
     * @return ArrayAdapter
     */
    public function getArrayAdapter()
    {
        if(is_null($this->arrayAdapter)){
            $this->setArrayAdapter(ArrayAdapter::getSharedInstance());
        }
    
        return $this->arrayAdapter;
    }
    
    public function setArrayAdapter(ArrayAdapter $arrayAdapter)
    {
        $this->arrayAdapter = $arrayAdapter;
    }
    
	public function getFilters()
    {
        return $this->filters;
    }

	public function getValidators()
    {
        return $this->validators;
    }
}