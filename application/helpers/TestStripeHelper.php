<?php

require_once(APPPATH.'third_party/stripe/lib/Stripe.php');
require_once(APPPATH.'config/creds.php');
require_once(APPPATH.'config/constants.php');

class TestStripeHelper {

    //  Can be passed to constructor to simulate actions being executed on other days
    private $_today;

    /**
     *  For testing purposes, the value of "today" can be passed
     *  to the constructor. This is only possible in dev environment
     */
    function __construct($today = NULL) {
        $this->_today =  $today;
    }

    /**
     *  Process successful charge
     */
    function processChargeSucceeded(Hook $oHook, $obj) {

        //  Attempt to find payment object
        $id = $obj->data->object->id;
        $oPayment = PaymentQuery::create()->findOneByStripeChargeId($id);

        //  For orphaned record, stop here
        if (!$oPayment) {
            $oHook->setStatus('Stopped');
            $oHook->setNotes('Payment with ID '.$id.' not found in database.');
            $oHook->save();
            return false;
        }

        //  Verify payment ID with Stripe
        Stripe::setAPIKey(STRIPE_SK);
        $stripeObj = Stripe_Charge::retrieve($id);
        if (!(bool)($stripeObj)) {
            $oHook->setStatus('Stopped');
            $oHook->setNotes('Payment ID '.$id.' could not be verified. Found failure status as null.');
            $oHook->save();
            return false;
        }

        //  Set verified
        $oHook->setIsVerified(1);

        //  Flag the payment FAILED
        $oPayment->succeed($oHook);

        //  Save
        $oHook->save();

        //  Get string ready for receipt
        $aStr = array();
        $format = '%s • %s (%s) - %s';
        foreach ($oPayment->getOrders() as $oOrder) {
            $aStr[] = sprintf($format,
                $oOrder->getDeliveryScheduledFor('M. d, Y'),
                $oOrder->getProductName(),
                money($oOrder->getPrice()),
                $oOrder->getStatus()
            );
        }
        $tStr = implode('<br/>', $aStr);

        //  Send an email to the customer and to the administrators
        $oUser = $oPayment->getUser();
        $helper = new SendGridHelper($oUser->getEmail(), 'monthly-receipt');
        $helper->merge(array(
            'firstname'     => $oUser->getFirstName(),
            'lastname'      => $oUser->getLastName(),
            'email'         => $oUser->getEmail(),
            'invoicenumber' => $oPayment->getId(),
            'total'         => money($oPayment->getAmountPaid()),
            'orderList'     => $tStr
        ));
        $helper->send();

    }

    /**
     *  Process failed charge
     */
    function processChargeFailed(Hook $oHook, $obj) {

        //  Attempt to find payment object
        $id = $obj->data->object->id;
        $oPayment = PaymentQuery::create()->findOneByStripeChargeId($id);

        //  For orphaned record, stop here
        if (!$oPayment) {
            $oHook->setStatus('Stopped');
            $oHook->setNotes('Payment with ID '.$id.' not found in database.');
            $oHook->save();
            return false;
        }

        //  Verify payment ID with Stripe
        Stripe::setAPIKey(STRIPE_SK);
        $stripeObj = Stripe_Charge::retrieve($id);
        if (!(bool)($stripeObj->failure_code)) {
            $oHook->setStatus('Stopped');
            $oHook->setNotes('Payment ID '.$id.' could not be verified. Found failure status as null.');
            $oHook->save();
            return false;
        }

        //  Set verified
        $oHook->setIsVerified(1);

        //  Flag the payment FAILED
        $oPayment->fail($oHook);

        //  Save
        $oHook->save();

        //  Get string ready for receipt
        $aStr = array();
        $format = '%s • %s (%s) - %s';
        foreach ($oPayment->getOrders() as $oOrder) {
            $aStr[] = sprintf($format,
                $oOrder->getDeliveryScheduledFor('M. d, Y'),
                $oOrder->getProductName(),
                money($oOrder->getPrice()),
                $oOrder->getStatus()
            );
        }
        $tStr = implode('<br/>', $aStr);

        //  Send an email to the administrators
        foreach (UserQuery::getAdmins() as $oAdmin) {
            $oUser = $oPayment->getUser();
            $helper = new SendGridHelper($oAdmin->getEmail(), 'failed-transaction-email');
            $helper->merge(array(
                'firstname'     => $oUser->getFirstName(),
                'lastname'      => $oUser->getLastName(),
                'email'         => $oUser->getEmail(),
                'invoicenumber' => $oPayment->getId(),
                'total'         => money($oPayment->getAmountPaid()),
                'errorcode'     => $stripeObj->failure_code,
                'orderList'     => $tStr
            ));
            $helper->send();
        }

    }

