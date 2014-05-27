<?php
namespace ValuModeler\Validator;

/**
 * Validator for filter chain specifications
 */
class FilterChain extends InputFilter
{
    public function isValid($value)
    {
        return parent::isValid(array('filters' => $value));
    }
}