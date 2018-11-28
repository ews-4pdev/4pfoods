<?php



/**
 * Skeleton subclass for representing a row from the 'bags_item' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class BagsItem extends BaseBagsItem
{
    function saveAlwaysAvailableItems($items, $bag_id){
        foreach ($items as $item) {
           $new =  BagsItemQuery::create()
                    ->filterByBagId($bag_id)
                    ->filterByPointId($item['ItemsPoint.id'])
                    ->filterByStatus('Secondary')
                    ->filterByPoints($item['ItemsPoint.points'])
                    ->findOneorCreate();

            if($new->isNew()){
                $new->save();
            }
        }
        return true;
    }
}
