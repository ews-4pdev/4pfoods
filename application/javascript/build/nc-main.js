/**
 *	Compiler Functions: These functions must remain exactly as they are in order for the compiler
 *	to function properly produce executable code!
 */

function isiPhone(){
  return (
    //Detect iPhone
    (navigator.platform.indexOf("iPhone") != -1) ||
    //Detect iPod
    (navigator.platform.indexOf("iPod") != -1)
  );
} 
 
function toMoney(amount) {
  return "$"+amount.toFixed(2);
}

//For use in the compiling process only. Should be used in source code to wrap the
//name for an Ajax task name so the compiler targets it in task decoding
function task(task) {
	return task;
}

//For use in the compiling process. In Source Mode, it does nothing,
//but the compiler will replace calls to this function with the task code
function ttask(input) {
	return input;
}

//For use with compiled build files. Wraps the string at the passed index in the $() function
function v(num) {
	return $('#'+s_[num]);
}

function $defined(checkMe) {
  return (typeof checkMe != 'undefined');
}

//For use with compiled build files. Wraps the string at the passed index in $defined($(**))
function d(num) {
	return ($defined(v(num)));
}

//For use with compiled build files. Outputs the string at the passed index with a "." before it.
//Normal context is to translate $$('.someclass') into p(#)
function p(num) {
	return "."+s_[num];
}

//A shortcut for the most common use of the p() function
function q(num) {
	return $(p(num));
}

/**
//These functions, together with the extensions of the Element class, allow common processes to be
//minified and obfuscated. Additionally, their use in the compiler reduces the size of the output
//build file by maximize the reuse of numerical characters
function c1_(elNum, classNum) {
	return (v(elNum).c1(classNum));
}
function c2_(elNum, classNum) {
	v(elNum).c2(classNum);
}
function c3_(elNum, classNum) {
	v(elNum).c3(classNum);
}
Element.implement({
	c1: function(num){ return this.hasClass(c_[num]); },
	c2: function(num){ return this.addClass(c_[num]); },
	c3: function(num){ return this.removeClass(c_[num]); }
});
**/

/** End Compiler Functions **/


var MyClass = Class.extend({

  _controller: 'main',

  init: function(input){
    
  },

  ajax: function(task, data, $done) {
    var _class = this;
    data.task = task;
    data.sessionID = _class.sessionID;
    var request = $.ajax({
      url:  '/ajax/'+_class._controller,
      type: 'post',
      data: data
    });
    _class._ajaxCore(request, $done);
  },

  ajaxForm: function(task, formID, $done) {
    var _class = this;
    var data = {};
    data.task = task;
    data.sessionID = _class.sessionID;
    var dataString = $('#'+formID).serialize()+'&'+$.param(data);
    var request = $.ajax({
      url:  '/ajax/'+_class._controller,
      type: 'post',
      data: dataString
    });
    _class._ajaxCore(request, $done);
  },

  processErrors: function(errors, elPrefix, elSuffix) {
    $('.error-on').removeClass('error-on');
    var suffix = $defined(elSuffix) ? elSuffix : '';
    $(errors).each(function(index, error){
      var info = error.split(':');
      var errorText = _errorCodes[info[0]];
      var el = $('#'+elPrefix+info[1]+suffix);
      var parentEl = el.parents('.form-group');
      parentEl.addClass('error-on');
      parentEl.find('.error-notify').html(errorText);
    });
  },

  cb_standard: function(response) {
    var _class = this;
    var data = $.parseJSON(response);
    if (data.success)
      location.href = data.url;
    else
      _class.processErrors(data.errorCodes, data.request.prefix);
  },

  cb_standardAlt: function(response) {
    var _class = this;
    var data = $.parseJSON(response);
    if (data.success) {
      if ($defined(data.msg))
        alertify.alert(data.msg);
      else
        location.href = data.url;
    } else
      _class._displayError(data.errorCodes[0]);
  },

  _ajaxCore: function(request, $done) {
    var _class = this;
    if ($done)
      request.done(_class._bind(this, $done));
    else {
      request.done(function(response) {
        var jsonData = $.parseJSON(response);
        if (jsonData.success) {
          window.location.href = jsonData.url;
        } else
          alert(jsonData.error);
      });
    }
    request.fail(function(response, textStatus, errorThrown) {
      alertify.error('There was an error with the request. Please contact administrator.');
    });
  },

  _displayError: function(errorCode) {
    var msg = err(errorCode);
    alertify.error(msg);
  },

  _bind: function(context, method) {
    return function() {
      return method.apply(context, arguments);
    }
  }

});


