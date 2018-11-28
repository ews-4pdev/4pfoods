<?php



/**
 * Skeleton subclass for representing a row from the 'user_bags' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class UserBag extends BaseUserBag
{
    /**
     * Create User Bags Based on Date. If bags already exist with specific date and product id
     * it will return -1 otherwise create bag and return bag id.
     *
     *
     * @param Subscription $subscription
     * @param Bags $bag
     *
     *
     * @param null $changedProductId
     * @return array
     * @throws PropelException
     */
    function createBagWithItems(Subscription $subscription, Bags $bag, $changedProductId = null)
    {
        $userBag =  UserBagQuery::create()
            ->filterByUserId($subscription->getUserId())
            ->filterBySubscriptionId($subscription->getId())
            ->filterByDate( $bag->getDate() )
            ->findOneOrCreate();

        $userBag->setProductId( $subscription->getProductId( $bag->getDate() ) );
        if( $changedProductId ){
            $userBag->setProductId( $changedProductId );
        }
        $userBag
            ->setStatus('Pending')
            ->setTotalPoints( $bag->getTotalPoints() )
            ->save();


        UserBagItem::createBagsItem($userBag->getId(), $bag->getBagsItems());
        return $userBag;
    }

    public function getAdminBag(){
        return BagsQuery::create()
            ->filterByDate($this->getDate())
            ->filterByProductId($this->getProductId())
            ->findOne();
    }

    public function getDeletedItems(){
        return UserBagItemArchiveQuery::getDeletedItems($this->getId());
    }

    public function getAvailableBags(){
        $selectedCategory = BagsQuery::create()
            ->filterByProductId($this->getProductId())
            ->filterByDate( $this->getDate() )
            ->findOne();

        $return = [];
        if($selectedCategory){
            $bags = BagsQuery::create()
                ->filterByDate($this->getDate())
                ->useProductQuery()
                ->filterByCategoryId($selectedCategory->getProduct()->getCategoryId())
                ->endUse()
                ->where('Product.id != ?', $this->getProductId())
                ->orderBy('id', 'ASC')
                ->find();

            foreach ($bags as $bag){
                $return[$bag->getId()] = $bag->getProduct()->getTitle();
            }
        }


        return $return;
    }

    public function deleteBagWithItems(){
        UserBagItemQuery::create()->filterByBagId( $this->getId() )->delete();
        $this->delete();
    }

    public function getActiveItems(){
        return UserBagItemQuery::create()
                    ->filterByBagId( $this->getId() )
                    ->filterByStatus('Active')
                    ->find();
    }

    public function getTotalQuantity(){
        $total =  UserBagItemQuery::create()
                    ->filterByBagId( $this->getId() )
                    ->filterByStatus('Active')
                    ->withColumn( 'SUM(quantity)', 'totalQuantity' )
                    ->select('totalQuantity')
                    ->find();

        return $total[0];
    }
}
