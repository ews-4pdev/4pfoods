<?php



/**
 * Skeleton subclass for performing query and update operations on the 'bags_type' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class BagsTypeQuery extends BaseBagsTypeQuery
{
    static function getList() {

        $return = array();
        $aStates = self::create()
            ->orderByType('ASC')
            ->find();
        foreach ($aStates as $oState)
            $return[$oState->getId()] = $oState->getType();
        return $return;

    }
}
