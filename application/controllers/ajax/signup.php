<?php

require_once(APPPATH.'core/AJAX_Controller.php');
require_once(APPPATH.'helpers/StripeHelper.php');

class Signup extends AJAX_Controller {

  /**
   *  Validate discount code
   */
  function validateDiscount($input) {

    $oDiscount = DiscountQuery::create()->findOneByCode($input['Code']);

    if (!$oDiscount)
      throwSingleError('InvalidCode');

    if (!$oDiscount->isPublished())
      throwSingleError('ExpiredDiscount');
    // Revision Start By EWS ------ Get State Tax for Discount Calculation
    $aTax = StateQuery::getTaxList();
    if(!empty($input['state']))
    {
      $tax = $aTax[$input['state']];
    } else {
      $tax = 0;
    }
    jsonSuccess(array(
        'id' => $oDiscount->getId(),
        'amount' => $oDiscount->getAmount(),
        'nOrders' => $oDiscount->getOrdersAffected(),
        'Tax'         => $tax
    ));
    // Revision End By EWS ------ Get State Tax for Discount Calculation
  }

  /**
   *  Cart abandonment: save email
   */
  function submitCartAbandonEmail($input) {

    //  Valid email
    if (!isValidEmail($input['Email']))
      return false;

    //  Check for email in cart abandonment table
    $n = CapturedEmailQuery::create()
        ->filterByEmail($input['Email'])
        ->count();
    if ($n > 0)
      return false;

    //  Check for email in users
    $n = UserQuery::create()
        ->filterByEmail($input['Email'])
        ->count();
    if ($n > 0)
      return false;

    $obj = new CapturedEmail();
    $obj->fromArray($input);
    $obj->save();

    jsonSuccess();

  }

  // Revision Start By EWS ------ Get State Tax According to Respective State Id

  function getStateTax ($input) {

    $aTax = StateQuery::getTaxList();
    jsonSuccess(array(
        'Tax'         => $aTax[$input['code']],
    ));

  }
  // Revision End By EWS ------ Get State Tax According to Respective State Id

  /**
   *  Validate Delivery Site access code
   */
  function validateAccessCode($input) {

    if($input['id'] == 1)
    {
      $oSite = DeliverySiteQuery::create()
          ->filterByIsPublished(true)
          ->filterByAcceptsDeliveries(true)
          ->findOneByAccessCode($input['Code']);


      $aTax = StateQuery::getTaxList();

      jsonSuccess(array('site' => array(
          'Address1'      => $oSite->getAddress1(),
          'Address2'      => $oSite->getAddress2(),
          'City'          => $oSite->getCity(),
          'StateId'       => $oSite->getStateId(),
          'Zip'           => $oSite->getZip(),
          'AccessCode'    => $oSite->getAccessCode(),
          'Notes'         => $oSite->getNotes(),
          'Tax'			  => $aTax[$oSite->getStateId()]
      ),'id'            => 1));
    } else {

      $oSite = DoorStepQuery::create()
          ->findOneByZip($input['code']);


      if( isset($oSite) )
      {
        jsonSuccess(array(
            'id'            => 0,
            'code'			 => $input['code'],
            'state'            => $oSite->getStateId(),
            'site'  => [
              'Tax' => $oSite->getState()->getTax()
            ]
        ));
      } else {
        jsonSuccess(array(
            'id'            => 2,
            'code'			 => $input['code']
        ));
      }
      // Revision Start By EWS ------ Get State Tax and Add in Json Success Respose
      $aTax = StateQuery::getTaxList();


    }

  }

