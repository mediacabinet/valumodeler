<?php
namespace ValuModeler\FieldType;

class Text extends String
{
    protected $multiline = true;
    
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
    
        if(!$this->getMultiline()){
            $filters[] = array('name' => 'stripnewlines');
        }
    
        return $filters;
    }
}