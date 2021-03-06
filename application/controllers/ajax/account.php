<?php

require_once(APPPATH.'core/AJAX_Controller.php');
require_once(APPPATH.'helpers/StripeHelper.php');
require_once(APPPATH.'helpers/SendGridHelper.php');

class Account extends AJAX_Controller {

  function __construct() {

    parent::__construct();

    if (!SKIP_LOGIN) {
      if (!$this->isLoggedIn() || $this->session->userdata('uid') != $_POST['iUser'])
        throwSingleError('InvalidCredentials');
    }

  }

  /**
   *  Get list of products, given category
   */
  function getProducts($input) {

    $cat = ProductCategoryQuery::create()->findPK($input['iCategory']);
    
    if (!$cat)
      throwSingleError('InvalidCategory');

    $aProducts = $cat->getPublishedProducts();
    $return = array();
    foreach ($aProducts as $oProduct) {
      $return[] = array(
        'id'      => $oProduct->getId(),
        'size'    => $oProduct->getSize(),
        'title'   => $oProduct->getTitle(),
        'price'   => money($oProduct->getPrice())
      );
    }

    jsonSuccess(array('products' => $return));

  }

  /**
   *  Update Stripe customer profile with new token
   */
  function updateBilling($input) {

    $oUser = UserQuery::create()->findPK($input['iUser']);

    if (!$oUser)
      throwSingleError('InvalidUser');

    $helper = new StripeHelper();
    $helper->updateCustomerBilling($oUser, $input['token']);

    jsonSuccess(array('url' => '/account/billing'));

  }

  /**
   *  Change password
   */
  function newPassword($input) {

    $oUser = UserQuery::create()->findPK($input['iUser']);

    //  Check for valid user
    if (!$oUser)
      throwSingleError('InvalidUser');

    //  Check that old password matches
    if (!UserPeer::authenticate($oUser->getEmail(), $input['OldPassword']))
      throwSingleError('InvalidOldPassword:OldPassword');

    //  Check that new password is not empty
    if (!(bool)($input['NewPassword']))
      throwSingleError('InvalidPassword:NewPassword');

    //  Check that passwords match
    if ($input['NewPassword'] != $input['ConfirmPassword'])
      throwSingleError('PasswordMismatch:ConfirmPassword');

    //  We're good now! Set it and save
    $oUser->setPassword(UserPeer::getHash($input['NewPassword']));
    $oUser->save();

    jsonSuccess(array('url' => $input['returnURL']));

  }

  /**
   *  Edit basic information
   */
  function editProfile($input) {

    $oUser = UserQuery::create()->findPK($input['iUser']);

    //  Check for valid user
    if (!$oUser)
      throwSingleError('InvalidUser');

    //  Propel validation
    $oUser->fromArray($input);
    if (!$oUser->validate())
      throwErrors(formatErrorObject($oUser));

    //  Check required fields separately
    /**
    $errors = array();
    foreach (User::getRequiredFields() as $field) {
      if (empty($input[$field]))
        $errors[] = 'RequiredFieldMissing:'.$field;
    }
    if (count($errors) > 0)
      throwErrors($errors);
    */

    $oUser->save();

    jsonSuccess(array('url' => $input['returnURL']));

  }

  /** 
   *  Remove a skipped order
   */
  function removeSkippedOrder($input) {

    $oUser = UserQuery::create()->findPK($input['iUser']);
    $oOrder = OrderQuery::create()->findPK($input['iOrder']);

    //  Check for invalid user and order objects
    if (!$oUser)
      throwSingleError('InvalidUser');
    if (!$oOrder)
      throwSingleError('InvalidOrder');

    //  Check that order belongs to user
    if ($oOrder->getSubscription()->getUserId() != $oUser->getId())
      throwSingleError('OrderMismatch');

    //  Check that order is in skipped status
    if (!in_array($oOrder->getStatus(), array('Skipped', 'Donated')))
      throwSingleError('InvalidOrderStatus');

    //  Check that order is not within 48 hours of today
    $cutoff = date('Y-m-d', strtotime('+'.OrderPeer::CHARGE_DAYS_BEFORE.' Days'));
    if (strtotime($oOrder->getDeliveryScheduledFor()) <= strtotime($cutoff))
      throwSingleError('CannotReactivateSkipped');

    //  Store subscription for later
    $oSub = $oOrder->getSubscription();
    $date = new DateTime( $oOrder->getDeliveryScheduledFor() );

    //  We're all good! Delete it.
    $oOrder->delete();

    $oSub->createBag( $date );
    //  Send email to customer
    $merge = [
      'firstname'   => $oUser->getFirstName(),
      'product'     => $oSub->getProduct()->getTitle(),
      'date'        => date('n/d/Y', strtotime($date->format('Y-m-d')))
    ];
    $helper = new SendGridHelper($oUser->getEmail(), 'reactivate-order-user');
    $helper->merge($merge);
    $helper->send();

    jsonSuccess(array(
      'url'     => '/account/subscriptions'
    ));

  }

