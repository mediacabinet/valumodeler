<?php
namespace ValuModeler\Validator;

use Zend\Validator\AbstractValidator;

class FieldNameValidator extends AbstractValidator
{
    const INVALID = 'invalid';
    
    private $reComponent = '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*';
    
    protected $messageTemplates = array(
        self::INVALID => 'Invalid field name. Valid field name may contain only letters from A to Z, numbers and underscores.',
    );
    
    public function isValid($value)
    {
        $this->setValue($value);
        
        if(!preg_match('/^'.$this->reComponent.'$/', $value)){
            $this->error(self::INVALID);
            return false;
        }
        
        return true;
    }
}