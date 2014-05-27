<?php
namespace ValuModeler\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ODM\Document(collection="valu_modeler_document")
 */
class Document
{
	/**
	 * @ODM\Id(strategy="UUID")
	 * @var string
	 */
    private $id;
    
    /**
     * @ODM\ReferenceOne(targetDocument="ValuModeler\Model\Document")
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
     * @ODM\Index(unique=true, order="asc", sparse=true)
     * @var string
     */
    private $collection;
    
    /**
     * @ODM\String
     * @var string
     */
    private $idFieldName = 'id';
    
    /**
     * @ODM\EmbedMany(targetDocument="ValuModeler\Model\Field")
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $fields;
    
    /**
     * @ODM\EmbedMany(targetDocument="ValuModeler\Model\Embed")
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $embeds;
    
    /**
     * @ODM\EmbedMany(targetDocument="ValuModeler\Model\Reference")
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $references;

    public function __construct($name)
    {
        $this->fields     = new ArrayCollection();
        $this->embeds     = new ArrayCollection();
        $this->references = new ArrayCollection();
        
        $this->setName($name);
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Retrieve document name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set document name
     * 
     * @param string $name
     */
    protected function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Retrieve collection name
     * 
     * @return string
     */
    public function getCollection()
    {
        return $this->collection;
    }
    
    /**
     * Set collection name
     * 
     * @param string $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }
    
    /**
     * Set identifier field name
     * 
     * @param string $name
     */
    public function setIdFieldName($name)
    {
        $this->idFieldName = $name;
    }
    
    /**
     * Retrieve identifier field name
     * 
     * @return string
     */
    public function getIdFieldName()
    {
        return $this->idFieldName;
    }
    
    /**
     * Retrieve parent document
     * 
     * @return \ValuModeler\Model\Document
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * Set parent document
     * 
     * @param Document $parent
     * @throws \InvalidArgumentException
     */
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
     * @return \ValuModeler\Model\Field|NULL
     */
    public function getField($name)
    {
        foreach($this->fields as $field){
            if($field->getName() == $name){
                return $field;
            }
        }
        
        return null;
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
        if (($item = $this->getItem($field->getName())) != false) {
            throw new \Exception(
                sprintf('Another item of type %s already exists with name %s',
                    get_class($item),
                    $field->getName()
                )
            );
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
        
        if ($field !== null) {
            return $this->fields->removeElement($field);
        }
        
        return false;
    }
    
    /**
     * Retrieve embed by name
     *
     * @param string $name
     * @return \ValuModeler\Model\Embed|NULL
     */
    public function getEmbed($name)
    {
        foreach($this->embeds as $embed){
            if($embed->getName() == $name){
                return $embed;
            }
        }
        
        return null;
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
        if (($item = $this->getItem($embed->getName())) != false) {
            throw new \Exception(
                sprintf('Another item of type %s already exists with name %s',
                    get_class($item),
                    $embed->getName()
                )
            );
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
        
        if ($embed !== null) {
            return $this->embeds->removeElement($embed);
        }
        
        return false;    
    }
    
    /**
     * Retrieve reference by name
     *
     * @param string $name
     * @return \ValuModeler\Model\Reference|NULL
     */
    public function getReference($name)
    {
        foreach($this->references as $reference){
            if($reference->getName() == $name){
                return $reference;
            }
        }
        
        return null;
    }
    
    /**
     * Retrieve references as an array
     *
     * @return array
     */
    public function getReferences()
    {
        $named = array();
    
        foreach($this->references as $reference){
            $named[$reference->getName()] = $reference;
        }
    
        return $named;
    }
    
    /**
     * Add reference
     *
     * @param Reference $reference
     */
    public function addReference(Reference $reference)
    {
        if (($item = $this->getItem($reference->getName())) != false) {
            throw new \Exception(
                sprintf('Another item of type %s already exists with name %s',
                    get_class($item),
                    $reference->getName()
                )
            );
        }
    
        $this->references->add($reference);
    }
    
    /**
     * Remove reference by name
     *
     * @param string $name
     */
    public function removeReference($name)
    {
        $reference = $this->getReference($name);
        
        if ($reference !== null) {
            return $this->references->removeElement($reference);    
        }
        
        return false;
    }
    
    /**
     * Test if a named item (field, embed or reference) exists
     * 
     * @param string $name
     * @return boolean
     */
    public function hasItem($name)
    {
        return $this->getItem($name) != false;
    }
    
    /**
     * Fetch item (field, embed or reference) by name
     * 
     * @param string $name
     * @return mixed
     */
    public function getItem($name)
    {
        $item = $this->getField($name);
        
        if(!$item){
            $item = $this->getEmbed($name);
        }
        
        if(!$item){
            $item = $this->getReference($name);
        }
        
        if(!$item && $this->getParent()){
            return $this->getParent()->getItem($name);
        }
        else{
            return $item;
        }
    }
    
    /**
     * Create and attach new association
     * 
     * @param string $name
     * @param string $type
     * @param Document $document
     * @param boolean $embedded
     * @param array $specs
     * @return \ValuModeler\Model\Embed|\ValuModeler\Model\Reference
     */
    public function createAssociation($name, $type, Document $document, $embedded, array $specs = array())
    {
        if ($embedded) {
            $embed = new Embed($name, $type, $document);
            $this->addEmbed($embed);
            
            return $embed;
        } else {
            $reference = new Reference($name, $type, $document);
            $this->addReference($reference);
            
            return $reference;
        }
    }
    
    /**
     * Retrieve association by name
     * 
     * @param string $name
     * @return \ValuModeler\Model\AbstractAssociation|NULL
     */
    public function getAssociation($name)
    {
        $item = $this->getItem($name);
        
        if ($item instanceof AbstractAssociation) {
            return $item;
        } else {
            return null;
        }
    }
    
    /**
     * Remove association by name
     * 
     * @param string $name
     */
    public function removeAssociation($name)
    {
        $association = $this->getAssociation($name);
        
        if (!$association) {
            return false;
        } elseif ($association instanceof Embed) {
            return $this->removeEmbed($name);
        } else {
            return $this->removeReference($name);
        }
    }
    
    /**
     * Retrieve input filter specifications as an array
     * 
     * @return array
     */
    public function getInputFilterSpecifications()
    {
        $specs = array();
        
        if($this->getParent()){
            $specs = $this->getParent()->getInputFilterSpecifications();
        }
        
        foreach($this->fields as $field)
        {
            $specs[$field->getName()] = $field->getInputFilterSpecifications();
        }
        
        // Remove key 'type' for Zend\InputFilter\Factory compatibility
        if(isset($specs['type'])){
            $specs[''] = $specs['type'];
            unset($specs['type']);
        }
        
        return $specs;
    }
}