  function skipSingleOrder($input) {

    $input['startDate'] = $input['endDate'] = $input['date'];

    $oSub = SubscriptionQuery::create()->findPK($input['iSub']);
    
    if ($oOrder = $oSub->getOrderOnDate($input['date'])) {
      $error = (isset($input['isDonate']) && $input['isDonate'] == 'on') ? 'CannotDonate' : 'CannotSkip';
      throwSingleError($error);
    }

    $iOrder = $this->skipOrders($input, true);

    //  Send email to customer
    $oUser = UserQuery::create()->findPK($input['iUser']);
    $merge = [
      'firstname'   => $oUser->getFirstName(),
      'product'     => $oSub->getProduct()->getTitle(),
      'date'        => date('n/d/Y', strtotime($input['date']))
    ];
    $helper = new SendGridHelper($oUser->getEmail(), 'skip-order-user');
    $helper->merge($merge);
    $helper->send();

    jsonSuccess(array(
      'iOrder' => $iOrder,
       'url'   => 'account/subscriptions'
    ));

  }

  function skipAllSubscriptions($input){

    $input['startDate'] = $input['endDate'] = $input['date'];

    $oUser = UserQuery::create()->findPk( $input['iUser'] );
    if( !$oUser ){
      throwSingleError('InvalidUser');
    }


    // Get All subscriptions for this user
    $aSubs = $oUser->getSubscriptions('Active');
    $aOrders = [];
    $error = [];
    foreach ($aSubs as $oSub) {
      if ($oOrder = $oSub->getOrderOnDate($input['date'])) {
          if( $oOrder->getStatus() == 'Skipped' || $oOrder->getStatus() == 'Donated' ){
                continue;
          }
        $error[] = (isset($input['isDonate']) && $input['isDonate'] == 'on') ? 'CannotDonate' : 'CannotSkip';
        throwSingleError($error);
      }
      $input['iSub'] = $oSub->getId();

      $aOrders[] = $this->skipOrders($input, true);
    }



    //  Send email to customer
    $oUser = UserQuery::create()->findPK($input['iUser']);
    $merge = [
        'firstname'   => $oUser->getFirstName(),
        'date'        => date('n/d/Y', strtotime($input['date']))
    ];
    $helper = new SendGridHelper($oUser->getEmail(), 'skip-all-subs');
    $helper->merge($merge);
    $helper->send();

    jsonSuccess(array(
        'url'   => 'account/subscriptions'
    ));
  }

  function donateSingleOrder($input) {

    $input['startDate'] = $input['endDate'] = $input['date'];
    $input['isDonate'] = 'on';
    $iOrder = $this->skipOrders($input, true);

    $oSub = SubscriptionQuery::create()->findPk($input['iSub']);
    $oUser = UserQuery::create()->findPk($input['iUser']);
    $merge = [
      'firstname'   => $oUser->getFirstName(),
      'product'     => $oSub->getProduct()->getTitle(),
      'date'        => date('n/d/Y', strtotime($input['date']))
    ];
    $helper = new SendGridHelper($oUser->getEmail(), 'donate-order-user');
    $helper->merge($merge);
    $helper->send();

    jsonSuccess(array(
      'iOrder' => $iOrder,
      'url'    => 'account/subscriptions'
    ));

  }

