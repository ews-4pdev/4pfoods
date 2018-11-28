<?php



/**
 * Skeleton subclass for performing query and update operations on the 'products' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class ProductQuery extends BaseProductQuery
{
    public static function getProductsWithCategory( $category ){
      return self::create()
            ->filterByCategoryId( $category )
            ->filterByIsPublished(true)
            ->select(['Id', 'Title'])
            ->find()
            ->toArray();
    }

    public static function getAllNames(){
        return self::create()
                    ->filterByIsPublished(true)
                    ->orderByCategoryId('ASC')
                    ->find();
    }
}
