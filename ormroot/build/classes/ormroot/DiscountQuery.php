<?php



/**
 * Skeleton subclass for performing query and update operations on the 'discounts' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class DiscountQuery extends BaseDiscountQuery {

  static function getListForUser($iUser) {

    return self::create()
      ->useXDiscountsUsersQuery()
        ->filterByUserId($iUser)
        ->orderByCreatedAt('DESC')
      ->endUse()
      ->groupBy('Discount.Id')
      ->orderByAmount('DESC')
      ->find();

  }

} // DiscountQuery
