<?php
namespace FoafModeler\FieldType;

class String extends AbstractFieldType
{
    private $multiline = true;
    
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
            $filters[] = 'stripnewlines';
        }
        
        return $filters;
    }
}