<?php



/**
 * Skeleton subclass for representing a row from the 'delivery_sites' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class DeliverySite extends BaseDeliverySite {

  function getCountryName() { return $this->getCountry()->getName(); }
  function getStateAbbrev() { return $this->getState()->getCode(); }

  function toJSONObject() {

    $keys = ['Doorstep' => 1,'Address1' => 1,'Address2' => 1,'City' => 1,'StateId' => 1,'Nickname' => 1, 'Zip' => 1, 'Id' => 1];
    return json_encode(array_intersect_key($this->toArray(), $keys));

  }

  /** 
   *  TEMPORARY: DO NOT ACCEPT DELIVERIES
   */
  function acceptsDeliveries() { 
  
    if (DISABLE_DELIVERIES)
      return false;

    return (bool)($this->getAcceptsDeliveries());
    
  }

  function getFullAddress() {
    return $this->getAddress1().', '.
      ($this->getAddress2() ? $this->address2.', ' : '').
      $this->getCity().', '.$this->getStateAbbrev().' '.$this->getZip();
  }

  function getPendingDeliveries() {

    return DeliveryQuery::getPendingForSite( $this->getId() );

  }

  function changeDeliveryDay($newDay) {

    //  Make sure that neither current day nor new day is within
    //  the X-day range of today
    $today = date('Y-m-d');
    if (OrderPeer::isWithinRangeOfWeekday($today, $this->getDefaultDeliveryDay()))
      return false;
    if (OrderPeer::isWithinRangeOfWeekday($today, $newDay))
      return false;
    
    //  Set default delivery day for this site
    $this->setDefaultDeliveryDay($newDay);

    //  Make changes to all subscriptions on this site
    foreach ($this->getSubscriptions() as $oSub) {

      //  Change the delivery day for each subscription
      $oSub->setDeliveryDay($newDay);

      //  Move skipped and donated orders in future to new day
      $aSkipped = OrderQuery::create()
        ->filterByDeliveryScheduledFor(array('min' => date('Y-m-d')))
        ->filterByStatus(array('Skipped', 'Donated'))
        ->filterBySubscriptionId($oSub->getId())
        ->find();
      foreach ($aSkipped as $oOrder) {
        $newDate = $oSub->getNextDeliveryDateFrom($oOrder->getDeliveryScheduledFor(), 'Y-m-d');
        $oOrder->setDeliveryScheduledFor($newDate);
        $oOrder->save();
      }

      //  Save the subscription
      $oSub->save();

    }

    //  Save the delivery site
    $this->save();
    return true;

  }

  function disableDeliveries() {

    $this->setAcceptsDeliveries(0);

    $aSubs = SubscriptionQuery::create()
      ->filterByDeliverySiteId($this->getId())
      ->filterByStatus('Active')
      ->find();

    foreach ($aSubs as $oSub) { 
      $oSub->setStatus('Pending');
      $oSub->save();
    }
    $this->save();
  }

  function enableDeliveries() {

    $this->setAcceptsDeliveries(1);

    $aSubs = SubscriptionQuery::create()
      ->filterByDeliverySiteId($this->getId())
      ->filterByStatus('Pending')
      ->useUserQuery()
        ->filterByCustomerStatus('Active')
      ->endUse()
      ->find();

    foreach ($aSubs as $oSub) { 
      $oSub->setStatus('Active');
      $oSub->save();
    }

    $this->save();

  }

  function getBothAddress( $userId = null ){
    $address = "";
    if( !$userId ){
      if( $this->getAddress1() )
          $address = $this->getAddress1();

      if( $this->getAddress2() )
        $address = $address.' '.$this->getAddress2();
    }else{
      $user = UserQuery::create()->findPk( $userId );

      if( $user->getAddress1() )
        $address = $user->getAddress1();

      if( $user->getAddress2() )
        $address = $address.' '.$user->getAddress2();
    }
    return $address;
  }

  function getDCRoute(){
    return $this->getCity().','.$this->getStateAbbrev().' '.$this->getZip();
  }

} // DeliverySite
