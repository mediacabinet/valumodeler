<?php
namespace ValuModeler\Validator;

use Zend\InputFilter\Factory;
use Zend\Validator\AbstractValidator;

class InputFilter extends AbstractValidator
{
    const INVALID = 'invalid';
    
    protected $_messageTemplates = array(
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