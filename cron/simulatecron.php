<?php

require_once('bootstrap.php');

$startDate = $current = date('Y-m-d');
$endDate = date('Y-m-d', strtotime('+27 Days'));

while (strtotime($current) <= strtotime($endDate)) {

  //  Create orders
  $helper = new CronHelper('createOrders', $current);
  $helper->execute();

  //  Create deliveries
  $helper = new CronHelper('createDeliveries', $current);
  $helper->execute();

  //  Execute charges, if applicable
  if (date('d', strtotime($current)) == 1) {
    $helper = new CronHelper('executeCharges', $current);
    $helper->execute();
  }

  $current = date('Y-m-d', strtotime($current.' +1 Day'));

}

?>
