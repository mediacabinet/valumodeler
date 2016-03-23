<?php
namespace ValuModeler\Validator;

use Zend\InputFilter\Factory;
use Zend\Validator\AbstractValidator;

abstract class AbstractInputFilterValidator extends AbstractValidator
{
    const INVALID = 'invalid';
    
    protected $messageTemplates = array(
        self::INVALID => 'Invalid input filter',
    );
    
    public function isValid($value)
    {
        $this->setValue($value);
        
        try {
            $factory = new Factory();
            $factory->createInput($value);
        } catch(\Exception $e) {
            $this->error(self::INVALID);
            return false;
        }
        
        return true;
    }
}