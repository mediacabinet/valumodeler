<?php
namespace ValuModeler;

class Utils
{
    const CLASS_NS = 'ValuX';
    
    const CACHE_PREFIX = 'ValuModeler_';
    
    /**
     * Convert document name to class name
     * 
     * @param string $docName
     * @return string
     */
    public static function docNameToClass($docName)
    {
        return self::CLASS_NS . '\\' . $docName;
    }
    
    /**
     * Convert class name to document name
     * 
     * @param string $className
     * @return string|boolean
     */
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
    }   return str_replace('\\', '_', $docName);
}