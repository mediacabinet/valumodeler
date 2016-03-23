<?php
namespace ValuModelerTest\Model;

use ValuModeler\FieldType\EmailField;
use ValuModeler\FieldType\StringField;
use ValuModeler\FieldType\TextField;
use ValuModeler\Model\AbstractAssociation;
use ValuModeler\Model\Document;
use ValuModeler\Model\Field;
use ValuModeler\Model\Embed;
use ValuModeler\Model\Reference;

class DocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Document
     */
    private $document;

    public function setUp()
    {
        $this->document = new Document('Test');
    }

    public function testGetId()
    {
        $this->assertNull($this->document->getId());
    }

    public function testGetNameUsedWithConstructor()
    {
        $this->assertEquals('Test', $this->document->getName());
    }

    public function testSetGetCollection()
    {
        $this->document->setCollection('test_document');
        $this->assertEquals('test_document', $this->document->getCollection());
    }

    public function testGetDefaultIdFieldName()
    {
        $this->assertEquals('id', $this->document->getIdFieldName());
    }

    public function testSetGetIdFieldName()
    {
        $this->document->setIdFieldName('Id');
        $this->assertEquals('Id', $this->document->getIdFieldName());
    }

    public function testSetGetParent()
    {
        $parent = new Document('Parent');
        $this->document->setParent($parent);
        $this->assertEquals($parent, $this->document->getParent());
    }

    public function testAddField()
    {
        $field = $this->addTestField();
        $this->assertEquals($field, $this->document->getField('name'));
        $this->assertEquals($field, $this->document->getItem('name'));
        $this->assertEquals(['name' => $field], $this->document->getFields());
    }

    /**
     * @expectedException \ValuModeler\Model\Exception\ItemAlreadyExistsException
     */
    public function testAddFieldWhenAnotherFieldWithSameNameAlreadyExists()
    {
        $field1 = new Field('name', new StringField());
        $field2 = new Field('name', new StringField());
        $this->document->addField($field1);
        $this->document->addField($field2);
    }

    public function testRemoveField()
    {
        $field = $this->addTestField();
        $this->document->removeField('name');
        $this->assertNull($this->document->getField('name'));
        $this->assertEquals([], $this->document->getFields());
    }

    public function testGetFieldsWhenNotDefined()
    {
        $this->assertEquals(
            [],
            $this->document->getFields());
    }

    public function testAddEmbed()
    {
        $embed = $this->addTestEmbed();
        $this->assertEquals($embed, $this->document->getEmbed('child'));
        $this->assertEquals($embed, $this->document->getEmbed('child'));
        $this->assertEquals(['child' => $embed], $this->document->getEmbeds());
    }

    /**
     * @expectedException \ValuModeler\Model\Exception\ItemAlreadyExistsException
     */
    public function testAddEmbedWhenAnotherEmbedWithSameNameAlreadyExists()
    {
        $ref = new Document('Child');
        $embed1 = new Embed('child', Embed::REFERENCE_ONE, $ref);
        $embed2 = new Embed('child', Embed::REFERENCE_ONE, $ref);

        $this->document->addEmbed($embed1);
        $this->document->addEmbed($embed2);
    }

    public function testRemoveEmbed()
    {
        $this->addTestEmbed();
        $this->document->removeEmbed('child');
        $this->assertNull($this->document->getEmbed('child'));
        $this->assertEquals([], $this->document->getEmbeds());
    }

    public function testGetEmbedsWhenNotDefined()
    {
        $this->assertEquals(
            [],
            $this->document->getEmbeds());
    }

    public function testAddReference()
    {
        $reference = $this->addTestReference();
        $this->assertEquals($reference, $this->document->getReference('child'));
        $this->assertEquals($reference, $this->document->getItem('child'));
        $this->assertEquals(['child' => $reference], $this->document->getReferences());
    }

    /**
     * @expectedException \ValuModeler\Model\Exception\ItemAlreadyExistsException
     */
    public function testAddReferenceWhenAnotherReferenceWithSameNameAlreadyExists()
    {
        $ref = new Document('Child');
        $reference1 = new Reference('child', Reference::REFERENCE_ONE, $ref);
        $reference2 = new Reference('child', Reference::REFERENCE_ONE, $ref);

        $this->document->addReference($reference1);
        $this->document->addReference($reference2);
    }

    public function testRemoveReference()
    {
        $this->addTestReference();
        $this->document->removeReference('child');
        $this->assertNull($this->document->getReference('child'));
        $this->assertEquals([], $this->document->getReferences());
    }

    public function testGetReferencesWhenNotDefined()
    {
        $this->assertEquals(
            [],
            $this->document->getReferences());
    }

    public function testHasItemWhenItemDoesNotExist()
    {
        $this->assertFalse($this->document->hasItem('name'));
    }

    public function testHasItemWhenFieldExists()
    {
        $this->addTestField();
        $this->assertTrue($this->document->hasItem('name'));
    }

    public function testHasItemWhenEmbedExists()
    {
        $this->addTestEmbed();
        $this->assertTrue($this->document->hasItem('child'));
    }

    public function testHasItemWhenReferenceExists()
    {
        $this->addTestReference();
        $this->assertTrue($this->document->hasItem('child'));
    }

    public function testGetItemReturnsField()
    {
        $field = $this->addTestField();
        $this->assertEquals($field, $this->document->getItem('name'));
    }

    public function testGetItemReturnsEmbed()
    {
        $embed = $this->addTestEmbed();
        $this->assertEquals($embed, $this->document->getItem('child'));
    }

    public function testGetItemReturnsReference()
    {
        $ref = $this->addTestReference();
        $this->assertEquals($ref, $this->document->getItem('child'));
    }

    public function testGetItemReturnsParentField()
    {
        $parent = new Document('Parent');
        $field = new Field('name', new StringField());
        $parent->addField($field);

        $this->document->setParent($parent);

        $this->assertEquals($field, $this->document->getItem('name'));
    }

    public function testGetItemReturnsParentEmbed()
    {
        $parent = new Document('Parent');
        $ref = new Document('Ref');
        $embed = new Embed('child', Embed::REFERENCE_ONE, $ref);
        $parent->addEmbed($embed);
        $this->document->setParent($parent);

        $this->assertEquals($embed, $this->document->getItem('child'));
    }

    public function testGetItemReturnsParentReference()
    {
        $parent = new Document('Parent');
        $ref = new Document('Ref');
        $ref = new Reference('child', Embed::REFERENCE_ONE, $ref);
        $parent->addReference($ref);
        $this->document->setParent($parent);

        $this->assertEquals($ref, $this->document->getItem('child'));
    }

    public function testGetInputFilterSpecifications()
    {
        $email = new Field('email', new EmailField());
        $parent = new Document('Parent');
        $parent->addField($email);
        $this->document->setParent($parent);

        $textType = new TextField();
        $textType->setMultiline(false);
        $description = new Field('description', $textType);

        $this->document->addField($description);

        $this->assertEquals([
            'email' => [
                'name' => 'email',
                'required' => false,
                'allow_empty' => true,
                'filters' => [],
                'validators' => [
                    ['name' => 'emailaddress']
                ]
            ],
            'description' => [
                'name' => 'description',
                'required' => false,
                'allow_empty' => true,
                'validators' => [],
                'filters' => [
                    ['name' => 'stripnewlines']
                ]
            ]
        ], $this->document->getInputFilterSpecifications());
    }

    public function testCreateEmbedAssociation()
    {
        $ref = new Document('Ref');
        $embed = $this->document->createAssociation('child', AbstractAssociation::REFERENCE_ONE, $ref, true);
        $this->assertInstanceOf('ValuModeler\Model\Embed', $embed);
        $this->assertEquals($embed, $this->document->getEmbed('child'));
    }

    public function testCreateReferenceAssociation()
    {
        $ref = new Document('Ref');
        $assoc = $this->document->createAssociation('child', AbstractAssociation::REFERENCE_ONE, $ref, false);
        $this->assertInstanceOf('ValuModeler\Model\Reference', $assoc);
        $this->assertEquals($assoc, $this->document->getReference('child'));
    }

    public function testGetEmbedAssociation()
    {
        $embed = $this->addTestEmbed();
        $this->assertEquals($embed, $this->document->getAssociation('child'));
    }

    public function testGetReferenceAssociation()
    {
        $ref = $this->addTestReference();
        $this->assertEquals($ref, $this->document->getAssociation('child'));
    }

    public function testRemoveEmbedAssociation() {
        $embed = $this->addTestEmbed();
        $this->document->removeAssociation('child');
        $this->assertNull($this->document->getEmbed('child'));
    }

    public function testRemoveReferenceAssociation() {
        $ref = $this->addTestReference();
        $this->document->removeAssociation('child');
        $this->assertNull($this->document->getReference('child'));
    }

    private function addTestField($name = 'name')
    {
        $field = new Field($name, new StringField());
        $this->document->addField($field);
        return $field;
    }

    private function addTestEmbed($name = 'child')
    {
        $ref = new Document('Ref');
        $embed = new Embed($name, Embed::REFERENCE_ONE, $ref);
        $this->document->addEmbed($embed);
        return $embed;
    }

    private function addTestReference($name = 'child')
    {
        $ref = new Document('Ref');
        $reference = new Reference($name, Reference::REFERENCE_ONE, $ref);
        $this->document->addReference($reference);
        return $reference;
    }
}
