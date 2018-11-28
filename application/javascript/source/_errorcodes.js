var _errorCodes = [];

_errorCodes['General'] = 'There was an error. Please contact the site administrator.';

//  General
_errorCodes['SignupGeneral'] = 'The following errors occurred:<br/><br/>';
_errorCodes['UserHasAddressPending'] = 'Deliveries are not available to your current address, so subscriptions cannot be added.';
_errorCodes['SignupMain'] = 'There was at least one error with your submission. Please fix the fields highlighted in red.';
_errorCodes['RequiredFieldMissing'] = 'This field is required.';
_errorCodes['InvalidEmail'] = 'Please enter a valid email address';
//Revision Start By EWS ------ Add Terms of Service Error Description
_errorCodes['terms'] = 'Please select the terms of services';
_errorCodes['address1'] = 'Please fill your address';
//Revision End By EWS ------ Add Terms of Service Error Description
_errorCodes['DuplicateEntry'] = 'We already have this email address on file.';
_errorCodes['DuplicateEmail'] = 'We already have this email address on file.';
_errorCodes['DuplicateSubscription'] = 'You can only have one subscription for each type of product.';
_errorCodes['InvalidOldPassword'] = 'Your old password was not entered correctly.';
_errorCodes['InvalidPassword'] = 'Please enter a new password.';
_errorCodes['PasswordMismatch'] = 'Passwords do not match.';
_errorCodes['EmptyPassword'] = 'Password cannot be empty.';
_errorCodes['InvalidAccessCode'] = 'The access code you entered is invalid. Please enter another one, or use a custom address.';
_errorCodes['InvalidLogin'] = 'Invalid email or password.';
_errorCodes['InvalidCode'] = 'This discount code is invalid.';
_errorCodes['InvalidUser'] = 'You do not have access to this page.';
_errorCodes['InvalidProductId'] = 'This product is not valid';
_errorCodes['NoProducts'] = 'No product selected';
_errorCodes['ExpiredDiscount'] = 'This discount is no longer being offered.';
_errorCodes['InvalidOrderStatus'] = 'This order is not in a state where it can be charged.';
_errorCodes['DisabledFeature'] = 'This feature is disabled right now.';
_errorCodes['NoOrders'] = 'There are no orders in this date range.';
_errorCodes['CannotSkip'] = "Uh Oh! Your order for this week has already been placed with our farmers. Our commitment to providing locally sourced food directly from the farm requires us to purchase exactly as many items as 4P Foods needs at least 48 hours prior to delivery. Please reach out to community@4pfoods.com if you have further questions and we'll be happy to assist you in any way that we can.";
_errorCodes['CannotDonate'] = "Thanks for trying to donate your bag! Unfortunately, it’s past the cut off time so your bag is already being set up to be delivered to you.  We can usually override that though if we have enough time, just contact Bonnie, Amber, or Tom at community@4pfoods.com and they’ll take care of you.  Otherwise, we’ll deliver your bag on your usual delivery day.  Thanks for your support!";
_errorCodes['CannotReactivateSkipped'] = 'Uh oh! Our orders for this week have already been sent out, so it is no longer possible to reactivate this skipped order.';
_errorCodes['NoDates'] = 'Please make sure you have entered a starting and ending date.';
_errorCodes['HasPendingOrder'] = 'Aw shucks, we have already sourced your order for the next delivery. Please be sure to cancel at least two days before your next delivery. If you have any questions please contact us at community@4pfoods.com.';
_errorCodes['UnconfirmedAccount'] = 'This account has not yet been confirmed.';
_errorCodes['InvalidAccount'] = 'We could not find an account with that address.';
_errorCodes['RefundMinAmount'] = 'Refund cannot be less than $1.';
_errorCodes['RefundMaxAmount'] = 'You cannot refund more than the amount of the payment.';
_errorCodes['AdminLogin'] = 'Administrators cannot login here.';
_errorCodes['InvalidUserState'] = 'User cannot be archived because they have active subscriptions.';
_errorCodes['InvalidSwitchDay'] = 'You cannot switch the day right now. Please contact an administrator for details.';
_errorCodes['InvalidDeliverySite'] = 'Invalid delivery site.';
_errorCodes['ActiveDeliverySite'] = 'Only inactive delivery sites can be hidden. Please deactivate this site first.';
_errorCodes['InvalidDelivery'] = 'Invalid delivery site.';

//  Stripe
_errorCodes['card_declined'] = 'This card has been declined.';
_errorCodes['processing_error'] = 'There was an error processing your credit card.';
_errorCodes['incorrect_number'] = 'There was an error processing your credit card.';
_errorCodes['invalid_cvc'] = 'The CVC number is incorrect.';

// For Item Categories
_errorCodes['InvalidCategory'] = 'We could not find category with that ID.';
_errorCodes['DependentExist'] = 'Selected Category has dependent food items. Deactivate them first.';

// For Items
_errorCodes['InvalidItems'] = 'We could not find Item in our system.';
_errorCodes['Suppliers']  = 'Supplier Required';

// For Bags
_errorCodes['InvalidItemProduct'] = 'We could not find Bag with ID.';

// Front end user interface.
_errorCodes['InvalidInput'] = 'No credentials provided. Please click valid link.';
_errorCodes['PointsOverFlow'] = 'You don\'t have enough points in your inventory';
_errorCodes['LastItemStanding'] = 'You only have one item left in your bag. In order to process your order we need at least one item.';
_errorCodes['InvalidUserBagItem'] = 'We could not find this item in our system';
_errorCodes['InvalidBagForCurrentUser'] = 'We could not find this subscription for current customer';
_errorCodes['CategoryNotMatch'] = 'Category Not matched';
_errorCodes['InvalidWeeklyBag'] = 'Subscribed bag not found in our system. Please follow correct link.';

// Suppliers
_errorCodes['dependentItems'] = 'This supplier has dependent items and can not be Unpublished.';

_errorCodes['RequestFailed'] = 'Request Failed Please contact system Admin.';
_errorCodes['OrdersCreated'] = 'Subscription can only be changed 48 hours prior to creating orders. Please try after your current order delivered.';
_errorCodes['BagsDateExceed'] = "Bags can only be published before 48 hours of delivery date.";
_errorCodes['OrderExist'] = 'Order already exist for this date please change subscription after order delivered';
_errorCodes['InvalidDate'] = "Please provide valid date.";

_errorCodes['OutOfRangeDate'] = 'Date is out of range';

function err(index) { return ($defined(_errorCodes[index])) ? _errorCodes[index] : _errorCodes['General']; }