  function skipOrders($input, $returnOrderID = false) {

    $oUser = UserQuery::create()->findPK($input['iUser']);
    $oSub = SubscriptionQuery::create()->findPK($input['iSub']);

    //  Donating or skipping?
    $isDonate = (isset($input['isDonate']) && $input['isDonate'] == 'on');

    //  Check for invalid user and product objects
    if (!$oUser)
      throwSingleError('InvalidUser');
    if (!$oSub)
      throwSingleError('InvalidSubscription');

    //  Check that subscription belongs to user
    if ($oSub->getUserId() != $oUser->getId())
      throwSingleError('SubscriptionMismatch');

    //  Check for both starting and ending dates
    if (!$input['startDate'] || !$input['endDate'])
      throwSingleError('NoDates');

    //  Check that end date is before or equal to start date
    if (strtotime($input['startDate']) > strtotime($input['endDate']))
      throwSingleError('InvalidDateRange');
    
    //  Starting from date of first order in range, skip orders until endDate
    $firstOrderDate = $oSub->getNextDeliveryDateFrom($input['startDate']);
    $current = $firstOrderDate;
    $notice = '';
    $orderCount = 0;
    while (strtotime($current) <= strtotime($input['endDate'])) {

      //  Check for existing order; if there is, continue with notice
      $oOrder = $oSub->getOrderOnDate($current);
      if ($oOrder) {
        $notice = 'ExistingOrderIgnored';
        $current = date('Y-m-d', strtotime($current.' +1 Week'));
        continue;
      }

      //  Check for startDate that is less than X days from now; should
      //  only be tripped if the user just signed up or uses a date in the past
      $daysAfterToday = (strtotime($current) - time()) / (24 * 60 * 60);
      if ($daysAfterToday < OrderPeer::CHARGE_DAYS_BEFORE)
        throwSingleError('OutOfRangeDate');

      //  We're all good now. Create the order
      $oOrder = new Order();
      $fn = ($isDonate) ? 'donate' : 'skip';
      $oOrder->setSubscriptionId( $oSub->getId() );
      $oOrder->$fn();
      $oOrder->setDeliveryScheduledFor($current);
      $oOrder->setDeliverySiteId( $oSub->getDeliverySiteId() );
	  
	  if ($isDonate)
	  {
		$oOrder->setPrice($oSub->getPricePaid( $oOrder->getDeliveryScheduledFor() ));
	  }
      $oOrder->save();

      // Delete User Bag
      $oSub->deleteUserBag( new DateTime($current) );
      $oSub->save();
      $orderCount++;
      $current = date('Y-m-d', strtotime($current.' +1 Week'));

      //  This option should only be used when function is skipping a single order
      if ($returnOrderID)
        return $oOrder->getId();

    }

    if ($orderCount == 0)
      throwSingleError('NoOrders');

    jsonSuccess(array('url' => '/account/subscriptions'));

  }

  /**
   *  Stop a subscription, change to Canceled status
   */
  function stopSubscription($input) {

    $oUser = UserQuery::create()->findPK($input['iUser']);
    $oSub = SubscriptionQuery::create()->findPK($input['iSub']);

    //  Check for invalid user and product objects
    if (!$oUser)
      throwSingleError('InvalidUser');
    if (!$oSub)
      throwSingleError('InvalidSubscription');

    //  Make sure user has an active account and valid delivery site
    if (!$oUser->getDefaultDeliverySite() || $oUser->getCustomerStatus() != 'Active')
      throwSingleError('UserHasAddressPending');

    //  Make sure subscription belongs to user
    if ($oSub->getUserId() != $oUser->getId())
      throwSingleError('SubscriptionMismatch');

    //  Make sure subscription isn't already canceled
    if ($oSub->getStatus() == 'Canceled')
      throwSingleError('InvalidSubscriptionStatus');

    //  If there is a pending order on this subscription,
    //  it cannot be canceled
    if ( $oSub->hasCurrentPendingOrder() )
      throwSingleError('HasPendingOrder');

    $oSub->cancel();
    $oSub->save();

    //  Send email to admins
    $aAdmins = UserQuery::create()
      ->filterByAdminAccess('SuperAdmin')
      ->find();
    foreach ($aAdmins as $oAdmin) {
      $merge = [
        'firstname' => $oAdmin->getFirstName(),
        'fullname' => $oUser->getFullName(),
        'product' => $oSub->getProduct()->getTitle(),
        'price' => money($oSub->getProduct()->getPrice())
      ];
      $helper = new SendGridHelper($oAdmin->getEmail(), 'cancel-subscription-admin');
      $helper->merge($merge);
      $helper->send();
    }

    //  Send email to user
    $merge['firstname'] = $oUser->getFirstName();
    $helper = new SendGridHelper($oUser->getEmail(), 'cancel-subscription-user');
    $helper->merge($merge);
    $helper->send();

    jsonSuccess(array('url' => '/account/subscriptions'));

  }

