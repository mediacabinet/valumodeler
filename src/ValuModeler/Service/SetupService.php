<?php
namespace ValuModeler\Service;

use ValuSetup\Service\AbstractSetupService;
use ValuSo\Annotation as ValuService;
use ValuModeler\Utils;

class SetupService extends AbstractSetupService
{
    
    protected $optionsClass = 'ValuModeler\Service\Setup\SetupOptions';
    
    /**
     * @see \ValuSetup\Service\AbstractSetupService::setup()
     */
    public function setup(array $options = array())
    {
        $this->reloadMeta();
        $this->ensureIndexes();
        $this->updateModelerDocuments();
        return true;
    }
    
    /**
     * @see \ValuSetup\Service\AbstractSetupService::uninstall()
     */
    public function uninstall(array $options = array())
    {
        $this->removeDocuments();
        return true;
    }
    
    /**
     * Reload proxy and hydrator classes
     * 
     * @return true
     * 
     * @ValuService\Context({"cli", "http", "http-post"})
     */
    public function reloadMeta()
    {
        $dm         = $this->getServiceLocator()->get('doctrine.documentmanager.valu_modeler');
        $injector   = $this->getServiceLocator()->get('valu_modeler.metadata_injector');
        $documents  = $this->getServiceBroker()->service('Modeler.Document')->findAll();
        
        $names = [];
        foreach ($documents as $document) {
            $names[] = $document->getName();
        }

        // Inject ValuX documents to document manager
        if (sizeof($names)) {
            $injector->injectDocuments(
                $dm,
                $names
            );
        }
        
        // Retrieve all metadata (still doesn't seem to include the injected)
        // and reload all proxy and hydrator classes
        $metadatas = $dm->getMetadataFactory()->getAllMetadata();
        
        $dm->getProxyFactory()->generateProxyClasses(
            $metadatas, $dm->getConfiguration()->getProxyDir());
        
        $dm->getHydratorFactory()->generateHydratorClasses(
            $metadatas, $dm->getConfiguration()->getHydratorDir());
        
        if (sizeof($documents)) {
            // Generate PHP classes for ValuX documents
            foreach ($documents as $document) {
                $injector->getFactory()->reloadClassMetadata(
                        $document
                );
            }
            
            // Fetch metadata for all ValuX classes and
            // generate missing hydrators and proxies
            $metadatas = [];
            foreach ($documents as $document) {
                $metadatas[] = $dm->getMetadataFactory()->getMetaDataFor(
                        Utils::docNameToClass($document->getName()));
            }
            
            $dm->getProxyFactory()->generateProxyClasses(
                    $metadatas, $dm->getConfiguration()->getProxyDir());
            
            $dm->getHydratorFactory()->generateHydratorClasses(
                    $metadatas, $dm->getConfiguration()->getHydratorDir());
        }
        
        return true;
    }
    
    /**
     * Ensure that database indexes exist
     *
     * @return boolean
     */
    public function ensureIndexes()
    {
        $sm = $this->getServiceLocator()->get('doctrine.documentmanager.valu_modeler')->getSchemaManager();
        $sm->ensureIndexes();
    
        return true;
    }
    
    /**
     * Update modeler documents
     */
    protected function updateModelerDocuments()
    {
        $broker = $this->getServiceBroker();
        $documents = $this->getOption('documents');

        $this->getServiceBroker()->service('Modeler.Importer')->import($documents);
    }
    
    /**
     * Remove documents
     */
    protected function removeDocuments()
    {
        $documents = $this->getOption('documents');
        
        foreach ($documents as $specs) {
            $this->getServiceBroker()->service('Modeler.Document')->remove($specs['name']);
        }
    }
}