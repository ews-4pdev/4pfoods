<?php



/**
 * Skeleton subclass for representing a row from the 'deliveries' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class Delivery extends BaseDelivery {

  private function _signDelivery(User $oUser, $isSuccess = true) {

    //  Signing user must have admin access
    if (!$oUser->getAdminAccess())
      return false;

    //  Set status and stamp
    $newStatus = ($isSuccess) ? 'Delivered' : 'Failed';
    $this->setStatus($newStatus);
    $this->setDeliveredAt(date('Y-m-d H:i:s'));

    //  Mark all orders delivered
    $fn = ($isSuccess) ? 'deliver' : 'fail';
    $aOrders = array();
    foreach ($this->getOrders() as $oOrder) {
      $oOrder->$fn();
      $aOrders[] = $oOrder;
    }
    
    //  Sign the delivery using passed Driver
    $this->setDeliveredByDriver($oUser);

    $this->save();

    return $aOrders;

  }

  function getDeliveredByName() {

    $oUser = $this->getDeliveredByDriver();
    return ($oUser) ? $oUser->getFullName() : false;

  }

  function markDeliveredBy(User $oUser) {

    return $this->_signDelivery($oUser);

  }

  function markFailedBy(User $oUser) {

    return $this->_signDelivery($oUser, false);

  }

  function getDeliverySite()
  {
    if( !$this->getDoorstep() )
    {
      return parent::getDeliverySite();
    }
    $oUser = UserQuery::create()->findPk( $this->getUserId() );

    return DoorStepQuery::create()->findOneByZip( $oUser->getZip() );
  }

  function getAddress()
  {
    if( !$this->getDoorstep() )
    {
      return $this->getDeliverySite()->getNickname();
    }
    return UserQuery::create()->findPk( $this->getUserId() )->getFullAddress();
  }

}