  /**
   *  Change a subscription from one product to another
   *  in the same category
   */
  function changeSubscription($input) {

    $oUser = UserQuery::create()->findPK($input['iUser']);
    $oSub = SubscriptionQuery::create()->findPK($input['iSub']);
    $oProduct = ProductQuery::create()->findPK($input['iProduct']);

    //  Check for invalid user and product objects
    if (!$oUser)
      throwSingleError('InvalidUser');
    if (!$oSub)
      throwSingleError('InvalidSubscription');
    if (!$oProduct)
      throwSingleError('InvalidProduct');

    //  Make sure user has an active account and valid delivery site
    if (!$oUser->getDefaultDeliverySite() || $oUser->getCustomerStatus() != 'Active')
      throwSingleError('UserHasAddressPending');

    //  Make sure subscription belongs to user
    if ($oSub->getUserId() != $oUser->getId())
      throwSingleError('SubscriptionMismatch');

    //  Make sure new product is different from existing product
    if ($oSub->getProductId() == $oProduct->getId())
      throwSingleError('DuplicateProduct');

    //  Make sure new product has same category as old one
    if ($oSub->getProduct()->getCategoryId() != $oProduct->getCategoryId())
      throwSingleError('InvalidProductCategory');

    // Make sure its before 48 hours.
//    timeConstraint( $oSub->getDeliveryDay() );

    // Check if this subscription has any current order
    $cOrder = $oSub->hasOrderOnDate( ['Pending', 'Donated'] , $oSub->getNextDeliveryDate() );
    if( $cOrder ){
      throwSingleError('OrderExist');
    }
    $oSub->cancel();
    $oSub->save();

    //  Set up new subscription
    $oSubNew = new Subscription();
    $oSubNew->setProduct($oProduct);
    $oSubNew->setUserId( $oUser->getId() );
    $oSubNew->setDeliverySite($oUser->getDefaultDeliverySite());
    $oSubNew->setStatus('Active');
    $oSubNew->setUserId( $oUser->getId() );
    $oSubNew->save();

    $oSubNew->createBag();

    //  Send email
    $helper = new SendGridHelper($oUser->getEmail(), 'change-subscription');
    $helper->merge(array(
      'firstname'     => $oUser->getFirstName(),
      'product'       => $oProduct->getTitle(),
      'price'         => money($oProduct->getPrice())
    ));
    $helper->send();

    jsonSuccess(array('url' => '/account/subscriptions'));

  }

  /**
   *  Add a new subscription to customer's account
   */
  function addSubscription($input) {

    $oUser = UserQuery::create()->findPK($input['iUser']);
    $oProduct = ProductQuery::create()->findPK($input['iProduct']);

    //  Check for invalid user and product objects
    if (!$oUser)
      throwSingleError('InvalidUser');
    if (!$oProduct)
      throwSingleError('InvalidProduct');

    //  Make sure user has an active account and valid delivery site
    if (!$oUser->getDefaultDeliverySite() || $oUser->getCustomerStatus() != 'Active')
      throwSingleError('UserHasAddressPending');

    //  Check for existing subscription with user and product
    $nSub = SubscriptionQuery::create()
      ->filterByUserId($oUser->getId())
      ->useProductQuery()
        ->filterByCategoryId($oProduct->getCategoryId())
      ->endUse()
      ->filterByStatus('Active')
      ->count();
    if ($nSub > 0)
      throwSingleError('DuplicateSubscription');

    //  Get default delivery site to establish delivery day
    $oSite = ( $oUser->getDoorstep() ) ? DoorStepQuery::create()->findOneByZip( $oUser->getZip() )
                                          : $oUser->getDefaultDeliverySite();

    //  Create it and save it
    $oSub = new Subscription();
    $oSub->setUserId( $oUser->getId() );
    $oSub->setProductId( $oProduct->getId() )  ;
    $oSub->setPricePaid( $oProduct->getPrice() ) ;
    $oSub->setDeliveryDay( $oSite->getDefaultDeliveryDay() ) ;
    $oSub->setDeliverySiteId( $oSite->getId() );
    $oSub->setStatus('Active');
    $oSub->save();

    $oSub->createBag();

    //  Send email to customer
    $helper = new SendGridHelper($oUser->getEmail(), 'add-subscription-user');
    $helper->merge(array(
      'firstname'     => $oUser->getFirstName(),
      'email'         => $oUser->getEmail(),
    ));
    $helper->send();

    //  Email all administrators
    $aAdmins = UserQuery::create()
      ->filterByAdminAccess('SuperAdmin')
      ->find();
    foreach ($aAdmins as $oAdmin) {
      $helper = new SendGridHelper($oAdmin->getEmail(), 'add-subscription-admin');
      $helper->merge(array(
        'admin'     => $oAdmin->getFirstName(),
        'firstname' => $oUser->getFirstName(),
        'lastname'  => $oUser->getLastName(),
        'product'       => $oProduct->getTitle(),
        'email'       => $oUser->getEmail(),
        'price'         => money($oProduct->getPrice())
      ));
      $helper->send();
    }

    jsonSuccess(array('id' => $oSub->getId(), 'url' => '/account/subscriptions'));

  }
  
