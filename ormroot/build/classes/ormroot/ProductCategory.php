<?php



/**
 * Skeleton subclass for representing a row from the 'product_categories' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class ProductCategory extends BaseProductCategory
{
    function getBags($date){

        $date = DateTime::createFromFormat('m-d-Y', $date)->format('Y-m-d');
        return BagsQuery::create()
            ->filterByDate($date)
            ->useProductQuery()
                ->useProductCategoryQuery()
                    ->filterById($this->getId())
                ->endUse()
            ->endUse()
            ->find();
    }

    function getItemss($all = false){
        if(!$all)
            return parent::getItemss();

        return ItemsQuery::create()
                    ->filterByActive(true)
                    ->filterByCategoryId( $this->getId() )
                    ->find();
    }

    function getNonSecondaryItems(){
        return ItemsQuery::create()
                ->filterByActive( true )
                ->filterByCategoryId( $this->getId() )
                ->filterBySecondary( false )
                ->find();
    }

    function getPublishedProducts(){
        return ProductQuery::create()
            ->filterByIsPublished(true)
            ->filterByProductCategory( $this )
            ->find();
    }
}
