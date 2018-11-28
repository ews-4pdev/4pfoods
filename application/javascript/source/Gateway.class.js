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