    /**
     *  Generates a Delivery object for pairs of date & site, to be
     *  signed by drivers when they complete drop-offs
     */
    function createDeliveries() {

        //  Pull number of hours for cutoff from OrderPeer object
        $daysBeforeNow = OrderPeer::CHARGE_DAYS_BEFORE;

        $matchDay = ( new DateTime($this->_today) )->format('D');
        $matchDate = ( new DateTime($this->_today) )->format('Y-m-d');

        //  Get PENDING order scheduled for the target date
        $aOrders = OrderQuery::create()
            ->filterByStatus('Pending')
            ->filterByDeliveryScheduledFor($matchDate)
            ->find();

        //  File orders together by siteID
        // Only add deliveries that doesn't have doorstep
        $aDeliveries = array();
        $doorStepDeliveries = [];
        $tempDeliveries = [];
        foreach ($aOrders as $oOrder){
            $oUser = $oOrder->getSubscription()->getUser();
            if( $oUser->getDoorStep() == 0 ){
                $aDeliveries[$oOrder->getDeliverySiteId()][] = $oOrder;
            }else{
                $doorStepDeliveries[$oUser->getId()] = [
                    'DeliverySiteId'      => $oOrder->getDeliverySiteId(),
                    'doorstep'            => 1
                ];
                $tempDeliveries[$oUser->getId()][] = $oOrder;
            }
        }

        //  Iterate through sites and create a delivery item for each.
        //  Add orders for that delivery to the delivery object
        foreach ($aDeliveries as $iSite => $ordersList) {
            $oDelivery = new Delivery();
            $oDelivery->fromArray(array(
                'DeliverySiteId'      => $iSite,
                'DeliveryDate'        => $matchDate,
                'DeliveryDay'         => $matchDay
            ));
            $oDelivery->save();
            foreach ($ordersList as $oOrder){
                $oOrder->setDeliveryId( $oDelivery->getId() )->save();
            }
        }
        // Now add doorstep deliveries
        foreach ($doorStepDeliveries as  $userId => $doorStepDelivery ) {
            $oDelivery = new Delivery();
            $oDelivery->fromArray(array(
                'DeliverySiteId'      => $doorStepDelivery['DeliverySiteId'],
                'DeliveryDate'        => $matchDate,
                'DeliveryDay'         => $matchDay,
                'Doorstep'            => 1,
                'UserId'              => $userId
            ));
            $oDelivery->save();
            foreach ($tempDeliveries[$userId] as $item) {
                $item->setDeliveryId( $oDelivery->getId() )->save();
            }
        }


    }

    /**
     *  Uses active subscriptions to create orders, each of which
     *  represents a paid-for deliverable for the customer and an
     *  amount of money owed
     */
    function createOrders($date = null) {
        //  Pull number of hours for cutoff from OrderPeer object
        $daysBeforeNow = OrderPeer::CHARGE_DAYS_BEFORE;

        //  We are looking for subscriptions whose delivery day is
        //  X days from now. Find weekday and date for matching
        $matchDay = ( new DateTime($this->_today) )->format('D');
        $matchDate = ( new DateTime($this->_today) )->format('Y-m-d');


        //  Get subscriptions with correct delivery day and Active status
        $aSubs = SubscriptionQuery::create()
            ->filterByDeliveryDay($matchDay)
            ->filterByStatus('Active')
            ->find();

        //  Cycle through subscriptions to create orders
        foreach ($aSubs as $oSub) {
            //  Check to make sure there is not already an order created
            //  for the delivery date. This would happen if the customer
            //  already chose to SKIP or DONATE the order in advance.
            $existingOrder = OrderQuery::create()
                ->filterBySubscriptionId($oSub->getId())
                ->filterByDeliveryScheduledFor($matchDate)
                ->findOne();

            //  If there is already an order for this subscription on this
            //  date, do nothing and move on
            if ($existingOrder)
                continue;

            //  Check to make sure the delivery site is valid and
            //  is accepting deliveries
            $oSite = $oSub->getDeliverySite();
            if (!$oSite)
                continue;
            if (!$oSite->acceptsDeliveries())
                continue;

            $oUserBag = UserBagQuery::create()
                ->filterByDate($matchDate)
                ->findOneBySubscriptionId($oSub->getId());

            if(!$oUserBag)
            {
                continue;
            }
            $oUser = $oSub->getUser();
            $pricePaid = $oUserBag->getProduct()->getPrice();

            //  If we are this far, we are ready to create an order
            $oOrder = new Order();

            //  Set delivery date so order is unique
            $oOrder->setDeliveryScheduledFor($matchDate);

            //  Afix order with subscription price
            $oOrder->setPrice($pricePaid);

            //Set User Bag ID
            $oOrder->setUserBagId( $oUserBag->getId() );


            //  Add to the Subscription object. This also sets the delivery
            //  site to the default delivery site (in the model class)
            $oOrder->setDeliverySiteId( $oSite->getId() );
            $oOrder->setSubscriptionId( $oSub->getId() );


            //  Find out whether the user is eligible for a discount
            $oDiscount = $oUser->getNextAvailableDiscount();
            if (!is_null($oDiscount)){
                $oOrder->applyDiscount($oDiscount);
            }

            //  Status should automatically be set to Pending. Save it.
            $oOrder->save();
            $oSub->save();

        }

        // Once orders are created Lock Admin bags
        $adminBags = BagsQuery::create()->filterByDate($matchDate)->find();
        foreach ($adminBags as $adminBag) {
            $adminBag->setLocked(1)->save();
        }

    }

