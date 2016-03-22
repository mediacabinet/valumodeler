<?php
namespace ValuModeler\FieldType;

class EmailField extends StringField
{
    protected $type = "email";
    
    protected $validators = array(
       array('name' => 'emailaddress')
    );
}
