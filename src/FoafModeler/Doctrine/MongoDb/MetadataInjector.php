<?php
namespace FoafModeler\Doctrine\MongoDb;

use Zend\Cache\Storage\StorageInterface;
use Doctrine\ODM\MongoDB\Tools\DocumentGenerator;
use Doctrine\ODM\MongoDB\Id\AutoGenerator;
use Doctrine\ODM\MongoDB\DocumentManager;
use FoafModeler\Utils;
use FoafModeler\Model;

class MetadataInjector
{
    const DOCUMENT_CLASS = 'FoafModeler\Model\Document';
    
    protected $dm;
    
    protected $documents;
    
    protected $factory;
    
    public function __construct(DocumentManager $dm, ClassMetadataFactory $factory)
    {
        $this->dm = $dm;
        $this->setFactory($factory);
    }
    
    /**
     * Retrieve class metadata factory instance
     * 
     * @return ClassMetadataFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }
    
    /**
     * Set class metadata factory
     * 
     * @param ClassMetadataFactory $factory
     */
    public function setFactory(ClassMetadataFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Inject documents to document manager
     * 
     * @param DocumentManager $dm
     * @param array $documents
     */
    public function injectDocuments(DocumentManager $dm, array $documents)
    {

        $factory = $this->getFactory();
        
        foreach($documents as $name){
            $document = $this->getDocument($name);
            $class    = Utils::docNameToClass($name);
            $metadata = $factory->getClassMetadata($document);
            
            $dm->getMetadataFactory()->setMetadataFor($class, $metadata);
        }
    }
    
    /**
     * Retrieve document instance by name
     * 
     * @param string $name
     */
    protected function getDocument($name){
        if(!isset($this->documents[$name])){
            $qb = $this->dm->getRepository(self::DOCUMENT_CLASS)->createQueryBuilder();
            $document = $qb->field('name')->equals($name)
                ->getQuery()
                ->execute()
                ->getSingleResult();
            
            $this->documents[$name] = $document;
        }
        
        return $this->documents[$name];
    }
}