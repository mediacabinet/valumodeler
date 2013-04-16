<?php
namespace ValuModeler\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Reference
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
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function setType($type)
    {
        if(!in_array($type, array(self::REFERENCE_ONE, self::REFERENCE_MANY))){
            throw new \InvalidArgumentException('Invalid reference type, reference_one or reference_many expected');
        }
        
        $this->type = $type;
    }
    
    public function getDocument()
    {
        return $this->document;
    }
    
    public function setDocument(Document $document)
    {
        $this->document = $document;
    }
}