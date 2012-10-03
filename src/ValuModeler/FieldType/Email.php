<?php
namespace ValuModeler\FieldType;

class Email extends String
{
    protected $validators = array(
       array('name' => 'emailaddress') 
    );
}