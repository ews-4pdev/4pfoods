<?php
// Include the main Propel script
//require_once APPPATH.'third_party/propel/runtime/lib/Propel.php';
//

$isTesting = (isset($_SERVER['APP_ENV'])) && ($_SERVER['APP_ENV'] == 'local');
$runtimeConf = ($isTesting) ? 'testing-conf.php' : 'ormroot-conf.php';

require_once(APPPATH.'../vendor/autoload.php');
// Initialize Propel with the runtime configuration
Propel::init(ORMPATH."build/conf/ormroot-conf.php");


// Add the generated 'classes' directory to the include path
set_include_path(ORMPATH."build/classes" . PATH_SEPARATOR . get_include_path());