var Engine = Class.extend({

  sessionKey: '',
  globals: {
    _appTriggerClass: 'app-trigger',
    _confirmLinkClass: 'confirmLink',
    _errorBlockClass: 'error-block',
    _autoMessageClass: 'auto-message'
  },

  init: function(sessionKey){
    var _class = this;
    _class.sessionKey = _class.globals._sessionKey = sessionKey;
    _class.loadGlobals();
    _class.setupConfirmLinks();
    _class.setupErrors();
    _class.setupAutoMessages();
    _class.initApps();
  },

  loadGlobals: function(){
    var _class = this;
    var globals = [];
    $.each(_class.globals, function(key, value){
      globals.push(key+" = '"+value+"';");
    });
    eval(globals.join(""));
  },

  setupAutoMessages: function() {
    $('.'+_autoMessageClass).each(function(index, el){
      alertify.alert($(el).text());
    });
  },

  setupConfirmLinks: function(){
    $('.'+_confirmLinkClass).on('click', function(e){
      var msg = $(e.target).attr('data-msg') ? $(e.target).attr('data-msg') : 'Are you sure?';
      alertify.confirm(msg, function(e2) {
        if (e2)
          location.href = e.target.href;
        else
          return false;
      });
      return false;
    });
  },

  setupErrors: function() {
    $('.'+_errorBlockClass).each(function(index, el){
      alertify.error($(el).text());
    });
  },

	initApps: function() {
		var init = [];
		$.each($('.'+_appTriggerClass), function(index, el){ init.push("window._"+el.id.substring(1)+" = new "+el.id.substring(1)+"();\n"); });
		eval(init.join(" "));
	}

});


