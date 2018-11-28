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
    var c = this;
    data.dfdcy_ = task;
    data.sessionID = c.sessionID;
    var request = $.ajax({
      url:  '/ajax/'+c._controller,
      type: 'post',
      data: data
    });
    c._3(request, $done);
  },

  ajaxForm: function(task, formID, $done) {
    var c = this;
    var data = {};
    data.dfdcy_ = task;
    data.sessionID = c.sessionID;
    var dataString = $('#'+formID).serialize()+'&'+$.param(data);
    var request = $.ajax({
      url:  '/ajax/'+c._controller,
      type: 'post',
      data: dataString
    });
    c._3(request, $done);
  },

  _0: function(errors, elPrefix, elSuffix) {
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

  _1: function(response) {
    var c = this;
    var data = $.parseJSON(response);
    if (data.success)
      location.href = data.url;
    else
      c._0(data.errorCodes, data.request.prefix);
  },

  _2: function(response) {
    var c = this;
    var data = $.parseJSON(response);
    if (data.success) {
      if ($defined(data.msg))
        alertify.alert(data.msg);
      else
        location.href = data.url;
    } else
      c._4(data.errorCodes[0]);
  },

  _3: function(request, $done) {
    var c = this;
    if ($done)
      request.done(c._5(this, $done));
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

  _4: function(errorCode) {
    var msg = err(errorCode);
    alertify.error(msg);
  },

  _5: function(context, method) {
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
    var c = this;
    c.sessionKey = c.globals._sessionKey = sessionKey;
    c._6();
    c._8();
    c._9();
    c._7();
    c._a();
  },

  _6: function(){
    var c = this;
    var globals = [];
    $.each(c.globals, function(key, value){
      globals.push(key+" = '"+value+"';");
    });
    eval(globals.join(""));
  },

  _7: function() {
    $('.'+_autoMessageClass).each(function(index, el){
      alertify.alert($(el).text());
    });
  },

  _8: function(){
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

  _9: function() {
    $('.'+_errorBlockClass).each(function(index, el){
      alertify.error($(el).text());
    });
  },

	_a: function() {
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
    var c = this;
    var rel = v(0).attr('rel');
    var params = $.parseJSON(rel);
    c.options = params;
    c.sessionID = params.sessionID;
    switch (params.p) {
      case 'subscriptions':
        c._b();
        c._f();
        c._e(c.defaultCategoryId);
        break;
      case 'billing':
        c._c();
        break;
      case 'profile':
        c._g();
        break;
    }
  },

  _b: function() {
    var c = this;
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
        iUser: c.options.iUser,
        status: 'skipped'
      };
      c.ajax('svr0', data, c._j);
    });
    $('.donateLink').on('click', function() {
      var infoEl = $(this).parents('li.infoEl')[0];
      var iSub = $(infoEl).attr('data-isub');
      var date = $(infoEl).attr('data-date');
      var data = {
        iSub: iSub,
        date: date,
        iUser: c.options.iUser,
        status: 'donated',
      };
      c.ajax('pdj1', data, c._j);
    });
    $('.reactivatePopupLink').on('click', function() {
      var infoEl = $(this).parents('li.infoEl')[0];
      var iSub = $(infoEl).attr('data-isub');
      var date = $(infoEl).attr('data-date');
      var data = {
        iSub: iSub,
        date: date,
        iUser: c.options.iUser,
        iOrder: $(infoEl).attr('data-iorder'),
        status: 'active'
      };
      c.ajax('spob2', data, c._j);
    });
  },

  _c: function() {
    var c = this;
    v(1).on('click', function(){
      c._h();
    });
  },

  _d: function(iSub, date, status, iOrder) {
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

  _e: function(category) {
    var c = this;
    c.ajax('onmd3', {iCategory: category, iUser: c.options.iUser}, function(response){
      var data = $.parseJSON(response);
      if (!data.success)
        alertify.error(err(data.errorCodes[0]));
      else {
        v(2).loadTemplate(v(3), data.products);
        $('.productCheck').each(function(index, el){
          el = $(el);
          el.attr('value', el.attr('alt'));
        });
      }
    });
  },

  _f: function() {
    var c = this;
    v(4).on('change', function(e){
      c._e($(e.target).val());
    });
    v(5).on('click', function(){
      c.ajaxForm('faitp', 'addSubscriptionForm', c._2);
      return false;
    });
    $('.confirmSkip').on('click', function(e){
      var id = $(e.target).attr('rel');
      c.ajaxForm('mgyfq', 'tripForm_'+id, c._2);
      return false;
    });
    $('.submitChange').on('click', function(e){
      var id = $(e.target).attr('rel');
      c.ajaxForm('epykr', 'changeSubscription'+id, c._2);
      return false;
    });
    $('.stopSubscription').on('click', function(e){
      var data = {
        iUser:  $(e.target).attr('data-uid'),
        iSub:   $(e.target).attr('data-sid')
      };
      alertify.confirm($(e.target).attr('data-msg'), function(e){
        if (e)
          c.ajax('wsge4', data, c._2);
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
        iUser:    c.options.iUser
      };
      c.ajax('spob2', data, c._2);
    });
  },

  _g: function() {
    var c = this;
    v(6).on('click', function(){
      c.ajaxForm('yxyds', 'fAccount', c._1);
      return false;
    });
    v(7).on('click', function(){
      c.ajaxForm('ztmut', 'security', c._1);
      return false;
    });
  },

  _h: function() {
    var c = this;
    Stripe.setPublishableKey(c.options.stripeKey);
    Stripe.card.createToken({
      number:   v(8).val(),
      cvc:      v(9).val(),
      exp_month:v(10).val(),
      exp_year: v(11).val()
    }, c._5(c, c._k));
  },

  _i: function(msg) {
    var c = this;
    $('.msg_msg').html(msg);
    $(".msg_error").removeClass('hide');	
    $(".msg_error").animate({
      height: "220px"
      }, 500, function(){
    });
  },

  _j: function(response) {
    var c = this;
    var data = $.parseJSON(response);
    if (!data.success) {
      var msg = err(data.errorCodes[0]);
      c._i(msg);
    } else
      c._d(data.request.iSub, data.request.date, data.request.status, (data.iOrder ? data.iOrder : null));
  },

  _k: function(status, response) {
    var c = this;
    if (response.error) {
      alertify.alert(response.error.message);
    } else {
      c._stripeToken = response['id'];
      c.ajax('jahu5', {token: response['id'], iUser: c.options.iUser}, c._2);
    }
  }

});
