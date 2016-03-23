<?php
namespace ValuModelerTest\Model;


use ValuModeler\Model\AbstractAssociation;
use ValuModeler\Model\Document;

abstract class AbstractAssociationTest extends \PHPUnit_Framework_TestCase
{
    protected $class;

    /**
     * @var AbstractAssociation
     */
    protected $association;

    /**
     * @var Document
     */
    protected $document;

    public function setUp()
    {
        $this->document = new Document('Ref');
        $this->association = new $this->class(
            'myRef', AbstractAssociation::REFERENCE_ONE, $this->document);
    }


    public function testGetNameFromConstructor()
    {
        $this->assertEquals('myRef', $this->association->getName());
    }

    public function testSetGetName()
    {
        $this->association->setName('newRef');
        $this->assertEquals('newRef', $this->association->getName());
    }

    public function testGetTypeFromConstructor()
    {
        $this->assertEquals(AbstractAssociation::REFERENCE_ONE, $this->association->getType());
    }

    public function testSetGetType()
    {
        $this->association->setType(AbstractAssociation::REFERENCE_MANY);
        $this->assertEquals(AbstractAssociation::REFERENCE_MANY, $this->association->getType());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidType()
    {
        $this->association->setType('foo');
    }

    public function testGetDocumentFromConstructor()
    {
        $this->assertEquals($this->document, $this->association->getDocument());
    }

    public function testSetGetDocument()
    {
        $doc = new Document('MyRef');
        $this->association->setDocument($doc);
        $this->assertEquals($doc, $this->association->getDocument());
    }
}