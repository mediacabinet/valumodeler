<?php
namespace FoafModeler\FieldType;

class Email extends String
{
    protected $multiline = false;
    
    protected $validators = array(
       array('name' => 'emailaddress') 
    );
}