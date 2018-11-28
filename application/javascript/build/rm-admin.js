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


var Admin = MyClass.extend({

  _controller: 'admin',
  _options: {},

  init: function() {
    var c = this;
    var rel = v(0).attr('rel');
    var params = $.parseJSON(rel);
    c.options = params;
    c.sessionID = params.sessionID;
    c._controller = 'admin';
    c.options = params;
    switch (params.p) {
      case 'login':
        c._h();
        break;
      case 'deliveries':
        c._l();
        break;
      case 'products':
        c._i();
        break;
      case 'viewproduct':
        c._k();
        break;
      case 'viewcustomer':
        c._d();
        break;
      case 'approval':
        c._j();
        break;
      case 'discounts':
        c._f();
        break;
      case 'vieworder':
        c._g();
        break;
      case 'viewpayment':
        c._e();
        break;
      case 'payments':
        c._c();
        break;
      case 'customers':
        c._b();
        break;
    }
  },

  _b: function() {
    var c = this;
    $('.archiveButton').on('click', function(){
      var data = {
        iUser:  $(this).attr('data-id')
      };
      c.ajax('esn6', data, c._2);
    });
  },

  _c: function() {
    var c = this;
    $('.refundLink').on('click', function(){
      var el = $(this);
      var info = $.parseJSON(el.attr('data-info'));
      v(1).attr('data-info', el.attr('data-info'));
      v(2).html(info.maxAmount);
    });
    v(1).on('click', function(){
      var info = $.parseJSON($(this).attr('data-info'));
      var data = {
        iPayment: info.iPayment,
        Amount: v(3).val()
      };
      c.ajax('tjdt7', data, c._2);
      return false;
    });
  },

  _d: function() {
    var c = this;
    v(4).on('click', function(){
      c.ajax('bykq8', {iUser: c.options.iUser}, c._2);
      return false;
    });
    $('.refundLink').on('click', function(){
      var el = $(this);
      var info = $.parseJSON(el.attr('data-info'));
      v(1).attr('data-info', el.attr('data-info'));
      v(2).html(info.maxAmount);
    });
    v(1).on('click', function(){
      var info = $.parseJSON($(this).attr('data-info'));
      var data = {
        iPayment: info.iPayment,
        Amount: v(3).val()
      };
      c.ajax('tjdt7', data, c._2);
      return false;
    });
    v(5).on('click', function(){
      var data = {
        iUser:    c.options.iUser,
        iSite:    v(6).val()
      };
      c.ajax('buqu9', data, c._2);
      return false;
    });
    $('.pay-on-demand').on('click', function(){
      var data = {
        iOrder: $(this).attr('data-id')
      };
      c.ajax('jevxa', data, c._2);
    });
  },

  _e: function() {
    var c = this;
    v(1).on('click', function(){
      var data = {
        iPayment:   c.options.iPayment,
        Amount:     v(7).val()
      };
      c.ajax('tjdt7', data, c._2);
      return false;
    });
  },

  _f: function() {
    var c = this;
    $('.unpublishButton').on('click', function(e){
      c.ajax('rhamb', {iDiscount: $(e.target).attr('rel')}, c._2);
    });
    v(8).on('click', function(){
      c.ajaxForm('jpsyu', 'addDiscount', c._1);
      return false;
    });
  },

  _g: function() {
    var c = this;
    v(9).on('click', function(){
    });
  },

  _h: function() {
    var c = this;
    v(10).on('click', function(){
      var data = {
        Email: v(11).val(),
        Password: v(12).val(),
        prefix: 'c'
      };
      c.ajax('zhric', data, c._2);
      return false;
    });
  },

  _i: function() {
    var c = this;
    v(13).on('click', function(){
      c.ajaxForm('gujwv', 'newProducts', c._1);
      return false;
    });
  },

  _j: function() {
    var c = this;
    $('.submitApproval').each(function(index, el){
      el = $(el);
      el.on('click', function(){
        var id = el.attr('rel');
        c.ajaxForm('qsdww', 'approvalForm'+id, c._n);
        return false;
      });
    });
    $('.assignSelect').on('change', function(e){
      var el = $(e.target);
      var data = {
        iUser:  el.attr('rel'),
        iSite:  el.val()
      };
      c.ajax('iychd', data, c._2);
    });
  },

  _k: function() {
    var c = this;
    v(14).on('click', function(){
      c.ajaxForm('gujwv', 'editProductForm', c._o);
      return false;
    });
  },

  _l: function() {
    var c = this;
    $('.hideSiteLink').on('click', function() {
      var el = $(this);
      alertify.confirm('This will hide the site permanently from this page. Are you sure?', function(e){
        if (e)
          c.ajax('buvve', {iSite: el.attr('data-id')}, c._2);
      });
    });
    $('.editSiteLink').on('click', function() {
      var el = $(this);
      var info = $.parseJSON($(this).parents('tr').attr('data-info'));
      $.each(info, function(key, value) {
        $('#update'+key).val(value);
      });
    });
    v(15).on('click', function(){
      c.ajaxForm('smpdx', 'updateSiteForm', c._1);
      return false;
    });
    v(16).on('click', function(){
      c.ajaxForm('qsdww', 'site', c._p);
      return false;
    });
    v(17).on('click', function(e){
      c.ajaxForm('tnjyy', 'addDriverForm', c._m);
      return false;
    });
    $('.disableSite').on('click', function(e){
      c.ajax('czodf', {iSite: $(e.target).attr('rel')}, c._2);
      return false;
    });
    $('.changeDeliveryDayLink').on('click', function(){
      v(18).val($(this).attr('data-id'));
    });
    v(19).on('click', function(){
      var data = {
        iSite:  v(18).val(),
        DefaultDeliveryDay:   v(20).val()
      };
      c.ajax('xzuyg', data, c._2);
    })
    $('.removeDriver').each(function(index, el) {
      el = $(el);
      el.on('click', function(){
        var data = {iUser: el.attr('rel')};
        if (confirm('Are you sure you want to delete this driver?')) {
          c.ajax('skrah', data, function(){ 
            location.href = '/admin/deliveries'; 
          });
        }
      });
    });
  },

  _m: function(response) {
    var c = this;
    var data = $.parseJSON(response);
    if (data.success)
      location.href = data.url;
    else
      c._0(data.errorCodes, 'driver');
  },

  _n: function(response) {
    var c = this;
    var data = $.parseJSON(response);
    if (data.success)
      location.href = data.url;
    else
      c._0(data.errorCodes, 'formSite', data.request.iUser);
  },

  _o: function(response) {
    var c = this;
    var data = $.parseJSON(response);
    if (data.success)
      location.href = data.url;
    else
      c._0(data.errorCodes, 'p');
  },

  _p: function(response) {
    var c = this;
    var data = $.parseJSON(response);
    if (data.success)
      location.href = data.url;
    else
      c._0(data.errorCodes, 'site');
  }

});