var SignupForm = MyClass.extend({

  _controller: 'signup',
  _stripeKey: '',
  _iProduct: '',
  discount: 0.00,

  init: function() {
    var _class = this;
    var rel = $('#_SignupForm').attr('rel');
    var params = $.parseJSON(rel);
    _class._stripeKey = params.stripeKey;
    _class._iProduct = params.iProduct;
    _class.sessionID = params.sessionID;
    switch (params.p) {
      case 'signup':
        //_class.setDefaults();
        _class.setupSignup();
        _class.updateSummary();
        _class.setupCartAbandonment();
        break;
    }
  },

  setDefaults: function() {
    $('#cFirstName').val('Tom');
    $('#cLastName').val('Exampleton');
    $('#cPhone').val('1231231234');
    $('#cEmail').val('tom.exampleton@4pfoods.com');
    $('#cPassword').val('123');
    $('#cConfirmPassword').val('123');
    $('#ccNum').val('4242424242424242');
    $('#ccExpMonth').val(11);
    $('#ccSec').val(123);
    $('#pinBox').val(1234);
  },

  setupCartAbandonment: function() {
    var _class = this;
    $('#cEmail').on('blur', function(){
      var data = {
        Email:    $('#cEmail').val(),
        FirstName: $('#cFirstName').val(),
        LastName: $('#cLastName').val()
      };
      _class.ajax('submitCartAbandonEmail', data, function(){});
    });
  },

  setupSignup: function() {
    var _class = this;
    _class.setupFormElements();
    _class.setupAccessCode();
    _class.setupDiscountCode();
    $('.productCheck').on('click', function(e){
      var el = $(e.target);
      var txt = '<p class="productTitle">'+el.attr('rel')+'</p>';
      var total = parseInt(el.attr('alt'));
      $('#productPrice').html(total);
      $('#itemList').html('');
      $('#itemList').append(txt);
    });
    $('#p'+_class._iProduct).attr('checked', 'checked');
    $('.productCheck').on('click', function(){_class.updateSummary()});
  },

  setupFormElements: function() {
    var _class = this;
    $('.collapseButton').on('click', function(e){
      var closeEl = $('#'+$(e.target).attr('data-this'));
      var openEl = $('#'+$(e.target).attr('data-next'));
      closeEl.collapse('hide');
      openEl.collapse('show');
    });
    $('#completeOrder').on('click', function(){
      $(this).button('loading');
      _class.getStripeToken();
    });
  },

  setupDiscountCode: function() {
    var _class = this;
    $('#applyDiscountButton').on('click', function(){
      var data = {Code: $('#DiscountCode').val()};
      _class.ajax('validateDiscount', data, _class.cb_validateDiscount);
    });
  },

  setupAccessCode: function() {
    var _class = this;
    $('.chooseSite').on('click', function(){
      $('#pinBox').val($(this).attr('data-code'));
      $('#pinBox').trigger('keyup');
    });
    $('#openCustomAddress').on('click', function(){
      $('#customAddress').collapse('show');
      $('#customAddress input, #customAddress select, #customAddress textarea').attr('disabled',false).val('');
      $('#pinWrapper').removeClass('highlightMe');
      alertify.set({ labels: {
          ok: 'Continue Creating Account',
          cancel: 'Just Notify Me'
        }
      });
      alertify.confirm($('#customText').html(), function(e){
        if (!e) {
          var data = {
            email: $('#cEmail').val(),
            firstname: $('#cFirstName').val(),
            lastname: $('#cLastName').val()
          };
          _class.ajax('subscribe', data, _class.cb_standardAlt);
        } 
      });
      alertify.set({ labels: {
          ok: 'OK',
          cancel: 'Cancel'
        }
      });
    });
    $('#pinBox').on('click', function(){ $(this).select(); });
    $('#pinBox').on('keyup', function(){
      if ($('#pinBox').val().length == 4)
        _class.validateAccessCode($('#pinBox').val());
    });
  },

  getStripeToken: function() {
    var _class = this;
    Stripe.setPublishableKey(_class._stripeKey);
    Stripe.card.createToken({
      number:   $('#ccNum').val(),
      cvc:      $('#ccSec').val(),
      exp_month:$('#ccExpMonth').val(),
      exp_year: $('#ccExpYear').val()
    }, _class._bind(_class, _class.cb_getStripeToken));
  },

  updateSummary: function() {
    var _class = this;
    var products = [];
    var total = 0;
    $('#itemList').html('');
    $('.productCheck:checked').each(function(index, el){
      var info = $('#pi_'+$(el).val());
      var price = parseFloat(info.attr('rel'));
      total += price;
      $('#itemList').append('<div>'+info.html()+'</div>');
    });
    total -= parseFloat(_class.discount);
    $('#productPrice').html(toMoney(total));
  },

  submitForm: function() {
    var _class = this;
    var products = [];
    $('.productCheck').each(function(index, el){
      if (el.checked)
        products.push(el.value);
    });
    var data = {
      FirstName:      $('#cFirstName').val(),
      LastName:       $('#cLastName').val(),
      Address1:       $('#cAddress1').val(),
      Address2:       $('#cAddress2').val(),
      City:           $('#cCity').val(),
      StateId:        $('#cStateId').val(),
      Zip:            $('#cZip').val(),
      Phone:          $('#cPhone').val(),
      Email:          $('#cEmail').val(),
      Password:       $('#cPassword').val(),
      ConfirmPassword:$('#cConfirmPassword').val(),
      DeliverySiteNotes:          $('#cDeliverySiteNotes').val(),
      DietaryRestrictions:          $('#cDietaryRestrictions').val(),
      DiscountCode:   $('#DiscountCode').val(),
      Token:          _class._stripeToken,
      Products:       products,
      AccessCode:     $('#pinBox').val()
    };
    _class.ajax('submitSignup', data, _class.cb_submitSignup);
  },

  validateAccessCode: function(code) {
    var _class = this;
    var data = {code: code};
    _class.ajax('validateAccessCode', data, _class.cb_validateAccessCode);
  },

  cb_validateDiscount: function(response) {
    var _class = this;
    var data = $.parseJSON(response);
    if (data.success) {
      _class.discount = parseFloat(data.amount);
      $('#discountInfo').removeClass('hide');
      $('#discountAmount').html(data.amount);
      $('#discountOrders').html(data.nOrders);
    } else {
      _class.discount = 0.00;
      _class._displayError(data.errorCodes[0]);
      $('#discountInfo').addClass('hide');
    }
    _class.updateSummary();
  },

  cb_submitSignup: function(response) {
    var _class = this;
    var data = $.parseJSON(response);
    if (data.success)
      location.href = data.url;
    else {
      $('#completeOrder').button('reset');
      var alertErrors = [];
      $('.error-on').removeClass('error-on');
      $.each(data.errorCodes, function(index, code) {
        var items = code.split(':');
        var error = items[0];
        var field = items[1];
        if (field == 'NoField')
          alertErrors.push(_errorCodes[error]);
        else {
          var el = $('#c'+field).parents('.form-group');
          el.find('.error-notify').html(_errorCodes[error]);
          el.addClass('error-on');
          el.parents('.panel-collapse').collapse('show');
        }
      });
      if (alertErrors.length > 0) {
        var msg = _errorCodes['SignupGeneral']+alertErrors.join('<br/>');
        alertify.alert(msg);
      } else
        alertify.alert(err('SignupMain'));
    }
  },

  cb_getStripeToken: function(status, response) {
    var _class = this;
    if (response.error) {
      $('#completeOrder').button('reset');
      alertify.alert(response.error.message);
    } else {
      _class._stripeToken = response['id'];
      _class.submitForm();
    }
  },

  cb_validateAccessCode: function(result) {
    var _class = this;
    var data = $.parseJSON(result);
    if (data.success) {
      $('#customAddress').collapse('show');
      $('#pinWrapper').removeClass('error-on');
      $('#pinWrapper').addClass('highlightMe');
      $.each(data.site, function(key, value){
        $('#c'+key).val(value);
        $('#c'+key).attr('disabled', 'disabled');
      });
    } else {
      $('#pinWrapper').addClass('error-on');
      $('#pinWrapper').removeClass('highlightMe');
    }
  }

});


