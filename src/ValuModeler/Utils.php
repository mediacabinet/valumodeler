<?php
namespace ValuModeler;

class Utils
{
    const CLASS_NS = 'ValuX';
    
    const CACHE_PREFIX = 'ValuModeler_';

    const INPUT_FILTER_NS = "modeler://";
    
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
            return substr($className, strlen(self::CLASS_NS)+1);
        }
        else{
            return false;
        }
    }

    /**
     * Test if input filter URL belongs to ValuModeler's input filter namespace
     *
     * @param $url
     * @return bool
     */
    public static function inputFilterNamespaceMatches($url)
    {
        return strpos($url, self::INPUT_FILTER_NS) === 0;
    }

    /**
     * Convert input filter URL in ValuModeler's input filter namespace to document name
     *
     * @param $url
     * @return boolean|string   Document name or false if URL is not in correct namespace
     */
    public static function inputFilterUrlToDocName($url)
    {
        return self::inputFilterNamespaceMatches($url)
            ? substr($url, strlen(self::INPUT_FILTER_NS)) : false;
    }
}