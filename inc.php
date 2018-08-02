<?php


function path_to_class($class) {
    if ((DIRECTORY_SEPARATOR !== '\\') && (strpos($class, '\\') !== false)) {
        $class =  str_replace('\\', DIRECTORY_SEPARATOR, $class);
    }
    return $class;
}

function smartindex_autoloader($class)
{
    $path = dirname(__FILE__) . "/classes" . DIRECTORY_SEPARATOR . path_to_class($class) . ".php";
    if (file_exists($path)) {
        require_once $path;
    }
}

function INC_constsDW() {
    if(!defined('DOKU_INC')) 
        define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');

    if (!defined('DOKU_LF')) 
        define('DOKU_LF', "\n");

    if (!defined('DOKU_TAB')) 
        define('DOKU_TAB', "\t");

    if (!defined('DOKU_PLUGIN')) 
        define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
}

function INC_constsSmartindex() {
    DEFINE('SMARTINDEX_DIR', dirname(__FILE__).'/');
    DEFINE('THEMES_DIR', SMARTINDEX_DIR.'themes/');
    DEFINE('HTML_DIR', SMARTINDEX_DIR.'html/');
    DEFINE('TEMPLATES_DIR', SMARTINDEX_DIR.'templates/');
    DEFINE('SMARTINDEX_URL', DOKU_BASE.'lib/plugins/smartindex/');
    DEFINE('THEMES_URL', SMARTINDEX_URL.'themes/');
}

function INC_includeDWCore() {
    require_once(DOKU_INC.'inc/init.php');
    require_once(DOKU_INC.'inc/auth.php');
}

function INC_requireDW() {
    if (!defined('DOKU_INC')) 
        die();
}

spl_autoload_register('smartindex_autoloader');