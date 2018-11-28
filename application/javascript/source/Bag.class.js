var Bag = MyClass.extend({

    _controller: 'admin',
    _options: {},
    _bagId: null,
    _productId: null,
    table: null,

    init: function () {
        var _class = this;
        alertify.set({delay: 900});
        var rel = $('#_Bag').attr('rel');
        var params = $.parseJSON(rel);
        _class.options = params;
        _class.sessionID = params.sessionID;
        _class._controller = 'admin';
        _class.options = params;
        _class.setDefaults();
    },

    setDefaults: function () {
        var _class = this;
        _class.datePicker();
        _class.tableSort();
        var dateSync = $('.dateSync');
        dateSync.datepicker({
            format: "mm-dd-yyyy",
            multidate: true,
            todayBtn: true,
            clearBtn: true,
            beforeShowDay: function(date) {
                var today = new Date();

                var month = parseInt( date.getUTCMonth() + 1 );
                var year = parseInt( date.getUTCFullYear() );
                var dayInMonth = parseInt( date.getDate() );
                var dayName = weekdays[ parseInt( date.getDay() ) ];

                if( date > today )
                {
                    if( bagsCreatedDates[year] && bagsCreatedDates[year][month] && bagsCreatedDates[year][month][dayInMonth] )
                    {
                        return {classes: 'highlight-range'};
                    }

                    if( futureDates[dayName] )
                    {
                        return { classes : 'highlight-future-dates' };
                    }
                }

                return false;
            }
        });
        dateSync.on('click', function (event) {
            event.stopPropagation();
            var ok = $('.today');
            ok.addClass('ok').removeClass('today').attr('datepicker_id', $(this).attr('id')).text('OK').css({'backgroung-color': 'lightgray'});
            $('.clean').css({'backgroung-color': 'tomato'});
        });

        $(document).on('focusout', '.updatePoints', function () {
            var input = $(this);
            if ( !isInt(input.val()) || input.val() < 0 ) {
                alertify.error('Value must be Integer.', '', 1000);
                input.val(input.data('default'));
                return;
            }

            if (input.val() < 1) {
                alertify.error('Minimum Points must be 1 otherwise simply delete the item.', '', 4000);
                input.val(input.data('default'));
                return;
            }
            if( input.val() == input.data('default') )return;

            var secondary = ( input.closest('tr').attr('class') ).search('primary');
            var temp = $(this).closest('tr').attr('id');

            temp = temp.replace('item-list-', '');
            var data = {
                Points: $(this).val(),
                defaultpoints: $(this).data('default'),
                iBagItem: temp
            };

            if (secondary > -1) {
                _class._bagId = parseInt(( $(this).closest('tr').attr('class') ).replace('primary-', ''));
            } else {
                _class._bagId = parseInt(( $(this).closest('tr').attr('class') ).replace('secondary-', ''));
            }

            _class.ajax('addBagItems', data, function (response) {
                var data = $.parseJSON(response);
                if (data.success && secondary > -1) {
                    bags[_class._bagId].total_points = data.total_points;
                    input.data('default', data.request.Points);
                    input.val(data.request.Points);
                    _class.updatePoints(_class._bagId);
                    alertify.success('Points Updated', '', 1000);
                    $('#syncButton').removeClass('hidden');
                    $('#syncButton').find('button').text('Update Customer Bags');
                    $('.sync_'+_class._bagId).removeClass('sync').addClass('no-sync').html('').html('<i class="fa fa-refresh fa-2x"></i>');
                    return;
                }
                if (data.success == false) {
                    _class._displayError(data.errorCodes[0]);
                    input.val(input.data('default'));
                }

            });
        });

        $(document).on('click', '.modalProducts', function () {
            $('#Action').attr('type', 'hidden');
            $('#Points').attr('type', 'hidden');
            _class._bagId = $(this).data('bag-id');
            _class._productId = $(this).data('product-id');
            var total_points = bags[_class._bagId]['total_points'];
            $('#totalPoints').text(total_points);
            $('#extraProduct').find('tbody').html('');
            if (items) {
                _class.drawTable( $(this).data('category'), $(this).data('product-type') );
            }
            //  Setup - add a text input to each header cell
            $('#extraProduct thead th').each(function () {
                var title = $('#extraProduct thead th').eq($(this).index()).text();
                if (title != '' && title != 'Action' && title != 'Points' && title != 'Swappable Items' && title != 'Product') {
                    $(this).html('<input type="text" id="' + title + '" placeholder="Search ' + title + '" />');
                }
            });
            _class.table.columns().every(function () {
                var that = this;
                $('input', this.header()).on('keyup change', function () {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
            //$('#Product').val($(this).data('product-type')).trigger('change');
        });

        $(document).on('click', '.ok', function () {
            var date = $(this).attr('datepicker_id');
            var utcDates = $('#' + date).datepicker('getDates');
            $(date).datepicker('clearDates');
            $(date).datepicker('hide');
            var simpleDates = [];
            var str = '<div style="color: tomato; font-weight: bold;text-align: center;">Created Bags must have 48 hours difference from today otherwise date will be skipped.<br> <br> Bags will be sync for following dates Any previous data will be lost. </div><br>';
            var ss = '';
            for (var aDate in utcDates) {
                var temp = new Date(utcDates[aDate]);
                var today = new Date();
                var timeDiff = Math.abs(temp - today) / 36e5;
                if( timeDiff <= 48 ){
                    continue;
                }
                simpleDates.push(temp.getFullYear() + '/' + ( ("0" + ( temp.getMonth() + 1 )).slice(-2) ) + '/' + ( "0" + temp.getDate() ).slice(-2));
                ss = ss + '<div style="text-align: center;">' + temp.toDateString() + '</div>';
            }
            alertify.confirm(str + ss, function (e) {
                if (e) {
                    var data = {
                        dates: simpleDates,
                        bag: $('#' + date).data('bag-id')
                    };
                    _class.ajax('createSyncBags', data, function (response) {
                        response = $.parseJSON(response);
                        if (response.success == false) {
                            _class._displayError(response.errorCodes[0]);
                            return;
                        }
                        if (response.url) {
                            location.href = response.url;
                        }
                        alertify.success('Bag Items created.', '', 1000);
                    });
                } else {
                    alertify.log('Canceled', '', 1000);
                }
            }).set('title', 'Create Bags');
        });

        $(document).on('click', '.addProduct', function (event) {
            event.preventDefault();
            event.stopPropagation();
            var row = $(this).closest('tr').attr('id');

            var points = $(this).closest('tr').find('td').eq(3).find('input');
            if( points.val() < 1 ||  !isInt(points.val()) ){
                alertify.error('Please Insert valid Point.');
                points.val(
                    points.prop('data-default')
                );
                return;
            }

            var data = {
                Points: points.val(),
                PointId: ( $(this).data('pointid') ).replace('tr-', ''),
                BagId: _class._bagId,
                rowId: row.replace('tr-', ''),
                Status: ( $(this).closest('tr')
                    .find('td')
                    .eq(4)
                    .find('input[name=Secondary]')
                    .is(':checked') ) ? 'Secondary' : 'Primary'
            };

            _class.ajax('addBagItems', data, _class.cb_setupBags);

        });

        $(document).on('click', '.item-list', function (event) {
            event.preventDefault();
            event.stopPropagation();
            _class._bagId = parseInt(($(this).closest('table').attr('id') ).replace('table_', ''));
            var delete_points = parseInt($(this).closest('tr').find('td').eq(2).find('input').val());

            var temp = $(this).closest('tr').attr('id');
            temp = temp.replace('item-list-', '');

            var data = {
                iBagItem: temp,
                action: 'delete',
                secondary: ($(this).closest('tr').attr('class')).search('secondary'),
                delete_points: delete_points
            };

            _class.ajax('addBagItems', data, _class.cb_setupBags);

        });

        $('#createCustomerBags').on('click', function () {
            var noPrimaryItems = [];

            for (var i in bags) {
                if (bags.hasOwnProperty(i)) {
                    if ($('.primary-' + i).length < 1) {
                        noPrimaryItems.push(bags[i].title);
                    }
                }
            }

            if (noPrimaryItems.length > 0) {
                alertify.alert(
                        'Following Bags has no items added. Please add at least one item.<br><strong>' + noPrimaryItems + '</strong>')
                    .set('title', 'Add Items');
                return;
            }
            var date = $(this).data('date');
            var data = {
                date   : date,
                status : published
            };

            _class.ajax('publishBags', data, function (response) {
                response = $.parseJSON(response);
                if (response.success) {
                    alertify.alert('Bags are successfully created for customers. An email notification will' +
                        'be sent to each customer who has subscribed for this day.').set('title', 'Publish Bags for Customers');
                    $('#createCustomerBags').closest('div').addClass('hidden');
                    for(var i in bags){
                        $('.sync_'+i).removeClass('no-sync').addClass('sync').html('').html('<i class="fa fa-refresh fa-2x"></i>');
                    }
                }else{
                    alertify.alert(response.error);
                }
            })
        });

        var secondary = $('.secondary').find('td');
        var primary = $('.primary').find('td');
        _class.tableCss();
        _class.tableSort();
        $(document).on('click', '.secondary', function () {
            var bag = $(this).find('td').data('bag-id');
            var secondaryItems = $('.secondary-' + bag);
            var secondary = $(this).find('td').attr('id');
            secondary = $('#' + secondary);

            if (secondaryItems.length < 1) {
                return;
            }
            ( secondaryItems.hasClass('hidden') ) ? secondaryItems.removeClass('hidden') : secondaryItems.addClass('hidden');
            ( secondary.find('i').hasClass('glyphicon glyphicon-minus') )
                ? secondary.find('i').removeClass('glyphicon glyphicon-minus').addClass('glyphicon glyphicon-plus')
                : secondary.find('i').removeClass('glyphicon glyphicon-plus').addClass('glyphicon glyphicon-minus');

        });
        $(document).on('click', '.primary', function () {
            var bag = $(this).find('td').data('bag-id');
            var primaryItems = $('.primary-' + bag);
            var primary = $(this).find('td').attr('id');
            primary = $('#' + primary);

            if (primaryItems.length < 1) {
                return;
            }
            ( primaryItems.hasClass('hidden') ) ? primaryItems.removeClass('hidden') : primaryItems.addClass('hidden');
            ( primary.find('i').hasClass('glyphicon glyphicon-minus') )
                ? primary.find('i').removeClass('glyphicon glyphicon-minus').addClass('glyphicon glyphicon-plus')
                : primary.find('i').removeClass('glyphicon glyphicon-plus').addClass('glyphicon glyphicon-minus');

        });
    },

    datePicker : function(){
        var _class = this;
        var inRange=false;

        var temp_date = $('.date');
        temp_date.datepicker({
            format : 'mm-dd-yyyy',
            setDate : new Date(),

            beforeShowDay: function(date) {
                var today = new Date();

                var month = parseInt( date.getUTCMonth() + 1 );
                var year = parseInt( date.getUTCFullYear() );
                var dayInMonth = parseInt( date.getDate() );
                var dayName = weekdays[ parseInt( date.getDay() ) ];

                if( bagsCreatedDates[year] && bagsCreatedDates[year][month] && bagsCreatedDates[year][month][dayInMonth] )
                {
                    return {classes: 'highlight-range'};
                }

                if( date > today )
                {

                    if( futureDates[dayName] )
                    {
                        return { classes : 'highlight-future-dates' };
                    }
                }

                return false;
            }
        });
        temp_date.on('changeDate', function (e) {
            var data = {
                date: $('#date').val()
            };
            if(  phpDate ==  data.date ) return;
            var today = new Date();
            var date = new Date(e.date);
            var timeDiff = Math.abs(date - today) / 36e5;

            if( timeDiff <= 48 ){
                _class.ajax('checkBagsDate', data, function(response){
                    response = $.parseJSON(response);
                    if( !response.success ){
                        var msg = err(response.errorCodes[0]);
                        alertify.alert(msg);
                    }else{
                        location.href = response.url;
                    }
                });
                return;
            }
            _class.ajax('createBags', data, function (response) {
                response = $.parseJSON(response);
                if( !response.success ){
                    _class._displayError(response.errorCodes[0]);
                }
                if (response.url) {
                    location.href = response.url;
                }
            });

        });

        if (phpDate != 0) {
            temp_date.find('input').val(phpDate);
            temp_date.datepicker('update');
        }
    },

    tableSort: function () {
        var _class = this;
        var primary = $('.primary');
        var secondary = $('.secondary');
        var noSecondary = 0;

        // Place all primary tr right after tbody and before Secondary
        if (primary.length > 0) {
            noSecondary++;
            primary.each(function (index, value) {
                $(value).closest('tbody').find('tr').eq(0).before($(value))
            });
        }
        // Place all secondary tr right after tbody and before Secondary
        if (noSecondary < 1) {
            if (secondary.length > 0) {
                secondary.each(function (index, value) {
                    $(value).closest('tbody').find('tr.primary').after($(value));
                });
            }
        }

        // Place all primary items under Primary tr in sorted order and same for secondary items.

        for (var i in bags) {
            if (bags.hasOwnProperty(i)) {
                var table = $('#table_' + i);
                if (table.find('tbody').find('tr.primary').length > 0) {
                    _class.sort(i, 'primary');
                    table.find('tbody').find('tr.primary').after($('.primary-' + i));
                }
                if (table.find('tbody').find('tr.secondary').length > 0) {
                    _class.sort(i, 'secondary');
                    table.find('tbody').find('tr.secondary').after($('.secondary-' + i));
                }
            }
        }

    },

    sort: function (i, status) {
        $('.' + status + '-' + i).sort(function (a, b) {

            var first = $(a).find('td:eq(0)').text().toLowerCase();
            var second = $(b).find('td:eq(0)').text().toLowerCase();

            if (first < second) {
                return -1;
            } else if (second < first) {
                return 1;
            } else {
                return 0;
            }
        }).appendTo($('#table_' + i + ' tbody'));
    },

    tableCss: function () {
        var secondary = $('.secondary').find('td');
        var primary = $('.primary').find('td');
        secondary.css({
            'background-color': 'tomato',
            'cursor': 'pointer',
            'border-bottom': '4px solid white',
            'border-top': '4px solid white'
        });

        primary.css({
            'background-color': 'tomato',
            'cursor': 'pointer',
            'border-top': '4px solid white',
            'border-bottom': '4px solid white'
        });
    },

    drawTable: function (category, productTitle) {
        var _class = this;
        // DataTable
        if ( $.fn.DataTable.isDataTable('#extraProduct') ) {
            $('#extraProduct').DataTable().destroy();
        }
        var item = [];
        if( items[category] ){
            item = _class.createTableArray(items[category]);
        }


        _class.table = $('#extraProduct').DataTable({
            "bSort": false,
            'info': false,
            "search": {
                "regex": true,
                "bSmart": false,
                "bRegex": true
            },
            "data": item,
            "columnDefs": [
                {
                    "targets": -1,
                    "searchable": false,
                    "orderable": false
                },
                {
                    "targets": -2,
                    "searchable": false,
                    "orderable": false
                }
            ],
            "columns": [
                {"item": "Item"},
                {"item": "Supplier"},
                {"item": "Product"},
                {
                    className :  'data_bag_table',
                    data: null,
                    defaultContent: '<input style="width: 100%;"  data-default="0" class="form-control text-center" type="text" value="" >'
                },
                {
                    data: null,
                    defaultContent: '<div class="input-group col-lg-12 text-center pull-right">' +
                    '<input type="checkbox" name="Secondary">' +
                    '</div>'
                },
                {
                    data: null,
                    defaultContent: '<div class="input-group col-lg-1 pull-right">' +
                    '<button style="margin:0;" class="addProduct btn btn-info" data-pointId="" >Add</button>' +
                    '</div>'
                }
            ],

            "createdRow": function (row, data, dataIndex) {
                // Add id to each tr
                $(row).attr('id', data[4]);
                //Add points inside input field
                $(row).find('td').eq(3).find('input').prop('value', data[3]);
                $(row).find('td').eq(3).find('input').prop('data-default', data[3]);

                //Add data-pointId into button
                var input = $(row).find('td').eq(5);
                input.find('div').find('button').attr('data-pointId', data[4]);
                input.css('width', '2%');
            }

        });
        _class.table
                .columns( 2 )
                .search( productTitle )
                .draw();

    },

    updatePoints: function (bagId) {
        $('#total_'+bagId).text( bags[bagId].total_points );
    },

    cb_setupBags: function (response) {
        var _class = this;
        var data = $.parseJSON(response);
        if (data.success) {
            $('#syncButton').removeClass('hidden');
            $('#syncButton').find('button').text('Update Customer Bags');

            $('.sync_'+_class._bagId).removeClass('sync').addClass('no-sync').html('').html('<i class="fa fa-refresh fa-2x"></i>');
            if (data.action == 'add') {
                _class.addRow(data.rowId, data.id, data.total_points);
            }
            else if (data.action == 'addition') {
                _class.additionRow(data);
            }
            else if (data.action == 'delete') {
                _class.deleteRow(data);
            }
        }
        else {
            _class.processErrors(data.errorCodes, 'site');
        }
    },

    addRow: function (id, bagItemId, total_points) {
        var _class = this;
        var tr = $('#tr-' + id);
        var items = tr.find('td').eq(0).text();
        var supplier = tr.find('td').eq(1).text();
        var type = tr.find('td').eq(2).text();
        var points = parseInt(tr.find('td').eq(3).find('input').val());
        var secondary = tr.find('td').eq(4).find('input[name=Secondary]').is(':checked');

        var secondaryTr = '<tr class="secondary">' +
            '<td  id="secondary-td-' + _class._bagId + '"  data-bag-id="' + _class._bagId + '"  style="text-align: left;" colspan="5">Secondary' +
            '<i class="glyphicon glyphicon-minus pull-right"></i>' +
            '</td>' +
            '</tr>';

        var primaryTr = '<tr class="primary">' +
            '<td  id="primary-td-' + _class._bagId + '"  data-bag-id="' + _class._bagId + '"  style="text-align: left;" colspan="5">Items' +
            '<i class="glyphicon glyphicon-minus pull-right"></i>' +
            '</td>' +
            '</tr>';

        var table = $('#table_' + _class._bagId + ' tbody');
        if (table.find('tr.secondary').length < 1 && secondary) {
            table.append(secondaryTr);
            _class.tableCss();
        }
        if (table.find('tr.primary').length < 1 && !secondary) {
            table.append(primaryTr);
            _class.tableCss();
        }
        if (secondary) {
            table.find('tr.secondary').after(
                '<tr class="' + 'secondary-' + _class._bagId + '"  id="item-list-' + bagItemId + '">' +
                '<td>' + items + '</td><td>' + supplier + '</td>' +
                '<td width="120"><input id="input-' + bagItemId + '" data-default="' + points + '" class="form-control updatePoints" type="text" value=' + points + ' name=""></td>' +
                '<td width="1">' +
                '<div class="input-group col-lg-1 pull-right">' +
                '<button style="margin:0;" class="item-list btn btn-danger" >Delete</button>' +
                '</div>' +
                '</td></tr>');
        } else {
            bags[_class._bagId].total_points = total_points;
            _class.updatePoints(_class._bagId);
            var remain = bags[_class._bagId].total_points;
            $('#Points').val(remain);
            $('#totalPoints').text(remain);
            $('#date_' + _class._bagId).show();
            table.find('tr.primary').after(
                '<tr class="' + 'primary-' + _class._bagId + '"  id="item-list-' + bagItemId + '">' +
                '<td>' + items + '</td>' +
                '<td>' + supplier + '</td>' +
                '<td width="120"><input id="input-' + bagItemId + '" class="form-control updatePoints" data-default="' + points + '" type="text" value=' + points + ' name=""></td>' +
                '<td width="1">' +
                '<div class="input-group col-lg-1 pull-right">' +
                '<button style="margin:0;" class="item-list btn btn-danger" >Delete</button>' +
                '</div>' +
                '</td></tr>');
        }

        alertify.success('Item Added', '', 1000);
        $('#alert_' + _class._bagId).closest('tr').remove();
        $('.date_' + _class._bagId).removeClass('hidden');
        _class.tableSort();
    },

    createTableArray: function (item) {
        var container_array = [];
        $.each(item, function (index, value) {
            var temp_array = [];
            temp_array.push(
                value.Item,
                value.Supplier,
                value.Product,
                value.Points,
                value.DT_RowId
            );
            container_array.push(temp_array);
        });

        return container_array;
    },

    additionRow: function (data) {
        var _class = this;
        var input = $('#input-' + data.id);
        input.val(data.Points);
        if (data.request.Status == 'Primary') {
            bags[_class._bagId].total_points = data.total_points;
            _class.updatePoints(_class._bagId);
            var remain = bags[_class._bagId].total_points;
            $('#Points').val(remain);
            $('#totalPoints').text(remain);
        }
        alertify.success('Item added');
        return true;
    },

    deleteRow: function (data) {
        var _class = this;
        if (data.request.secondary < 0) {
            bags[_class._bagId].total_points = data.total_points;
            this.updatePoints(_class._bagId);
        }
        $('#item-list-' + data.id).remove();
        alertify.error('Row Deleted', '', 1000);
        var tbody = $('#table_' + _class._bagId).find('tbody');
        var primaryItems = $('.primary-' + _class._bagId).length;
        var secondaryItems = $('.secondary-' + _class._bagId).length;

        if (primaryItems < 1) {
            tbody.find('tr.primary').remove();
        }
        if (secondaryItems < 1) {
            tbody.find('tr.secondary').remove();
        }

        if (primaryItems < 1 && secondaryItems < 1) {
            tbody.find('tr.primary').remove();
            tbody.find('tr.secondary').remove();
            $('#date_' + _class._bagId).hide();
            var td = $('<td></td>');
            td.attr('id', 'alert_' + _class._bagId)
                .attr('colspan', 5)
                .css('text-align', 'center')
                .html('<div class="alert alert-danger">No Items Selected</div>');

            tbody.append($('<tr></tr>').html(td));
        }
        return true;
    },

    getDays: function (daysArray) {

        var d = new Date(),
            month = d.getMonth(),
            days = [];

        d.setDate(1);

        var weekdays = {
            'Monday' : 1,
            'Tuesday' : 1,
            'Wednesday' : 1,
            'Thursday' : 1,
            'Friday' : 1,
            'Saturday' : 1,
            'Sunday' : 1
        };

        daysArray.each(function(index, value){
            // Get the first Monday in the month
            while (d.getDay() !== 2) {
                d.setDate(d.getDate() + 1);
            }


            // Get all the other Mondays in the month
            while (d.getMonth() === month) {
                days.push(new Date(d.getTime()));
                d.setDate(d.getDate() + 7);
            }

        });



        return days;

    }

});
