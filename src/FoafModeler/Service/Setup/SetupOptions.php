<?php
namespace FoafModeler\Service\Setup;

class SetupOptions extends \Zend\Stdlib\AbstractOptions{
    
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