<?php

namespace Smartindex\Utils;

class IndexTools
{

    public static $NS_SEPARATOR = ':';

    /**
     * @param $dataDir
     * @param $namespace
     * @return string
     */
    public static function getNamespaceDataDirectory($namespace)
    {
        global $conf;
        $namespace = trim($namespace, self::$NS_SEPARATOR);
        return rtrim($conf['datadir'], '/') . ((strlen($namespace) == 0) ? "" : "/") . str_replace(self::$NS_SEPARATOR, "/", $namespace);
    }

    public static function fileIsPage($file) {
        if (preg_match('/^[\._]/', $file)) {
            return false;
        }
        return true;
    }

    public static function getPageIdentifier($pagePath)
    {
        global $conf;
        $res = substr($pagePath, strlen($conf["datadir"]) + 1);
        if ($pagePath->isFile())
            $res = substr($res, 0, strlen($res) - 4);
        $res = str_replace(array("/", "\\"), ":", $res);
        return $res;
    }


    public static function getPageFromId($id, &$namespace, &$page)
    {
        if ($pos = strripos($id, ":")) {
            $namespace = substr($id, 0, $pos);
            $page = substr($id, $pos + 1);
        } else {
            $namespace = "";
            $page = $id;
        }
    }

    public static function endsWith($haystack, $needle)
    {
        return substr($haystack, -strlen($needle)) == $needle;
    }

    public static function excludePageExtension($file)
    {
        if (self::endsWith($file, ".txt")) {
            return substr($file, 0, strlen($file) - 4);
        } else {
            return $file;
        }
    }

    public static function getPageURL($namespace, $id){
        return wl(IndexTools::getItemId($namespace, $id));
    }

    public static function getNamespaceURL($namespace, $id){
        $x = wl("",
            array('idx' => IndexTools::getItemId($namespace, $id)));

        $x = str_replace(self::$NS_SEPARATOR, '&amp;', $x);
        return $x;
    }

    public static function getItemId($namespace, $page)
    {
        return ($namespace == "") ? $page : $namespace . self::$NS_SEPARATOR . $page;
    }

    public static function isSubnamespace($namespace, $child) {
        $child .= self::$NS_SEPARATOR;

        if ((strpos($namespace, $child) === 0))
            return true;
        else
            return false;

    }

    public static function isPathPart($path, $part)
    {
        if (is_null($path) || is_null($path)) {
            return false;
        }

        if (strlen($path) != strlen($part)) {
            $part .= self::$NS_SEPARATOR;
        }
        if ((strpos($path, $part) === 0)) {
            return true;
        } else {
            return false;
        }
    }

    //https://stackoverflow.com/a/15575293
    public static function getPagePath($namespacePath, $pageFile) {
        return $namespacePath . DIRECTORY_SEPARATOR . $pageFile;
    }
}