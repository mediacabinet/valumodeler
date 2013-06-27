<?php
namespace ValuModeler\Service;

use ValuSetup\Service\AbstractSetupService;

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
     */
    public function reloadMeta()
    {
        $dm = $this->getServiceLocator()->get('doctrine.documentmanager.valu_modeler');
        $metadatas = $dm->getMetadataFactory()->getAllMetadata();
        $dm->getProxyFactory()->generateProxyClasses($metadatas, $dm->getConfiguration()->getProxyDir());
        $dm->getHydratorFactory()->generateHydratorClasses($metadatas, $dm->getConfiguration()->getHydratorDir());
        
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