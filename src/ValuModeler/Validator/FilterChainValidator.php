<?php
namespace ValuModeler\Validator;

/**
 * Validator for filter chain specifications
 */
class FilterChainValidator extends AbstractInputFilterValidator
{
    protected $messageTemplates = array(
        self::INVALID => 'Invalid filter chain configuration',
    );

    public function isValid($value)
    {
        return parent::isValid(array('filters' => $value));
    }
}