<?php



/**
 * Skeleton subclass for representing a row from the 'users' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class User extends BaseUser {

  private static $_requiredFields = array(
    'FirstName', 'LastName', 'Address1', 'City',
    'Zip', 'StateId', 'CountryId'
  );

  public function getDefaultDeliverySite( PropelPDO $con = null, $doQuery = true )
  {
    if( $this->getDoorstep() ){
      return DoorStepQuery::create()->findPk( $this->getDefaultDeliverySiteId() );
    }
    return parent::getDefaultDeliverySite( $con, $doQuery );
  }

  function convertToDoorStep( DoorStep $oSite ){
    $this->setDoorstep(true);
      $this
          ->setDefaultDeliverySiteId($oSite->getId())
          ->setZip( $oSite->getZip() )
          ->setStateId( $oSite->getStateId() );


      if( $this->getDoorstep() === false ){
          $this->setAddress1( $oSite->getAddress1() );
      }
      $this->save();
      $sub = SubscriptionQuery::create()->filterByStatus('Active')->filterByUserId($this->getId())->find();
      foreach ($sub as $oSubscription) {
          $oSubscription
              ->setDeliveryDay( $oSite->getDefaultDeliveryDay() )
              ->setDeliverySiteId($oSite->getId());
          $oSubscription->save();
      }
  }

  function convertToPickUp( DeliverySite $oSite ){
    $this->setDoorstep(false);
      $this
          ->setDefaultDeliverySiteId($oSite->getId())
          ->setZip( $oSite->getZip() )
          ->setStateId( $oSite->getStateId() );


      if( $this->getDoorstep() === false ){
          $this->setAddress1( $oSite->getAddress1() );
      }
      $this->save();
      $sub = SubscriptionQuery::create()->filterByStatus('Active')->filterByUserId($this->getId())->find();
      foreach ($sub as $oSubscription) {
          $oSubscription
              ->setDeliveryDay( $oSite->getDefaultDeliveryDay() )
              ->setDeliverySiteId($oSite->getId());
          $oSubscription->save();
      }
  }

  /**
   *  Convenience methods
   */
  function getFullName() { return $this->getFirstName().' '.$this->getLastName(); }
  function getStateAbbrev() { return ($this->getState()) ? $this->getState()->getCode() : ''; }
  function getRequiredFields() { return self::$_requiredFields; }
  function isConfirmed() { return (bool)($this->getIsConfirmed()); }
  function isArchived() { return (bool)($this->getIsArchived()); }
  function getPaidOrders() { return OrderQuery::getPaidOrdersForUser($this->getId()); }
  function getOrders() { return OrderQuery::getForUser($this->getId()); }

  function getUnpaidOrders() { return OrderQuery::getUnpaidOrdersForUser( $this->getId() ); }
  function getSiteNickname() {

      if( $this->getDoorstep() ){
            return $this->getFullAddress();
      }elseif($oSite = $this->getDefaultDeliverySite()){
          return $oSite->getNickname();
      }else{
          return '[not set]';
      }
  }

  function getSkippedOrders($date) {

    return OrderQuery::getSkippedOrdersForUser($this->getId(), $date);

  }


  /** 
   *  Handy text summary of subscribed products
   */
  function getSubscriptionSummary() {
    $info = array();
    foreach ($this->getSubscriptions('Active') as $oSub)
      $info[] = $oSub->getProduct()->getTitle();

    return implode(', ', $info);
  }

  function getAllSubscriptionString(){
    $info = "";
    foreach ($this->getSubscriptions('Active') as $oSub)
      $info = "<div>".$oSub->getProduct()->getTitle()."</div>".$info;

    return $info;
  }

  /**
   *  Set the default delivery site and attach it to
   *  all subscriptions
   */
  function associateWithDeliverySite( $oSite ) {

    $this
        ->setDefaultDeliverySiteId($oSite->getId())
        ->setZip( $oSite->getZip() )
        ->setStateId( $oSite->getStateId() );


    if( $this->getDoorstep() === false ){
      $this->setAddress1( $oSite->getAddress1() );
    }

    //  Change all subscriptions so they are associated with this site
    foreach ($this->getSubscriptions('Active') as $oSubscription) {
      $oSubscription
          ->setDeliveryDay( $oSite->getDefaultDeliveryDay() )
          ->setDeliverySiteId($oSite->getId());
      $oSubscription->save();
    }
  }



  /**
   *  For use with email confirmation: switches the status
   *  depending on whether or not an address is attached
   */
  function confirm() {
    $this->setIsConfirmed(1);
    $newSubscriptionStatus = ($this->getCustomerStatus() == 'AddressPending') ? 'Pending' : 'Active';

    foreach ($this->getSubscriptions() as $oSub) {
      $oSub->setStatus($newSubscriptionStatus);
      $oSub->save();
    }

    $this->save();
  }

  /**
   *  Overriding so status can be passed in
   */
  function getSubscriptions($status = NULL) {
    if (!$status)
      return parent::getSubscriptions();

    $aSub = array();
    foreach (parent::getSubscriptions() as $oSub) {
      if ($oSub->getStatus() == $status)
        $aSub[] = $oSub;
    }
    return $aSub;
    

  }

  function getCanceledSubscriptions() {

    return $this->getSubscriptions('Canceled');

  }

  /**
   *  Get all subscriptions in Active state
   */
  function getActiveSubscriptions() {

    return $this->getSubscriptions('Active');

  }

  /**
   *  Get all subscriptions in Pending state
   */
  function getPendingSubscriptions() {

    return $this->getSubscriptions('Pending');

  }

  /**
   * Get sum of all payments
   */
  function getTotalRevenue() {

    $total = 0;
    foreach ($this->getPayments() as $oPayment)
      $total += $oPayment->getAmountPaid();

    return $total;

  }

  /**
   * Get most recent payment date
   */
  function getMostRecentPaymentDate() {

    $oPayment = PaymentQuery::create()
      ->filterByUserId($this->getId())
      ->orderByCreatedAt('DESC')
      ->findOne();
    return ($oPayment) ? $oPayment->getCreatedAt('Y-m-d') : NULL;

  }

  /**
   * If applicable, return the date the most recent subscription
   * was canceled
   */
  function getCanceledAt() {

    $return = false;
    if (count($this->getActiveSubscriptions()) == 0) {
      $useDate = '2014-01-01';
      foreach ($this->getCanceledSubscriptions() as $oSub) {
        $date = $oSub->getCanceledAt('Y-m-d');
        if (strtotime($date) > strtotime($useDate))
          $useDate = $date;
      }
      if ($useDate != '2014-01-01')
        $return = $useDate;
    }

    return $return;

  }


  /**
   *  Find the highest value discount attached to the user
   *  that has not yet been used
   */
  function getNextAvailableDiscount() {

    $aDiscounts = DiscountQuery::getListForUser($this->getId());
    foreach ($aDiscounts as $oDiscount) {
      if (!$oDiscount->isExpiredForUser($this->getId()))
        return (count($aDiscounts) > 0) ? $aDiscounts[0] : NULL;
    }

  }

  function getConfirmLink() {

    return 'http://'.$_SERVER['SERVER_NAME'].'/gateway/confirmAccount/'.$this->getId().'/'.$this->getHash();

  }


  function getCanceledSubscriptionsWithOrders(){
    $aSubs = $this->getSubscriptions('Canceled');
    //Does this subscription has order
    $arr = [];

    foreach ($aSubs as $oSub) {
      $date = getDateFromDay( $oSub->getDeliveryDay() );
      $order = $oSub->hasOrderOnDate(null, $date);
      if( $order != false )
      {
        $arr[] = $oSub;
      }

    }
    return $arr;
  }

  function getFullAddress(){

    $address = "";
      if ( $this->getAddress1() ){
        $address = $this->getAddress1();
      }

      if ( $this->getAddress2() ){
        $address = ( !empty( $address ) ) ? $address.', ' . $this->getAddress2() : $this->getAddress2();
      }

      return $address;

  }

  function getFullAddressWithOutComma(){

    $address = "";
    if ( $this->getAddress1() ){
      $address = str_replace( ',', '-', $this->getAddress1() );
    }

    if ( $this->getAddress2() ){
      $address2 = str_replace( ',', '-', $this->getAddress2() );
      $address = ( !empty( $address ) ) ? $address.' - ' . $address2 : $address2;
    }

    return $address;

  }

  function getBuildingTypesTags(){
      $string = ( $this->getApartmentCondo() ) ? '<b class="badge">Apartment / Condo</b> ' : '';
      $string .= ( $this->getOfficeBuilding() ) ? '<b class="badge">Office Building</b> ' : '';
      $string .= ( $this->getTownHouse() ) ? '<b class="badge">Town House</b> ' : '';
      $string .= ( $this->getSingleFamilyHome() ) ? '<b class="badge">Single Family home</b> ' : '';
      return $string;
  }

  function getStateTax()
  {
      return $this->getDefaultDeliverySite()->getState()->getTax();
  }

  function getDeliveryCharge(){
    if( $this->getDoorstep() )
        return PaymentPeer::DELIVERY_CHARGE;
    
    return 0;
  }

  function hasPendingOrders(){
    $aSub = $this->getActiveSubscriptions();
    $exist = false;
    if($aSub){
      foreach ( $aSub  as $oSub) {
        if( $oSub->hasCurrentPendingOrder() )
                $exist = true;
      }
    }
    return $exist;
  }

}
