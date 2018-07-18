<?php
require_once(dirname(__FILE__).'/../inc.php');
INC_constsDW();
INC_includeDWCore();
INC_constsSmartindex();

if (!auth_isadmin()) {
    echo "Just for administrators!";
}

$col = new ThemesCollector();
$col->collect();
