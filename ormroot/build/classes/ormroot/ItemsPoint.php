<?php



/**
 * Skeleton subclass for representing a row from the 'items_point' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class ItemsPoint extends BaseItemsPoint
{
    function deleteBeforeUpdate(Items $item){
        if( $item->countItemsPoints() )
        {
            foreach ($item->getItemsPoints() as $point) {
                $point->delete();
            }
        }
    }

    function preSave(PropelPDO $con = null)
    {
        if($this->getId())
        {
            if( in_array( 'items_point.points', $this->getModifiedColumns() ) )
            {
                ( new PointsVersion() )->setPointId($this->getId())
                    ->setPoints($this->getPoints())
                    ->save();
            }
        }
        return true;
    }
}
