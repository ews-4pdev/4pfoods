<?php



/**
 * Skeleton subclass for performing query and update operations on the 'payments' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class PaymentQuery extends BasePaymentQuery {

  static function getForUser($uid) {

    return self::create()
      ->filterByUserId($uid)
      ->orderByCreatedAt('DESC')
      ->find();

  }

static function getTotalEarned() {
$aPayments = self::create()
->filterByStatus('Succeeded')
->withColumn('SUM(Payment.AmountPaid)', 'total')
->find();
return $aPayments->get(0)->gettotal();

}

} // PaymentQuery
