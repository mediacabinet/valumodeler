<?php
namespace ValuModeler\Odm;

use Doctrine\ODM\MongoDB\DocumentManager;
use ValuModeler\Utils;

class MetadataInjector
{
    const DOCUMENT_CLASS = 'ValuModeler\Model\Document';
    
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
     * @param boolean $ignoreMissing
     * @throws Exception\DocumentNotFoundException
     */
    public function injectDocuments(DocumentManager $dm, array $documents, $ignoreMissing = false)
    {

        $factory = $this->getFactory();
        
        foreach($documents as $name){
            $document = $this->getDocument($name);
            
            if (!$document) {
                if ($ignoreMissing) {
                    continue;
                } else {
                    throw new Exception\DocumentNotFoundException(
                        sprintf('Document %s not found', $name)
                    );
                }
            }
            
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