<?php
namespace FoafModeler\Service;

use FoafModeler\Utils;
use FoafModeler\Model;
use FoafModeler\Service\Exception;
use Foaf\Model\ArrayAdapter;
use Foaf\Service\AbstractService;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Zend\InputFilter\Factory;

class Document
    extends AbstractService
{
    
    /**
     * Document manager
     *
     * @var DocumentManager
     */
    private $dm;
    
    /**
     * Array of input filters by model name
     * 
     * @var array 
     */
    private $inputFilters;
    
    public function __construct(DocumentManager $dm)
    {
        $this->setDocumentManager($dm);
    }
    
    public static function version()
    {
        return '0.1';    
    }
    
    /**
     * Create a new document model
     * 
     * @param string $name Unique name of the document
     * @param array $fields
     * @param array $embeds
     * @param array $refs
     * @param array $indexes
     * @param array $options
     * @throws Exception\DocumentAlreadyExistsException
     */
    public function create($name, $fields = array(), $embeds = array(), $refs = array(), $indexes = array(), array $options = array())
    {
        // Test that URI is not reserved
        if($this->getDocumentRepository()->findOneByName($name)){
            throw new Exception\DocumentAlreadyExistsException(
                'Document %NAME% already exists',
                array('NAME' => $name)
            );
        }
        
        $specs = array(
            'name'       => $name,
            'collection' => isset($options['collection']) ? $options['collection'] : null
        );
        
        // Filter and validate
        $specs = $this->getModelInputFilter()->filter(
            $specs, false, true);
        
        $document = new Model\Document($specs['name']);
        
        if(isset($specs['collection'])){
            $document->setCollection($specs['collection']);
        }
        
        if(isset($options['parent'])){
            $parent = $this->resolveDocument($options['parent'], true);
            
            $document->setParent($parent);
        }
        
        // add fields, embeds and references
        $this->populateDocument($document, $fields, $embeds, $refs);
        
        $this->getDocumentManager()->persist($document);
        $this->getDocumentManager()->flush($document);
        
        return $document->getUuid();
    }
    
    /**
     * Batch create documents
     * 
     * @param array $documents
     * @return array Document UUIDs
     */
    public function createMany(array $documents, array $options = array())
    {
        $options = array_merge(
            array('skip_existing' => false),
            $options        
        );
        
        $ids = array();
        
        foreach($documents as $key => $specs){
            
            $name = isset($specs['name'])
                ? $specs['name']
                : $key;
            
            $fields = isset($specs['fields'])
                ? (array) $specs['fields']
                : array();
            
            $embeds = isset($specs['embeds'])
                ? (array) $specs['embeds']
                : array();
            
            $refs = isset($specs['refs'])
                ? (array) $specs['refs']
                : array();
            
            $indexes = isset($specs['indexes'])
                ? (array) $specs['indexes']
                : array();
            
            if(!isset($specs['refs']) && isset($specs['references'])){
                $refs = $specs['references'];
            }
            
            unset($specs['name']);
            unset($specs['fields']);
            unset($specs['embeds']);
            unset($specs['refs']);
            unset($specs['references']);
            unset($specs['indexes']);
            
            try{
                $ids[] = $this->create($name, $fields, $embeds, $refs, $indexes, $specs);
            }
            catch(Exception\DocumentAlreadyExistsException $e){
                if($options['skip_existing']){
                    continue; // Skip existing
                }
                else{
                    throw $e;
                }
            }
        }
        
        return $ids;
    }
    
    /**
     * Remove one document
     * 
     * @param string $documentName
     * @return string UUID of removed document
     */
    public function remove($documentName)
    {
        return $this->doRemove($documentName, true);
    }
    
    /**
     * Batch remove documents
     * 
     * @param array $documents
     */
    public function removeMany(array $documents)
    {
        $ids = array();
        
        foreach($documents as $name){
            $uuid = $this->doRemove($name, false);
            
            if($uuid){
                $ids[] = $uuid;
            }
        }
        
        $this->getDocumentManager()->flush();
        return $ids;
    }
    
    /**
     * Retrieve input filter specifications for document
     * 
     * @param string $documentName
     * @throws Exception\DocumentNotFoundException
     * @return array Specifications, compatible with Zend\InputFilter\Factory 
     */
    public function getInputFilterSpecs($document)
    {
        $document = $this->resolveDocument($document);
        
        return $document->getInputFilterSpecifications();
    }
    
    /**
     * Retrieve input filter instance
     *
     * @param string $name
     * @return Ambigous <\Zend\InputFilter\InputFilterInterface, unknown>
     */
    public function getInputFilter($name)
    {
        $document = $this->resolveDocument($name);
        
        $specs = $this->getServiceBroker()
            ->service('Modeler.Document')
            ->getInputFilterSpecs($document->getName());
    
        $factory = new Factory();
        return $factory->createInputFilter($specs);
    }
    
    /**
     * Set document manager instance
     *
     * @param DocumentManager $dm
     * @return User
     *
     * @foaf\service\ignore
     */
    public function setDocumentManager(DocumentManager $dm)
    {
        $this->dm = $dm;
        return $this;
    }
    
    /**
     * Retrieve document manager instance
     *
     * @return DocumentManager
     *
     * @foaf\service\ignore
     */
    public function getDocumentManager()
    {
        return $this->dm;
    }
    
    /**
     * Retrieve document repository instance
     * 
     * @return \Doctrine\ODM\MongoDb\DocumentRepository
     */
    protected function getDocumentRepository()
    {
        return $this->getDocumentManager()->getRepository('FoafModeler\Model\Document');
    }
    
    /**
     * Resolve document by its name
     * 
     * @param string|Model\Document $document
     * @param boolean $require
     * @throws Exception\DocumentNotFoundException
     * @return \FoafModeler\Model\Document
     */
    protected function resolveDocument($document, $require = false)
    {
        if($document instanceof Model\Document){
            return $document;
        }
        else{
            $document = $this->getDocumentRepository()->findOneByName($document);
            
            if(!$document && $require){
                throw new Exception\DocumentNotFoundException(
                    'Document %NAME% not found',
                    array('NAME' => $document)
                );
            }
            
            return $document;
        }
    }
    
    /**
     * Populate document with fields, embeds and references
     * 
     * @param Model\Document $document
     * @param array $fields
     * @param array $embeds
     * @param array $refs
     * @throws Exception\DocumentNotFoundException
     */
    protected function populateDocument(Model\Document $document, array $fields, array $embeds, array $refs)
    {
        // Insert fields
        if(sizeof($fields)){
        
            foreach($fields as $key => $specs){
                
                if (!isset($specs['name'])) {
                    $specs['name'] = $key;
                }
                
                // Filter and validate
                $specs = $this->getModelInputFilter('field')->filter(
                    $specs, false, true);
        
                $field = new Model\Field($specs['name'], $specs['type'], $specs);
                $document->addField($field);
            }
        }
        
        // Insert embeds
        if(sizeof($embeds)){
            foreach($embeds as $key => $specs){
                
                if (!isset($specs['name'])) {
                    $specs['name'] = $key;
                }
                
                // Filter and validate
                $specs = $this->getModelInputFilter('embed')->filter(
                        $specs, false, true);
        
                // Find reference document by its name
                $reference = $this->getDocumentRepository()->findOneByName($specs['document']);
        
                if(!$reference){
                    throw new Exception\DocumentNotFoundException(
                        'Unable to locate document with name %NAME%',
                        array('NAME' => $specs['document'])
                    );
                }
                
                $embed = new Model\Embed($specs['name'], $specs['type'], $reference);
                $document->addEmbed($embed);
            }
        }
        
        // Insert references
        if(sizeof($refs)){
            foreach($refs as $key => $specs){

                if (!isset($specs['name'])) {
                    $specs['name'] = $key;
                }
                
                // Filter and validate
                $specs = $this->getModelInputFilter('reference')->filter(
                    $specs, false, true);
                
                // Find reference document by its name
                $reference = $this->getDocumentRepository()->findOneByName($specs['document']);
        
                if(!$reference){
                    throw new Exception\DocumentNotFoundException(
                        'Unable to locate document with name %NAME%',
                        array('NAME' => $specs['document'])
                    );
                }
                
                $ref = new Model\Reference($specs['name'], $specs['type'], $reference);
                $document->addReference($ref);
            }
        }
    }
    
    /**
     * Remove document by name
     * 
     * @param string $documentName
     * @param boolean $flush
     * @return boolean
     */
    protected function doRemove($documentName, $flush = true)
    {
        $document = $this->getDocumentRepository()->findOneByName($documentName);
    
        if($document){
            $this->getDocumentManager()->remove($document);
            
            if($flush){
                $this->getDocumentManager()->flush($document);
            }
            
            return $document->getUuid();
        }
        else{
            return false;
        }
    }
    
    /**
     * Retrieve input filter instance
     *
     * @return \Foaf\InputFilter\InputFilter
     */
    protected function getModelInputFilter($type)
    {
        if(!$this->inputFilters[$type]){
            switch($type){
                case 'document':
                    $inputFilter = clone Model\Document::getDefaultInputFilter();
                    break;
                case 'embed':
                    $inputFilter = clone Model\Embed::getDefaultInputFilter();
                    break;
                case 'reference':
                    $inputFilter = clone Model\Reference::getDefaultInputFilter();
                    break;
                case 'field':
                    $inputFilter = clone Model\Field::getDefaultInputFilter();
                    break;
                default:
                    $inputFilter = null;
                    break;
            }
            
            $this->inputFilters[$type] = $inputFilter;
        }
    
        return $this->inputFilters[$type];
    }
}