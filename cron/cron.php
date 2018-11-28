<?php

require_once('bootstrap.php');

$task = $argv[1];

if ( isset($argv[2]) && !isset( $argv[3] ) && $argv[2] != "null" ){
  $helper = new CronHelper($task, $argv[2]);
} elseif(isset($argv[3])){
  $helper = new CronHelper($task, null, $argv[3]);
}else{
  $helper = new CronHelper($task);
}
$helper->execute();