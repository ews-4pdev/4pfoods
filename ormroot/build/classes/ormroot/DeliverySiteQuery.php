<?php



/**
 * Skeleton subclass for performing query and update operations on the 'delivery_sites' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class DeliverySiteQuery extends BaseDeliverySiteQuery {
    static function getDays(){
        $days =  self::create()
            ->filterByIsPublished(true)
            ->groupByDefaultDeliveryDay()
            ->select('default_delivery_day')
            ->find()
            ->toArray();

        $newArray = [];
        foreach ($days as $day) {
            $newArray[ getFullDay($day) ] = 1;
        }
        return $newArray;
    }

    static function getValidSitesList(){
        $aSites=  self::create()->filterByIsPublished(1)->filterByAcceptsDeliveries(1)->find();
        $return = [];
        foreach ( $aSites as $oSite ) {
            $return[$oSite->getId()] = $oSite->getNickname();
        }
        return $return;
    }
}
