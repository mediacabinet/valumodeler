<?php
namespace FoafModeler\FieldType;

interface FieldTypeInterface
{
    public function getPrimitiveType();
    
    public function setOptions(array $options);
    
    public function getFilters();
    
    public function getValidators();
}