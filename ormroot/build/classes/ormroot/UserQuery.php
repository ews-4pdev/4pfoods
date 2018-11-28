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
class UserQuery extends BaseUserQuery {

  /**
   *  Verify access without pulling entire User object
   *  NOTE: This can be refactored to be more efficient
   */
  public static function adminAccess($iUser) {
    $oUser = self::create()->findPK($iUser);
    return $oUser->getAdminAccess();

  }

  public static function getAdmins() {

    return self::create()
      ->filterByAdminAccess('SuperAdmin')
      ->find();

  }

  public static function getRecentSignups() {

    return self::create()
      ->filterByAdminAccess(NULL)
      ->filterByCreatedAt(array('min' => date('Y-m-d H:i:s', strtotime('-30 Days'))))
      ->orderByCreatedAt('DESC')
      ->find();

  }

  /**
   *  Get count of all customers
   */
  public static function getTotalCustomers() {

    return self::create()
      ->filterByAdminAccess(NULL)
      ->count();

  }

  /**
   *  Get all users with Driver access privileges
   */
  public static function getDrivers() {
    
    return self::create()
      ->filterByAdminAccess('Driver')
      ->orderByLastName('ASC')
      ->find();

  }

  /**
   *  Get all users with a pending delivery on the
   *  passed date
   */
  public static function getWithPendingDeliveryOn($date) {

    return self::create()
      ->useSubscriptionQuery()
        ->useOrderQuery()
          ->filterByStatus('Pending')
          ->filterByDeliveryScheduledFor($date)
        ->endUse()
      ->endUse()
      ->groupBy('User.Id')
      ->find();

  }

  /**
   *  Get all users with addresses pending approval
   */
  public static function getPendingAddresses() {
    return self::create()
      ->filterByCustomerStatus('AddressPending')
      ->find();

  }

  public static function getUsersWithChangedBags($date){
    $date = DateTime::createFromFormat('m-d-Y', $date);
    if(!$date){ return false; }

    return self::create()
                  ->useSubscriptionQuery()
                    ->filterByDeliveryDay( $date->format('D') )
                    ->useUserBagQuery()
                        ->filterByIsChanged(true)
                        ->filterByDate($date)
                    ->endUse()
                  ->endUse()
                  ->orderBy('id')
                  ->find();
  }

  public static function createCsvOfChangedBag($date){
    $date = DateTime::createFromFormat('m-d-Y', $date)->format('Y-m-d');
    if(!$date){ return false; }

    $userBags = UserBagQuery::create()
                ->filterByDate($date)
                ->filterByIsChanged(true)
                ->orderBy('id')
                ->find();

    //create CSV  data and save it on server.
    $fileName = 'changedBags.csv';
    //create file
    $file = fopen('temp/'.$fileName, 'w');

    //Insert CSV data
    fputcsv($file, ['Date', 'Customer Name', 'Email',  'Subscription Type', 'Item Name', 'Points', 'Quantity']);
    foreach ($userBags   as $bag) {
      foreach ($bag->getUserBagItems() as $userBagItem) {
        if( $userBagItem->getIsDeleted() == 0 && $userBagItem->getStatus() == 'Active' ){
          fputcsv($file, [
              $bag->getDate('d F Y'),
              $bag->getSubscription()->getUser()->getFullName(),
              $bag->getSubscription()->getUser()->getEmail(),
              $bag->getProduct()->getTitle(),
              $userBagItem->getItemsPoint()->getItems()->getName(),
              $userBagItem->getPoints(),
              $userBagItem->getQuantity()
          ]);
        }
      }
      fputcsv($file, []);
    }
    fclose($file);


    return $fileName;
  }

  public static function getUsersForBag( Bags $aBag, $is_changed ){
    if(!$is_changed){
      return UserQuery::create()
          ->useSubscriptionQuery()
          ->useUserBagQuery()
          ->filterByProductId( $aBag->getProductId()  )
          ->filterByDate( $aBag->getDate() )
          ->endUse()
          ->endUse()
          ->find();
    }

    return UserQuery::create()
        ->useSubscriptionQuery()
          ->useUserBagQuery()
            ->filterByProductId( $aBag->getProductId()  )
            ->filterByDate( $aBag->getDate() )
            ->filterByIsChanged(true)
          ->endUse()
        ->endUse()
        ->find();

  }

  public static function getAllDoorStepUsers( DoorStep $oDoorStep )
  {
    return self::create()
            ->filterByZip( $oDoorStep->getZip() )
            ->filterByDoorstep(1)
            ->filterByCustomerStatus('Active')
            ->filterByIsArchived(0)
            ->filterByIsConfirmed(1)
            ->find();
  }

} // UserQuery
