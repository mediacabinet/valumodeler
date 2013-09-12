<?php
namespace ValuModeler\Validator;

use Zend\InputFilter\Factory;
use Zend\Validator\AbstractValidator;

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