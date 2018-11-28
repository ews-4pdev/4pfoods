<?php

define('SIM', false);

class CronHelper {

  private $_oLog;
  private $_today;
  private $_isSimulation = false;
  private $_args;

  function __construct($task, $simDate = NULL, $arg = NULL) {

    if (!method_exists($this, $task))
      throw new Exception('Invalid task.');

    $this->_today = date('Y-m-d');
    $this->_args = $arg;

    if (SIM && $simDate) {
      $this->_isSimulation = true;
      $this->_today = $simDate;
    }

    $this->_oLog = new CronLog();
    $this->_oLog->setTask($task);
    $this->_oLog->save();

  }

  function execute() {

    $task = $this->_oLog->getTask();

    try {
      $result = $this->$task();
      $this->_oLog->logSuccess($result);
    } catch (Exception $e) {
      $this->_oLog->setErrorCode($e->getMessage());
    }

  }

  /**
   *  Dump the database and upload the dump file to S3
   */
  private function backupDatabase() {

    //  Where are we saving it?
    $file = '/home/rarenson/backmeup.sql';

      //  Execute database dump
      $cmd = 'mysqldump -u4pfoods_cb --password="Tq6ub7^4" production_cb > '.$file;
      exec($cmd);

      //  Upload it
      require(APPPATH.'misc/backup-upload.php');

      return $result;

  }

  /**
   *  @runs Once per day at 12pm
   *  @description Runs the StripeHelper function createOrders
   *    to charge customers for orders 48 hours before delivery
   */
  private function createOrders() {

    $helper = new StripeHelper($this->_today);
    $helper->createOrders();

  }

  /**
   *  @runs Once per day at 12pm
   *  @description Runs the StripeHelper function createDeliveries
   *    to create delivery lists for drivers
   */
  private function createDeliveries() {

    $helper = new StripeHelper($this->_today);
    $helper->createDeliveries();

  }

  /**
   *  @runs Every day at noon
   *  @description Emails users who abandoned their cart the
   *    previous day
   */
  private function sendCartAbandonmentEmails() {

    //  All emails captured yesterday
    $date = date('Y-m-d', strtotime('Yesterday'));
    $aEmails = CapturedEmailQuery::getEmailsFromDate($date);

    //  Make sure they didn't complete the signup process
    //  and haven't already been emails, then contact them
    foreach ($aEmails as $oEmail) {

      //  Already signed up?
      if ($oEmail->isSignedUp())
        return false;

      //  Already emailed?
      if ($oEmail->getFollowupSentAt() != NULL)
        return false;

      //  Send it
      $helper = new SendGridHelper($oEmail->getEmail(), 'cart-abandonment');
      $helper->merge(array(
        'firstname'     => ($oEmail->getFirstName()) ? $oEmail->getFirstName() : 'Friend'
      ));
      $helper->send();

      //  Mark it sent
      $oEmail->markSent();

    }

  }

  /**
   *  @runs On the first of every month at 12:01 am
   *  @description Aggregates orders and prices into list of
   *    customers and monies due, then charges every customer
   *    once using Stripe
   */
  private function executeCharges() {
      $this->log("Payment - Cron hit the execute charges.\n");
    $helper = new StripeHelper($this->_today);
    $helper->executeCharges();

  }


    private function log($msg) {
        $fs = filesize(APPPATH.'logs/log.txt');

        if ( $fs > 2000000 ) // 2000 kb
            rename(APPPATH.'logs/log.txt',APPPATH."logs/log".gmdate("Ymdhms").".txt");

        $file = APPPATH.'logs/log.txt';
        $current = file_get_contents($file);
        $current .= date('Y-m-d h:i:sa').' --> '.$msg;
        file_put_contents($file, $current);

    }

  /**
   *  @runs Once per day at 12pm
   *  @description Iterates through all users with orders
   *    in Pending status with delivery dates tomorrow and
   *    sends a reminder email to each customer
   */
  private function sendDeliveryReminders() {

    //  Get users with deliveries tomorrow
    $deliveryOn = date('Y-m-d', strtotime('+1 Day'));
    $aUsers = UserQuery::getWithPendingDeliveryOn($deliveryOn);

    //  Send each of them a reminder - this is per-user and
    //  not per-order
    $catchall = array();
    foreach ($aUsers as $oUser) {

      //  Compose and send
      $helper = new SendGridHelper($oUser->getEmail(), 'bag-reminder');
      $helper->merge(array(
        'firstname'     => $oUser->getFirstName()
      ));
      $helper->send();

      //  Store the result for logging
      $catchall[] = array(
        'iUser'     => $oUser->getId(),
        'message'   => $helper->toArray()
      );

    }

    return json_encode($catchall);
    
  }


  /*
      This cron function is responsible to get all customers who needed to be informed about changed bags.
  */
  private function publishBags(){
    $date = DateTime::createFromFormat( 'm-d-Y', $this->_args );
    $customers = BagsQuery::publishBagsForActiveCustomers( $date , null );
    ksort($customers);
    foreach ($customers as $customerId) {
      $customer = UserQuery::create()->findPk( $customerId['id'] );
      $productsTitle = implode(', ', $customerId['title']);

      $helper = null;
      if( $customerId['email'] == 'second' ){
        $helper = new SendGridHelper($customer->getEmail(), 'bag-changed');
      }
//      else{
//        $helper = new SendGridHelper($customer->getEmail(), 'bag-ready');
//      }

      $helper->merge(array(
          'firstname'     => $customer->getFirstName(),
          'lastname'      => $customer->getLastName(),
          'email'         => $customer->getEmail(),
          'date'          => $date->format('F j, Y,'),
          'bag_title'     => strtoupper( $productsTitle ),
      ));
      $helper->send();
      UserBagQuery::setIsChanged( $date, $customer->getId() );

    }
    return json_encode($customers);
  }

}