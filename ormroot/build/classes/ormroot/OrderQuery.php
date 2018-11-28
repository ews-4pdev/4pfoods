<?php

/**
 * Skeleton subclass for performing query and update operations on the 'orders' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class OrderQuery extends BaseOrderQuery {

  static function getFullCount() {

    return self::create()
               ->filterByStatus(array('Pending', 'Delivered'))
               ->count();

  }

  static function getForUser($userID) {

    $result = OrderQuery::create()
                        ->useSubscriptionQuery()
                        ->filterByUserId($userID)
                        ->endUse()
                        ->orderByCreatedAt('DESC')
                        ->find();
    return $result;

  }

  static function getPaidOrdersForUser($userID) {

    $result = OrderQuery::create()
                        ->filterByPaidAt(null, Criteria::ISNOTNULL)
                        ->filterByPaymentId(null, Criteria::ISNOTNULL)
                        ->useSubscriptionQuery()
                        ->filterByUserId($userID)
                        ->endUse()
                        ->orderByCreatedAt('DESC')
                        ->find();
    return $result;

  }

  static function getUnpaidOrdersForUser( $userID ){

    $result = OrderQuery::create()
                        ->filterByPaymentId(null)
                        ->filterByPaidAt(null)
                        ->filterByStatus('Skipped', Criteria::NOT_EQUAL)
                        ->useSubscriptionQuery()
                        ->filterByUserId($userID)
                        ->endUse()
                        ->orderByCreatedAt('DESC')
                        ->find();

    $byDate = [];
    foreach ( $result as $order ) {
      if( !isset( $byDate[ $order->getDeliveryScheduledFor() ] ) ){
        $byDate[ $order->getDeliveryScheduledFor() ][] = $order;
      }else{
        $byDate[ $order->getDeliveryScheduledFor() ][] = $order;
      }
    }

    return $byDate;
  }

  static function getOrderTable($iUser, $fromDate, $toDate) {

    $table = [];
    $aItems = OrderQuery::create()
                        ->useSubscriptionQuery()
                        ->filterByUserId($iUser)
                        ->endUse()
                        ->filterByDeliveryScheduledFor(array('min' => $fromDate, 'max' => $toDate))
                        ->find();
    foreach ($aItems as $oItem) {
      if (in_array($oItem->getStatus(), ['Delivered', 'Donated']))
        $status = money($oItem->getPrice());
      else
        $status = $oItem->getStatus()[0];
      $table[$oItem->getDeliveryScheduledFor('Y-m-d')] = $status;
    }

    foreach (self::getDeliveryDateList($fromDate, $toDate) as $date)
      $table[$date] = (isset($table[$date])) ? $table[$date] : 0;

    krsort($table);

    return $table;

  }

  static function getDeliveryDateList($fromDate, $toDate) {

    return OrderQuery::create()
                     ->select('DeliveryScheduledFor')
                     ->filterByDeliveryScheduledFor(array('min' => $fromDate, 'max' => $toDate))
                     ->groupBy('DeliveryScheduledFor')
                     ->orderByDeliveryScheduledFor('ASC')
                     ->find();

  }

  static function getTotalDonated() {

    //  Orders explicitly donated
    $nOrders = self::create()
                   ->filterByStatus('Donated')
                   ->count();

    //  Every Nth signup
    $nUsers = UserQuery::create()->count();
    $nthUsers = floor($nUsers / OrderPeer::AUTOMATIC_DONATE_FREQUENCY);

    return $nOrders + $nthUsers;

  }

  static function getDonatedLastWeek() {

    //  Find Last Sunday
    $current = date('Y-m-d');
    while (date('D', strtotime($current)) != 'Sun')
      $current = date('Y-m-d', strtotime($current.' -1 Day'));
    $lastSunday = $current;

    //  Find the previous Monday
    $previousMonday = date('Y-m-d', strtotime($lastSunday.' -6 Days'));

    //  Get donated during timeframe
    return self::getDonatedDuringTimeframe($previousMonday, $lastSunday);

  }

  static function getDonatedDuringTimeframe($startDate, $endDate) {

    //  Orders explicitly donated
    $nOrders = self::create()
                   ->filterByStatus('Donated')
                   ->filterByDonatedAt(array('min' => $startDate, 'max' => $endDate))
                   ->count();

    //  Total Nth users up until end date
    $nUsers = UserQuery::create()
                       ->filterByCreatedAt(array('max' => $endDate))
                       ->count();
    $allNthUsers = floor($nUsers / OrderPeer::AUTOMATIC_DONATE_FREQUENCY);

    //  Total Nth users up until the start date
    $nUsers = UserQuery::create()
                       ->filterByCreatedAt(array('max' => $startDate))
                       ->count();
    $prevNthUsers = floor($nUsers / OrderPeer::AUTOMATIC_DONATE_FREQUENCY);

    //  Nth users in date range is difference
    $nthUsers = $allNthUsers - $prevNthUsers;

    return $nOrders + $nthUsers;

  }

  function getSkippedOrders($fromDate, $toDate) {

    return self::create()
               ->filterByStatus('Skipped')
               ->filterByDeliveryScheduledFor(array('min' => $fromDate, 'max' => $toDate))
               ->orderByDeliveryScheduledFor('ASC')
               ->find();

  }

  function getSkippedOrdersForSubscription($iSub, $fromDate, $includeDonated = false) {

    $statusFilter = ($includeDonated) ? array('Skipped', 'Donated') : 'Skipped';
    return self::create()
               ->filterBySubscriptionId($iSub)
               ->filterByStatus($statusFilter)
               ->filterByDeliveryScheduledFor(array('min' => $fromDate))
               ->orderByDeliveryScheduledFor('ASC')
               ->find();

  }

  /**
   *  Returns an array of orders, indexed by delivery site Id
   */
  function getOutForDeliveryOnDate($date = NULL) {

    $date = ($date) ? $date : date('Y-m-d');
    $all = self::create()
               ->filterByStatus('Pending')
               ->filterByDeliveryScheduledFor($date)
               ->find();
    return self::organizeOrders($all);

  }

  /**
   *  Returns all orders due to be delivered in next X days.
   *  Effectively, deliveries that are paid for but not
   *  out for delivery
   */
  function getQueued($fromDate = NULL, $skipOrganize = false) {

    $fromDate = ($fromDate) ? $fromDate : date('Y-m-d', strtotime('Tomorrow'));
    $untilDate = date('Y-m-d', strtotime($fromDate.' +'.OrderPeer::CHARGE_DAYS_BEFORE.' Days'));
    $all = self::create()
               ->filterByStatus('Pending')
               ->filterByDeliveryScheduledFor(array('min' => $fromDate, 'max' => $untilDate))
               ->find();
    return ($skipOrganize) ? $all : self::organizeOrders($all);

  }

  /**
   *  Based on active subscriptions, returns a list of
   *  theoretical orders that will be queued up along the
   *  provided date range. Only counts orders that are NOT
   *  created already, whether pending or skipped
   */
  function getTheoretical($fromDate = NULL, $toDate = NULL) {

    $fromDate = ($fromDate) ? $fromDate : date('Y-m-d');
    $toDate = ($toDate) ? $toDate : date('Y-m-d', strtotime('+6 Days'));

    $return = array(
        'sites' => [
            'DoorStep' => [],
            'DeliverySite' => []
        ],
        'products' => array(),
        'contactList' => array(),
        'totalBags' => 0
    );
    $currentPointer = null;
    //  We are looping by day of the week so items are sorted
    //  by date order
    $current = $fromDate;
    while (strtotime($current) <= strtotime($toDate)) {

      //  What day of the week is this?
      $weekday = date('D', strtotime($current));

      //  Get all active subscriptions with a delivery day on this
      //  day of the week
      $aSubscriptions = SubscriptionQuery::create()
                                         ->filterByStatus('Active')
                                         ->filterByDeliveryDay($weekday)
                                         ->useUserQuery()
                                         ->orderByLastName('ASC')
                                         ->endUse()
                                         ->find();


      foreach ($aSubscriptions as $oSub) {

        $item = array(
            'stamp'       => $current,
            'oSub'        => $oSub,
            'oProduct'    => $oSub->getProduct(),
            'oUser'       => $oSub->getUser(),
            'oOrder'      => NULL
        );

        //  Exclude if there is already a non-donated order on this date
        if ($oSub->hasOrderOnDate(array('Skipped', 'Pending', 'Failed', 'Delivered'), $current))
          continue;

        //  If there is a donated order, include it
        if ($oOrder = $oSub->hasOrderOnDate('Donated', $current))
          $item['oOrder'] = $oOrder;

        //  Initialize site slot if needed
        $iSite = $oSub->getDeliverySiteId();
        $oSite = $oSub->getDeliverySite();
        $oUserId = $oSub->getUser()->getId();

        if( $oSite instanceof  DoorStep ){
          $currentPointer = &$return['sites']['DoorStep'];
          $iSite = $oUserId;
        }
        else{
          $currentPointer = &$return['sites']['DeliverySite'];
        }

        if (!isset($currentPointer[$iSite])) {

          $siteItem = array(
              'user'      => $oUserId,
              'site'      => $oSite,
              'orders'     => array(),
              'products'  => array()
          );
          $currentPointer[$iSite] = $siteItem;

        }

        //  Initialize product slot if needed
        $oProduct = $oSub->getProduct();
        $iProduct = !empty($oProduct) ? $oProduct->getId() : 0;
        if (!isset($currentPointer[$iSite]['products'][$iProduct])) {
          $array = array(
              'product'   => $oProduct,
              'count'     => 0
          );
          $currentPointer[$iSite]['products'][$iProduct] = $array;
        }

        //  Add the order and increment the product total
        $currentPointer[$iSite]['products'][$iProduct]['count']++;
        $currentPointer[$iSite]['orders'][] = $item;

        //  Same with the overall product slot
        if (!isset($return['products'][$iProduct])) {
          $array = array(
              'product'   => $oProduct,
              'count'     => 0
          );
          $return['products'][$iProduct] = $array;
        }
        $return['products'][$iProduct]['product'] = $oProduct;
        $return['products'][$iProduct]['count']++;
        $return['totalBags']++;

        //  Add the customer's info to the contact list
        if (!isset($return['contactList'][$oSub->getUserId()])) {
          $oUser = $oSub->getUser();
          $info = array(
              'Email'     => $oUser->getEmail(),
              'FirstName' => $oUser->getFirstName(),
              'LastName'  => $oUser->getLastName(),
              'Phone'     => $oUser->getPhone()
          );
          $return['contactList'][$oUser->getId()] = $info;
        }

      }

      $current = date('Y-m-d', strtotime($current.' +1 Day'));

    }

    return $return;

  }

  /**
   *  Organize a list of orders into a structure
   *  to be used in generating admin reports for drivers
   */
  private function organizeOrders($aOrders) {

    $return = array(
        'sites'   => [
            'DoorStep' => [],
            'DeliverySite' => []
        ],
        'products'  => array(),
        'totalBags' => count($aOrders)
    );
    $currentPointer = null;

    foreach ($aOrders as $order) {
      $iSite = $order->getDeliverySiteId();
      $site =  $order->getDeliverySite();
      $oUserId = $order->getSubscription()->getUser()->getId();

      $currentPointer = &$return['sites']['DeliverySite'];
      if( $site instanceof  DoorStep ){
        $currentPointer = &$return['sites']['DoorStep'];
        $iSite = $oUserId;
      }

      if (!isset($currentPointer[$iSite])) {
        $array = array(
            'user'    => $oUserId,
            'site'    => $site,
            'orders'  => array(),
            'products' => array(),
        );
        $currentPointer[$iSite] = $array;
      }
      $oProduct = $order->getSubscription()->getProduct();
      $iProduct = $oProduct->getId();
      if (!isset($return['products'][$iProduct])) {
        $array = array(
            'product'   => $oProduct,
            'count'     => 0
        );
        $return['products'][$iProduct] = $array;
      }
      $return['products'][$iProduct]['product'] = $oProduct;
      $return['products'][$iProduct]['count']++;
      if (!isset($currentPointer[$iSite]['products'][$iProduct])) {
        $array = array(
            'product'   => $oProduct,
            'count'     => 0
        );
        $currentPointer[$iSite]['products'][$iProduct] = $array;
      }
      $currentPointer[$iSite]['products'][$iProduct]['count']++;
      $currentPointer[$iSite]['orders'][] = $order;
    }

    return $return;

  }

  function getDeliveryManifestReport($input, $status = 'Pending'){
    $dateFrom = DateTime::createFromFormat('m-d-Y', $input['start']);
    $dateTo = DateTime::createFromFormat('m-d-Y', $input['end']);

    if(!$dateFrom || !$dateTo){return false;}


    $orders = OrderQuery::create()
                        ->filterByStatus($status)
                        ->filterByDeliveryScheduledFor(['min' => $dateFrom, 'max' => $dateTo])
                        ->select('user_bag_id')
                        ->find();


    $aUserBags = UserBagQuery::create()
                             ->where('user_bag.id IN ?', $orders)
                             ->find();



    $arr = [];
    $maximumItems = [];
    $maximumItems['biggestValue'] = 0;

    foreach ( $aUserBags as  $oUserBag ) {
      $items = [];

      $date = $oUserBag->getDate();

      $user = UserQuery::create()->findPk( $oUserBag->getUserId() );
      $arr[$date][] =  [
          'delivery_schedule' => $date,
          'product'           => $oUserBag->getProduct()->getTitle(),
          'stop'              => '',
          'delivery_site'     => ( $user->getDoorstep() ) ? 'DoorStep'  : $user->getDefaultDeliverySite()->getNickname(),
          'f_name'            => $user->getFirstName(),
          'l_name'            => $user->getLastName(),
          'email'             => $user->getEmail(),
          'phone'             => $user->getPhone(),
          'DeliverySiteNotes' => $user->getDeliverySiteNotes(),
          'Address'           => ( $user->getDoorstep() ) ? $user->getFullAddress() : $user->getDefaultDeliverySite()->getAddress1()
      ];

      $aUserBagItems = $oUserBag->getActiveItems();
      $totalQuantity = $oUserBag->getTotalQuantity();
      $biggest = $totalQuantity;

      $arrKey = each($arr[$date])[0];
      foreach ($aUserBagItems as $key => $value) {
        $quantity = $value->getQuantity();
        if( $quantity > 1 ){
          for( $quantity; $quantity > 0; $quantity-- ) {
            $arr[$date][$arrKey]['item'.$totalQuantity] =  $value->getItemsPoint()->getItems()->getName();
            $totalQuantity--;
          }
        }else{
          $arr[$date][$arrKey]['item'.$totalQuantity] =  $value->getItemsPoint()->getItems()->getName();
          $totalQuantity--;
        }
      }

      if( $biggest >  $maximumItems['biggestValue'] )
      {
        $key = each($arr[$date])[0];
        $maximumItems['biggestValue'] = $biggest;
        $maximumItems['date'] = $date;
        $maximumItems['key'] = $key;
      }
    }
    $arr['max'] = $maximumItems;
    return $arr;
  }

  public static function getProductCount( $productId, $deliveryId, $date )
  {
    $subscriptions = self::create()
                         ->filterByDeliveryId( $deliveryId )
                         ->select('subscription_id')
                         ->find();

    return  UserBagQuery::create()
                        ->filterByProductId( $productId )
                        ->filterBySubscriptionId( $subscriptions->toArray() )
                        ->filterByDate( $date )
                        ->count();
  }
}