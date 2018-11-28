<?php

class Account extends MY_Controller {

  private static $_skipAuth = array(
    'login'
  );

  private static $_navData = array(
    'subscriptions' => array(
      'url'     => '/account/subscriptions',
      'title'   => 'Subscriptions'
    ),
    'billing' => array(
      'url'     => '/account/billing',
      'title'   => 'Billing'
    ),
    'profile' => array(
      'url'     => '/account/profile',
      'title'   => 'My Profile'
    )
  );

  function __construct() {

    parent::__construct();

    //  Require login unless URL falls into specific category
    if (!SKIP_LOGIN && !$this->isLoggedIn() && !in_array($this->uri->segment(2), self::$_skipAuth))
      $this->redirectWithError('You must be logged in to view this page.', '/gateway/login');

  }

  public function index() {

    redirect('/account/subscriptions');

  }

  public function logout() {

    $this->session->unset_userdata('uid');
    redirect('/gateway/login');

  }

  public function billing() {

    require_once(APPPATH.'config/creds.php');

    $oUser = $this->getUser();
    $aPayments = PaymentQuery::getForUser($this->session->userdata('uid'));
    $data = array(
      'oUser'         => $oUser,
      'aPayments'     => $aPayments,
      'currentPage'   => 'billing'
    );
    $this->_loadWrappedPage('billing', $data);

  }

  public function profile() {

    $oUser = $this->getUser();
    $data = array(
      'oUser'         => $oUser,
      'currentPage'   => 'profile'
    );
    $this->_loadWrappedPage('profile', $data);

  }

  public function subscriptions() {

    $oUser = $this->getUser();
    $aSubscriptions = $oUser->getActiveSubscriptions();
    $aCanceled = $oUser->getCanceledSubscriptions();


    $aCategories = ProductCategoryQuery::create()
      ->filterByIsPublished(1)
      ->orderById('ASC')
      ->find();

    $skipDays = [];
    foreach ($aSubscriptions as $oSub) {
      $firstday = $oSub->getNextDeliveryDate();
      for ($i = 0; $i < 24; $i++)
        $skipDays[$oSub->getId()][] = date('Y-m-d', strtotime(sprintf('%s +%d Weeks', $firstday, $i)));
    }


    $data = array(
      'oUser'         => $oUser,
      'aCategories'   => $aCategories,
      'aSubscriptions'=> $aSubscriptions,
      'aCanceled'     => $aCanceled,
      'skipDays'      => $skipDays,
      'currentPage'   => 'subscriptions',
    );


    $this->_loadWrappedPage('subscriptions', $data);

  }

  private function _loadWrappedPage($view, $data) {

    $data['error'] = ($this->session->flashdata('error')) ? $this->session->flashdata('error') : '';
    $data['message'] = ($this->session->flashdata('message')) ? $this->session->flashdata('message') : '';
    $data['navData'] = self::$_navData;
    $data['_get_build'] = 'customer';

    $this->load->view('component/header', $data);
    $this->load->view('component/customer-header-inner', $data);
    $this->load->view('account/'.$view, $data);
    $this->load->view('component/customer-footer-inner', $data);
    $this->load->view('component/footer', $data);

  }

}