    /**
     *  Cycles through all Orders delivered since this day last month, aggregates
     *  monies owed from each customer, then cycles through customers and
     *  charges their accounts using Stripe
     */
    function executeCharges(){

        //  Cut-off date is one month ago from today
        // $beginDate = date('Y-m-d', strtotime($this->_today.' -1 Month'));

        $beginDate = $this->_today->format('Y-m-d');

        //  Get all Orders with status ACTIVE, DONATE, or PENDING that have
        //  delivery dates between the cutoff date and today. Must be unpaid.
        $aOrders = OrderQuery::create()
            ->filterByStatus(array('Delivered', 'Donated', 'Pending'))
            ->filterByDeliveryScheduledFor(array('min' => $beginDate, 'max' => $beginDate))
            ->filterByPaidAt(NULL)
            ->find();
        
        //  Build a table of customers and aggregate price data
        $aCharges = array();
        foreach ($aOrders as $oOrder) {

            //  Pull the Subscription and Customer from the order
            $oSub = $oOrder->getSubscription();
            $oUser = $oSub->getUser();

            //  If the User is not already in the charges table, add them
            if (!isset($aCharges[$oUser->getId()])) {
                $aCharges[$oUser->getId()] = array(
                    'oUser'   => $oUser,
                    'aOrders' => array(),
                    'totalCharge' => 0.00,
                    'Deliverycharge' => 0,
                    'tax'            => 0.00
                );
            }

            //  Push the order onto the customer's orders array and increment the
            //  total charge amount by the order price
            $userInfo =& $aCharges[$oUser->getId()];
            $userInfo['aOrders'][] = $oOrder;

            //Revision Start By EWS ------ Set total price as totalCharge
            $userInfo['totalCharge'] += $oOrder->getPrice();

        }

        //  Iterate through charges and execute each one
        foreach ($aCharges as $charge) {

            // Add tax and delivery Charge for user.
            $charge['tax'] = $charge['oUser']->getStateTax();

            $charge['Deliverycharge'] = $charge['oUser']->getDeliveryCharge();

            // Add total charges
            if( $charge['tax']  > 0 )
            {
                $charge['tax'] = round( ( $charge['totalCharge'] * ( $charge['tax'] /100 ) ), 2);
                $charge['totalCharge'] = $charge['totalCharge'] + $charge['tax'];
            }
            $charge['totalCharge'] =  (float)$charge['totalCharge'] + $charge['Deliverycharge'] ;

            //  Execute the charge
            $result = $this->executeSingleCharge($charge['oUser'], $charge);

            //  Handle errors
            if (!$result['success']) {

                //  Log it
                $oError = new PaymentError();
                $oError->setRegardingUser($charge['oUser']);
                $data = array(
                    'result'    => $result,
                    'charge'    => $charge
                );
                $oError->setContents(json_encode($data));
                $oError->save();

                //  Get string ready for receipt
                $aStr = array();
                $format = '%s • %s (%s) - %s';
                foreach ($charge['aOrders'] as $oOrder) {
                    $aStr[] = sprintf($format,
                        $oOrder->getDeliveryScheduledFor('M. d, Y'),
                        $oOrder->getSubscriptionProductTitle(),
                        money($oOrder->getPrice()),
                        $oOrder->getStatus()
                    );
                }
                $tStr = implode('<br/>', $aStr);

                //  Send an email to the administrators
//                foreach (UserQuery::getAdmins() as $oAdmin) {
//                    $oUser = $charge['oUser'];
//                    $helper = new SendGridHelper($oAdmin->getEmail(), 'failed-transaction-admin');
//                    $helper->merge(array(
//                        'firstname'     => $oUser->getFirstName(),
//                        'lastname'      => $oUser->getLastName(),
//                        'email'         => $oUser->getEmail(),
//                        'total'         => money($charge['totalCharge']),
//                        'errorcode'     => $result['fullResult']['fullResult']->getMessage(),
//                        'orderList'     => $tStr
//                    ));
//                    $helper->send();
//                }

                continue;

            }

            //  Iterate through orders and mark them PAID with a timestamp
            foreach ($charge['aOrders'] as $oOrder) {
                $oOrder->setPaidAt(strtotime($this->_today));
                $oOrder->setPaymentId($result['paymentID']);
                $oOrder->save();
            }

        }

    }

