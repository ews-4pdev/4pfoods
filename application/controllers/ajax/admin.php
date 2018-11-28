<?php


require_once(APPPATH.'core/AJAX_Controller.php');
require_once(APPPATH.'helpers/SendGridHelper.php');
require_once(APPPATH.'helpers/StripeHelper.php');

/**
 * Class Admin
 */
class Admin extends AJAX_Controller {

  private static $_skipLogin = array(
      'submitLogin'
  );

  private static $_driverAccess = array(
  );

  function __construct() {

    parent::__construct();

    if (!SKIP_LOGIN && !in_array($this->task, self::$_skipLogin)) {
      if (!$this->hasAdminAccess())
      {
        throwSingleError('AccessDenied');
      }
      if ($this->hasAdminAccess() == 'Driver' && !in_array($this->task, self::$_driverAccess))
      {
        throwSingleError('AccessDenied');
      }
    }

  }


  /**
   *  Logging in
   */
  function submitLogin($input) {
    //  Basic authentication
    if (!($oUser = UserPeer::authenticate($input['Email'], $input['Password'], true)))
      throwSingleError('InvalidLogin');

    //  Check that account has admin access
    if (!$oUser->getAdminAccess())
      throwSingleError('InvalidLogin');

    $this->session->set_userdata('iAdmin', $oUser->getId());
    jsonSuccess(array('url' => '/admin'));

  }

  /**
   *  Add a new discount
   */
  function addDiscount($input) {

    $oDiscount = new Discount();
    $oDiscount->fromArray($input);

    if (!$oDiscount->validate())
      throwErrors(formatErrorObject($oDiscount));

    $oDiscount->save();

    jsonSuccess(array('id' => $oDiscount->getId(), 'url' => $input['returnURL']));

  }

  /**
   *  Hide Delivery Site
   */
  function hideDeliverySite($input) {

    $oSite = DeliverySiteQuery::create()->findPK($input['iSite']);

    if (!$oSite)
      throwSingleError('InvalidDeliverySite');

    if ($oSite->getAcceptsDeliveries() == 1)
      throwSingleError('ActiveDeliverySite');

    $oSite->setIsPublished(0);
    $oSite->save();

    jsonSuccess(['url' => '/admin/deliveries']);

  }


  /**
   *  Issue payment for single order
   */
  function submitSinglePayment($input) {

    $input['iOrder'] = explode(",", $input['iOrder']);
    $aOrders = OrderQuery::create()->filterById($input['iOrder'])->find();

    if ( count($aOrders) < 0 )
      throwSingleError('InvalidOrder');

    foreach ( $aOrders as $oOrder ) {
      if (!$oOrder->isPayable())
        throwSingleError('InvalidOrderStatus');
    }


    $helper = new StripeHelper();
    if ($helper->executeChargeForOrders($aOrders))
      jsonSuccess(array('url' => '/admin/customers/'.$aOrders[0]->getUser()->getId()));
    else
      throwSingleError('OrderFailed');

  }

  /**
   *  Toggle archive user
   */
  function toggleArchiveUser($input) {

    $oUser = UserQuery::create()->findPK($input['iUser']);

    if ($oUser->getAdminAccess() != NULL)
      throwSingleError('AdminNotAllowed');

    //  Do not allow archiving if customer has active subscriptions
    $aSub = $oUser->getSubscriptions('Active');
    if (count($aSub) > 0)
      throwSingleError('InvalidUserState');

    $switchTo = ($oUser->isArchived()) ? 0 : 1;
    $oUser->setIsArchived($switchTo);
    $oUser->save();

    jsonSuccess(array(
        'url'   => '/admin/customers'
    ));

  }

