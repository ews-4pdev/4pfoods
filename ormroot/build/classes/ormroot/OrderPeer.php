<?php



/**
 * Skeleton subclass for performing query and update operations on the 'orders' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class OrderPeer extends BaseOrderPeer {

  //  Orders are created from subscriptions X days before delivery date
  const CHARGE_DAYS_BEFORE = 2;

  //  Maximum number of orders that can be skipped at once
  const MAX_SKIP_ORDERS = 4;

  //  An order is donated for every Xth signup
  const AUTOMATIC_DONATE_FREQUENCY = 10;

  /**
   *  Determines if the passed in date is X days or less
   *  from the next @weekday
   */
  static function isWithinRangeOfWeekday($date, $weekday) {

    $nextWeekday = dateOfNextWeekday($date, $weekday);
    $daysEllapsed = (strtotime($nextWeekday) - time()) / (60 * 60 * 24);
    return ($daysEllapsed <= self::CHARGE_DAYS_BEFORE);

  }

} // OrderPeer
