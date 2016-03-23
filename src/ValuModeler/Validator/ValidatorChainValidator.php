<?php
namespace ValuModeler\Validator;

/**
 * Validator for validator chain specifications
 */
class ValidatorChainValidator extends AbstractInputFilterValidator
{
    protected $messageTemplates = array(
        self::INVALID => 'Invalid validator chain configuration',
    );

    public function isValid($value)
    {
        return parent::isValid(array('validators' => $value));
    }
}