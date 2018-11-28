<?php



/**
 * Skeleton subclass for performing query and update operations on the 'captured_emails' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class CapturedEmailQuery extends BaseCapturedEmailQuery {

  static function getEmailsFromDate($date) {

    $start = $date.' 00:00:00';
    $end = $date.' 23:59:00';
    return self::create()
      ->filterByCreatedAt(array('min' => $start, 'max' => $end))
      ->find();

  }

} // CapturedEmailQuery
