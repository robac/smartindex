<?php

namespace Smartindex\Utils;

class Utils {
    
    public static function getFloatMicrotime($prefix = NULL) {
         list($usec, $sec) = explode(" ", microtime());
         return ((is_null($prefix))?"":$prefix).str_replace(".", "_", ((float)$usec + (float)$sec));
    }
    
    public static function parseBoolean($string) {
        return ($string === "true") ? true : (($string === "false") ? false : null);
    }

    public static function parseNumeric($string) {
        return intval($string);
    }

    public static function generateIndexId() {
        return Utils::getFloatMicrotime("smartindex_");
    }
}