  function changeCustomerDeliveryOption($input){

    $oUser = UserQuery::create()->findPk($input['iUser']);
      $log = array(
          'User ID'   => $input['iUser'],
          'New Site ID'   => $input['iSite'],
          'Old Site ID' => $oUser->getDefaultDeliverySiteId(),
          'Is DoorStep' => $oUser->getDoorstep(),
          'Status'       => $input['status'],
          'Browser'       => $_SERVER['HTTP_USER_AGENT'],
          'IP Address'       => getenv('REMOTE_ADDR'),
      );
      logIt($input['status'], $log, $message = '');

    if (!$oUser)
      throwSingleError('InvalidUser');

    if( isset( $input['status'] ) && $input['status'] == 'DoorToPickup' ){
      $oSite = DeliverySiteQuery::create()->findPk($input['iSite']);
      if (!$oSite)
        throwSingleError('InvalidDeliverySite');

      $oUser->convertToPickUp( $oSite );
      
    }elseif( isset( $input['status'] ) && $input['status'] == 'PickupToDoor' ){
      $oSite = DoorStepQuery::create()->findPk( $input['iSite'] );
      if (!$oSite)
        throwSingleError('InvalidDeliverySite');

      if( empty($input['Address1'])  && empty($input['Address2'])  )
          throwSingleError('address1');

      $oUser->fromArray( $input );
      $oUser->convertToDoorStep( $oSite );
    }

    jsonSuccess([
        'url' => '/admin/customers/'.$oUser->getId()
    ]);

  }

  /**
   *  Reset customer password
   */
  function resetCustomerPassword($input) {

    $oUser = UserQuery::create()->findPK($input['iUser']);

    if (!$oUser)
      throwSingleError('InvalidUser');

    $newPass = buildHash(10);
    $oUser->setPassword(UserPeer::getHash($newPass));
    $oUser->save();

    $helper = new SendGridHelper($oUser->getEmail(), 'reset-password');
    $helper->merge(array(
        'firstname'     => $oUser->getFirstName(),
        'password'      => $newPass
    ));
    $helper->send();

    jsonSuccess(array('msg' => 'Link has been sent to customer.'));

  }

  /**
   *  Edit delivery site information
   */
  function editSite($input) {

    $oSite = DeliverySiteQuery::create()->findPK($input['iSite']);

    if (!$oSite)
      throwSingleError('InvalidSite');

    unset($input['DefaultDeliveryDay']);

    $oSite->fromArray($input);
    $oSite->save();

    //  Send notifications
    $emails = array();
    $ids = array();
    foreach ($oSite->getSubscriptions() as $oSub) {
      if (!in_array($oSub->getUser()->getId(), $ids)) {
        $emails[] = $oSub->getUser()->getFullName().' ('.$oSub->getUser()->getEmail().')';
        $ids[] = $oSub->getUser()->getId();
      }
    }
    $aAdmins = UserQuery::create()
        ->filterByAdminAccess('SuperAdmin')
        ->find();
    foreach ($aAdmins as $oAdmin) {
      $helper = new SendGridHelper($oAdmin->getEmail(), 'site-edit-admin');
      $helper->merge(array(
          'firstname'       => $oAdmin->getFirstName(),
          'nickname'        => $oSite->getNickname(),
          'customers'       => implode('<br/>', $emails)
      ));
      $helper->send();
    }

    jsonSuccess(array(
        'url' => '/admin/deliveries'
    ));

  }

