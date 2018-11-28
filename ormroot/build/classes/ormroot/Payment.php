<?php



/**
 * Skeleton subclass for representing a row from the 'payments' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class Payment extends BasePayment {

  function getCustomerFirstName() { return $this->getUser()->getFirstName(); }
  function getCustomerLastName() { return $this->getUser()->getLastName(); }

  function succeed(Hook $oHook) {

    $this->setStatus('Succeeded');
    $this->addHook($oHook);
    $this->save();

  }

  function fail(Hook $oHook) {

    //  Payment status failed, and associate with hook
    $this->setStatus('Failed');
    $this->addHook($oHook);
    $this->save();

    //  Suspend the user's account
    $oUser = $this->getUser();
    $oUser->setCustomerStatus('Suspended');
    $oUser->save();

    //  Suspend all of the user's subscriptions
    foreach ($oUser->getSubscriptions() as $oSub) {
      $oSub->setStatus('Suspended');
      $oSub->save();
    }

  }

  function getRefundedTotal() {

    $amount = 0;
    foreach ($this->getRefunds() as $oRefund)
      $amount += $oRefund->getAmount();

    return $amount;

  }

  function getRefundableAmount() {

    return $this->getAmountPaid() - $this->getRefundedTotal();

  }

  function getTotalPrice(){
    $amount =  OrderQuery::create()
                      ->filterByPaymentId( $this->getId() )
                      ->withColumn('SUM(price)', 'total')
                      ->select('total')
                      ->find();
    return $amount[0];
  }

  function getProductsName(){
    $aOrders = $this->getOrders();
    $pNames = [];
    foreach ( $aOrders as $oOrder ) {
      $pNames[] = $oOrder->getSubscriptionProductTitle();
    }
    return implode(', ', $pNames);
  }

} // Payment
