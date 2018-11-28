<?php



/**
 * Skeleton subclass for performing query and update operations on the 'user_bags' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class UserBagsQuery extends BaseUserBagsQuery
{
    /**
     * @param $date
     * @param $productId
     * @return bool
     */
    static function bagExist($date, $productId)
    {
        return self::create()
                ->filterByDate($date)
                ->filterByProductId($productId)
                ->exists();
    }

    static function getBags($id)
    {
        if(empty($id)){return false;}

        $aBags = UserBagsQuery::create()
            ->filterByUserId($id)
            ->joinWith('UserBags.Product', Criteria::LEFT_JOIN)
            ->joinWith('Product.ProductCategory', Criteria::LEFT_JOIN)
            ->joinWith('UserBags.UserBagItem', Criteria::LEFT_JOIN)
            ->joinWith('UserBagItem.Items', Criteria::LEFT_JOIN)
            ->joinWith('Items.ItemSupplier', Criteria::LEFT_JOIN)
            ->joinWith('ItemSupplier.Suppliers', Criteria::LEFT_JOIN)
            ->setFormatter('PropelArrayFormatter')
            ->orderBy('id')
            ->find();

        return $aBags;
    }
}
