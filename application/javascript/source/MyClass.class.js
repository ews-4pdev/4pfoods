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
    var prefix = $defined(elPrefix) ? elPrefix : '';
    $(errors).each(function(index, error){
      var info = error.split(':');
      var errorText = _errorCodes[info[0]];
      var el = $('#'+prefix+info[1]+suffix);
      var parentEl = el.parents('.form-group');
      parentEl.addClass('error-on');
      parentEl.find('.error-notify').html(errorText);
      parentEl.find('.error-notify').css('display', 'block');
    });
  },

  cb_standard: function(response) {
    var _class = this;
    var data = $.parseJSON(response);
    if (data.success){
      location.href = data.url;
    }
    else{
      _class.processErrors(data.errorCodes, data.request.prefix);
    }
  },

  cb_standardAlt: function(response) {
    var _class = this;
    var data = $.parseJSON(response);
    if (data.success) {
      if ($defined(data.msg))
        alertify.alert(data.msg);
      else {
          location.href = data.url;
      }
    } else
      _class._displayError(data.errorCodes[0]);
  },

  _ajaxCore: function(request, $done) {
    var _class = this;
    if ($done) {
        request.done(_class._bind(this, $done));
    }else {
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
