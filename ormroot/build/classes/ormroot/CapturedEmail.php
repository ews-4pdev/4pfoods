<?php



/**
 * Skeleton subclass for representing a row from the 'captured_emails' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class CapturedEmail extends BaseCapturedEmail {

  function isSignedUp() {

    $n = UserQuery::create()
      ->filterByEmail($this->getEmail())
      ->count();
    return ($n > 0);

  }

  function markSent() {

    $this->setFollowupSentAt(date('Y-m-d H:i:s'));
    $this->save();

  }

} // CapturedEmail
