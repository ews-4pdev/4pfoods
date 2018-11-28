<?php



/**
 * Skeleton subclass for performing query and update operations on the 'bags' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class BagsQuery extends BaseBagsQuery
{
    static function getBagsWithDate($date)
    {
        if(empty($date)){return false;}
        return ProductCategoryQuery::create()->filterByIsPublished(true)->find();

    }

    static function bagExistWithDate($date, $productId){
        return self::create()
            ->filterByDate($date)
            ->filterByProductId($productId)
            ->find()
            ->count();
    }

    public static function arePublished(DateTime $date){
        $ret['published'] = BagsQuery::create()
                ->filterByDate($date->format('Y-m-d'))
                ->filterByIsPublished(true)
                ->count();

        $ret['total'] = ProductQuery::create()->count();

        $ret['sync'] = BagsQuery::create()
            ->filterByDate($date->format('Y-m-d'))
            ->filterBySync(true)
            ->count();

        $ret['lock'] = BagsQuery::create()
            ->filterByDate($date->format('Y-m-d'))
            ->filterByLocked(true)
            ->count();

        return $ret;
    }

    static function publishBagsForActiveCustomers($date = null, $status = null){
        $bags = self::getAdminBagsReadyForPublish( $date );
        $subscriptions = SubscriptionQuery::getAllSubscriptionsWithDate( $date, 'Active' );
        $users = [];
        $f_email = false;
        $s_email = false;
        foreach ( $bags as $bag) {

            if( $bag->getIsPublished(true) && $bag->getSync(true) ){
                continue;
            }
            $date = DateTime::createFromFormat('Y-m-d', $bag->getDate('%Y-%m-%d'));
            foreach ($subscriptions  as $subscription ) {
                if( $subscription->hasOrderOnDate( null, $date ) )
                        continue;
                $id = $subscription->getUserId();
                $uBag = null;
                if( $subscription->getProductId($date) ==  $bag->getProductId() ){

                    $uBag = ( new UserBag() )->createBagWithItems($subscription, $bag);

                    if( $bag->getFEmail() == false ){
                        if( !isset($users[$id]) ){
                            $users[$id] = [
                                'id' => $id,
                                'email' => 'first'
                            ] ;
                        }
                        $users[$id]['title'][] = $uBag->getProduct()->getTitle();
                        $f_email = true;

                    }elseif( $bag->getFEmail() == true && $bag->getSEmail() == false ){
                        if( $uBag->getIsChanged(true) ){
                            if( !isset($users[$id]) ){
                                $users[$id] = [
                                    'id' => $id,
                                    'email' => 'second'
                                ] ;
                            }
                            $users[$id]['title'][] = $uBag->getProduct()->getTitle();
                            $s_email = true;
                        }
                    }
                }
            }
            if($f_email){
                $bag->setFEmail(true);
            }elseif($s_email){
                $bag->setSEmail(true);
            }
            $bag->setIsPublished(true)
                ->setSync(true)
                ->save();
            $f_email = $s_email = false;
        }
        return $users;
    }

    public function getAdminBagsReadyForPublish($date){
       return BagsQuery::create()
            ->filterByDate($date)
            ->find();

    }

    public static function getCurrentMonthBagsDates(){
        $tt = self::create()
            ->withColumn('MONTH(date)', 'month')
            ->withColumn('YEAR(date)', 'year')
            ->select(['date', 'month', 'year'])
            ->groupBy('date')
            ->find();
        $retArray = [];
        foreach ($tt as $item){
            $retArray[ $item['year'] ][ $item['month'] ][date('j', strtotime($item['date']) )] = $item['date'];
        }
        return $retArray;
    }

    /**
     * @return PropelObjectCollection
     */
    public static function getBagsForCron(){
        $days = OrderPeer::CHARGE_DAYS_BEFORE;

        return self::create()
                    ->filterByIsPublished(true)
                    ->filterBySync(true)
                    ->filterByLocked( false )
                    ->filterByDate( ['min' => date('Y-m-d H:i:s', strtotime("+$days days")) ] )
                    ->find();
    }

    public static function adjustTotalPoints( $bagId ){
        $total_points = BagsItemQuery::create()
                ->filterByBagId( $bagId )
                ->filterByStatus( 'Primary' )
                ->withColumn('SUM(points)', 'total')
                ->select('total')
                ->find();

        if( $total_points[0] ){
            BagsQuery::create()
                ->findPk( $bagId )
                ->setTotalPoints( $total_points[0] )
                ->save();
            return $total_points[0];
        }
        return 0;
    }

    public function createBags( DateTime $date ){
        $products = ProductQuery::create()->filterByIsPublished(1)->select(['Id'])->find();
        foreach ($products as $product) {
            $newBag = BagsQuery::create()->filterByProductId($product)->filterByDate($date->format('Y-m-d'))->findOneOrCreate();
            if($newBag->isNew())
            {
                if( $newBag->save() ){
                    $newBag->addAlwaysAvailableItems();
                }
            }
        }
    }
}