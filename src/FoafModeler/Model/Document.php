<?php
namespace FoafModeler\Model;

use FoafModeler\Utils;
use Foaf\Utils\UuidGenerator;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ODM\Document(collection="foaf_modeler_document")
 */
class Document
{
	/**
	 * @ODM\Id
	 * @var string
	 */
    private $id;
    
    /**
     * @ODM\String
     * @ODM\Index
     * @var string
     */
    private $uuid;
    
    /**
     * @ODM\ReferenceOne(targetDocument="FoafModeler\Model\Document")
     * @var Document
     */
    private $parent;
    
    /**
     * @ODM\String
     * @ODM\Index(unique=true, order="asc")
     * @var string
     */
    private $name;
    
    /**
     * @ODM\String
     * @var string
     */
    private $collection;
    
    /**
     * @ODM\String
     * @var string
     */
    private $idFieldName = 'id';
    
    /**
     * @ODM\EmbedMany(targetDocument="FoafModeler\Model\Field")
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $fields;
    
    /**
     * @ODM\EmbedMany(targetDocument="FoafModeler\Model\Embed")
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $embeds;

    public function __construct($name)
    {
        $this->uuid = UuidGenerator::generate(
            UuidGenerator::VERSION_3,
            (string) new \MongoId(),
            'foaf-modeler-document'
        );
        
        $this->fields = new ArrayCollection();
        $this->embeds = new ArrayCollection();
        
        $this->setName($name);
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getUuid()
    {
        return $this->uuid;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    protected function setName($name)
    {
        $this->name = $name;
    }
    
    public function getCollection()
    {
        return $this->collection;
    }
    
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }
    
    public function setIdFieldName($name)
    {
        $this->idFieldName = $name;
    }
    
    public function getIdFieldName()
    {
        return $this->idFieldName;
    }
    
    public function getParent()
    {
        return $this->parent;
    }
    
    public function setParent(Document $parent)
    {
        if($parent === $this || $parent->getName() == $this->getName()){
            throw new \InvalidArgumentException('Parent cannot reference to document itself');
        }
        
        $this->parent = $parent;
    }
    
    /**
     * Retrieve field by name
     * 
     * @param string $name
     * @return \FoafModeler\Model\Field|NULL
     */
    public function getField($name)
    {
        foreach($this->fields as $field){
            if($field->getName() == $name){
                return $field;
            }
        }
    }
    
    /**
     * Retrieve fields as an array
     * 
     * @return array
     */
    public function getFields()
    {
        $named = array();

        foreach($this->fields as $field){
            $named[$field->getName()] = $field;
        }
        
        return $named;
    }
    
    /**
     * Add field
     * 
     * @param Field $field
     */
    public function addField(Field $field)
    {
        if($this->getField($field->getName())){
            throw new \Exception('Field '.$field->getName().' already exists');
        }
        
        $this->fields->add($field);
    }
    
    /**
     * Remove field by name
     * 
     * @param string $name
     */
    public function removeField($name)
    {
        $field = $this->getField($name);
        $this->fields->removeElement($field);
    }
    
    /**
     * Retrieve embed by name
     *
     * @param string $name
     * @return \FoafModeler\Model\Embed|NULL
     */
    public function getEmbed($name)
    {
        foreach($this->embeds as $embed){
            if($embed->getName() == $name){
                return $embed;
            }
        }
    }
    
    /**
     * Retrieve embeds as an array
     *
     * @return array
     */
    public function getEmbeds()
    {
        $named = array();

        foreach($this->embeds as $embed){
            $named[$embed->getName()] = $embed;
        }
        
        return $named;
    }
    
    /**
     * Add embed
     *
     * @param Embed $embed
     */
    public function addEmbed(Embed $embed)
    {
        if($this->getEmbed($embed->getName())){
            throw new \Exception('Embed '.$embed->getName().' already exists');
        }
    
        $this->embeds->add($embed);
    }
    
    /**
     * Remove embed by name
     *
     * @param string $name
     */
    public function removeEmbed($name)
    {
        $embed = $this->getEmbed($name);
        $this->embeds->removeElement($embed);
    }
    
    /**
     * Retrieve input filter specifications as an array
     * 
     * @return array
     */
    public function getInputFilterSpecifications()
    {
        $specs = array();
        
        foreach($this->fields as $field)
        {
            $specs[$field->getName()] = array(
                'required'    => $field->getRequired(),
                'filters'     => $field->getFilters(),
                'validators'  => $field->getValidators()      
            );
        }
        
        return $specs;
    }
}