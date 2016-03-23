<?php
namespace ValuModeler\Service\Setup;

use Zend\Stdlib\AbstractOptions;

class SetupOptions extends AbstractOptions{
    
    /**
     * Documents
     *
     * @var array
     */
    protected $documents = array();
    
	public function getDocuments()
    {
        return $this->documents;
    }

	public function setDocuments($documents)
    {
        $this->documents = $documents;
    }
}