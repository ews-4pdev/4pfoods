var Account = MyClass.extend({

    _controller: 'account',
    defaultCategoryId: 1,
    subId: null,
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
                _class.updatePoints();
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
        $(".modal-hide").on("hidden.bs.modal", function () {
            location.href = '/account/subscriptions';
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
        $('.skipAllLink').on('click', function() {
            var infoEl = $(this).parents('li.infoEl')[0];
            var iSub = $(infoEl).attr('data-isub');
            var date = $(infoEl).attr('data-date');
            var data = {
                iSub: iSub,
                date: date,
                iUser: _class.options.iUser,
                status: 'skipped'
            };

            _class.ajax('skipAllSubscriptions', data, _class.cb_changeDeliveryOption);
        });
        $('.donateLink').on('click', function() {
            var infoEl = $(this).parents('li.infoEl')[0];
            var iSub = $(infoEl).attr('data-isub');
            var date = $(infoEl).attr('data-date');
            var data = {
                iSub: iSub,
                date: date,
                iUser: _class.options.iUser,
                status: 'donated'
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
        $(document).on('click', '.item-list' ,function(e) {
            // Check if this the last item in list than don't allow to delete.
            var row = $(this).closest('tr').attr('id');

            var data = {
                userBagItemId :  $(this).data('bag'),
                iUser :  $(this).attr('data-uid'),
                iSub  :  $(this).attr('data-subid')
            };
            _class.ajax('deleteItem', data, _class.cb_deleteditem);
        });
        $(document).on('click', '.add-item' ,function(e) {
            e.stopPropagation();
            var data = {
                iUser:  $(this).attr('data-uid'),
                id:  $(this).attr('data-bag')
            };
            _class.ajax('addItem', data, _class.cb_addeditem);
        });
        $(document).on('click', '.addProduct' ,function(e) {
            e.preventDefault(); e.stopPropagation();
            var row = $(this).closest('tr').attr('id');
            var rowId;
            var status;
            var data = {};
            if(row.search('delete') > -1){
                rowId = row.replace('delete-tr-', '');
                data = {
                    UserBagItemId:  $(this).data('bag'),
                    status : 'delete',
                    rowId : rowId
                };
            }else{
                rowId = row.replace('secondary-tr-', '');
                data = {
                    status : 'secondary',
                    UserBagId:  $(this).attr('data-bagid'),
                    itemPointId : $(this).data('item-point-id'),
                    rowId : rowId
                };
            }
            data.iUser =  $(this).attr('data-uid');
            data.iSub =  $(this).attr('data-subid');
            data.Points =  $(this).closest('tr').find('td').eq(1).text();

            status = data.status;
            var subid = $(this).attr('data-subid');
            var remain = parseInt( $('#available_'+subid).text()  );
            _class.ajax('addSecondaryItem', data, _class.cb_addSecondaryItem);

        });
        $('#submitAddSubscription').on('click', function(){
            _class.ajaxForm('addSubscription', 'addSubscriptionForm', _class.cb_standardAlt);
            return false;
        });
        $(document).on('focusout', '.Qty' ,function(e) {

            if( $(this).val() < 1 ){
                alertify.error('Quantity must be greater than 0');
                $(this).val($(this).data('default'));
                return false;
            }
            if( !isInt( $(this).val() ) ){
                alertify.error('Quantity must be an integer');
                $(this).val($(this).data('default'));
                return false;
            }
            if( $(this).val() == $(this).data('default') ){
                $(this).val($(this).data('default'));
                return false;
            }

            var data = {
                rowId : $(this).closest('tr').attr('id'),
                iQty: $(this).val(),
                iUser:  $(this).attr('data-uid'),
                iSub:  $(this).attr('data-subid'),
                userBagItemId:  $(this).data('bag')
            };

            var subid = $(this).attr('data-subid');
            var points = parseInt( $(this).closest('tr').find('td').eq(2).text() );
            var  remain = parseInt( $('#available_'+subid).text()  );
            var quantity = parseInt(data.iQty);
            //var currentPoints = (  ( points * quantity ) > remain  ) ? ( points * quantity ) - remain : remain - ( points * quantity );

            //if( currentPoints > ( remain + extra_points ) ){
            //    alertify.error('You don\'t have enough points' );
            //    $(this).val($(this).data('default'));
            //    return false;
            //}else{
            _class.ajax('editQty', data, _class.cb_editQty);
            //}
            return false;
        });
        $('.confirmSkip').on('click', function(e){
            var id = $(e.target).attr('rel');
            _class.ajaxForm('skipOrders', 'tripForm_'+id, _class.cb_standardAlt);
            return false;
        });
        $('.submitChange').on('click', function(e){
            var id = $(e.target).attr('rel');

            if( $('#weekly_'+id).prop('checked') ){
                _class.ajaxForm('changeSubscriptionForWeek', 'changeSubscription'+id, _class.cb_standardAlt);
                return false;
            }else{
                _class.ajaxForm('changeSubscription', 'changeSubscription'+id, _class.cb_standardAlt);
                return false;
            }
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
        $('.changeWeekSubscription').on('change', function(event){
            event.stopPropagation();
            event.preventDefault();

            var adminBagId = parseInt($(this).val());
            var bag_id = parseInt( $(this).closest('div').find('input').eq(0).val() );
            var subscription_id = parseInt( $(this).closest('div').find('input').eq(1).val() );

            if(isInt(adminBagId) && isInt(bag_id) && isInt(subscription_id)  ){
                var data = {
                    adminBagId : adminBagId,
                    iUser:    _class.options.iUser,
                    userBagId : bag_id,
                    iSub : subscription_id
                };
                alertify.confirm('This change will only persist for this week.  <br>' +
                    ' <strong>This will not carry any changes you have made in this bag.</strong> <br> <br> Are you sure you want to continue?', function(e){
                    if(e){
                        _class.ajax('changeSubscriptionForWeek', data, _class.cb_standardAlt);
                    }
                }).set('title', 'Change Subscription For this week');

            }
        });
        $('.changeSub').on('click', function(){
            var modalId = $(this).find('a').data('target');
            var subscriptionId = parseInt( modalId.split('_')[1] );

            alertify.confirm('Changing Subscription will delete all future skipped and donated orders.' +
                ' </br></br> Are you sure you want to continue?', function(e){
                if(e){
                    var nowwekly = $('#noweekly_'+subscriptionId);

                    if( nowwekly.length > 0 && nowwekly.val() == false ){
                        $('#weekly_'+subscriptionId).addClass('hidden');
                    }
                    var modal = $('#'+modalId);
                    modal.modal('show');
                }
            });
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
        } else{
            _class.switchStatus(data.request.iSub, data.request.date, data.request.status, (data.iOrder ? data.iOrder : null));
            //location.href = data.url;
        }
    },

    cb_getStripeToken: function(status, response) {
        var _class = this;
        if (response.error) {
            alertify.alert(response.error.message);
        } else {
            _class._stripeToken = response['id'];
            _class.ajax('updateBilling', {token: response['id'], iUser: _class.options.iUser}, _class.cb_standardAlt);
        }
    },

    cb_deleteditem: function(response) {
        _class = this;
        var data = $.parseJSON(response);
        if (data.success) {
            if(data.Secondary == false)
            {
                _class.deleteRow(data);
            }
            if(data.Secondary == true)
            {
                var row = $('#primary-tr-'+data.request.userBagItemId);
                var points = row.find('td').eq(1).text();
                var qty = row.find('td').eq(2).find('input').val();
                bags[data.request.iSub].available_points = bags[data.request.iSub].available_points + (points * qty);
                this.updatePoints();
                var tr = $('<tr id="secondary-tr-'+data.row.tr+'">' +
                    '<td>'+data.row.name+'</td>' +
                    '<td>'+data.row.points+'</td>' +
                    '<td><button data-item-point-id="'+data.row.pointid+'" class="margin0 addProduct btn btn-info no-margin"  ' +
                    'data-uid="'+data.row.uid+'"  data-subid="'+data.row.subid+'" data-pointid="'+data.row.pointid+'"   ' +
                    'data-bagid="'+data.row.bagid+'" ' +
                    '>Add</button></td>' +
                    '</tr>');
                $('#secondary-'+data.request.iSub+' tbody').append(tr);
                row.remove();
                alertify.log('Item Removed');
            }
        }else{
            _class._displayError(data.errorCodes[0]);
        }
    },

    cb_addeditem: function(response) {
        var data = $.parseJSON(response);
        if (data.success) {
            $('#tr-'+data.id).css('background-color', 'white');
            $('#btn-'+data.id).text('REMOVE');
            $('#btn-'+data.id).removeClass('btn-info add-item');
            $('#btn-'+data.id).addClass('btn-danger item-list');

        }
    },

    cb_addSecondaryItem: function(response) {
        var _class = this;

        var data = $.parseJSON(response);

        if (data.success) {
            var id  = null; var points = null;
            if(data.action == 'add')
            {
                _class.addRow(data);
            }
            if(data.action == 'addPoints')
            {
                id = $("#primary-tr-"+data.id);
                points = id.find('td').eq(1).text();
                bags[data.request.iSub].available_points = bags[data.request.iSub].available_points - points;
                this.updatePoints();
                id.find('td').eq(2).find('input').val(data.qty);
                alertify.success('Item Added');
            }
        }else{
            _class._displayError(data.errorCodes[0]);
        }
    },

    cb_editQty: function(response) {
        var _class = this;
        var data = $.parseJSON(response);
        if (data.success) {
            var id = $("#primary-tr-"+data.id);
            bags[data.request.iSub].available_points = bags[data.request.iSub].available_points - (data.points * data.qty);
            this.updatePoints();

            id.find('td').eq(2).find('input').data('default', data.request.iQty);
            id.find('td').eq(2).find('input').val( data.request.iQty );
            alertify.success('Quantity Changed');
        }else{
            _class._displayError(data.errorCodes[0]);
            var rowId = $("#"+data.request.rowId).find('td').eq(2).find('input');
            rowId.val( rowId.data('default') );
        }
    },

    updatePoints: function () {

        for(var key in bags)
        {
            $('#available_'+key).text(bags[key].available_points);
            $('#total_'+key).text(bags[key].total_points);
        }
    },

    addRow: function (data) {
        var _class = this;
        var id = null;
        if(data.request.status == 'delete'){
            id = $('#delete-tr-'+data.request.rowId);
        }else if(data.request.status == 'secondary'){
            id = $('#secondary-tr-'+data.request.rowId);
        }

        var type = id.attr('class');
        var items = id.find('td').eq(0).text();
        var supplier = id.find('td').eq(1).text();
        var points = parseInt( id.find('td').eq(1).text() );
        bags[data.request.iSub].available_points = bags[data.request.iSub].available_points - (points * data.qty);
        _class.updatePoints();
        $('#primary-'+data.request.iSub+' tbody')
            .append(
                '<tr id="primary-tr-'+data.id+'">' +
                '<td>'+items+'</td>' +
                '<td width="120">'+points+'</td>' +
                '<td width="120">' +
                '<input type="text" data-default="'+data.qty+'" value="'+data.qty+'" style="width:50px;" class="Qty" data-subid="'+data.request.iSub+'" data-uid="'+data.request.iUser+'" data-bag="'+data.id+'"></td>' +
                '<td>' +
                '<button class="item-list btn btn-danger" ' +
                'data-uid="'+data.request.iUser+'" ' +
                'data-bagid="'+data.bagId+'" ' +
                'data-subid="'+data.request.iSub+'" ' +
                'data-bag="'+data.id+'"  ' +
                'style="margin: 0;">REMOVE' +
                '</button></td></tr>');
        //if(data.request.status == 'delete'){
        id.remove();
        //}
        alertify.success('Item Added');
        //_class.sort(tableId);
    },

    deleteRow: function (data) {
        var _class = this;
        // Since tr is coming from primary items table
        var row = $('#primary-tr-'+data.id);

        //var type = id.attr('class');
        //var items = id.find('td').eq(0).text();
        //var supplier = id.find('td').eq(1).text();
        var points = row.find('td').eq(1).text();
        var qty = row.find('td').eq(2).find('input').val();

        bags[data.request.iSub].available_points = bags[data.request.iSub].available_points + (points * qty);
        _class.updatePoints();
        row.find('td:last-child button').text('Add').removeClass('item-list').addClass('addProduct');
        row.attr('id', 'delete-tr-'+data.id);
        row.find('td').eq(2).remove();
        $('#secondary-'+data.request.iSub+' tbody').append(row);
        alertify.log('Item Deleted');
    }



});



