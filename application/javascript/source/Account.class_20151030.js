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
