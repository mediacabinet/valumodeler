<?php
namespace FoafModeler\Service;

use Foaf\Service\Setup\AbstractSetup;
use Foaf\Service\Setup\Utils;
use Foaf\Service\Broker;

class Setup extends AbstractSetup
{
    
    protected $optionsClass = 'FoafModeler\Service\Setup\SetupOptions';
    
    public static function version()
    {
        return '0.1';
    }
    
    public function getName()
    {
        return $this->utils()->whichModule(__FILE__);
    }

    public function setup(array $options = array())
    {
        $this->createDocuments();
    }

    public function upgrade($from, array $options = array())
    {
        /**
         * Upgrade dependencies and execute upgradeFrom()
         * when complete
         */
        $this->upgradeDependencies('upgradeFrom', array('from' => $from));
    }

    public function upgradeFrom($from)
    {
        
    }

    public function uninstall(array $options = array())
    {
        $this->removeDocuments();
    }
    
    /**
     * Create documents
     */
    protected function createDocuments()
    {
        $documents = $this->getOption('documents');
        
        $this->getServiceBroker()->service('Modeler.Document')->createMany(
            $documents,
            array('skip_existing' => true)        
        );
    }
    
    /**
     * Remove documents
     */
    protected function removeDocuments()
    {
        $documents = $this->getOption('documents');
        
        $names = array();
        foreach($documents as $specs){
            $names[] = $specs['name'];
        }
        
        $this->getServiceBroker()->service('Modeler.Document')->removeMany(
            $names     
        );
    }
    
}