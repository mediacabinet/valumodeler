<?php
namespace ValuModeler\Doctrine\MongoDb;

use ValuModeler\Utils;
use ValuModeler\Model;
use Doctrine\ODM\MongoDB\Tools\DocumentGenerator;
use Doctrine\ODM\MongoDB\Id\AutoGenerator;
use Zend\Cache\Storage\StorageInterface;

class ClassMetadataFactory
{
    const CACHE_PREFIX = 'valu_modeler_class_metadata_';
    
    /**
     * PHP class directory
     * 
     * @var string
     */
    protected $directory;
    
    /**
     * Metadata driver
     * 
     * @var \ValuModeler\Doctrine\MongoDb\Driver
     */
    protected $driver;

    /**
     * Cache storage adapter
     *
     * @var \Zend\Cache\Storage\StorageInterface
     */
    protected $cache;
    
    public function getClassMetadata(Model\Document $document)
    {
        return $this->loadClassMetadata($document);
    }
    
    public function reloadClassMetadata(Model\Document $document)
    {
        $this->loadClassMetadata($document, true);
    }
    
    /**
     * Get metadata driver
     * 
     * @return \ValuModeler\Doctrine\MongoDb\Driver
     */
    public function getDriver()
    {
        if(!$this->driver){
            $this->driver = new Driver();
        }
    
        return $this->driver;
    }
    
    /**
     * Set metadata driver
     * 
     * @param Driver $driver
     */
    public function setDriver(Driver $driver)
    {
        $this->driver = $driver;
    }
    
    /**
     * Retrieve cache storage adapter
     *
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function getCache()
    {
        return $this->cache;
    }
    
    /**
     * Set cache storage adapter
     *
     * @param StorageInterface $cache
     */
    public function setCache(StorageInterface $cache)
    {
        $this->cache = $cache;
    }
    
    /**
     * Get target directory for PHP classes
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }
    
    /**
     * Set target directory for PHP classes
     * 
     * @param string $directory
     * @throws \InvalidArgumentException
     */
    public function setDirectory($directory)
    {
        // Try creating directory
        if(!is_dir($directory)){
            mkdir($directory, 0744, true);
        }
    
        if(is_dir($directory) && is_writable($directory)){
            $this->directory = $directory;
        }
        else{
            throw new \InvalidArgumentException('Target directory '.$directory.' is not found or not writable');
        }
    }
    
    /**
     * Load class metadata for document
     * 
     * @param Model\Document $document
     * @param boolean $refresh
     * @throws \Exception
     * @return \ValuModeler\Doctrine\MongoDb\ClassMetadata
     */
    protected function loadClassMetadata(Model\Document $document, $refresh = false)
    {
        $driver = $this->getDriver();
    
        $name         = $document->getName();
        $class        = Utils::docNameToClass($name);
        $classExists  = class_exists($class);
        $cacheId      = self::getCacheId($name);
        $cached       = false;
        $metadata     = null;
    
        if( !$refresh &&
            $classExists &&
            $this->getCache() &&
            $this->getCache()->hasItem($cacheId)){
    
            $metadata = $this->getCache()->getItem($cacheId);
            $cached = true;
        }
        
        if(!$metadata){
            $metadata = new ClassMetadata($class);
            $metadata->setIdGenerator(new AutoGenerator());
    
            // Define identifier field
            $metadata->mapField(array(
                'name' => $document->getIdFieldName(),
                'id' => true,
                'strategy' => 'NONE'
            ));
    
            $this->loadMetadata($document, $metadata);
    
            $cached = false;
        }
    
        if(!$classExists || $refresh){
            $this->writePhpClass($class, $metadata);
        }
    
        if(!class_exists($class)){
            throw new \Exception('Unable to write PHP class for '.$class);
        }
    
        // Set reflection class and namespace
        if(!$cached){
            $metadata->loadReflClass();
    
            // Store to cache
            if($this->getCache()){
                $this->getCache()->setItem($cacheId, $metadata);
            }
        }
    
        return $metadata;
    }
    
    /**
     * Write PHP class
     * 
     * @param string $class
     * @param \ValuModeler\Doctrine\MongoDb\ClassMetadata $metadata
     */
    protected function writePhpClass($class, $metadata)
    {
        $generator = new DocumentGenerator();
        $generator->setRegenerateDocumentIfExists(true);
        $generator->setGenerateStubMethods(true);
        $generator->generate(array($metadata), $this->getDirectory());
    }
    
    /**
     * Recursively load class metadata for document
     * 
     * @param Model\Document $document
     * @param ClassMetadata $metadata
     */
    protected function loadMetadata(Model\Document $document, ClassMetadata $metadata)
    {
        $class = Utils::docNameToClass($document->getName());
        $this->getDriver()->addDocument($document);
    
        if($document->getParent()){
            $this->loadMetadata($document->getParent(), $metadata);
        }
    
        $this->getDriver()->loadMetadataForClass($class, $metadata);
    }
    
    /**
     * Retrieve cache ID for document name
     *
     * @param string $documentName
     * @return string
     */
    protected static function getCacheId($documentName)
    {
        return self::CACHE_PREFIX . str_replace('\\', '_', $documentName).'_classmeta';
    }
}