  function deleteItem($input) {

    $oUser = UserQuery::create()->findPk( $input['iUser'] );
    $oSub = SubscriptionQuery::create()->findPk( $input['iSub'] );

    if(!$oUser){  throwSingleError('InvalidUser');   }
    if(!$oSub){ throwSingleError('InvalidSubscription'); }
    // Does subscription belongs to this user
    if( $oUser->getId() != $oSub->getUserId() ){ throwSingleError('InvalidSubscription'); }


	  $oItem = UserBagItemQuery::create()->findPK($input['userBagItemId']);
	  if(!$oItem){jsonError('Invalid Id provided.');}

    $oItem->validateUserBagItem( $oUser, $oSub );

	 if($oItem->getSecondary() == false)
	 {
       $oItem->setStatus('Deleted')->setQuantity(1)->save();
       $oItem->bagChanged();
	  jsonSuccess(array('id' => $oItem->getId(),'Secondary' => false));
	 }elseif($oItem->getSecondary() == true)
	 {
       $item = ItemsPointQuery::create()->findPk( $oItem->getPointId() );

       $data = [
         'bagid'     => $oItem->getBagId(),
         'uid'       => $oItem->getUserBag()->getUserId(),
         'pointid'   => $oItem->getPointId(),
         'subid'     => $oItem->getUserBag()->getSubscriptionId(),
         'tr'        => $oSub->getAdminBag()->getItemPoint( $oItem->getPointId() )->getId(),
         'name'      => $item->getItems()->getName(),
         'points'    => $item->getPoints()
       ];
       $oItem->bagChanged();
       $oItem->deleteWithoutArchive();
	  jsonSuccess(array('Secondary' => true, 'row' => $data));
	 }
  }
  
  function addItem($input) {
	  
	  $oItem = UserBagItemQuery::create()->findPK($input['id']);
	  $oItem->setStatus('Active');
	  $oItem->save();
	  jsonSuccess(array('id' => $input['id']));
  }
  
  function addSecondaryItem($input) {
    if(!$input){ jsonError(['InvalidInput']);}

    $oUser = UserQuery::create()->findPk( $input['iUser'] );
    $oSub = SubscriptionQuery::create()->findPk( $input['iSub'] );

    if(!$oUser){  throwSingleError('InvalidUser');   }
    if(!$oSub){ throwSingleError('InvalidSubscription'); }
    // Does subscription belongs to this user
    if( $oUser->getId() != $oSub->getUserId() ){ throwSingleError('InvalidSubscription'); }

    $oItem = null;

    // If Item is already in the User Bag Item table but has status deleted than if otherwise if
    // item came from Admin side and is Secondary item.
    if($input['status'] == 'delete'){
      // In this case exact ID of UserBag item is present.
      $oItem = UserBagItemQuery::create()->findPk($input['UserBagItemId']);
      if( !$oItem ){ throwSingleError('InvalidUserBagItem'); }

      $oItem->validateUserBagItem($oUser, $oSub);

      $oItem->setStatus('Active')->setQuantity(1)->save();
      $oItem->bagChanged();
      jsonSuccess(array('bagId' => $oItem->getBagId(), 'id' => $oItem->getId(), 'action' => 'add', 'qty' => 1));
    }else{

      // Secondary items comes from admin so we have User Bag Id and Item Point Id.
      $oItem = UserBagItemQuery::create()
                ->filterByBagId( $input['UserBagId'] )
                ->filterByPointId( $input['itemPointId'] )
                ->filterByPoints( $input['Points'] )
                ->filterByStatus( 'Active' )
                ->filterBySecondary( true )
                ->findOneOrCreate();

      if(!$oItem->isNew()) {

        $oItem->validateUserBagItem($oUser, $oSub);
        $oItem->setQuantity( $oItem->getQuantity() + 1 )->save();
        $oItem->bagChanged();
        jsonSuccess(array('id' => $oItem->getId(), 'action' => 'addPoints', 'qty' => $oItem->getQuantity() ));

      } else {

        $oItem->validateUserBagItem($oUser, $oSub);
        $oItem->save();
        $oItem->bagChanged();
        jsonSuccess(array( 'bagId' => $oItem->getBagId(),'id' => $oItem->getId(), 'qty' => $oItem->getQuantity(), 'action' => 'add'));

      }
    }
  }
  
