<?php
namespace ValuModeler\Model;

use ValuModeler\FieldType\FieldTypeFactory;
use ValuModeler\FieldType\FieldTypeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Field
{
	/**
	 * @ODM\Id(strategy="UUID")
	 * @var string
	 */
    private $id;
    
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    private $name;
    
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    private $type;
    
    /**
     * @ODM\Field(type="hash")
     * @var array
     */
    protected $filters = array();
    
    /**
     * @ODM\Field(type="hash")
     * @var array
     */
    protected $validators = array();
    
    /**
     * @ODM\Field(type="boolean")
     * @var boolean
     */
    protected $required = false;
    
    /**
     * @ODM\Field(type="boolean")
     * @var boolean
     */
    protected $allowEmpty = true;

    /**
     * @ODM\Field(type="hash")
     * @var array
     */
    protected $options = array();
    
    /**
     * @var \ValuModeler\FieldType\FieldTypeInterface
     */
    private $typeObject = null;
    
    /**
     * Field type factory
     * 
     * @var \ValuModeler\FieldType\FieldTypeFactory
     */
    private static $typeFactory;
    
    public function __construct($name, $type, array $options = array())
    {
        $this->setName($name);
        $this->setType($type);
        $this->setOptions($options);
    }
    
    /**
     * Retrieve field name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set field name
     * 
     * @param string $name
     */
    protected function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Retrieve field type
     * 
     * @return \ValuModeler\FieldType\FieldTypeInterface
     */
    public function getType()
    {
        if($this->typeObject === null){
            $this->typeObject = self::getTypeFactory()->createFieldType($this->type);
            $this->typeObject->setOptions($this->options);
        }
        
        return $this->typeObject;
    }
    
    /**
     * Set field type
     * 
     * @param string|\ValuModeler\FieldType\FieldTypeInterface $type
     */
    public function setType($type)
    {
        if($type instanceof FieldTypeInterface){
            $specs = $type->toArray();
            
            $type = $specs['type'];
            unset($specs['type']);
            
            $this->options = array_merge(
                $this->options,
                $specs        
            );
        } else {
            $this->typeObject = self::getTypeFactory()->createFieldType($type);
        }
        
        $this->type = $type;
    }
    
    /**
     * Is the field required?
     * 
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }
    
    /**
     * Set field as required
     * 
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = (bool) $required;
    }
    
    /**
     * Does the field allow empty value?
     * 
     * @return boolean
     */
    public function getAllowEmpty()
    {
        return $this->allowEmpty;
    }

	/**
	 * Set whether or not the field should allow empty value
	 * 
     * @param boolean $allowEmpty
     */
    public function setAllowEmpty($allowEmpty)
    {
        $this->allowEmpty = (bool) $allowEmpty;
    }
    
    /**
     * Retrieve filter specifications
     * 
     * @return array
     */
    public function getFilters()
    {
        return array_merge(
            $this->getType()->getFilters(),
            $this->filters
        );
    }
    
    /**
     * Set filter specifications
     * 
     * @param array $filters
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }
    
    /**
     * Retrieve validator specifications
     * 
     * @return array
     */
    public function getValidators()
    {
        return array_merge(
            $this->getType()->getValidators(),
            $this->validators
        );
    }
    
    /**
     * Set validator specifications
     * 
     * @param array $validators
     */
    public function setValidators(array $validators)
    {
        $this->validators = $validators;
    }
    
    /**
     * Set type specific options
     * 
     * @param array $options
     */
	public function setOptions(array $options)
    {
        if (array_key_exists('type', $options)) {
            $this->setType($options['type']);
            unset($options['type']);
        } elseif (array_key_exists('fieldType', $options)) {
            $this->setType($options['fieldType']);
            unset($options['fieldType']);
        }
        
        if (array_key_exists('required', $options)) {
            if (isset($options['required'])) {
                $this->setRequired($options['required']);
            }
            
            unset($options['required']);
        }
        
        if (array_key_exists('allowEmpty', $options)) {
            if (isset($options['allowEmpty'])) {
                $this->setAllowEmpty($options['allowEmpty']);
            }
            
            unset($options['allowEmpty']);
        }
        
        if (array_key_exists('validators', $options)) {
            if (isset($options['validators'])) {
                $this->setValidators($options['validators']);
            }
            
            unset($options['validators']);
        }
        
        if (array_key_exists('filters', $options)) {
            if (isset($options['filters'])) {
                $this->setFilters($options['filters']);
            }
            
            unset($options['filters']);
        }
        
        // Pass remaining options to type object
        if ($this->getType()) {
            $this->getType()->setOptions($options);
            $this->options = $this->getType()->getOptions();
        }
    }
    
    /**
     * Fetch input filter specifications
     * 
     * @return array
     */
    public function getInputFilterSpecifications()
    {
        return array(
            'name'        => $this->getName(),
            'required'    => $this->getRequired(),
            'allow_empty' => $this->getAllowEmpty(),
            'filters'     => $this->getFilters(),
            'validators'  => $this->getValidators()
        );
    }
    
    /**
     * Set static type factory
     * 
     * @param FieldTypeFactory $factory
     */
    public static function setTypeFactory(FieldTypeFactory $factory)
    {
        self::$typeFactory = $factory;
    }
    
    /**
     * Retrieve static type factory
     * 
     * @return \ValuModeler\FieldType\FieldTypeFactory
     */
    public static function getTypeFactory()
    {
        return self::$typeFactory;
    }
}