  /**
   *  Change delivery date for a site and all subscriptions associated
   */
  function changeSiteDeliveryDay($input) {

    $oSite = DeliverySiteQuery::create()->findPK($input['iSite']);
      $log = array(
          'Site ID'   => $input['iSite'],
          'New Site day'   => $input['DefaultDeliveryDay'],
          'Old Site day'       => $oSite->getDefaultDeliveryDay(),
          'Browser'       => $_SERVER['HTTP_USER_AGENT'],
          'IP Address'       => getenv('REMOTE_ADDR'),
      );
      logIt('dayChange', $log, $message = 'Starting Change Site Delivery Day');
    if (!$oSite)
    {
        throwSingleError('InvalidSite');
    }


    $oldDay = $oSite->getDefaultDeliveryDay();

    $days = array(
        'Sun', 'Mon', 'Tue', 'Wed',
        'Thu', 'Fri', 'Sat'
    );
    if (!in_array($input['DefaultDeliveryDay'], $days))
      throwSingleError('InvalidDay');

    if (!$oSite->changeDeliveryDay($input['DefaultDeliveryDay']))
      throwSingleError('InvalidSwitchDay');

    //  Send notifications
    $ids = array();
    foreach ($oSite->getSubscriptions() as $oSub) {
      if (!in_array($oSub->getUserId(), $ids))
        $ids[] = $oSub->getUserId();
    }
    $aUsers = UserQuery::create()->findPKs($ids);
    foreach ($aUsers as $oUser) {
      $helper = new SendGridHelper($oUser->getEmail(), 'site-change-day');
      $helper->merge(array(
          'firstname'       => $oUser->getFirstName(),
          'newDay'             => date('l', strtotime($input['DefaultDeliveryDay'])),
          'oldDay'          => date('l', strtotime($oldDay)),
          'nickname'        => $oSite->getNickname()
      ));
      $helper->send();
    }

    jsonSuccess(array(
        'url' => '/admin/deliveries'
    ));

  }

  /**
   *  Toggle publish a discount
   */
  function togglePublishDiscount($input) {

    $oDiscount = DiscountQuery::create()->findPK($input['iDiscount']);

    //  Validate discount
    if (!$oDiscount)
      throwSingleError('InvalidDiscount');

    //  Switch published state
    $oDiscount->togglePublish();

    jsonSuccess(array('url' => '/admin/discounts'));

  }

  /**
   *  Toggle whether or not a delivery site is enabled
   */
  function toggleEnabledDeliverySite($input) {


    $oSite = DeliverySiteQuery::create()->findPK($input['iSite']);

    if (!$oSite)
      throwSingleError('InvalidDeliverySite');

    if ($oSite->acceptsDeliveries())
      $oSite->disableDeliveries();
    else
      $oSite->enableDeliveries();

    jsonSuccess(array('url' => '/admin/deliveries'));

  }

  /**
   *  Change customer delivery site
   */
  function changeCustomerSite($input) {

    $oUser = UserQuery::create()->findPk($input['iUser']);
    if( $oUser->getDoorstep() ){
      $oSite = DoorStepQuery::create()->findPk($input['iSite']);
    }else{
      $oSite = DeliverySiteQuery::create()->findPk($input['iSite']);
    }

    //  Validate user and site
    if (!$oUser)
      throwSingleError('InvalidUser');
    if (!$oSite)
      throwSingleError('InvalidDeliverySite');

    $oUser->associateWithDeliverySite($oSite);
    $oUser->save();

    jsonSuccess(array(
        'url'       => '/admin/customers/'.$oUser->getId()
    ));

  }

  /**
   *  Issue a refund
   */
  function issueRefund($input) {

    $oPayment = PaymentQuery::create()->findPK($input['iPayment']);

    //  Valid payment
    if (!$oPayment)
      throwSingleError('InvalidPayment');

    //  Valid amount
    if ($input['Amount'] < RefundPeer::MIN_AMOUNT)
      throwSingleError('RefundMinAmount');
    else if ($input['Amount'] > $oPayment->getRefundableAmount())
      throwSingleError('RefundMaxAmount');

    //  Make it happen
    $helper = new StripeHelper();
    $result = $helper->issueRefund($oPayment, $input['Amount']);

    if ($result['success']) {
      jsonSuccess(array(
          'url'     => '/admin/payments/'.$oPayment->getId()
      ));
    } else
      throwSingleError($result['errorCode']);

  }

