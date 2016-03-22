<?php
namespace ValuModeler\FieldType;

interface FieldTypeInterface
{
    public function getType();

    public function getPrimitiveType();
    
    public function getOptions();
    
    public function setOptions(array $options);
    
    public function getFilters();
    
    public function getValidators();
}