var Gateway = MyClass.extend({

  _controller: 'auth',

  init: function() {
    var _class = this;
    var rel = $('#_Gateway').attr('rel');
    var params = $.parseJSON(rel);
    _class.options = params;
    _class.sessionID = params.sessionID;
    switch (params.p) {
      case 'login':
        _class.setupLogin();
        break;
      case 'confirm':
        _class.setupConfirm();
        break;
    }
  },

  setupConfirm: function() {
    var _class = this;
    $('#resend').on('click', function(){
      _class.ajax('resendEmail', {hash: _class.options.hash}, _class.cb_resend);
    });
  },

  setupLogin: function() {
    var _class = this;
    $('#cSubmit').on('click', function(){
      _class.ajaxForm('submitLogin', 'formLogin', _class.cb_standardAlt);
      return false;
    });
    $('#reset').on('click', function(){
      _class.ajaxForm('submitForgotPassword', 'nPassword', _class.cb_standardAlt);
      return false;
    });
    $('#joinButton').on('click', function() { location.href = '/gateway/signup'; });
  },

  cb_resend: function(response) {
    var _class = this;
    var data = $.parseJSON(response);
    if (data.success)
      alertify.success('Email has been resent.');
    else
      alertify.error(err(data.errorCodes[0]));
  }

});