  /**
   *  Assign an existing delivery site to a user
   */
  function assignSite($input) {

    $oUser = UserQuery::create()->findPK($input['iUser']);
    $oSite = DeliverySiteQuery::create()->findPK($input['iSite']);

    //  Valid user
    if (!$oUser)
      throwSingleError('InvalidUser');

    //  Valid delivery site
    if (!$oSite)
      throwSingleError('InvalidSite');

    //  Okay then... do it!
    $oUser->associateWithDeliverySite($oSite);
    $oUser->setCustomerStatus('Active');
    $oUser->save();
    foreach ($oUser->getSubscriptions() as $oSub) {
      $oSub->setDeliverySite($oSite);
      if ($oSub->getStatus() == 'Pending'){
        $oSub->setStatus('Active');
      }
      $oSub->save();

      // Check if Bag is ready for this week
      $oSub->createBag();
    }

    $helper = new SendGridHelper($oUser->getEmail(), 'new-user-approved');
    $helper->merge(array(
        'firstname'     => $oUser->getFirstName(),
        'address'       => $oSite->getFullAddress()
    ));
    $helper->send();

    jsonSuccess(array('url' => '/admin/approval'));

  }

  /**
   *  Remove a driver account
   */
  function removeDriver($input) {

    $oUser = UserQuery::create()->findPK($input['iUser']);

    //  Valid user
    if (!$oUser)
      throwSingleError('InvalidUser');

    //  Muse be a driver
    if ($oUser->getAdminAccess() != 'Driver')
      throwSingleError('InvalidUserAccess');

    //  Cannot have any deliveries attached
    if (count($oUser->getDeliverys()) > 0)
      throwSingleError('UserIsFrozen');

    $oUser->delete();

    jsonSuccess();


  }


  function deleteCategory($input){

    if( empty($input) ){ throwSingleError('InvalidCategory'); }
    $oCategory = ProductCategoryQuery::create()->findPk($input['id']);
    if(!$oCategory) { throwSingleError('InvalidCategory'); }
    ( $oCategory->getIsPublished() == 0 ) ? $oCategory->setIsPublished(1) : $oCategory->setIsPublished(0);
    $oCategory->save();
    jsonSuccess(array('true',
        'url'     => '/admin/categories/'
    ));
  }

  /**
   *  Add a new delivery account
   */
  function addDriverAccount($input) {

    //  Create new user
    $oUser = new User();
    $oUser->fromArray($input);

    //  Password is separate
    $password = $input['Password'];
    unset($input['Password']);

    //  Validate
    if (!$oUser->validate())
      throwErrors(formatErrorObject($oUser));

    //  Check for duplicate email
    if (UserQuery::create()->filterByEmail($input['Email'])->count() > 0)
      throwSingleError('DuplicateEmail:Email');

    //  Set Admin access level Driver
    $oUser->setAdminAccess('Driver');

    //  Set password
    $oUser->setPassword(UserPeer::getHash($password));

    $oUser->setIsConfirmed(1);

    //  Save
    $oUser->save();

    jsonSuccess(array('id' => $oUser->getId(), 'url' => $input['returnURL']));

  }

  /**
   *  Add a new product, or edit existing
   */
  function addProduct($input) {
    //  Create product object
    $oProduct = null;
    if (isset($input['iProduct'])) {
      $oProduct = ProductQuery::create()->findPK($input['iProduct']);
      if (!$oProduct)
        throwSingleError('InvalidProduct');
    } else{
      $oProduct = new Product();
    }

    $oProduct->fromArray($input);

    if (!$oProduct->validate())
      throwErrors(formatErrorObject($oProduct));

    $oProduct->save();

    jsonSuccess(array('id' => $oProduct->getId(), 'url' => $input['returnURL']));

  }

  /**
   *  Add a new Item, or edit existing
   */
  function addItem() {
    $input = $this->input->post();

    if(!isset($input['Secondary'])){$input['Secondary'] = false;}
    //  Create product object
    $oItem = null;
    if( isset($input['iItem']) && !empty($input['iItem']) ){
      $oItem = ItemsQuery::create()->findPk( $input['iItem'] );
    }else{
      $oItem = new Items();
    }
    $oItem->fromArray($input);

    $errors = $oItem->validate($input);
    if ( count( $errors )  > 0 ){ jsonError('', $errors); }
    $oItem->save();

    $oItem->addSuppliersArray($input['suppliers'], $oItem);
    $oItem->addItemPoints($input['size'], $oItem);
    jsonSuccess(array('id' => $oItem->getId(), 'url' => $input['returnURL']));
  }

