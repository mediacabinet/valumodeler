<?php
namespace ValuModeler\FieldType;

class TextField extends StringField
{
    protected $multiline = true;

    protected $type = "text";

    public function getOptions()
    {
        return array(
            'multiline' => $this->getMultiline()
        );
    }

    public function setOptions(array $options)
    {
        if (isset($options['multiline'])) {
            $this->setMultiline($options['multiline']);
        }
    }

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
            $filters[] = ['name' => 'stripnewlines'];
        }

        return $filters;
    }
}
