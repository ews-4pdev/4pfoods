<?php



/**
 * Skeleton subclass for performing query and update operations on the '_states' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class StateQuery extends BaseStateQuery {

  static function getList() {

    $return = array();
    $aStates = self::create()
      ->orderByName('ASC')
      ->find();
    foreach ($aStates as $oState)
      $return[$oState->getId()] = $oState->getName();
    return $return;

  }
  
  static function getTaxList() {

    $return = array();
    $aStates = self::create()
      ->orderByName('ASC')
      ->find();
    foreach ($aStates as $oState)
      $return[$oState->getId()] = $oState->getTax();
    return $return;

  }

} // StateQuery
