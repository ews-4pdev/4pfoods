<?php



/**
 * Skeleton subclass for performing query and update operations on the 'doorsteps' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class DoorStepQuery extends BaseDoorStepQuery
{
    static function getValidSitesList(){
        $aSites = self::create()->filterByIsPublished(1)->filterByAcceptsDeliveries(1)->find();
        $return = [];
        foreach ( $aSites as $oSite ) {
            $return[$oSite->getId()] = $oSite->getZip();
        }
        return $return;
    }
}
