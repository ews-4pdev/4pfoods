<?php

class Mobile extends MY_Controller {

  function index() {

    if (!$this->isDriver())
      redirect('/mobile/login');

    $oDriver = UserQuery::create()->findPK($this->session->userdata('iDriver'));
    $aSites = DeliveryQuery::getSiteList();

    $data = array(
      '_get_build'=> 'driver',
      'oDriver'   => $oDriver,
      'aSites'    => $aSites
    );
    $this->_loadSimplePage('mobile/home', $data);

  }

  function login() {
    if ($this->isDriver())
      redirect('/mobile');

    $data = array(
      '_get_build'  => 'driver'
    );
    $this->_loadSimplePage('mobile/login', $data);

  }

  function logout() {

    $this->session->unset_userdata('iDriver');
    redirect('/mobile/login');

  }

}