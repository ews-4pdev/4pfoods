<?php



/**
 * Skeleton subclass for representing a row from the 'item_supplier' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class ItemSupplier extends BaseItemSupplier
{
    public static function saveSingleRow($supplierId, $itemId){
        ( new ItemSupplier() )->setItemId($itemId)->setSupplierId($supplierId)->save();
    }

    public static function deleteBeforeUpdate(Items $item){
        if( $item->countItemSuppliers() )
        {
            foreach ($item->getItemSuppliersJoinSuppliers() as $supplier) {
                $supplier->delete();
            }
        }
    }

}
