<?php
namespace ValuModeler\Validator;

use Zend\Validator\AbstractValidator;

class DocumentNameValidator extends AbstractValidator
{
    const INVALID = 'invalid';
    
    private $reComponent = '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*';
    
    protected $messageTemplates = array(
        self::INVALID => 'Invalid document name. Valid document name may contain only letters from A to Z, numbers, underscores and backslashes.',
    );
    
    public function isValid($value)
    {
        $this->setValue($value);
        
        if(!preg_match('/^' . $this->reComponent . '(\\\\' . $this->reComponent . ')*$/', $value)){
            $this->error(self::INVALID);
            return false;
        }
        
        return true;
    }
}