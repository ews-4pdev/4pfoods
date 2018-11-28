<?php



/**
 * Skeleton subclass for representing a row from the 'items' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class Items extends BaseItems
{
    function addSuppliersArray($input, Items $I){
        if( empty($I) ){return false;}

        ItemSupplier::deleteBeforeUpdate($I);

        if(is_array($input) && !empty($input))
        {
            foreach ($input as $key => $value) {
                ItemSupplier::saveSingleRow($value, $I->getID());
            }

        }
        return true;
    }

    function addItemPoints($input, Items $I){
        if( empty($I) ){return false;}

        if(is_array($input) && !empty($input))
        {
          if( Items::getItemsPoints()->count() > 0 ){
              foreach (Items::getItemsPoints() as $aItemPoint) {
                  $aItemPoint->setPoints($input[$aItemPoint->getProductId()])->save();
                }
          }else{
              foreach ($input as  $key => $value) {
                  (new ItemsPoint())
                      ->setPoints($value)
                      ->setItemId($I->getId())
                      ->setProductId($key)
                      ->save();
              }
          }

        }
        return false;
    }

    public function getSuppliersName(){

        $supplier_name = null;
        foreach ($this->getItemSuppliers() as $itemSupplier)
        {
            $supplier_name = (empty($supplier_name)) ? $itemSupplier->getSuppliers()->getName() : $itemSupplier->getSuppliers()->getName().','.$supplier_name;
        }
        return $supplier_name;
    }

    public function validate($input = null)
    {
        $error = [];
        if( !$input['Name'] )
                $error[] = 'Name Required.';
        if( !isset( $input['suppliers'] ) || !$input['suppliers'] )
            $error[] = 'Suppliers Required.';
        if( !$input['CategoryId'] || $input['CategoryId'] == 'Select Category' ){
            $error[] = 'Category Required.';
        }elseif( isset($input['size']) || !empty( $input['size'] ) ){
                // Check if Category ID is valid
                $category = ProductCategoryQuery::create()->findPk( $input['CategoryId'] );
                if( !$category ){ $error[] = 'Invalid Category Provided.';  return $error; }

                // Get all products related to this category
                $allProducts = ProductQuery::getProductsWithCategory( $input['CategoryId'] );

                if( count( $allProducts ) == count( $input['size'] ) ){
                    foreach( $allProducts  as $value ){
                        if( !$input['size'][$value['Id']] )
                            $error[] = $value['Title'].' Required.';
                        else if( !is_int((int) $input['size'][$value['Id']]) || (int) $input['size'][$value['Id']] < 1 ){
                            $error[] = $value['Title'].' must be a number and must be above 0.';
                        }

                    }
                }else{
                    $error[] = 'All Product sizes needed to be filled.';
                    return $error;
                }
        }else{
            $error[] = 'Selected category has no product defined. Please add at least one product for this category.';
        }
        return $error;
    }
}
