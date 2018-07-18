<?php

class PageTools {
    
     public static $NS_SEPARATOR = ':';
    
     public static function getPageDirFromNamespace($dataDir, $namespace) {
        $namespace = trim($namespace, self::$NS_SEPARATOR);
        return rtrim($dataDir, '/').((strlen($namespace)==0)?"":"/").str_replace(self::$NS_SEPARATOR, "/", $namespace);
    }
    
    public static function getPageIdentifier($pagePath) {
        global $conf;
        $res = substr($pagePath, strlen($conf["datadir"])+1);
        if ($pagePath->isFile())
            $res = substr($res, 0, strlen($res)-4);
        $res = str_replace(array("/","\\"), ":", $res);
        return $res;
    }
    
    
    public static function excludePageNamespaces($pageId, &$namespace, &$page) {
        if ($pos = strripos($pageId, ":")){
            $namespace = substr($pageId, 0, $pos);
            $page = substr($pageId, $pos+1);
        }
        else {
            $namespace = "";
            $page = $pageId;
        }
    }
    
    public static function endsWith($haystack, $needle)
    {
        return substr($haystack, -strlen($needle)) == $needle;
    }
    
    public static function excludePageExtension($file) {
        if (self::endsWith($file, ".txt")) {
            return substr($file, 0, strlen($file)-4);
        } else {
            return $file;
        }
    }
    
    public static function constructPageName ($namespace, $page) {
        return ($namespace == "") ? $page : $namespace.self::$NS_SEPARATOR.$page;
    }
    
    public static function isPathPart($path, $part) {
        if(is_null($path)||is_null($path)) {
            return false;
        }
        
        if (strlen($path)!=strlen($part)) {
            $part .= self::$NS_SEPARATOR;
        }
        if ((strpos($path, $part) === 0)) {
            return true;
        } else {
            return false;
        }
    }
}