  function changeItemStatus($input){
    if( empty($input) ){  throwSingleError('InvalidInput'); }
    $item = ItemsQuery::create()->findPk($input['id']);
    if(!$item){ throwSingleError('InvalidInputID'); }
    ( $item->getactive() == 0 ) ? $item->setactive(1) : $item->setactive(0);
    $item->save();
    jsonSuccess(array('id' => $item->getId(), 'url' => '/admin/items'));
  }

  /**
   *  Add a new Supplier, or edit existing
   */
  function addSupplier($input) {
    //  Create product object
    $oSupplier = null;

    if (isset($input['iSupplier'])) {
      $oSupplier = SuppliersQuery::create()->findPK($input['iSupplier']);
      if (!$oSupplier)
        throwSingleError('InvalidSupplier');

    } else {
      $oSupplier = new Suppliers();
    }
    $oSupplier->fromArray($input);

    if (!$oSupplier->validate())
      throwErrors(formatErrorObject($oSupplier));

    $oSupplier->save();

    jsonSuccess(array('id' => $oSupplier->getId(), 'url' => $input['returnURL']));

  }

  /**
   * @param $input
   * @throws Exception
   * @throws PropelException
   */
  function addCategory($input) {
    //  Create product object
    $oCategory = new ProductCategory();

    $oCategory->setTitle($input['title']);
    $oCategory->setDescription($input['description']);
    $oCategory->save();

    jsonSuccess(array('id' => $oCategory->getId(), 'url' => $input['returnURL']));

  }

  /**
   *  Add a new delivery site
   */
  function addDeliverySite($input) {
    //  Create a new object
    if (isset($input['iSite'])) {
      $oSite = DeliverySiteQuery::create()->findPK($input['iSite']);
      if (!$oSite)
        throwSingleError('InvalidSite');
    } else
      $oSite = new DeliverySite();
    $oSite->fromArray($input);
    //  If there is a userID present, associate that user with new object
    if (isset($input['iUser'])) {
      $oUser = UserQuery::create()->findPK($input['iUser']);
      if (!$oUser)
        throwSingleError('InvalidUser');
    }

    //  Unique access code
    do {
      $code = rand(1000, 9999);
    } while (DeliverySiteQuery::create()->filterByAccessCode($code)->count() > 0);
    $oSite->setAccessCode($code);

    //  Validate
    if (!$oSite->validate())
      throwErrors(formatErrorObject($oSite));

    $oSite->save();

    //  Associate with User and update user status
    if (isset($oUser)) {
      $oUser->associateWithDeliverySite($oSite);
      $oUser->setCustomerStatus('Active');
      $oUser->save();
      foreach ($oUser->getSubscriptions() as $oSub) {
        $oSub->setDeliverySite($oSite);
        if ($oSub->getStatus() == 'Pending')
          $oSub->setStatus('Active');
        $oSub->save();
      }
    }

    jsonSuccess(array('id' => $oSite->getId(), 'url' => '/admin/deliveries'));

  }

