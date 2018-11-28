<?php



/**
 * Skeleton subclass for performing query and update operations on the 'items' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class ItemsQuery extends BaseItemsQuery
{
    static function getItems(){
        $aItems = ItemsQuery::create()
                    ->orderByUpdatedAt('DESC')
                    ->find();
        return $aItems;
    }

    static function getJsonItems()
    {
        $aCats = ProductCategoryQuery::create()->find();
        $data = [];
        foreach ($aCats as $cat) {
            $items = $cat->getNonSecondaryItems();
            foreach ($items as $item) {
                foreach ($item->getItemsPoints() as $itemsPoint) {
                    $data[$item->getProductCategory()->getTitle()][] = [
                        'Item' => $item->getName(),
                        'Supplier' => $item->getSuppliersName(),
                        'Product' => $itemsPoint->getProduct()->getTitle(),
                        'Points' => $itemsPoint->getPoints(),
                        'DT_RowId' => "tr-" . $itemsPoint->getId()
                    ];
                }
            }
        }

        return $data;
    }

    static function getSecondaryItems($productId){
        return ItemsQuery::create()
            ->filterByActive(true)
            ->filterBySecondary(true)
            ->useItemsPointQuery()
                ->filterByProductId($productId)
            ->endUse()
            ->joinWith('Items.ItemsPoint')
            ->select(['ItemsPoint.id', 'ItemsPoint.points'])
            ->find();
    }
}
