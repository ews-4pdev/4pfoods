<?php



/**
 * Skeleton subclass for performing query and update operations on the 'product_categories' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class ProductCategoryQuery extends BaseProductCategoryQuery
{
    static function getList() {

        $return = array();
        $aStates = self::create()
            ->filterByIsPublished(true)
            ->orderByTitle('ASC')
            ->find();
        foreach ($aStates as $oState)
            $return[$oState->getId()] = $oState->getTitle();
        return $return;

    }

    static function getAllCatWithProducts(){
        $product = ProductQuery::create()
                    ->filterByIsPublished( true )
                    ->groupByCategoryId()
                    ->select('category_id')
                    ->find()->toArray();

       return ProductCategoryQuery::create()
            ->filterById( $product )
            ->filterByIsPublished(true)
            ->orderBy('title', 'Asc')
            ->find();
    }

}