    /**
     *  Charge user for a single order, and associate that order with the payment
     */
    function executeChargeForOrders( $aOrders ) {

        $totalCharge = 0;

        //  Check for valid status
        foreach ( $aOrders as $oOrder ) {
            if ( !$oOrder->isPayable() )
                return false;

            $totalCharge += (float)$oOrder->getPrice();
        }

        //  Pull the Subscription and Customer from the order
        $oSub = $aOrders[0]->getSubscription();
        $oUser = $oSub->getUser();

        $charge = [
            'oUser'   => $oUser,
            'aOrders' => $aOrders,
            'totalCharge' => ( $totalCharge + $oUser->getDeliverycharge() ),
            'deliveryCharge' => $oUser->getDeliverycharge(),
            'tax'            => $oUser->getStateTax()
        ];

        $charge['tax'] = round( ( $charge['totalCharge'] * ( $charge['tax'] / 100 ) ), 2 );
        $charge[ 'totalCharge' ] = $totalCharge + $charge['tax'];

        //  Execute the charge
        $result = $this->executeSingleCharge($oUser, $charge);

        //  Handle errors
        if (!$result['success']) {

            //  Log it
            $oError = new PaymentError();
            $oError->setRegardingUser($charge['oUser']);
            $data = array(
                'result'    => $result,
                'charge'    => $charge
            );
            $oError->setContents(json_encode($data));
            $oError->save();

            //  Get string ready for receipt
            $aStr = array();
            $format = '%s • %s (%s) - %s';
            foreach ($charge['aOrders'] as $oOrder) {
                $aStr[] = sprintf($format,
                    $oOrder->getDeliveryScheduledFor('M. d, Y'),
                    $oOrder->getSubscriptionProductTitle(),
                    money($oOrder->getPrice()),
                    $oOrder->getStatus()
                );
            }
            $tStr = implode('<br/>', $aStr);

            //  Send an email to the administrators
            foreach (UserQuery::getAdmins() as $oAdmin) {
                $oUser = $charge['oUser'];
                $helper = new SendGridHelper($oAdmin->getEmail(), 'failed-transaction-admin');
                $helper->merge(array(
                    'firstname'     => $oUser->getFirstName(),
                    'lastname'      => $oUser->getLastName(),
                    'email'         => $oUser->getEmail(),
                    'total'         => money($charge['totalCharge']),
                    'errorcode'     => $result['fullResult']['fullResult']->getMessage(),
                    'orderList'     => $tStr
                ));
                $helper->send();
            }
        }

        foreach ($charge['aOrders'] as $oOrder) {
            $oOrder->setPaidAt(strtotime($this->_today));
            $oOrder->setPaymentId($result['paymentID']);
            $oOrder->save();
        }

        return true;
    }

