<?php
namespace FoafModeler\FieldType;

class String extends AbstractFieldType
{
    protected $multiline = true;
    
    protected $primitiveType = 'string';
   
    public function getMultiline()
    {
        return $this->multiline;
    }

    public function setMultiline($multiline)
    {
        $this->multiline = $multiline;
    }
    
    public function getFilters()
    {
        $filters = $this->filters;
        
        if(!$this->multiline){
            $filters[] = array('name' => 'stripnewlines');
        }
        
        return $filters;
    }
}