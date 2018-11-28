<?php



/**
 * Skeleton subclass for representing a row from the 'cron_logs' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class CronLog extends BaseCronLog {

  function logError($error, $printout = NULL) {

    $this->setIsSuccess(0);
    $this->setErrorCode($error);
    $this->setPrintout($printout);
    $this->save();

  }

  function logSuccess($printout = NULL) {

    $this->setIsSuccess(1);
    $this->setPrintout($printout);
    $this->save();

  }

} // CronLog
