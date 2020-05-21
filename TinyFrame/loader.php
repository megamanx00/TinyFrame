<?php
define('BASE_DIR', realpath(__DIR__ . '/..'));
define('WEB_DIR', BASE_DIR . '/www');

//Register our simple autoloader.
function autoloader ($classFile)
{
    $classFile = str_replace('\\','/', $classFile);
    require_once(BASE_DIR . '/'. $classFile . ".php");
}
spl_autoload_register('autoloader');