  function validateAccessZipCode($input) {

    if(isset($input['code']))
    {
      $base_uri = 'https://www.zipcodeapi.com/rest/SokjGyBKG7KP2bDqDiMy3WeqYrxKB9VuyfNfNp0uxLjAWX0AgN9jJnKIPhsqroUZ/radius.json/'.$input['code'].'/5/mile';
      $client = new Guzzle\Http\Client($base_uri);
      $response = null;

      $request = $client->get();
      // if no response is returned from API than send "No Service" response to user.
      try{
        $response = $request->send();
      }catch (Exception $e){
        jsonSuccess(array(
            'Zip'           => 1
        ));
      }

      $jsonResponse = $response->json();
      $test = DeliverySiteQuery::create()
          ->filterByIsPublished(true)
          ->filterByAcceptsDeliveries(true)
          ->filterByZip( array_column($jsonResponse['zip_codes'], 'zip_code') )
          ->setFormatter('PropelArrayFormatter')
          ->find();

      if( !is_null($test) && count($test) > 0 ){
        $apiResponseArray = [];
        $retArray = [];
        foreach ($jsonResponse['zip_codes'] as $zip_code) {
          $apiResponseArray[ $zip_code['zip_code'] ] = $zip_code;
        }

        foreach ($test as $key => $value) {
          if( $apiResponseArray[ $value['Zip'] ] ){
            $retArray[$key] = $value;
            $retArray[$key]['distance'] = $apiResponseArray[ $value['Zip'] ]['distance'];
          }
        }
        usort($retArray, 'sortArray');

        jsonSuccess(array(
            'Zip'           => $retArray
        ));
      }else{
        jsonSuccess(array(
            'Zip'           => 1
        ));
      }
    }
  }

  /**
   *  Subscribe to email list
   */
  function subscribe($input) {

    require_once(APPPATH.'config/creds.php');

    if (!isValidEmail($input['email']))
      throwSingleError('InvalidEmail');

    $chimp = new \Drewm\MailChimp(MAILCHIMP_KEY);

    $result = $chimp->call('lists/subscribe', array(
        'id'      => MAILCHIMP_LISTID,
        'email'   => array('email' => $input['email']),
        'merge_vars' => array(
            'FNAME'   => $input['firstname'],
            'LNAME'   => $input['lastname']

        ),
        'send_welcome' => true
    ));

    jsonSuccess(array(
        'url' => '/gateway/confirmsubscribe'
    ));

  }

