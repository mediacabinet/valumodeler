<?php
namespace FoafModeler\Doctrine\MongoDb;

use Doctrine\ODM\MongoDB\Tools\DocumentGenerator;
use Doctrine\ODM\MongoDB\Id\AutoGenerator;
use Doctrine\ODM\MongoDB\DocumentManager;
use FoafModeler\Utils;

class MetadataInjector
{
    const DOCUMENT_CLASS = 'FoafModeler\Model\Document';
    
    protected $documents;
    
    protected $directory;
    
    protected $driver;
    
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }
    
    public function getDirectory()
    {
        return $this->directory;
    }
    
    public function setDirectory($directory)
    {
        // Try creating directory
        if(!is_dir($directory)){
            mkdir($directory, 0644, true);
        }
        
        if(is_dir($directory) && is_writable($directory)){
            $this->directory = $directory;
        }
        else{
            throw new \InvalidArgumentException('Target directory '.$directory.' is not found or not writable');
        }
    }
    
    public function getDriver()
    {
        if(!$this->driver){
            $this->driver = new Driver();
        }
        
        return $this->driver;
    }
    
    public function setDriver(Driver $driver)
    {
        $this->driver = $driver;
    }
    
    public function injectDocuments(DocumentManager $dm, array $documents)
    {

        $driver = $this->getDriver();
        
        foreach($documents as $name){
            $document = $this->getDocument($name);
            $class    = Utils::docNameToClass($name);
            
            $driver->setDocument($name, $document);
            
            $metadata = new ClassMetadata($class);
            $metadata->setIdGenerator(new AutoGenerator());
            
            $driver->loadMetadataForClass($class, $metadata);
            
            if(!class_exists($class)){
                $this->writePhpClass($class, $metadata);
            }
            
            if(!class_exists($class)){
                throw new \Exception('Unable to write PHP class for '.$class);
            }
            
            // Set reflection class and namespace
            $metadata->loadReflClass();
            
            $dm->getMetadataFactory()->setMetadataFor($class, $metadata);
        }
    }
    
    protected function writePhpClass($class, $metadata)
    {
        $generator = new DocumentGenerator();
        $generator->setGenerateStubMethods(true);
        $generator->generate(array($metadata), $this->getDirectory());
    }
    
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