  function addBagItems($input){

    if(isset($input['iBagItem']) && !empty($input['iBagItem']) )
    {
      $aBagItem = BagsItemQuery::create()->findPk($input['iBagItem']);
      if (!$aBagItem){throwSingleError('InvalidItemProduct'); }
      if( isset($input['action']) && $input['action'] == 'delete')
      {
        $this->outOfSync( $aBagItem->getBagId() );
        $aBagItem->delete();
        $total_points = BagsQuery::adjustTotalPoints( $aBagItem->getBagId() );
        jsonSuccess(array('id' => $input['iBagItem'], 'action' => 'delete', 'outOfSnc' => true, 'total_points' => $total_points));
      }else{
        $this->outOfSync($aBagItem->getBagId());
        $aBagItem->fromArray($input);
        $aBagItem->save();
        $total_points = BagsQuery::adjustTotalPoints( $aBagItem->getBagId() );
        jsonSuccess([ 'id' => $input['iBagItem'] , 'outOfSnc' => true, 'total_points' => $total_points]);
      }
    }

    if( $bagItem = BagsItemQuery::create()->filterByBagId($input['BagId'])->filterByPointId($input['PointId'])->findOne() ){
      $this->outOfSync($bagItem->getBagId());
      $bagItem->setPoints( ( $bagItem->getPoints() + $input['Points'] ) )->save();
      $total_points = BagsQuery::adjustTotalPoints( $bagItem->getBagId());
      jsonSuccess(array
      ('Points' => $bagItem->getPoints(), 'id' => $bagItem->getId(), 'rowId' =>  $input['rowId'], 'action' => 'addition',  'total_points' => $total_points)
      );
    }
    $bagItem = new BagsItem();
    $bagItem->fromArray($input);
    if(!$bagItem->validate()){throwErrors(formatErrorObject($bagItem));}
    $bagItem->save();
    $total_points = BagsQuery::adjustTotalPoints( $bagItem->getBagId());
    $this->outOfSync($bagItem->getBagId());
    jsonSuccess(
        array('id' => $bagItem->getId(), 'rowId' =>  $input['rowId'], 'action' => 'add', 'outOfSnc' => true,  'total_points' => $total_points)
    );
  }

  function changeSupplierStatus($input){
    if( empty($input) ){ throwSingleError('InvalidInput'); }
    $supplier = SuppliersQuery::create()->findPk($input['id']);
    if(!$supplier){ throwSingleError('InvalidInputID'); }
    ( $supplier->getactive() == false ) ? $supplier->setactive(true) : $supplier->setactive(false);

    if( $supplier->save() ){
      jsonSuccess(array('id' => $supplier->getId(), 'url' => $input['returnURL']));
    }else{
      jsonError( '', ['dependentItems'] );
    }

  }

  protected function outOfSync($id){
    BagsQuery::create()
        ->findPk($id)
        ->setSync(false)
        ->setSEmail(false)
        ->save();
  }

  function getProductSizes($input)
  {
    if(empty($input)){ die('Invalid input'); }
    $aProductSizes = ProductQuery::create()
        ->filterByIsPublished(true)
        ->filterByCategoryId($input['id'])
        ->select(['title', 'id', 'size'])
        ->find();
    jsonSuccess( array('data' => $aProductSizes ));

  }

  function checkBagsDate($input)
  {
    if(!isset($input) || $input['date'] == null){throwSingleError('InvalidInput');}
    $date = ( DateTime::createFromFormat('m-d-Y', $input['date']) );
    $date = $date->format('Y-m-d');
    if(BagsQuery::create()->filterByDate( $date )->count() > 0){
      jsonSuccess([ 'url' => '/admin/bags/'.$input['date'] ]);
    }
    throwSingleError('BagsDateExceed');
  }

  function createBags($input){
    if(!$input){jsonError('Invalid Date');}
    $date = DateTime::createFromFormat('m-d-Y', $input['date']);
    ( new BagsQuery() )->createBags( $date );
    jsonSuccess(['url' => "/admin/bags/".$date->format('m-d-Y')]);
  }

  function createSyncBags($input){
    if(!$input){jsonError('Invalid Date');}
    $bag = BagsQuery::create()->findPk($input['bag']);
    if(!$bag){ jsonError('Invalid Bag Provided'); }
    if( count( $bag->getBagsItems() ) < 1 ){ jsonError('Your bag is empty. Please add at least one Item.'); }
    $bag->createSyncBagsWithItems($input['dates']);
    if(in_array($bag->getDate('%Y/%m/%d'), $input['dates'])){
      jsonSuccess(['url' => "/admin/bags/".$bag->getDate('%m-%d-%Y')]);
    }else{
      jsonSuccess([]);
    }

  }

  function isSync($input){
    jsonSuccess(['sync' => BagsQuery::create()
        ->select('Sync')
        ->findPk($input['id'])]);
  }

  function publishBags($input)
  {
    $date = $input['date'];
    $path = CRON."cron.php";
    exec("php  $path publishBags null $date > /dev/null 2>&1 &");
    jsonSuccess([]);
  }

    function test($input)
    {
    }
}