  /**
   *  Process signup submission
   */
  function submitSignup($input) {

    //  Create new user
    $oUser = new User();
    $oUser->fromArray($input);
    $oUser->setDoorstep( $input['DoorStep'] );
    //  Validate using Propel validators
    // Revision Start By EWS ------ Validate Terms of services
    if (!$oUser->validate())
    {
      if ($input['terms'] == 'false'){
        $errors = formatErrorObject($oUser);
        array_push($errors, "terms:terms");
        throwErrors($errors);
      }
      else
      {
        throwErrors(formatErrorObject($oUser));
      }
    } else {
      if ($input['terms'] == 'false'){
        throwSingleError('terms:terms');
      }
    }
    if( empty( $input['Address1'] ) && empty( $input['Address2'] )  ){
          throwSingleError('address1:NoField');
    }
    // Revision End By EWS ------ Validate Terms of services
    //  Check for duplicate email
    $count = UserQuery::create()
        ->filterByEmail($input['Email'])
        ->count();
    if ($count > 0)
      throwSingleError('DuplicateEmail:Email');

    //  Check for matching, valid passwords
    if (empty($input['Password']))
      throwSingleError('EmptyPassword:Password');
    if ($input['Password'] != $input['ConfirmPassword'])
      throwSingleError('PasswordMismatch:ConfirmPassword');

    //  Use credit card token to create Stripe customer ID
    $sendToStripe = array(
        'card'        => $input['Token'],
        'email'       => $input['Email'],
        'metadata'    => array(
            'first'     => $input['FirstName'],
            'last'      => $input['LastName']
        )
    );

    $helper = new StripeHelper();
    $result = $helper->createCustomer($sendToStripe);
    if (!$result['success'])
      throwSingleError($result['errorCode']);

    //  Store the customer ID
    $oUser->setStripeId($result['id']);

    //  If access code is submitted, validate it
    if ( !empty($input['AccessCode'])  ) {
      $oSite = DeliverySiteQuery::create()
          ->findOneByAccessCode( $input[ 'AccessCode' ] );

      if ( !$oSite )
        throwSingleError( 'InvalidAccessCode:NoField' );

      $oUser->associateWithDeliverySite($oSite);
    }elseif( !empty($input['Zip']) && $oUser->getDoorstep() ) {
      $oSite = DoorStepQuery::create()->findOneByZip( $input['Zip'] );

      if ( !$oSite )
        throwSingleError( 'InvalidZip:NoField' );

      $oUser->associateWithDeliverySite($oSite);
    }//  Otherwise, put customer in AddressPending state
     else{
      $oUser->setCustomerStatus('AddressPending');
    }

    if (!isset($input['Products']) || !is_array($input['Products']) || empty($input['Products']))
      throwSingleError('NoProducts:Products');

    //  Validate discount codes, if applicable
    if (isset($input['DiscountCode']) && $input['DiscountCode'] != '') {

      $oDiscount = DiscountQuery::create()->findOneByCode($input['DiscountCode']);

      //  Check for valid code
      if (!$oDiscount)
        throwSingleError('InvalidCode:DiscountCode');

      //  Check that code is still enabled
      if (!$oDiscount->isPublished())
        throwSingleError('ExpiredDiscount:DiscountCode');

      //  Associate discount with user
      $oUser->addDiscount($oDiscount);

    }

    //  Validate products and create subscriptions
    $categories = array();
    foreach ($input['Products'] as $id) {

      //  Check that product is valid
      $oProduct = ProductQuery::create()->findPK($id);
      if (!$oProduct)
        throwSingleError('InvalidProductId:Products');

      //  Check that there is no more than one product per category
      if (in_array($oProduct->getCategoryId(), $categories))
        throwSingleError('DuplicateCategory:Products');
      $categories[] = $oProduct->getCategoryId();

      //  Create a subscription row for each product
      $oSub = new Subscription();
      $oSub->setProduct($oProduct);
      $oSub->setDefaultProductId($oProduct->getId());

      $oUser->addSubscription($oSub);
      if (isset($oSite)){
        if( !$oUser->getDoorstep() ){
          $oSub->setDeliverySite($oSite);
        }else{
          $doorstep = DoorStepQuery::create()->findOneByZip( $input['Zip'] );
          if(!$doorstep)
            throwSingleError('Invalid Zip Code');

          $oSub->setDeliveryDay( $doorstep->getDefaultDay() );
          $oSub->setDeliverySiteId( $oSite->getId() );

        }
      }


      //  If user is in status AddressPending, set subscription
      //  status to Pending
      if ($oUser->getCustomerStatus() == 'AddressPending')
        $oSub->setStatus('Pending');

    }

    //  Encrypt the password
    $oUser->setPassword(UserPeer::getHash($input['Password']));

    //  Set hash for email confirmation
    $oUser->setHash(buildHash(15));

    //  Finally, save the user
    $oUser->save();

    foreach ($oUser->getSubscriptions() as $subscription) {
      $subscription->createBag();
    }

    //  Email them the welcome email!
    $url = $oUser->getConfirmLink();
    $helper = new SendGridHelper($oUser->getEmail(), 'confirm-email');
    $helper->merge(array(
        'firstname'     => $oUser->getFirstName(),
        'confirmURL'    => $url
    ));
    $helper->send();

    //  Email all administrators that there is a new user
    $aAdmins = UserQuery::create()
        ->filterByAdminAccess('SuperAdmin')
        ->find();
    $products = array();
    foreach ($oUser->getSubscriptions() as $oSub)
      $products[] = $oSub->getProduct()->getTitle().' - '.money($oSub->getProduct()->getPrice()).'/delivery';
    foreach ($aAdmins as $oAdmin) {
      $helper = new SendGridHelper($oAdmin->getEmail(), 'new-user-admin');
      $helper->merge(array(
          'admin'     => $oAdmin->getFirstName(),
          'firstname' => $oUser->getFirstName(),
          'lastname'  => $oUser->getLastName(),
          'email'     => $oUser->getEmail(),
          'products'  => implode('<br/>', $products)
      ));
      $helper->send();
    }

    jsonSuccess(array('id' => $oUser->getId(), 'url' => '/gateway/confirm/'.$oUser->getHash()));

  }

}