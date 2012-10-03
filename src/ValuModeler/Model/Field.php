<?php
namespace ValuModeler\Model;

use Valu\Model\InputFilterTrait;
use ValuModeler\FieldType\FieldTypeFactory;
use ValuModeler\FieldType\FieldTypeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Field
{
    use InputFilterTrait;
    
	/**
	 * @ODM\Id
	 * @var string
	 */
    private $id;
    
    /**
     * @ODM\String
     * @var string
     */
    private $name;
    
    /**
     * @ODM\String
     * @var string
     */
    private $type;
    
    /**
     * @ODM\Hash
     * @var array
     */
    protected $filters = array();
    
    /**
     * @ODM\Hash
     * @var array
     */
    protected $validators = array();
    
    /**
     * @ODM\Boolean
     * @var boolean
     */
    protected $required = false;

    /**
     * @ODM\Hash
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
    
    /**
     * Default input filter instance
     *
     * @var Zend\InputFilter\InputFilter
     */
    protected static $defaultInputFilter;
    
    public function __construct($name, $type, array $options = array())
    {
        $this->setName($name);
        $this->setType($type);
        $this->setOptions($options);
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    protected function setName($name)
    {
        $this->name = $name;
    }
    
    public function getType()
    {
        if($this->typeObject === null){
            $this->typeObject = self::getTypeFactory()->createFieldType($this->type);
            $this->typeObject->setOptions($this->options);
        }
        
        return $this->typeObject;
    }
    
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
        }
        
        $this->type = $type;
    }
    
    public function getRequired()
    {
        return $this->required;
    }
    
    public function setRequired($required)
    {
        $this->required = (bool) $required;
    }
    
    public function getFilters()
    {
        return array_merge(
            $this->getType()->getFilters(),
            $this->filters
        );
    }
    
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }
    
    public function getValidators()
    {
        return array_merge(
            $this->getType()->getValidators(),
            $this->validators
        );
    }
    
    public function setValidators(array $validators)
    {
        $this->validators = $validators;
    }
    
    public function setOptions(array $options)
    {
        
        if(isset($options['validators'])){
            $this->setValidators($options['validators']);
            unset($options['validators']);
        }
        
        if(isset($options['filters'])){
            $this->setFilters($options['filters']);
            unset($options['filters']);
        }
        
        if(isset($options['required'])){
            $this->setRequired($options['required']);
            unset($options['required']);
        }
        
        if($this->typeObject && sizeof($options)){
            $this->typeObject->setOptions($options);
        }
        
        $this->options = $options;
    }
    
    public static function setTypeFactory(FieldTypeFactory $factory)
    {
        self::$typeFactory = $factory;
    }
    
    public static function getTypeFactory()
    {
        return self::$typeFactory;
    }
}