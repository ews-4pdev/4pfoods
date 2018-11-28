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


var Driver = MyClass.extend({

  _controller: 'driver',

  init: function() {
    var _class = this;
    var rel = $('#_Driver').attr('rel');
    var params = $.parseJSON(rel);
    _class.options = params;
    _class.sessionID = params.sessionID;
    switch (params.p) {
      case 'login':
        _class.setupLogin();
        break;
      case 'home':
        _class.setupHome();
        break;
    }

  },

  setupHome: function() {
    var _class = this;
    $('#submitDelivery').on('click', function(){
      var data = {
        iSite:      $('#SiteId').val(),
        isSuccess:  $('#isSuccess').val(),
        iDriver:    _class.options.iDriver
      };
      _class.ajax('signDelivery', data, _class.cb_standardAlt);
    });


  },

  setupLogin: function() {
    var _class = this;
    $('#cSubmit').on('click', function(){
      var data = {
        Email: $('#cEmail').val(),
        Password: $('#cPassword').val(),
        prefix: 'c'
      };
      _class.ajax('submitLogin', data, _class.cb_standardAlt);
      return false;
    });
  }

});
