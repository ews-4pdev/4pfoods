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
