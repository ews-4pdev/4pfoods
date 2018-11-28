var Admin = MyClass.extend({

    _controller: 'admin',
    _options: {},

    init: function () {
        var _class = this;
        var rel = $('#_Admin').attr('rel');
        var params = $.parseJSON(rel);
        _class.options = params;
        _class.sessionID = params.sessionID;
        _class._controller = 'admin';
        _class.options = params;
        switch (params.p) {
            case 'preport':
                _class.setupReport();
                break;
            case 'suppliers':
                _class.setupSupplier();
                break;
            case 'login':
                _class.setupLogin();
                break;
            case 'items':
                _class.setupItems();
                break;
            case 'deliveries':
                _class.setupDeliveries();
                break;
            case 'products':
                _class.setupProducts();
                break;
            case 'categories':
                _class.setupCategory();
                break;
            case 'viewproduct':
                _class.setupViewProduct();
                break;
            case 'viewcustomer':
                _class.setupViewCustomer();
                break;
            case 'approval':
                _class.setupApproval();
                break;
            case 'discounts':
                _class.setupDiscounts();
                break;
            case 'vieworder':
                _class.setupViewOrder();
                break;
            case 'viewpayment':
                _class.setupViewPayment();
                break;
            case 'payments':
                _class.setupPayments();
                break;
            case 'customers':
                _class.setupCustomers();
                break;
        }
    },

    setupReport: function () {

        var date = $('.input-daterange');
        date.datepicker({
            multidate: true,
            clearBtn: true
        });
        if (sDate && eDate) {
            $('.sDate').val(sDate);
            $('.eDate').val(eDate);
        }
        date.css('width', '41.66666667%');
        $('#downloadCsv').on('click', function (event) {
            event.preventDefault();
            if (file) {
                var form = $('<form></form>')
                    .attr('action', '/admin/getCsvFile')
                    .attr('target', '_blank')
                    .attr('method', 'post');
                form.append($("<input type='text' name='file'>").val(file));
                form.appendTo("body").submit();
            }
        });

        dateRange.daterangepicker({}, function (start, end) {
            dateRange.find('span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        });
        dateRange.on('apply.daterangepicker', function (ev, picker) {
            var startDate = picker.startDate.format('YYYY-MM-DD');
            var endDate = picker.endDate.format('YYYY-MM-DD');
            var status = $('#status').find('option:selected').text();
            if (status == 'Select Order Status') {
                alertify.alert('Please Select Appropriate Value From Drop Down');
                return;
            }
            if (status == selectedStatus && sDate == startDate && eDate == endDate) {
                alertify.alert('Please Select Different Option');
                return;
            }
            var form = $('<form method="post" action="/admin/preport"></form>');
            form.append('<input name="dateFrom" value="' + startDate + '"/>');
            form.append('<input name="dateTo" value="' + endDate + '"/>');
            form.append('<input name="status" value="' + status + '" />');
            form.appendTo($('body'));
            form.submit();
        });
    },

    setupCustomers: function () {
        var _class = this;
        $(document).on('click', '.archiveButton', function () {
            var data = {
                iUser: $(this).attr('data-id')
            };
            _class.ajax('toggleArchiveUser', data, _class.cb_standardAlt);
        });

        $('#activeCustomersTable').DataTable({
            "bInfo": false,
            "aaSorting": [],
            "columnDefs": [
                // { "visible": false, "targets": 3 },
                // { "visible": false, "targets": 2 },
                // { "visible": false, "targets": 4 }
            ]
        });
        $('#archivedCustomersTable').DataTable({
            "bInfo": false,
            "aaSorting": []
        });
    },

    setupPayments: function () {
        var _class = this;

        $(document).on('click', '.refundLink', function () {
            var el = $(this);
            var info = $.parseJSON(el.attr('data-info'));
            $('#submitRefund').attr('data-info', el.attr('data-info'));
            $('#maxAmount').html(info.maxAmount);
        });
        $(document).on('click', '#submitRefund', function () {
            var info = $.parseJSON($(this).attr('data-info'));
            var data = {
                iPayment: info.iPayment,
                Amount: $('#createIssueRefund').val()
            };
            _class.ajax('issueRefund', data, _class.cb_standardAlt);
            return false;
        });
    },

    setupViewCustomer: function () {
        var _class = this;
        $(document).on('click', '#resetPassword', function () {
            _class.ajax('resetCustomerPassword', {iUser: _class.options.iUser}, _class.cb_standardAlt);
            return false;
        });
        $(document).on('click', '.refundLink', function () {
            var el = $(this);
            var info = $.parseJSON(el.attr('data-info'));
            $('#submitRefund').attr('data-info', el.attr('data-info'));
            $('#maxAmount').html(info.maxAmount);
        });
        $(document).on('click', '#submitRefund', function () {
            var info = $.parseJSON($(this).attr('data-info'));
            var data = {
                iPayment: info.iPayment,
                Amount: $('#createIssueRefund').val()
            };
            _class.ajax('issueRefund', data, _class.cb_standardAlt);
            return false;
        });
        $(document).on('click', '#submitModifySite', function () {
            var data = {
                iUser: _class.options.iUser,
                iSite: $('#updateSite').val()
            };
            _class.ajax('changeCustomerSite', data, _class.cb_standardAlt);
            return false;
        });
        $(document).on('click', '.pay-on-demand', function () {
            var data = {
                iOrder: $(this).attr('data-id')
            };
            _class.ajax('submitSinglePayment', data, _class.cb_standardAlt);
        });
        $(document).on('click', '#submitPickUpSite', function () {
            var data = {
                iUser: _class.options.iUser,
                iSite: $('#updatePickUpSite').val(),
                status: 'DoorToPickup'
            };
            _class.ajax('changeCustomerDeliveryOption', data, _class.cb_standardAlt);
            return false;
        });
        $(document).on('click', '#submitDoorStepSite', function () {
            var data = {
                iUser: _class.options.iUser,
                iSite: $('#updateDoorStepSite').val(),
                Address1: $('#cAddress1').val(),
                Address2: $('#cAddress2').val(),
                Parking: $('#Parking').val(),
                KeyFob: $('#KeyFob').prop('checked') == true ? 'true' : 'false' ,
                status: 'PickupToDoor',
                ConciergeNumber: $('#concierge_number').val(),
                Concierge: $('#concierge').prop('checked'),
                SingleFamilyHome: $('#SingleFamilyHome').prop('checked'),
                OfficeBuilding: $('#OfficeBuilding').prop('checked'),
                ApartmentCondo: $('#ApartmentCondo').prop('checked'),
                TownHouse: $('#TownHouse').prop('checked')
            };
            _class.ajax('changeCustomerDeliveryOption', data, _class.cb_standardAlt);
            return false;
        });
        $(document).on('click', '#submitDoorStepModifySite', function () {
            var data = {
                iUser: _class.options.iUser,
                iSite: $('#updateZip').val()
            };
            _class.ajax('changeCustomerSite', data, _class.cb_standardAlt);
            return false;
        });
    },

    setupViewPayment: function () {
        var _class = this;
        $(document).on('click', '#submitRefund', function () {
            var data = {
                iPayment: _class.options.iPayment,
                Amount: $('#Amount').val()
            };
            _class.ajax('issueRefund', data, _class.cb_standardAlt);
            return false;
        });
    },

    setupDiscounts: function () {
        var _class = this;
        $('table').DataTable({
            'info' : false
        });

        $(document).on('click', '.unpublishButton', function (e) {
            _class.ajax('togglePublishDiscount', {iDiscount: $(e.target).attr('rel')}, _class.cb_standardAlt);
        });
        $(document).on('click', '#addCode', function () {
            _class.ajaxForm('addDiscount', 'addDiscount', _class.cb_standard);
            return false;
        });
    },

    setupViewOrder: function () {
        var _class = this;
        $('#refundButton').on('click', function () {
        });
    },

    setupLogin: function () {
        var _class = this;
        $('#cSubmit').on('click', function () {
            var data = {
                Email: $('#cEmail').val(),
                Password: $('#cPassword').val(),
                prefix: 'c'
            };
            _class.ajax('submitLogin', data, _class.cb_standardAlt);
            return false;
        });
    },

    setupProducts: function () {
        var _class = this;

        $('#modalNewProduct').on('hidden.bs.modal', function (e) {
            $('#newProducts').find('.error-on').removeClass('error-on');
        });

        $(document).on('click', '#submitNewProduct', function () {
            _class.ajaxForm('addProduct', 'newProducts', _class.cb_standard);
            return false;
        });
    },

    setupItems: function () {
        var _class = this;
        $('.productCategory').on('click', function () {
            var id = $(this).val();
            var form = $(this).closest('form').attr('id');

            var data = {
                id: id,
                task: 'getProductSizes'
            };
            _controller = 'admin';
            var request = $.ajax({
                url: '/ajax/admin',
                type: 'post',
                data: data,
                datatype: "application/json"
            });
            var str = '';
            request.done(function (response) {
                response = $.parseJSON(response);
                $.each(response.data, function (index, value) {
                    if (value.size) {
                        str += " <label class='text-capitalize' for='pPrice'>" + value.title + "<input class='form-control size' name='size[" + value.id + "]' id='' type='text'> <div class='error-notify'></div> </label>";
                    }

                });

                if (form == 'editItems') {
                    $('#editSizeDiv').html(str)
                } else {
                    $('#sizeDiv').html(str)
                }

            })
        });

        $("#suppliers").select2({
            placeholder: 'Suppliers'
        });

        $('#submitNewItem').on('click', function (event) {
            event.preventDefault();
            _class.ajaxForm('addItem', 'newItems', function (response) {
                var data = $.parseJSON(response);
                if( data.success ){
                    location.href = data.url;
                }else{
                    var errorDiv = $('.newItemsError');
                    errorDiv.show().html('');
                    data.errorCodes.forEach(function (item, value) {
                        $('.newItemsError').append('<div>' + item + '</div>');
                    });
                }
            });
        });

        $('#submitEditItem').on('click', function (event) {
            event.preventDefault();
            _class.ajaxForm('addItem', 'editItems', function(response){
                var data = $.parseJSON(response);
                if( data.success ){
                    location.href = data.url;
                }else{
                    var errorDiv = $('.editItemsError');
                    errorDiv.show().html('');
                    data.errorCodes.forEach(function (item, value) {
                        $('.editItemsError').append('<div>' + item + '</div>');
                    });
                }
            });
        });

        $('.changeItemStatus').on('click', function () {

            var data = {
                id: parseInt($(this).attr('id'))
            };
            alertify.confirm("Are you sure you want to change item status.", function(e){
                if(e){
                    _class.ajax('changeItemStatus', data, _class.cb_removeItem);
                    return false;
                }
            });
        });
        $(document).on('click', '#activeItem', function () {
            window.location="/admin/items/publishedItem";
        });

        $(document).on('click', '#InactiveItem', function () {
            window.location="/admin/items/unpublishedItem";
        });

        $( document ).ready(function() {
            var pathname = window.location.pathname;
            path = pathname.split('/');
            if(path[3] == 'unpublishedItem')
            {
                $('#activeItem').parent().removeClass('active');
                $('#activeItems').removeClass('active');
                $('#InactiveItem').parent().addClass('active');
                $('#InactiveItems').addClass('active');
            }
            else if(path[3] == 'publishedItem')
            {
                $('#activeItem').parent().addClass('active');
                $('#activeItems').addClass('active');
                $('#InactiveItem').parent().removeClass('active');
                $('#InactiveItems').removeClass('active');
            }

        });

        $('#modalNewItem').on('hidden.bs.modal', function (e) {
            $('.newItemsError').hide().html('');
        });

        $('#modalEditItem').on('hidden.bs.modal', function (e) {
            $('.editItemsError').hide().html('');
        });

    },

    setupSupplier: function () {
        var _class = this;
        $(document).on('click', '#submitNewSupplier', function () {
            _class.ajaxForm('addSupplier', 'newSuppliers', _class.cb_standard);
            return false;
        });
        $(document).on('click', '#submitEditSupplier', function () {
            _class.ajaxForm('addSupplier', 'editSupplier', _class.cb_standard);
            return false;
        });
        $(document).on('click', '.changeSupplierStatus', function () {
            var data = {
                id: parseInt($(this).data('id')),
                returnURL: '/admin/suppliers'
            };
            var msg = 'Are you sure you want to change Supplier status.';
            if ($(this).find('i').hasClass('active')) {
                msg = 'Are you sure you want to disable Supplier. This action will also disable all items that are associated with this supplier.';
            }

            alertify.confirm(msg, function (e) {
                if (e) {
                    _class.ajax('changeSupplierStatus', data, _class.cb_standardAlt);
                } else {
                    return false;
                }
            });
        });
        $(document).on('click', '.editSupplier', function () {
            var data = getAllTds($(this).attr('id'));
            fillForm('editSuppliers', data);
            $('#modalEditSupplier').modal('show');
        });
        $('#modalEditSupplier').on('hidden.bs.modal', function(e){
            $('#editSupplier').find('div.error-on').removeClass('error-on');
        });
        $('#modalNewSupplier').on('hidden.bs.modal', function(e){
            $('#newSuppliers').find('div.error-on').removeClass('error-on');
        });
    },

    setupCategory: function () {
        var _class = this;

        $('#submitNewCategory').on('click', function () {
            _class.ajaxForm('addCategory', 'newCategories', _class.cb_standard);
            return false;
        });

        $('.deleteCategory').on('click', function () {
            var data = {
                id: parseInt($(this).data('href'))
            };

            _class.ajax('deleteCategory', data, _class.cb_standardAlt);
        });
    },

    setupApproval: function () {
        var _class = this;
        $('table').DataTable({
            'info' : false
        });

        $('.submitApproval').each(function (index, el) {
            el = $(el);
            el.on('click', function () {
                var id = el.attr('rel');
                _class.ajaxForm('addDeliverySite', 'approvalForm' + id, _class.cb_submitApproval);
                return false;
            });
        });
        $('.assignSelect').on('change', function (e) {
            var el = $(e.target);
            var data = {
                iUser: el.attr('rel'),
                iSite: el.val()
            };
            _class.ajax('assignSite', data, _class.cb_standardAlt);
        });
    },

    setupViewProduct: function () {
        var _class = this;
        $('#submitEditProduct').on('click', function () {
            _class.ajaxForm('addProduct', 'editProductForm', _class.cb_submitEditProduct);
            return false;
        });
    },

    setupDeliveries: function () {
        var _class = this;
        $('table').DataTable({
            'info' : false
        });
        $('.hideSiteLink').on('click', function () {
            var el = $(this);
            alertify.confirm('This will hide the site permanently from this page. Are you sure?', function (e) {
                if (e)
                    _class.ajax('hideDeliverySite', {iSite: el.attr('data-id')}, _class.cb_standardAlt);
            });
        });
        $(document).on('click', '.editSiteLink',function () {
            var el = $(this);
            var info = $.parseJSON($(this).parents('tr').attr('data-info'));
            $.each(info, function (key, value) {
                var node = $('#update' + key);

                if( node.is(':checkbox') ){ node.prop('checked', value); }

                node.val(value);
            });
        });
        $('#submitUpdateSite').on('click', function () {
            _class.ajaxForm('editSite', 'updateSiteForm', _class.cb_standard);
            return false;
        });
        $('#submitNewDeliverySite').on('click', function () {
            _class.ajaxForm('addDeliverySite', 'site', _class.cb_submitAddDelivery);
            return false;
        });
        $('#submitAddDriver').on('click', function (e) {
            _class.ajaxForm('addDriverAccount', 'addDriverForm', _class.cb_submitAddDriver);
            return false;
        });
        $('.disableSite').on('click', function (e) {
            _class.ajax('toggleEnabledDeliverySite', {iSite: $(e.target).attr('rel')}, _class.cb_standardAlt);
            return false;
        });
        $(document).on('click', '.changeDeliveryDayLink' ,function() {
            $('#iSite').val($(this).attr('data-id'));
        });
        $('#submitNewDeliveryDay').on('click', function () {
            var data = {
                iSite: $('#iSite').val(),
                DefaultDeliveryDay: $('#newDeliveryDay').val()
            };
            _class.ajax('changeSiteDeliveryDay', data, _class.cb_standardAlt);
        });
        $('.removeDriver').each(function (index, el) {
            el = $(el);
            el.on('click', function () {
                var data = {iUser: el.attr('rel')};
                if (confirm('Are you sure you want to delete this driver?')) {
                    _class.ajax('removeDriver', data, function () {
                        location.href = '/admin/deliveries';
                    });
                }
            });
        });
        $(document).on('click', '#userSite', function(){
            location.href = '/admin/customers/'+$('#userSite').val();
        });
    },

    cb_submitAddDriver: function (response) {
        var _class = this;
        var data = $.parseJSON(response);
        if (data.success)
            location.href = data.url;
        else
            _class.processErrors(data.errorCodes, 'driver');
    },

    cb_submitApproval: function (response) {
        var _class = this;
        var data = $.parseJSON(response);
        if (data.success)
            location.href = data.url;
        else
            _class.processErrors(data.errorCodes, 'formSite', data.request.iUser);
    },

    cb_submitEditProduct: function (response) {
        var _class = this;
        var data = $.parseJSON(response);
        if (data.success)
            location.href = data.url;
        else
            _class.processErrors(data.errorCodes, 'p');
    },

    cb_removeItem: function (response) {
        var data = $.parseJSON(response);
        if (data.success)
        {
            $('#tr_'+data.id).remove();
        }
    },

    cb_submitAddDelivery: function (response) {
        var _class = this;
        var data = $.parseJSON(response);
        if (data.success)
            location.href = data.url;
        else
            _class.processErrors(data.errorCodes, 'site');
    },

});
