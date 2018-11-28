<?php

require_once(APPPATH.'core/AJAX_Controller.php');
require_once(APPPATH.'helpers/SendGridHelper.php');

class Driver extends AJAX_Controller {

  /**
   *  Driver login
   */
  function submitLogin($input) {

    //  Basic authentication
    if (!($oUser = UserPeer::authenticate($input['Email'], $input['Password'], true)))
      throwSingleError('InvalidLogin');

    //  Check for unconfirmed account
    if (!$oUser->isConfirmed())
      throwSingleError('UnconfirmedAccount');

    //  Check for admin access
    if (!$oUser->getAdminAccess())
      throwSingleError('InvalidLogin');

    $this->session->set_userdata('iDriver', $oUser->getId());
    jsonSuccess(array('url' => '/mobile'));

  }

  /**
   *  Driver submits report on delivery
   */
  function signDelivery($input) {

    $oDriver = UserQuery::create()->findPK($input['iDriver']);

    //  Valid user
    if (!$oDriver)
      throwSingleError('InvalidUser');

    $oDelivery = DeliveryQuery::create()->findPk( $input['iSite'] );

    if (!$oDelivery)
      throwSingleError('InvalidDelivery');


    $oSite = null;
    if( $oDelivery->getDoorstep() )
    {
      $oSite = DoorStepQuery::create()->findPK( $oDelivery->getDeliverySiteId() );
    }else{
      $oSite = DeliverySiteQuery::create()->findPK( $oDelivery->getDeliverySiteId() );
    }

    //  Valid site
    if (!$oSite)
      throwSingleError('InvalidDelivery');

    //  User is driver or administrator
    if (!$oDriver->getAdminAccess())
      throwSingleError('NoAdminAccess');

    //  Make sure there are deliveries for site
    if( $oDelivery->getDoorstep() ){
      $pendingDeliveries[0] = $oDelivery;
    }else{
      $pendingDeliveries = DeliveryQuery::getPendingForSite( $oSite->getId(), $oDelivery->getDeliveryDate() );
    }

    if (count($pendingDeliveries) == 0)
      throwSingleError('NoDeliveriesForSite');

    foreach ($pendingDeliveries as $oDelivery) {
      //  Delivery is valid
      if (!$oDelivery)
        throwSingleError('InvalidDelivery');

      //  Delivery is in Pending status
      //  Note: this should never happen because orders not
      //  in pending will not show up in getPendingDeliveries method
      if ($oDelivery->getStatus() != 'Pending')
        throwSingleError('InvalidDeliveryStatus');

      //  All is good. Do the deed
      $fn = ($input['isSuccess']) ? 'markDeliveredBy' : 'markFailedBy';
      $aOrders = $oDelivery->$fn($oDriver);

      foreach ($aOrders as $oOrder) {
        $oUser = $oOrder->getUser();

        $helper = new SendGridHelper($oUser->getEmail(), 'delivered');
        $helper->merge(array(
          'firstname'       => $oUser->getFirstName(),
          'product'         => $oOrder->getProductName(),
          'ordernumber'     => $oOrder->getId()
        ));
        $helper->send();
      }

    }

    jsonSuccess(array('url' => '/mobile'));

  }

}
