<?php



/**
 * Skeleton subclass for performing query and update operations on the 'subscriptions' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class SubscriptionQuery extends BaseSubscriptionQuery {

  static function getRecent() {

    $date = date('Y-m-d', strtotime('-14 Days'));
    return self::create()
      ->filterByCreatedAt(array('min' => $date))
      ->count();

  }

  static function getAllSubscriptionsWithDate(DateTime $date, $status = null, $product_id = null)
  {
    $subscription = SubscriptionQuery::create('Subscription');
    $subscription->filterByDeliveryDay( $date->format('D')  );
    (!empty($status)) ? $subscription->filterByStatus( $status ) : null;
    (!empty($product_id)) ? $subscription->filterByProductId( $product_id ) : null;
    return $subscription->find();
  }

  static function getProductsTitleForUSer( $userId ){
    $aSubs = self::create()
              ->filterByUserId( $userId )
              ->filterByStatus( 'Active' )
              ->find();
    $ret = "";
    foreach ($aSubs as $aSub) {
      $ret = (!empty($ret)) ? $ret.", ".$aSub->getProduct()->getTitle() : $aSub->getProduct()->getTitle() ;
    }

    return $ret;
  }


}