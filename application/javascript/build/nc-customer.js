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


var Account = MyClass.extend({

  _controller: 'account',
  defaultCategoryId: 1,
  options: {},

  init: function() {
    var _class = this;
    var rel = $('#_Account').attr('rel');
    var params = $.parseJSON(rel);
    _class.options = params;
    _class.sessionID = params.sessionID;
    switch (params.p) {
      case 'subscriptions':
        _class.setupSlider();
        _class.setupSubscriptions();
        _class.populateAddSubscription(_class.defaultCategoryId);
        break;
      case 'billing':
        _class.setupBilling();
        break;
      case 'profile':
        _class.setupProfile();
        break;
    }
  },

  setupSlider: function() {
    var _class = this;
		$(".del-tog").click(function(event) {
			$(".delOptions").addClass('hide');
			event.stopPropagation(); 
			$(this).nextAll("ul.active").removeClass('hide');			
		});
		$(document).click(function(){  
  			$(".delOptions").addClass('hide'); //hide the button
        $(".msg_error").animate({
        height: "0"
          }, 500, function() {
  			});	
		});
    $('.makeSlider').each(function(i, el){
      $(el).liquidSlider({
        autoHeight:false,
        dynamicTabs: true,
        hoverArrows: false,
        dynamicTabsPosition: "bottom"
      });
    });
    $('.skipLink').on('click', function() {
      var infoEl = $(this).parents('li.infoEl')[0];
      var iSub = $(infoEl).attr('data-isub');
      var date = $(infoEl).attr('data-date');
      var data = {
        iSub: iSub,
        date: date,
        iUser: _class.options.iUser,
        status: 'skipped'
      };
      _class.ajax('skipSingleOrder', data, _class.cb_changeDeliveryOption);
    });
    $('.donateLink').on('click', function() {
      var infoEl = $(this).parents('li.infoEl')[0];
      var iSub = $(infoEl).attr('data-isub');
      var date = $(infoEl).attr('data-date');
      var data = {
        iSub: iSub,
        date: date,
        iUser: _class.options.iUser,
        status: 'donated',
      };
      _class.ajax('donateSingleOrder', data, _class.cb_changeDeliveryOption);
    });
    $('.reactivatePopupLink').on('click', function() {
      var infoEl = $(this).parents('li.infoEl')[0];
      var iSub = $(infoEl).attr('data-isub');
      var date = $(infoEl).attr('data-date');
      var data = {
        iSub: iSub,
        date: date,
        iUser: _class.options.iUser,
        iOrder: $(infoEl).attr('data-iorder'),
        status: 'active'
      };
      _class.ajax('removeSkippedOrder', data, _class.cb_changeDeliveryOption);
    });
  },

  setupBilling: function() {
    var _class = this;
    $('#update').on('click', function(){
      _class.getStripeToken();
    });
  },

  switchStatus: function(iSub, date, status, iOrder) {
    var el = $('#skipSlider_'+iSub).find('[data-date="'+date+'"]');
    var iOrder = (iOrder == null) ? null : iOrder;
    $.each(['skipped', 'donated', 'active'], function(i, str){
      $(el).removeClass('del-'+str);
    });
    $(el).addClass('del-'+status);
    switch (status) {
      case 'skipped':
        $(el).find('h3').html('Delivery<br/>Skipped');
        $(el).find('.delOptions').removeClass('active');
        $(el).find('.delOptions.for-skipped').addClass('active');
        $(el).attr('data-iorder', iOrder);
        break;
      case 'donated':
        $(el).find('h3').html('Delivery<br/>Donated');
        $(el).find('.del-tog').hide();
        break;
      case 'active':
        $(el).find('h3').html('Delivery<br/>Scheduled');
        $(el).find('.delOptions').removeClass('active');
        $(el).find('.delOptions.for-active').addClass('active');
        break;
    }
  },

  populateAddSubscription: function(category) {
    var _class = this;
    _class.ajax('getProducts', {iCategory: category, iUser: _class.options.iUser}, function(response){
      var data = $.parseJSON(response);
      if (!data.success)
        alertify.error(err(data.errorCodes[0]));
      else {
        $('#radioContainer').loadTemplate($('#tmpl-product'), data.products);
        $('.productCheck').each(function(index, el){
          el = $(el);
          el.attr('value', el.attr('alt'));
        });
      }
    });
  },

  setupSubscriptions: function() {
    var _class = this;
    $('#selectAddSubscription').on('change', function(e){
      _class.populateAddSubscription($(e.target).val());
    });
    $('#submitAddSubscription').on('click', function(){
      _class.ajaxForm('addSubscription', 'addSubscriptionForm', _class.cb_standardAlt);
      return false;
    });
    $('.confirmSkip').on('click', function(e){
      var id = $(e.target).attr('rel');
      _class.ajaxForm('skipOrders', 'tripForm_'+id, _class.cb_standardAlt);
      return false;
    });
    $('.submitChange').on('click', function(e){
      var id = $(e.target).attr('rel');
      _class.ajaxForm('changeSubscription', 'changeSubscription'+id, _class.cb_standardAlt);
      return false;
    });
    $('.stopSubscription').on('click', function(e){
      var data = {
        iUser:  $(e.target).attr('data-uid'),
        iSub:   $(e.target).attr('data-sid')
      };
      alertify.confirm($(e.target).attr('data-msg'), function(e){
        if (e)
          _class.ajax('stopSubscription', data, _class.cb_standardAlt);
      });
    });
    $('.datepickerInput').datepicker({
        format: 'mm/dd/yyyy'
    }).on('changeDate', function(){
      $(this).datepicker('hide');
    });
    $('.reactivateLink').on('click', function(){
      var data = {
        iOrder:   $(this).attr('rel'),
        iUser:    _class.options.iUser
      };
      _class.ajax('removeSkippedOrder', data, _class.cb_standardAlt);
    });
  },

  setupProfile: function() {
    var _class = this;
    $('#submitEditProfile').on('click', function(){
      _class.ajaxForm('editProfile', 'fAccount', _class.cb_standard);
      return false;
    });
    $('#updatePassword').on('click', function(){
      _class.ajaxForm('newPassword', 'security', _class.cb_standard);
      return false;
    });
  },

  getStripeToken: function() {
    var _class = this;
    Stripe.setPublishableKey(_class.options.stripeKey);
    Stripe.card.createToken({
      number:   $('#ccNum').val(),
      cvc:      $('#ccSec').val(),
      exp_month:$('#ccExpMonth').val(),
      exp_year: $('#ccExpYear').val()
    }, _class._bind(_class, _class.cb_getStripeToken));
  },

  skipDisplayError: function(msg) {
    var _class = this;
    $('.msg_msg').html(msg);
    $(".msg_error").removeClass('hide');	
    $(".msg_error").animate({
      height: "220px"
      }, 500, function(){
    });
  },

  cb_changeDeliveryOption: function(response) {
    var _class = this;
    var data = $.parseJSON(response);
    if (!data.success) {
      var msg = err(data.errorCodes[0]);
      _class.skipDisplayError(msg);
    } else
      _class.switchStatus(data.request.iSub, data.request.date, data.request.status, (data.iOrder ? data.iOrder : null));
  },

  cb_getStripeToken: function(status, response) {
    var _class = this;
    if (response.error) {
      alertify.alert(response.error.message);
    } else {
      _class._stripeToken = response['id'];
      _class.ajax('updateBilling', {token: response['id'], iUser: _class.options.iUser}, _class.cb_standardAlt);
    }
  }

});
