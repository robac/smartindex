<?php

function smartindex_autoloader($class) {
    $classpath = dirname(__FILE__)."/classes/{$class}.php"; 
    if (file_exists($classpath)) {
        require_once $classpath;
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
    DEFINE('SMARTINDEX_URL', DOKU_BASE.'lib/plugins/smartindex/');
    DEFINE('THEMES_URL', SMARTINDEX_URL.'themes/');
    DEFINE('AJAX_URL', SMARTINDEX_URL.'exe/ajax.php');
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