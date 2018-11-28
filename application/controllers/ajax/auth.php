<?php

require_once(APPPATH.'core/AJAX_Controller.php');
require_once(APPPATH.'helpers/SendGridHelper.php');

class Auth extends AJAX_Controller {

  public function submitLogin($input) {
    
    //  Basic authentication
    if (!($oUser = UserPeer::authenticate($input['Email'], $input['Password'], true)))
      throwSingleError('InvalidLogin');

    //  Check for unconfirmed account
    if (!$oUser->isConfirmed())
      throwSingleError('UnconfirmedAccount');

    //  Make sure user is not an administrator
    if ((bool)($oUser->getAdminAccess()))
      throwSingleError('AdminLogin');

    $this->session->set_userdata('uid', $oUser->getId());
    jsonSuccess(array('url' => '/account'));

  }

  public function resendEmail($input) {

    $oUser = UserQuery::create()->findOneByHash($input['hash']);
    error_log($input['hash']);

    if (!$oUser)
      throwSingleError('InvalidUser');

    //  Email them the welcome email!
    $url = 'http://'.$_SERVER['SERVER_NAME'].'/gateway/confirmAccount/'.$oUser->getId().'/'.$oUser->getHash();
    $helper = new SendGridHelper($oUser->getEmail(), 'confirm-email');
    $helper->merge(array(
      'firstname'     => $oUser->getFirstName(),
      'confirmURL'    => $url
    ));
    $helper->send();

    jsonSuccess();

  }

  public function submitForgotPassword($input) {

    if (!($oUser = UserQuery::create()->filterByEmail($input['Email'])->findOne()))
      throwSingleError('InvalidAccount');

    if (!$oUser->isConfirmed())
      throwSingleError('UnconfirmedAccount');

    $newPass = buildHash(10);
    $oUser->setPassword(UserPeer::getHash($newPass));
    $oUser->save();

    $helper = new SendGridHelper($oUser->getEmail(), 'reset-password');
    $helper->merge(array(
      'firstname'     => $oUser->getFirstName(),
      'password'      => $newPass
    ));
    $helper->send();

    jsonSuccess(array('msg' => 'An email has been sent to you with a new password.'));

  }

}

?>
