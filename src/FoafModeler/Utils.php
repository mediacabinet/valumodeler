<?php
namespace FoafModeler;

class Utils
{
    const CLASS_NS = 'FoafX';
    
    public static function docNameToClass($docName)
    {
        return self::CLASS_NS . '\\' . $docName;
    }
    
    public static function classToDocName($className)
    {
        if(strpos($className, self::CLASS_NS . '\\') === 0){
            $documentName = substr($className, strlen(self::CLASS_NS)+1);
            
            return $documentName;
            //return str_replace('\\', '/', $documentName);
        }
        else{
            return false;
        }
    }
    
}