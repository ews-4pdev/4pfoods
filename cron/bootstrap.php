<?php

define('ENVIRONMENT', 'production');
define('CRONDIR', dirname(__FILE__).'/');
define('BASEPATH', CRONDIR.'../');
define('APPPATH', BASEPATH.'application/');
define('ORMPATH', BASEPATH.'ormroot/');
define('RLIB', APPPATH.'third_party/rlib/');

require_once(APPPATH.'config/rconfig.php');
require_once(RLIB.'functions.php');
require_once(ORMPATH.'bootstrap.php');
require_once(APPPATH.'../vendor/autoload.php');

set_include_path( get_include_path() . PATH_SEPARATOR . APPPATH.'third_party/phpunit/lib/phpunit' );

require_once(APPPATH.'helpers/StripeHelper.php');
require_once(APPPATH.'helpers/SendGridHelper.php');
require_once(APPPATH.'helpers/CronHelper.php');

?>
