<?php



/**
 * Skeleton subclass for representing a row from the 'discounts' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class Discount extends BaseDiscount {

  function isPublished() { return (bool)($this->getIsPublished()); }

  /**
   *  Has the discount been used to maximum capacity?
   */
  function isExpiredForUser($iUser) {

    $nUses = OrderQuery::create()
      ->useSubscriptionQuery()
        ->filterByUserId($iUser)
      ->endUse()
      ->filterByDiscountId($this->getId())
      ->count();
    
    return ($nUses >= $this->getOrdersAffected());

  }

  function togglePublish() {

    $newState = ($this->isPublished()) ? 0 : 1;
    $this->setIsPublished($newState);
    $this->save();

  }

} // Discount
