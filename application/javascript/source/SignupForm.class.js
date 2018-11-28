var SignupForm = MyClass.extend({

    _controller: 'signup',
    _stripeKey: '',
    _iProduct: '',
    discount: 0.00,
    doorstep : false,
    tax : 0.00,
    deliveryCharges: 0.00,
    doorstepZip: 0,

    init: function () {
        var _class = this;
        var rel = $('#_SignupForm').attr('rel');
        var params = $.parseJSON(rel);
        _class._stripeKey = params.stripeKey;
        _class._iProduct = params.iProduct;
        _class.sessionID = params.sessionID;
        _class.deliveryCharges = params.deliveryCharges;

        //_class.setDefaults();
        _class.setupSignup();
        _class.updateSummary();
        _class.setupCartAbandonment();
        _class.togglePanels('hidden');
        $('#cat1none').prop('checked', true);
    },

    togglePanels : function(status){
        var panels = $('.panel-default').not(':eq(0)');
        if(status == 'hidden'){
            panels.removeClass('show').addClass('hidden');
        }else{
            panels.removeClass('hidden').addClass('show');
        }
    },

    setDefaults: function () {
        $('#cFirstName').val('Tom');
        $('#cLastName').val('Exampleton');
        $('#cPhone').val('1231231234');
        $('#cEmail').val('tom.exampleton@4pfoods.com');
        $('#cPassword').val('123');
        $('#cConfirmPassword').val('123');
        $('#ccNum').val('4242424242424242');
        $('#ccExpMonth').val(11);
        $('#ccSec').val(123);
        $('#pinBox').val(1234);
    },

    setupCartAbandonment: function () {
        var _class = this;
        $('#cEmail').on('blur', function () {
            var data = {
                Email: $('#cEmail').val(),
                FirstName: $('#cFirstName').val(),
                LastName: $('#cLastName').val()
            };
            _class.ajax('submitCartAbandonEmail', data, function () {
            });
        });
    },

    setupSignup: function () {
        var _class = this;
        _class.setupFormElements();
        _class.setupAccessCode();
        _class.setupDiscountCode();
        $('.doorstep').hide();
        $('.catnone').on('click', function(){
            var cat = $(this).data('cat');
            $('#itemList').find('div.cat_'+cat ).remove();
            _class.updateSummary();
        });
        //$('.productCheck').on('click', function (e) {
        //    var el = $(e.target);
        //    var txt = '<p class="productTitle">' + el.attr('rel') + '</p>';
        //    var total = parseInt(el.attr('alt'));
        //    $('#productPrice').html(total);
        //    $('#itemList').html('');
        //    $('#itemList').append(txt);
        //    //Revision Start By EWS ------ Function Call Get State Tax
        //    stateID = $('#cStateId').val();
        //    _class.getStateTax($('#cStateId').val());
        //    //Revision End By EWS ------ Function Call Get State Tax
        //});
        //$('#p' + _class._iProduct).attr('checked', 'checked');
        $('.productCheck').on('click', function () {
            // $('#cStateId').trigger('change');
            _class.updateSummary();
        });

        //Subscription form submit
        $('#subscribe').on('click', function(e){
            e.preventDefault();
            var data = {
                        email: $('#email').val(),
                        firstname: $('#fname').val(),
                        lastname: $('#lname').val(),
                        zipCode : $('#pinBox').val()
                    };

            _class.ajax('subscribe', data, _class.cb_standardAlt);

        });

        // DoorStep Facade
        $('#doorstep_pickup').on('click', function(){
            $('#cAccessCode').val('');
            $('#doorstep_pickup').addClass('hidden');
            _class.toggleDoorStep(true);
            _class.togglePanels('show');
            $('#doorstep_access_code').addClass('hidden');
            $('#access_code').addClass('hidden');
            $('#notify_user').addClass('hidden');
            $('.doorstep').show();
            $('#pickup').removeClass('hidden');
            $('#customAddress').collapse('show');
            $('#customAddress input, #customAddress select, #customAddress textarea').attr('disabled', false).val('');
            $('#pinWrapper').removeClass('highlightMe');
            $('#doorstep').removeClass('hidden');
            $('#noaddress').addClass('hidden');
            $('#zipcode-panel').addClass('hidden');
            $('#doorstepInput').val(1);
            $('#pinBox').val(_class.doorstepZip);
            $('#cStateId').val(_class.doorstepState);
            $('#cStateId').prop('disabled', true);
            $('#cStateId').trigger('change');
            $('#cZip').val($('#pinBox').val());
            $('#cZip').prop('disabled', true).css({
                'color': 'rgb(92, 184, 92)',
                'border' : '1px solid rgb(92, 184, 92)'
            });
            _class.updateSummary();
        });
    },

    setupFormElements: function () {
        var _class = this;
        $('.collapseButton').on('click', function (e) {
            var closeEl = $('#' + $(e.target).attr('data-this'));
            var openEl = $('#' + $(e.target).attr('data-next'));
            closeEl.collapse('hide');
            openEl.collapse('show');
        });
        $('#completeOrder').on('click', function () {
            $(this).button('loading');
            _class.getStripeToken();
        });
    },

    setupDiscountCode: function () {
        var _class = this;
        $('#applyDiscountButton').on('click', function () {
            //Revision Start By EWS ------ Add State value in Data Array
            var data = {Code: $('#DiscountCode').val(), state: $('#cStateId').val()};
            //Revision End By EWS ------ Add State value in Data Array
            _class.ajax('validateDiscount', data, _class.cb_validateDiscount);
        });
    },

    setupAccessCode: function () {
        var _class = this;
        $(document).on('click', '.chooseSite', function () {
            $('#pinBox').val($(this).attr('data-zip'));
            var data = {Code: $(this).attr('data-code'), id: 1};
            _class.ajax('validateAccessCode', data, _class.cb_validateAccessCode);
        });

        //Revision Start By EWS ------ Check State ID
        $('#cStateId').on('change', function () {
            setTimeout(function () {
                stateID = $('#cStateId').val();
                _class.getStateTax($('#cStateId').val());
            }, 1000);
        });
        //Revision End By EWS ------ Check State ID
        $('#pinBox').on('click', function () {
            $(this).select();
        });

        // Prevent page from submitting form on enter. More specifically on entering inside zip code field.
        $(window).keydown(function(event){
            if(event.keyCode == 13) {
                event.preventDefault();
                if ($('#pinBox').val().length == 5){
                    $('#doorstepInput').val(0);
                    _class.validateAccessCode($('#pinBox').val());
                }
                return false;
            }
        });

        $(document).on('change', '#pinBox', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if ($('#pinBox').val().length == 5){
                $('#doorstepInput').val(0);
                _class.validateAccessCode($('#pinBox').val());
            }

        });
        $('#pickup').on('click', function () {
            var data = {
                code: $('#pinBox').val()
            };
            $('.doorstep').hide();
            $("#site-feed").html('');
            $('#doorstep').addClass('hidden');
            $('#zipcode-panel').removeClass('hidden');
            $('#pickup').addClass('hidden');
            _class.ajax('validateAccessZipCode', data, _class.cb_validateAccessZipCode);
        });
    },

    getStripeToken: function () {
        var _class = this;
        Stripe.setPublishableKey(_class._stripeKey);
        Stripe.card.createToken({
            number: $('#ccNum').val(),
            cvc: $('#ccSec').val(),
            exp_month: $('#ccExpMonth').val(),
            exp_year: $('#ccExpYear').val()
        }, _class._bind(_class, _class.cb_getStripeToken));
    },

    updateSummary: function () {
        var _class = this;
        products = [];
        var total = 0;
        $('#itemList').html('');
        $('.productCheck:checked').each(function (index, el) {
            var info = $('#pi_' + $(el).val());
            var catId = $(el).data('cat');
            total += parseFloat( info.attr('rel') );
            $('#itemList').append('<div data-value="'+info.attr('rel')+'" class="cat_'+catId+'">' + (info.html()).split('/')[0] + '</div>');
        });
        //Revision Start By EWS ------ Check State Tax and Calculate Total Price
        $('#deliveryCharge').addClass('hide');
        $('#deliveryChargeAmount').addClass('hide');

        if (_class.tax != 0) {
            if( total > 0 && _class.doorstep ){
                $('#deliveryCharge').removeClass('hide');
                $('#deliveryChargeAmount').removeClass('hide').text(toMoney(  _class.deliveryCharges  ) );
            }
            total -=  parseFloat(_class.discount);
            var taxPricevalue = total * (_class.tax / 100);
            $('#TaxAmmount').text( toMoney( taxPricevalue ) );
            taxPricevalue +=  _class.deliveryCharges;
            var totaltaxPrice = total + taxPricevalue;

            $('#productPrice').html(toMoney(totaltaxPrice));
        }
        if (_class.tax == 0 || isNaN(_class.tax) == true) {
            $('#TaxAmmount').text( toMoney(0) );
            total -= parseFloat(_class.discount);
            if( total > 0 && _class.doorstep ){
                $('#deliveryCharge').removeClass('hide');
                $('#deliveryChargeAmount').removeClass('hide').text(toMoney( parseInt( _class.deliveryCharges ) ) );
                total +=  _class.deliveryCharges;
            }
            $('#productPrice').html(toMoney(total));
        }
    },
//Revision End By EWS ------ Check State Tax and Calculate Total Price
    submitForm: function () {
        var _class = this;
        var products = [];
        $('.productCheck').each(function (index, el) {
            if (el.checked)
                products.push(el.value);
        });
        //Revision Start By EWS ------ Add Terms of Services Value in array

        // If Doorstep is selected than get all extra information needed for doorstep
        var doorstep = $('#doorstepInput').val();
        var data = {
            DoorStep: doorstep,
            FirstName: $('#cFirstName').val(),
            LastName: $('#cLastName').val(),
            Address1: $('#cAddress1').val(),
            Address2: $('#cAddress2').val(),
            City: $('#cCity').val(),
            StateId: $('#cStateId').val(),
            Zip: $('#cZip').val(),
            Phone: $('#cPhone').val(),
            Email: $('#cEmail').val(),
            Password: $('#cPassword').val(),
            ConfirmPassword: $('#cConfirmPassword').val(),
            terms: $('#cterms').is(':checked'),
            DeliverySiteNotes: $('#cDeliverySiteNotes').val(),
            DietaryRestrictions: $('#cDietaryRestrictions').val(),
            DiscountCode: $('#DiscountCode').val(),
            Token: _class._stripeToken,
            Products: products,
            AccessCode: $('#cAccessCode').val()
        };
        if( doorstep ){
            $temp = '';
            data['Mobile'] = $('#Mobile').val();
            data['TownHouse'] = $('#TownHouse').prop('checked');
            data['OfficeBuilding'] = $('#OfficeBuilding').prop('checked');
            data['ApartmentCondo'] = $('#ApartmentCondo').prop('checked');
            data['SingleFamilyHome'] = $('#SingleFamilyHome').prop('checked');
            data['AMobile'] = $('#AMobile').val();
            data['Concierge'] = $('#concierge').prop('checked');
            data['ConciergeNumber'] = $('#concierge_number').val();
            data['KeyFob'] = $('input:radio[name=KeyFob]:checked').data('value');
            data['Parking'] = $('#Parking').val();
        }

        //Revision End By EWS ------ Check State Tax and Calculate Total Price
        _class.ajax('submitSignup', data, _class.cb_submitSignup);
    },

    validateAccessCode: function (code) {
        var _class = this;
        $('#customAddress').removeClass('in');
        var data = {code: code, id: 0};
        _class.ajax('validateAccessCode', data, _class.cb_validateAccessCode);
    },

    cb_validateDiscount: function (response) {
        var _class = this;
        console.log(_class.tax);
        var data = $.parseJSON(response);
        if (data.success) {
            _class.discount = parseFloat(data.amount);
            //Revision Start By EWS ------ Get Tax from Json Response Array
            _class.tax = parseFloat(data.Tax);
            //Revision End By EWS ------ Get Tax from Json Response Array
            $('#discountInfo').removeClass('hide');
            $('#discountAmount').html(data.amount);
            $('#discountOrders').html(data.nOrders);
        } else {
            _class.discount = 0.00;
            _class._displayError(data.errorCodes[0]);
            $('#discountInfo').addClass('hide');
        }
        _class.updateSummary();
    },

    cb_submitSignup: function (response) {
        var _class = this;
        var data = $.parseJSON(response);
        if (data.success)
            location.href = data.url;
        else {
            $('#completeOrder').button('reset');
            var alertErrors = [];
            $('.error-on').removeClass('error-on');
            $.each(data.errorCodes, function (index, code) {
                var items = code.split(':');
                var error = items[0];
                var field = items[1];
                if (field == 'NoField')
                    alertErrors.push(_errorCodes[error]);
                else {
                    var el = $('#c' + field).parents('.form-group');
                    el.find('.error-notify').html(_errorCodes[error]);
                    el.addClass('error-on');
                    el.parents('.panel-collapse').collapse('show');
                }
            });
            if (alertErrors.length > 0) {
                var msg = _errorCodes['SignupGeneral'] + alertErrors.join('<br/>');
                alertify.alert(msg);
            } else
                alertify.alert(err('SignupMain'));
        }
    },

    cb_getStripeToken: function (status, response) {
        var _class = this;
        if (response.error) {
            $('#completeOrder').button('reset');
            alertify.alert(response.error.message);
        } else {
            _class._stripeToken = response['id'];
            _class.submitForm();
        }
    },

    cb_validateAccessCode: function (result) {

        var _class = this;
        var data = $.parseJSON(result);

        if( typeof data.site != 'undefined' && typeof data.site.Tax != 'undefined'  ){
            _class.tax = data.site.Tax;
        }

        // If doorstep is found
        if (data.id == 0) {
            _class.doorstepZip = $('#pinBox').val();
            _class.doorstepState = data.state;
            $('#doorstep_access_code').addClass('hidden');
            _class.toggleDoorStep(true);
            _class.togglePanels('show');
            $('#access_code').addClass('hidden');
            $('#notify_user').addClass('hidden');
            $('.doorstep').show();
            $('#pickup').removeClass('hidden');
            $('#customAddress').collapse('show');
            $('#customAddress input, #customAddress select, #customAddress textarea').attr('disabled', false).val('');
            $('#pinWrapper').removeClass('highlightMe');
            $('#doorstep').removeClass('hidden');
            $('#noaddress').addClass('hidden');
            $('#zipcode-panel').addClass('hidden');
            $('#doorstepInput').val(1);
            $('#cStateId').val(data.state);
            $('#cStateId').prop('disabled', true);
            $('#cStateId').trigger('change');
            $('#cZip').val($('#pinBox').val());
            $('#cZip').prop('disabled', true).css({
                'color': 'rgb(92, 184, 92)',
                'border' : '1px solid rgb(92, 184, 92)'
            });
            _class.updateSummary();
        }

        // If used access code
        if (data.id == 1) {
            if (data.success) {
                _class.toggleDoorStep(false);
                _class.togglePanels('show');
                $('#noaddress').addClass('hidden');
                $('#deliveryCharge').addClass('hide');
                $('#deliveryChargeAmount').addClass('hide');
                $('#notify_user').addClass('hidden');
                $('.doorstep').hide();
                $('#doorstepInput').val(0);
                $('#pickup').addClass('hidden');
                $('#customAddress').collapse('show');
                $('#pinWrapper').removeClass('error-on');
                $('#pinWrapper').addClass('highlightMe');
                $.each(data.site, function (key, value) {
                    $('#c' + key).val(value);
                    $('#c' + key).attr('disabled', 'disabled');
                });
                _class.updateSummary();
                //Revision Start By EWS ------ Check State ID and Add Tax
                // if (data.site.Tax != 0) {
                //     var total = 0;
                //     $('.productCheck:checked').each(function (index, el) {
                //         var info = $('#pi_' + $(el).val());
                //         var price = parseFloat(info.attr('rel'));
                //         total += price;
                //     });
                //     var taxPricevalue = total * (data.site.Tax / 100);
                //
                //     var totalptice = total + taxPricevalue;
                //     totalptice -= parseFloat(_class.discount);
                //     $('#productPrice').html(toMoney(totalptice));
                //     $('#Tax').removeClass('hide');
                //     $('#TaxAmmount').removeClass('hide');
                //     $('#TaxAmmount').html(toMoney(taxPricevalue));
                // } else if (data.site.Tax == 0) {
                //     var total = 0;
                //     $('.productCheck:checked').each(function (index, el) {
                //         var info = $('#pi_' + $(el).val());
                //         var price = parseFloat(info.attr('rel'));
                //         total += price;
                //         total -= parseFloat(_class.discount);
                //         $('#productPrice').html(toMoney(total));
                //     });
                //     $('#Tax').addClass('hide');
                //     $('#TaxAmmount').addClass('hide');
                // }
            }
            //Revision End By EWS ------ Check State ID and Add Tax
            else {
                $('#pinWrapper').addClass('error-on');
                $('#pinWrapper').removeClass('highlightMe');
            }
        }

        // If doorstep is not found
        if (data.id == 2) {
            _class.toggleDoorStep(false);
            $('#doorstep_access_code').addClass('hidden');
            $('#notify_user').addClass('hidden');
            $('.doorstep').hide();
            $('#access_code').addClass('hidden');
            $('#doorstepInput').val(0);
            $('#pickup').addClass('hidden');
            $('#zipcode-panel').addClass('hidden');
            $('#doorstep').addClass('hidden');
            data = {code: data.code};
            _class.ajax('validateAccessZipCode', data, _class.cb_validateAccessZipCode);
        }
    },

    cb_validateAccessZipCode: function (result) {
        var _class = this;
        var data = $.parseJSON(result);

        if (data.Zip == 1) {
            //$('#noaddress').removeClass('hidden');
            _class.noAddress();
            //alertify.alert("Services are not available in your area. Instead use custom address.");

        } else {
            $("#site-feed").html('');
            if(_class.doorstep == true){
                $('#access_code').addClass('hidden');
                $('#doorstep_access_code').removeClass('hidden');
                $('#doorstep_pickup').removeClass('hidden');
            }else{
                $('#doorstep_pickup').addClass('hidden');
                $('#zipcode-panel').removeClass('hidden');
                $('#access_code').removeClass('hidden');
                $('#doorstep_access_code').addClass('hidden');
                $('#noaddress').addClass('hidden');
            }
            $.each(data.Zip, function (key, value) {
                test = '<li class="site-pin clearfix b-b m-b"><div class="select-pickup"><button class="btn btn-primary chooseSite" type="button" data-loading-text="Processing..." data-code="' + value.AccessCode + '" data-zip="' + value.Zip + '">Pickup here</button></div><img src="/images/site-address.jpg" alt="Site location" width="50" height="50" class="hidden-xs" /><div class="site-id">' + value.Nickname + '<div class="muted address">' + value.Address1 + ', ' + value.City + ', ' + value.Zip + '</div></div></li>';
                $("#site-feed").append(test);
            });
        }

    },
    //Revision Start By EWS ------ Get State Tax
    getStateTax: function (code) {
        var _class = this;
        var data = {code: code};
        _class.ajax('getStateTax', data, _class.cb_getStateTax);
    },
    //Revision End By EWS ------ Get State Tax
    //Revision Start By EWS ------ Calculate Total tax inclusive Tax
    cb_getStateTax: function (result) {
        var _class = this;
        var data = $.parseJSON(result);
        if (data.Tax != 0) {
            var total = 0;
            $('.productCheck:checked').each(function (index, el) {
                var info = $('#pi_' + $(el).val());
                var price = parseFloat(info.attr('rel'));
                total += price;
            });
            if(total > 0 && _class.doorstep){
                total  += _class.deliveryCharges;
            }
            var taxPricevalue = total * (data.Tax / 100);
            var totaltaxPrice = total + taxPricevalue;
            var totalptice = totaltaxPrice;
            totalptice -= parseFloat(_class.discount);

            $('#productPrice').html(toMoney(totalptice));
            $('#Tax').removeClass('hide');
            $('#TaxAmmount').removeClass('hide');
            $('#TaxAmmount').html(toMoney(taxPricevalue));
        } else if (data.Tax == 0) {
            var total = 0;
            $('.productCheck:checked').each(function (index, el) {
                var info = $('#pi_' + $(el).val());
                var price = parseFloat(info.attr('rel'));
                total += price;
            });
            total -= parseFloat(_class.discount);
            if(total && _class.doorstep){
                total +=  _class.deliveryCharges;
            }
            $('#productPrice').html(toMoney(total));
            $('#Tax').addClass('hide');
            $('#TaxAmmount').addClass('hide');
        }
    },
    //Revision End By EWS ------ Calculate Total tax inclusive Tax

    noAddress: function () {
        var _class = this;
        _class.togglePanels('hidden');
        $('#customAddress').removeClass('in');
        $('#notify_user').removeClass('hidden');

        //$('#customAddress input, #customAddress select, #customAddress textarea').attr('disabled', false).val('');
        //$('#pinWrapper').removeClass('highlightMe');
        //alertify.set({
        //    labels: {
        //        ok: 'Continue Creating Account',
        //        cancel: 'Just Notify Me'
        //    }
        //});
        //alertify.confirm($('#customText').html(), function (e) {
        //    if (!e) {
        //        var data = {
        //            email: $('#cEmail').val(),
        //            firstname: $('#cFirstName').val(),
        //            lastname: $('#cLastName').val()
        //        };
        //        _class.ajax('subscribe', data, _class.cb_standardAlt);
        //    }
        //});
        //alertify.set({
        //    labels: {
        //        ok: 'OK',
        //        cancel: 'Cancel'
        //    }
        //});

    },

    toggleDoorStep: function(status){
        var _class = this;
        if( status == false ){
            _class.doorstep = false;
        }else{
            _class.doorstep = true;
        }



    }
});
