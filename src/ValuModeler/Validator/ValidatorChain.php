<?php
namespace ValuModeler\Validator;

use Zend\InputFilter\Factory;
use Zend\Validator\AbstractValidator;

/**
 * Validator for validator chain specifications
 */
class ValidatorChain extends InputFilter
{
    public function isValid($value)
    {
        return parent::isValid(array('validators' => $value));
    }
}