  function editQty($input) {

    if(!$input){ jsonError(['InvalidInput']);}

    $oUser = UserQuery::create()->findPk( $input['iUser'] );
    $oSub = SubscriptionQuery::create()->findPk( $input['iSub'] );

    if(!$oUser){  throwSingleError('InvalidUser');   }
    if(!$oSub){ throwSingleError('InvalidSubscription'); }
    // Does subscription belongs to this user
    if( $oUser->getId() != $oSub->getUserId() ){ throwSingleError('InvalidSubscription'); }

    $oItem = UserBagItemQuery::create()->findPK($input['userBagItemId']);
    if( !$oItem ){ throwSingleError('InvalidUserBagItem'); }

    $oItem->validateUserBagItem($oUser, $oSub);

    $qty = $input['iQty'] - $oItem->getQuantity();
    $oItem->setQuantity($input['iQty']);
    $oItem->save();
    $oItem->bagChanged();
    jsonSuccess(array('id' => $input['userBagItemId'], 'points' => $oItem->getPoints(), 'iSub' =>  $input['iSub'], 'qty' =>  $qty));
  }

  /**
   * Change subscription just for this week and add new items accordingly.
   * Keep User Bag ID same but change user bag item's contents.
   *
   * @param $input
   * @throws Exception
   * @throws PropelException
   *
     */
  function changeSubscriptionForWeek($input){

      $oUser = UserQuery::create()->findPK($input['iUser']);
      $oSub = SubscriptionQuery::create()->findPK($input['iSub']);
      $oProduct = ProductQuery::create()->findPK($input['iProduct']);

      //  Check for invalid user and product objects
      if (!$oUser)
        throwSingleError('InvalidUser');
      if (!$oSub)
        throwSingleError('InvalidSubscription');
      if (!$oProduct)
        throwSingleError('InvalidProduct');

      //  Make sure user has an active account and valid delivery site
      if (!$oUser->getDefaultDeliverySite() || $oUser->getCustomerStatus() != 'Active')
        throwSingleError('UserHasAddressPending');

      //  Make sure subscription belongs to user
      if ($oSub->getUserId() != $oUser->getId())
        throwSingleError('SubscriptionMismatch');

      //  Make sure new product is different from existing product
      if ($oSub->getProductId() == $oProduct->getId())
        throwSingleError('DuplicateProduct');

      //  Make sure new product has same category as old one
      if ($oSub->getProduct()->getCategoryId() != $oProduct->getCategoryId())
        throwSingleError('InvalidProductCategory');

      // Make sure its before 48 hours.
      timeConstraint( $oSub->getDeliveryDay() );

    // Check if this subscription has any current order
    $cOrder = $oSub->hasOrderOnDate( ['Pending', 'Donated'] , $oSub->getNextDeliveryDate() );
    if( $cOrder ){
      throwSingleError('OrderExist');
    }

    // This is admin current bag ID just for this week
    $adminBag = BagsQuery::create()
          ->filterByProductId( $oProduct->getId() )
          ->filterByDate( getDateFromDay( $oSub->getDeliveryDay() ) )
          ->findOne();

    if(!$adminBag){ throwSingleError('InvalidWeeklyBag'); }

      $user_current_bag = UserBagQuery::create()
                            ->filterByDate( $adminBag->getDate() )
                            ->filterBySubscriptionId( $oSub->getId() )
                            ->filterByUserId( $oUser->getId() )
                            ->findOne();

      if(!$user_current_bag){ throwSingleError('InvalidUserBag'); }

      $user_current_bag->setProductId( $oProduct->getId() )->setIsChanged(false)->save();
      $user_current_bag->createBagWithItems( $oSub, $adminBag,  $oProduct->getId() );
      jsonSuccess(['url' => '/account/subscriptions']);
    }

}