    /**
     *  Executes a Stripe charge for a customer and creates a Payment object
     *  with all of the details. Note that this method does not handle connecting
     *  Payment objects with Order objects. This must be done separately.
     */
    function executeSingleCharge(User $oUser, $amount) {

        //  Set up the Stripe charge
        $sendToStripe = array(
            'amount'    => $amount['totalCharge'] * 100,
            'currency'  => 'usd',
            'customer'  => $oUser->getStripeId()
        );
        
        //  Set up array for returning result of charge
        $return = array(
            'success'     => false,
            'paymentID'   => NULL,
            'errorCode'   => NULL
        );

        $result = $this->chargeCustomer($sendToStripe);
        if (!$result['success']) {
            $return['errorCode'] = $result['errorCode'];
            $return['fullResult'] = $result;
            return $return;
        }

        //  If we are this far, the request succeeded. Store the
        //  charge info in a payment object and return the ID
        $oPayment = new Payment();
        $oPayment->fromArray(array(
            'StripeCustomerId'      => $result['card']['customer'],
            'StripeCardId'          => $result['card']['id'],
            'StripeChargeId'        => $result['id'],
            'StripeResponse'        => $result['response'],
            'CardType'              => $result['card']['type'],
            'CardLastFour'          => $result['card']['last4'],
            'CardExpMonth'          => $result['card']['exp_month'],
            'CardExpYear'           => $result['card']['exp_year'],
            'AmountPaid'            => $result['amount'] / 100,
            'Tax'                   => $amount['tax'],
            'Deliverycharge'        => $amount['Deliverycharge']
        ));
        $oUser->addPayment($oPayment);
        $oUser->save();

        //  Set success=true and return the ID
        $return['success'] = true;
        $return['paymentID'] = $oPayment->getId();
        return $return;

    }

    /**
     *  Creates a new Stripe customer account and returns either
     *  an error or the customer's ID
     */
    function createCustomer($data) {

        //  Send the request
        try {
            $result = $this->_stripe('Customer', $data);
        } catch (Exception $e) {
            return $this->_parseError($e);
        }

        // Return the customer ID and success=true
        $return = array(
            'success' => true,
            'id'      => $result->id
        );

        return $return;

    }

    /**
     *  Charges a customer using Stripe
     */
    function chargeCustomer($data) {

        //  Send the request
        try {
            $result = $this->_stripe('Charge', $data);
        } catch (Exception $e) {
            return $this->_parseError($e);
        }

        // Return the customer ID and success=true
        $return = array(
            'success' => true,
            'id'      => $result->id,
            'card'    => $result->card,
            'amount'  => $result->amount,
            'response'=> json_encode($result)
        );

        return $return;

    }

    /**
     *  Updates a customer with new card information
     */
    function updateCustomerBilling(User $oUser, $token) {

        Stripe::setAPIKey(STRIPE_SK);
        $customer = Stripe_Customer::retrieve($oUser->getStripeId());
        $customer->card = $token;
        $customer->save();

    }

    /**
     *  Issue a refund for a payment
     */
    function issueRefund(Payment $oPayment, $amount) {

        //  Verify amount
        if ($amount > $oPayment->getRefundableAmount())
            return false;

        //  Execute Stripe refund
        $data = array(
            'amount'    => $amount * 100
        );
        Stripe::setAPIKey(STRIPE_SK);
        $charge = Stripe_Charge::retrieve($oPayment->getStripeChargeId());
        try {
            $result = $charge->refund($data);
        } catch (Exception $e) {

            //  Fill me in

        }

        $oRefund = new Refund();
        $oRefund->setAmount($amount);
        $oRefund->setStripeTxnId($result->refunds[0]->balance_transaction);
        $oPayment->addRefund($oRefund);
        $oPayment->save();

        $return = array(
            'success'   => true,
            'id'        => $oRefund->getId()
        );

        return $return;

    }

    /**
     *  Retrieve a charge from Stripe using PaymentID. This does
     *  not have an implementation that bypasses Stripe, so only
     *  use it with live data
     */
    function retrieveCharge(Payment $oPayment) {

        $stripeID = $oPayment->getStripeChargeId();

        Stripe::setAPIKey(STRIPE_SK);
        $result = Stripe_Charge::retrieve($stripeID);
        $return = array(
            'AmountPaid'    => $result->amount / 100,
            'IsRefunded'      => ($result->refunded) ? 1 : 0,
            'StripeCustomerId'=> $result->card->customer
        );
        return $return;

    }

    /**
     *  Actual Stripe request encapsulated to allow for bypassing
     *  Stripe in testing
     */
    private function _stripe($type, $data) {
        Stripe::setAPIKey(STRIPE_SK);
        $class = 'Stripe_'.$type;
        if ((ENVIRONMENT != 'production') && SKIP_STRIPE) {
            $fn = 'getStripe'.$type;
            return SampleData::$fn($data);
        } else{
            return $class::create($data);
        }

    }

    /**
     *  Takes a raw JSON result from Stripe, parses it, and returns
     *  a readable response
     */
    private function _parseError($result) {

        $return = array(
            'success'     => false,
            'testing'     => 'no',
            'errorCode'   => $result->getCode().':NoField',
            'fullResult'  => $result
        );

        return $return;

    }

}