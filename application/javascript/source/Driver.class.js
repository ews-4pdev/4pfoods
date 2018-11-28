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
