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


var SignupForm = MyClass.extend({

  _controller: 'signup',
  _stripeKey: '',
  _iProduct: '',
  discount: 0.00,

  init: function() {
    var c = this;
    var rel = v(0).attr('rel');
    var params = $.parseJSON(rel);
    c._stripeKey = params.stripeKey;
    c._iProduct = params.iProduct;
    c.sessionID = params.sessionID;
    switch (params.p) {
      case 'signup':
        //c._b();
        c._d();
        c._i();
        c._c();
        break;
    }
  },

  _b: function() {
    v(1).val('Tom');
    v(2).val('Exampleton');
    v(3).val('1231231234');
    v(4).val('tom.exampleton@4pfoods.com');
    v(5).val('123');
    v(6).val('123');
    v(7).val('4242424242424242');
    v(8).val(11);
    v(9).val(123);
    v(10).val(1234);
  },

  _c: function() {
    var c = this;
    v(4).on('blur', function(){
      var data = {
        Email:    v(4).val(),
        FirstName: v(1).val(),
        LastName: v(2).val()
      };
      c.ajax('fink', data, function(){});
    });
  },

  _d: function() {
    var c = this;
    c._e();
    c._g();
    c._f();
    $('.productCheck').on('click', function(e){
      var el = $(e.target);
      var txt = '<p class="productTitle">'+el.attr('rel')+'</p>';
      var total = parseInt(el.attr('alt'));
      v(11).html(total);
      v(12).html('');
      v(12).append(txt);
    });
    $('#p'+c._iProduct).attr('checked', 'checked');
    $('.productCheck').on('click', function(){c._i()});
  },

  _e: function() {
    var c = this;
    $('.collapseButton').on('click', function(e){
      var closeEl = $('#'+$(e.target).attr('data-this'));
      var openEl = $('#'+$(e.target).attr('data-next'));
      closeEl.collapse('hide');
      openEl.collapse('show');
    });
    v(13).on('click', function(){
      $(this).button('loading');
      c._h();
    });
  },

  _f: function() {
    var c = this;
    v(14).on('click', function(){
      var data = {Code: v(15).val()};
      c.ajax('gbvil', data, c._l);
    });
  },

  _g: function() {
    var c = this;
    $('.chooseSite').on('click', function(){
      v(10).val($(this).attr('data-code'));
      v(10).trigger('keyup');
    });
    v(16).on('click', function(){
      v(17).collapse('show');
      $('#customAddress input, #customAddress select, #customAddress textarea').attr('disabled',false).val('');
      v(18).removeClass('highlightMe');
      alertify.set({ labels: {
          ok: 'Continue Creating Account',
          cancel: 'Just Notify Me'
        }
      });
      alertify.confirm(v(19).html(), function(e){
        if (!e) {
          var data = {
            email: v(4).val(),
            firstname: v(1).val(),
            lastname: v(2).val()
          };
          c.ajax('wqdym', data, c._2);
        } 
      });
      alertify.set({ labels: {
          ok: 'OK',
          cancel: 'Cancel'
        }
      });
    });
    v(10).on('click', function(){ $(this).select(); });
    v(10).on('keyup', function(){
      if (v(10).val().length == 4)
        c._k(v(10).val());
    });
  },

  _h: function() {
    var c = this;
    Stripe.setPublishableKey(c._stripeKey);
    Stripe.card.createToken({
      number:   v(7).val(),
      cvc:      v(9).val(),
      exp_month:v(8).val(),
      exp_year: v(20).val()
    }, c._5(c, c._n));
  },

  _i: function() {
    var c = this;
    var products = [];
    var total = 0;
    v(12).html('');
    $('.productCheck:checked').each(function(index, el){
      var info = $('#pi_'+$(el).val());
      var price = parseFloat(info.attr('rel'));
      total += price;
      v(12).append('<div>'+info.html()+'</div>');
    });
    total -= parseFloat(c.discount);
    v(11).html(toMoney(total));
  },

  _j: function() {
    var c = this;
    var products = [];
    $('.productCheck').each(function(index, el){
      if (el.checked)
        products.push(el.value);
    });
    var data = {
      FirstName:      v(1).val(),
      LastName:       v(2).val(),
      Address1:       v(21).val(),
      Address2:       v(22).val(),
      City:           v(23).val(),
      StateId:        v(24).val(),
      Zip:            v(25).val(),
      Phone:          v(3).val(),
      Email:          v(4).val(),
      Password:       v(5).val(),
      ConfirmPassword:v(6).val(),
      DeliverySiteNotes:          v(26).val(),
      DietaryRestrictions:          v(27).val(),
      DiscountCode:   v(15).val(),
      Token:          c._stripeToken,
      Products:       products,
      AccessCode:     v(10).val()
    };
    c.ajax('prbln', data, c._m);
  },

  _k: function(code) {
    var c = this;
    var data = {code: code};
    c.ajax('qwjho', data, c._o);
  },

  _l: function(response) {
    var c = this;
    var data = $.parseJSON(response);
    if (data.success) {
      c.discount = parseFloat(data.amount);
      v(28).removeClass('hide');
      v(29).html(data.amount);
      v(30).html(data.nOrders);
    } else {
      c.discount = 0.00;
      c._4(data.errorCodes[0]);
      v(28).addClass('hide');
    }
    c._i();
  },

  _m: function(response) {
    var c = this;
    var data = $.parseJSON(response);
    if (data.success)
      location.href = data.url;
    else {
      v(13).button('reset');
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

  _n: function(status, response) {
    var c = this;
    if (response.error) {
      v(13).button('reset');
      alertify.alert(response.error.message);
    } else {
      c._stripeToken = response['id'];
      c._j();
    }
  },

  _o: function(result) {
    var c = this;
    var data = $.parseJSON(result);
    if (data.success) {
      v(17).collapse('show');
      v(18).removeClass('error-on');
      v(18).addClass('highlightMe');
      $.each(data.site, function(key, value){
        $('#c'+key).val(value);
        $('#c'+key).attr('disabled', 'disabled');
      });
    } else {
      v(18).addClass('error-on');
      v(18).removeClass('highlightMe');
    }
  }

});


var Gateway = MyClass.extend({

  _controller: 'auth',

  init: function() {
    var c = this;
    var rel = v(31).attr('rel');
    var params = $.parseJSON(rel);
    c.options = params;
    c.sessionID = params.sessionID;
    switch (params.p) {
      case 'login':
        c._q();
        break;
      case 'confirm':
        c._p();
        break;
    }
  },

  _p: function() {
    var c = this;
    v(32).on('click', function(){
      c.ajax('uzrsj', {hash: c.options.hash}, c._r);
    });
  },

  _q: function() {
    var c = this;
    v(33).on('click', function(){
      c.ajaxForm('zhric', 'formLogin', c._2);
      return false;
    });
    v(34).on('click', function(){
      c.ajaxForm('bgiz', 'nPassword', c._2);
      return false;
    });
    v(35).on('click', function() { location.href = '/gateway/signup'; });
  },

  _r: function(response) {
    var c = this;
    var data = $.parseJSON(response);
    if (data.success)
      alertify.success('Email has been resent.');
    else
      alertify.error(err(data.errorCodes[0]));
  }

});
