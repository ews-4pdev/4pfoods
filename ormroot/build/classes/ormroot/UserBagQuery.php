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
class UserBagQuery extends BaseUserBagQuery
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


    /**
     * Get Secondary Items from Admin bag.
     *
     *
     */
    static function getSecondary($productId, $date){

    }


    static function setIsChanged( $date, $customerId ){
       $uBags =  self::create()
                    ->filterByDate( $date )
                    ->filterByUserId( $customerId )
                    ->find();

        foreach ($uBags as $uBag) {
            $uBag->setIsChanged(0)->save();
        }
    }

    function getOrderedItemsFromRange($input, $status = 'Pending'){
        $dateFrom = $input['start'];
        $dateTo = $input['end'];

        if(!$dateFrom || !$dateTo){return false;}


//    $orders = OrderQuery::create()
//              ->filterByStatus($status)
//              ->filterByDeliveryScheduledFor(['min' => $dateFrom, 'max' => $dateTo])
//              ->select('user_bag_id')
//              ->find();

        $aBags = UserBagQuery::create()
                ->filterByDate(['min' => $dateFrom, 'max' => $dateTo])
                ->select('id')
                ->find();

        $items = UserBagItemQuery::create()
                ->where('user_bag_item.bag_id IN ?', $aBags)
                ->where('user_bag_item.status = ?', 'Active')
                ->find();


        $arr = [];
        foreach ($items as $key => $value) {
            $name = null;
            $item_name = $value->getItemsPoint()->getItems()->getId();
            if( !isset($arr[ $item_name ]) )
                $arr[ $item_name ] = [
                    'item_name' => '',
                    'points'    => 0
                ];

            $arr[ $item_name ]['item_name'] =  $value->getItemsPoint()->getItems()->getName();
            $arr[ $item_name ]['points'] = $arr[ $item_name ]['points'] + ( $value->getQuantity() * $value->getPoints() );
        }
        return $arr;
    }
}
