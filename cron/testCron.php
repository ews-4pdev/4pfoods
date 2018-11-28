<?php

require_once('bootstrap.php');
require_once(APPPATH.'helpers/TestCronHelper.php');
require_once(APPPATH.'helpers/TestStripeHelper.php');

;
$task = $argv[1];
$helper = new TestCronHelper($task, $argv[2]);
$helper->execute();