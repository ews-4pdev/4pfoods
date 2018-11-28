<?php



/**
 * Skeleton subclass for representing a row from the 'subscriptions' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class Subscription extends BaseSubscription {

  public function getStatus() {

    $oSite = $this->getDeliverySite();
    if (!$oSite || !$oSite->acceptsDeliveries())
      return (parent::getStatus() == 'Active') ? 'Pending' : parent::getStatus();

    return parent::getStatus();

  }

  public function getDeliverySite( PropelPDO $con = null, $doQuery = true )
  {
    $oUser = $this->getUser();
    if( $oUser->getDoorstep() == 1 )
    {
      return DoorStepQuery::create()->findPk( $this->getDeliverySiteId() );
    }
    return parent::getDeliverySite( $con, $doQuery );
  }

  public function hasOrderOnDate($status, $onDate) {

    $order = OrderQuery::create()
      ->filterBySubscriptionId($this->getId())
      ->_if($status)
        ->filterByStatus($status)
      ->_endif()
      ->filterByDeliveryScheduledFor($onDate)
      ->findOne();
    return ($order) ? $order : false;

  }

  public function hasOrder($status = null) {

    $count = OrderQuery::create()
      ->filterBySubscriptionId($this->getId())
      ->_if($status)
      ->filterByStatus($status)
      ->_endif()
      ->count();
    return ($count > 0);


  }

  //  Order that is pending and still out for delivery
  public function hasCurrentPendingOrder() {

    $count = OrderQuery::create()
      ->filterBySubscriptionId($this->getId())
      ->filterByStatus('Pending')
      ->filterByDeliveryScheduledFor(array('min' => date('Y-m-d')))
      ->count();
    return ($count > 0);

  }

  public function hasPendingOrder() {

    return $this->hasOrder('Pending');

  }

  public function getSkippedOrders($fromDate = NULL, $includeDonated = false) {

    $fromDate = ($fromDate) ? $fromDate : date('Y-m-d');
    return ( new OrderQuery() )->getSkippedOrdersForSubscription($this->getId(), $fromDate, $includeDonated);

  }

  /**
   *  Get the other products in subscription's category
   */
  public function getAllInCategory() {
    return $this->getProduct()->getProductCategory()->getPublishedProducts();
  }

  /**
   *  Get next delivery day from provided date
   */

  public function getNextDeliveryDateFrom($fromDate, $format = 'n/d/Y') {
    return date($format, strtotime(dateOfNextWeekday(date('Y-m-d', strtotime($fromDate)), $this->getDeliveryDay())));
  }

  /**
   *  Find the date associated with the next delivery day
   */
  function getNextDeliveryDate($format = 'n/d/Y') {
    return self::getNextDeliveryDateFrom(date('Y-m-d'), $format);
  }

  /**
   *  Find the order associated with the future date
   */
  function getUpcomingOrder() {

    $date = self::getNextDeliveryDateFrom(date('Y-m-d', strtotime('+1 Week')), 'Y-m-d');
    return $this->getOrderOnDate($date);

  }

  /**
   *  Find the order associated with the upcoming date
   */
  function getCurrentOrder() {

    $date = self::getNextDeliveryDateFrom(date('Y-m-d'), 'Y-m-d');
    return $this->getOrderOnDate($date);

  }

  /**
   *  Find the order associated with the previous date
   */
  function getPreviousOrder() {

    $date = self::getNextDeliveryDateFrom(date('Y-m-d', strtotime('-1 Week')), 'Y-m-d');
    return $this->getOrderOnDate($date);

  }

  /**
   *  Get order on specific date, if it exists
   */
  function getOrderOnDate($date) {

    $oOrder = OrderQuery::create()
      ->filterBySubscriptionId($this->getId())
      ->filterByDeliveryScheduledFor($date)
      ->findOne();
    return $oOrder;

  }

  /**
   *  Sets the product and syncs delivery day and price info
   */
  function setProduct(Product $oProduct) {

    parent::setProduct($oProduct);
    $this->setPricePaid($oProduct->getPrice());

  }

  function setDeliverySite( $oSite ) {

    $oUser = $this->getUser();
    if( $oUser->getDoorstep() == 1 ){
      $day = DoorStepQuery::create()->findOneByZip( $oUser->getZip() );
      $this->setDeliverySiteId( $day->getId() );
      $this->setDeliveryDay( $day->getDefaultDay() );
    }else{
      $this->setDeliveryDay($oSite->getDefaultDeliveryDay());
      $this->setDeliverySiteId( $oSite->getId() );
    }
  }

  /**
   *  When adding an Order, the Order takes on the delivery
   *  site of the subscription. This can be changed manually late
   *  if need be
   */
  function addOrder(Order $oOrder) {

    //$oOrder->setDeliverySite($this->getDeliverySite());
    parent::addOrder($oOrder);

  }

  /**
   *  Pause subscription until a certain date
   */
  function pauseUntil($date) {

    //  Date must be tomorrow or later
    if (strtotime($date) < strtotime('+1 Day'))
      return false;

    $this->setPausedUntil($date);
    $this->setStatus('Paused');
    $this->save();

  }

  /**
   *  Reactivate a subscription, clearing pausedUntil date
   */
  function reactivate() {

    //  Check for paused status
    if ($this->getStatus() != 'Paused')
      return false;

    //  Check for active user account
    if ($this->getUser()->getCustomerStatus() != 'Active')
      return false;

    $this->setStatus('Active');
    $this->setPausedUntil(NULL);
    $this->save();

  }

  function cancel() {
    $this->setCanceledAt(date('Y-m-d H:i:s'));
    $this->setStatus('Canceled');

    foreach ($this->getOrders() as $oOrder) {
      if ( $oOrder->getStatus() == 'Pending' && strtotime($oOrder->getDeliveryScheduledFor()) < time() ){
        $oOrder->fail();
        // Only cancel bag if Order is set to failed.
        UserBagQuery::create()
            ->filterByDate( $oOrder->getDeliveryScheduledFor() )
            ->filterBySubscriptionId( $this->getId() )
            ->findOne()
            ->deleteBagWithItems();

      }

    }

    // Delete current and all future bags
    foreach ($this->getAllFutureUserBags() as $userBag) {
      $userBag->deleteBagWithItems();
    }
    $this->save();

  }

  function getAllFutureUserBags(){
    return UserBagQuery::create()
            ->filterByDate( ['min' => getDateFromDay( $this->getDeliveryDay() )] )
            ->filterBySubscriptionId( $this->getId() )
            ->find();
  }

  function setStatus($status) {

    if ($status != 'Active') {
      parent::setStatus($status);
      return true;
    }

    $oSite = $this->getDeliverySite();
    if (!$oSite || !$oSite->acceptsDeliveries()) {
      parent::setStatus('Pending');
      return false;
    }
    parent::setStatus($status);

  }

  function getPricePaid( $date = null ){

      return $this->getProduct( $date )->getPrice();
  }

  function getUserBagOnDate( $date = null ){
    if(!$date){
      return UserBagQuery::create()
              ->filterBySubscriptionId( $this->getId() )
              ->findOneByDate( $this->getNextDeliveryDate('Y-m-d') );
    }

    return UserBagQuery::create()
        ->filterBySubscriptionId( $this->getId() )
        ->findOneByDate( $date );
  }

  function getProductId( $date = null )
  {
     $uBag = (!$date) ? $this->getUserBagOnDate() : $this->getUserBagOnDate($date);
    if(!$uBag)
      return parent::getProductId();

    return $uBag->getProductId();
  }

  function createBag( DateTime $date = null ){

    if( $date ){
      // Does Admin Bag Exist on this date
      $aBag = $this->getAdminBag($date);

      if( !($aBag instanceof Bags) ){ return false; }

      return ( new UserBag() )->createBagWithItems($this, $aBag);
    }
    // If has order than skip
    if( $this->hasOrderOnDate('Pending', $this->getNextDeliveryDate() ) ){ return false; }

    // if has a bag than skip
    if($aBag = $this->getUserBagOnDate($date) ){ return false; }

    $date = $this->getValidDate();

    // Does Admin Bag Exist on this date
    $aBag = $this->getAdminBag($date);

    if( !($aBag instanceof Bags) ){ return false; }

    return ( new UserBag() )->createBagWithItems($this, $aBag);
  }

  function getValidDate(){

    $date = new DateTime();
    $date->modify( $this->getNextDeliveryDateFrom(date('Y-m-d'), 'Y-m-d') );
    $today = DateTime::createFromFormat( 'Y-m-d H:i:s', date('Y-m-d H:i:s') );
    $difference = $today->diff( $date );
    $hours = ($difference->days * 24) + $difference->h;


    if( $hours <= ( OrderPeer::CHARGE_DAYS_BEFORE * 24 ) ){
      return ( new DateTime() )->modify( '+1 week '.$this->getDeliveryDay() );
    }
    return ( new DateTime() )->modify( $this->getDeliveryDay() );
  }

  function getAdminBag($date = null){
    if( $date == null ){
      return BagsQuery::create()
                ->filterByProductId($this->getProductId())
                ->filterByDate( date('Y-m-d', strtotime($this->getDeliveryDay())) )
                ->filterByIsPublished(true)
                ->findOne();

    }
    return  BagsQuery::create()
            ->filterByProductId($this->getProductId())
            ->filterByDate( $date )
            ->filterByIsPublished(true)
            ->findOne();
  }

  function getProduct( $date = null ){
      $uBag = $this->getUserBagOnDate($date);
    if(!$uBag)
      return parent::getProduct();

    return $uBag->getProduct();
  }

  function deleteUserBag( DateTime $date = null ){
     $uBag = UserBagQuery::create()
               ->filterByDate($date)
               ->filterBySubscriptionId($this->getId())
               ->findOne();
    if( $uBag ){
      $uBag->deleteBagWithItems();
    }
    return true;
  }

  function getNextWeekDeliveryDate(){

  }

}
