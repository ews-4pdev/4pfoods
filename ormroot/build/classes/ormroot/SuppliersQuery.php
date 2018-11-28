<?php



/**
 * Skeleton subclass for performing query and update operations on the 'suppliers' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class SuppliersQuery extends BaseSuppliersQuery
{
    static function getList() {

        $return = array();
        $aStates = self::create()
            ->filterByActive(1)
            ->orderByName('ASC')
            ->find();
        foreach ($aStates as $oState)
            $return[$oState->getId()] = $oState->getName();
        return $return;

    }

    public function getSuppliersByStatus($status = false){
        return $this->create()
                    ->filterByActive($status)
                    ->orderById( Criteria::DESC )
                    ->find();
    }
}
