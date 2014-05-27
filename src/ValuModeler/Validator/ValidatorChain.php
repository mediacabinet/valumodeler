<?php
namespace ValuModeler\Validator;

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