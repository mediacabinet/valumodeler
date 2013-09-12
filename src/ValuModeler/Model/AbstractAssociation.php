<?php
namespace ValuModeler\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

Abstract class AbstractAssociation
{
    const REFERENCE_ONE = 'reference_one';
    
    const REFERENCE_MANY = 'reference_many';

	/**
	 * @ODM\Id(strategy="UUID")
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
     * @ODM\ReferenceOne(targetDocument="ValuModeler\Model\Document")
     * @var Document
     */
    private $document;
    
    public function __construct($name, $type, Document $document)
    {
        $this->setName($name);
        $this->setType($type);
        $this->setDocument($document);
    }
    
    /**
     * Retrieve name of the association
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set name of the association
     * 
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Retrieve reference type
     * 
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Set reference type
     * 
     * @param string $type
     * @throws \InvalidArgumentException
     */
    public function setType($type)
    {
        if(!in_array($type, array(self::REFERENCE_ONE, self::REFERENCE_MANY))){
            throw new \InvalidArgumentException('Invalid reference type, reference_one or reference_many expected');
        }
        
        $this->type = $type;
    }
    
    /**
     * Retrieve referenced document
     * 
     * @return \ValuModeler\Model\Document
     */
    public function getDocument()
    {
        return $this->document;
    }
    
    /**
     * Set referenced document
     * 
     * @param Document $document
     */
    public function setDocument(Document $document)
    {
        $this->document = $document;
    }
}