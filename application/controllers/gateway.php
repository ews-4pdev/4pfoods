<?php

require_once(APPPATH.'helpers/StripeHelper.php');
require_once(APPPATH.'helpers/SendGridHelper.php');
require_once(APPPATH.'config/creds.php');

class Gateway extends MY_Controller {

  function confirmAccount($id = NULL, $hash = NULL) {

    if (!$id || !$hash)
      $this->redirectWithError('Invalid URL. Please make sure you copied the link correctly from your email.', '/gateway/login');

    $oUser = UserQuery::create()->findPK($id);

    if (!$oUser)
      $this->redirectWithError('Invalid URL. Please make sure you copied the link correctly from your email.', '/gateway/login');

    if ($oUser->getHash() != $hash)
      $this->redirectWithError('Invalid URL. Please make sure you copied the link correctly from your email.', '/gateway/login');


    if ($oUser->isConfirmed())
      $this->redirectWithError('This account has already been confirmed. Please log in.', '/gateway/login');
      
    //  Send welcome email
    $products = array();
    foreach ($oUser->getSubscriptions() as $oSub)
      $products[] = $oSub->getProduct()->getTitle().' - '.money($oSub->getProduct()->getPrice()).'/delivery';
    $helper = new SendGridHelper($oUser->getEmail(), 'welcome');
    $helper->merge(array(
      'firstname'       => $oUser->getFirstName(),
      'email'           => $oUser->getEmail(),
      'products'        => implode('<br/>', $products)
    ));

    $oUser->confirm();
    $this->session->set_userdata('uid', $oUser->getId());

    $helper->send();

    $this->redirectWithMessage(
      'Welcome to 4P Foods! This is where you will modify your weekly deliveries and update your billing information. Let\'s get started!',
      '/account'
    );


  }

  public function confirmsubscribe() {

    $data = array();
    $this->_loadSimplePage('public/confirm-subscribe', $data);

  }

  public function signup($iProduct = 1) {

    $aSites = DeliverySiteQuery::create()
      ->filterByAcceptsDeliveries(1)
      ->find();

    $aProducts = ProductCategoryQuery::create()
      ->filterByIsPublished(1)
      ->find();

    $aStates = StateQuery::getList();
    $data = array(
      'iProduct'    => $iProduct,
      'aSites'      => $aSites,
      'aProducts'   => $aProducts,
      'aStates'     => $aStates,
      'deliveryCharges' => PaymentPeer::DELIVERY_CHARGE
    );
    $this->_loadSimplePage('public/signup', $data);

  }

  public function confirm($hash = NULL) {

    $data = array(
      'hash'    => $hash
    );
    $this->_loadSimplePage('public/confirm', $data);

  }

  public function login() {
    if ($this->isLoggedIn())
      redirect('/account/subscriptions');

    $data = array();
    $this->_loadSimplePage('public/login', $data);

  }

  public function hook() {

    $helper = new StripeHelper();

    $msg = @file_get_contents('php://input');
    $obj = json_decode($msg);

    $oHook = new Hook();
    $oHook->setType($obj->type);
    $oHook->setContents(json_encode($obj));
    $oHook->save();

    switch ($oHook->getType()) {
      case 'charge.failed':
        $helper->processChargeFailed($oHook, $obj);
        break;
      case 'charge.succeeded':
        $helper->processChargeSucceeded($oHook, $obj);
        break;
    }

  }
}
