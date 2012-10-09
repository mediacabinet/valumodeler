<?php
namespace ValuModeler\Service;

use Valu\Service\Setup\AbstractSetup;
use Valu\Service\Setup\Utils;
use Valu\Service\Broker;

class Setup extends AbstractSetup
{
    
    protected $optionsClass = 'ValuModeler\Service\Setup\SetupOptions';
    
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
        $this->updateModelerDocuments();
        return true;
    }

    public function upgrade($from, array $options = array())
    {
        /**
         * Upgrade dependencies and execute upgradeFrom()
         * when complete
         */
        $this->upgradeDependencies('upgradeFrom', array('from' => $from));
        return true;
    }

    public function upgradeFrom($from)
    {
        
    }

    public function uninstall(array $options = array())
    {
        $this->removeDocuments();
        return true;
    }
    
    /**
     * Update modeler documents
     */
    protected function updateModelerDocuments()
    {
        $documents = $this->getOption('documents');
        
        $this->getServiceBroker()->service('Modeler.Document')->createMany(
            $documents,
            array('skip_existing' => true)        
        );
    }
}