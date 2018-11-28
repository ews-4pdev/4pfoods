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
class UserBags extends BaseUserBags
{

    /**
     * Create User Bags Based on Date. If bags already exist with specific date and product id
     * it will return -1 otherwise create bag and return bag id.
     *
     *
     * @return integer - 0 / 1
     *
     */
    function createBag($pId, $uId, $date)
    {
        $id =  UserBagsQuery::create()
                    ->filterByProductId($pId)
                    ->filterByUserId($uId)
                    ->filterByDate($date)
                    ->findOneOrCreate();
        $id->save();
        return $id->getId();
    }
}
