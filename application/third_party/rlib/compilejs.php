<?php

define('ROOT', dirname(__FILE__).'/../../..');
define('APPPATH', ROOT.'/application/');
define('WEBROOT', ROOT.'/webroot/');

require_once(APPPATH.'third_party/rlib/functions.php');
require_once(APPPATH.'third_party/rlib/JSHandler.class.php');

JSHandler::createBuilds();

?>
