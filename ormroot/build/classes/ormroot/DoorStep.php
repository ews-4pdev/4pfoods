<?php



/**
 * Skeleton subclass for representing a row from the 'doorsteps' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class DoorStep extends BaseDoorStep
{
    public function getPendingDeliveries(){
        return DeliveryQuery::getPendingForSite( $this->getId() );
    }

    public function acceptsDeliveries(){
        return (bool)($this->getAcceptsDeliveries());
    }

    public function getDefaultDeliveryDay(){
        return $this->getDefaultDay();
    }

    public function getNickname(){
        return 'DoorStep';
    }

    public function getUsersList(){
        $aUsers =  UserQuery::getAllDoorStepUsers( $this );

        $return = [];
        foreach ( $aUsers as $oUser )
            $return[$oUser->getId()] = $oUser->getFullName();

        return $return;
    }
}
