<?php
namespace ValuModeler\FieldType;

class EmailField extends StringField
{
    protected $validators = array(
       array('name' => 'emailaddress')
    );
}
