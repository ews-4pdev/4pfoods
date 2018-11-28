<?php



/**
 * Skeleton subclass for performing query and update operations on the 'users' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ormroot
 */
class UserPeer extends BaseUserPeer {

  static function authenticate($email, $password, $returnObj = false) {
    $oUser = UserQuery::create()->filterByEmail($email)->findOne();
    if (!$oUser)
      return false;
    if (!$oUser->getPassword()) {
      if (sha1(SALT.$password) != $oUser->getLegacyPassword())
        return false;
      $hash = self::getHash($password);
      $oUser->setPassword($hash);
      $oUser->save();
    } else {
      if (!self::checkHash($password, $oUser->getPassword()))
        return false;
    }
    return ($returnObj) ? $oUser : true;
  }

  static function getHash($password) {
      
    return sha1(SALT.$password);

    require_once(APPPATH.'third_party/phpass/library/Phpass.php');
    $pw = new \Phpass\Hash;
    $hash = $pw->hashPassword($password);
    return $hash;
                          
  }

  static function checkHash($password, $hash) {
                                    
    return (self::getHash($password) == $hash);
                                                            
  }

} // UserPeer
