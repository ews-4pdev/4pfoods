<div class="margin">
    <div class="row">
        <section class="">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="input-group date">
                            <input id="date" placeholder="Select Date" style="background-color: whitesmoke;" type="text"
                                   class="form-control"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                    <?php if( isset($customers) && count($customers) > 0 && isset($file) && !empty($file) ) { ?>
                        <div class="col-lg-2">
                            <button  id="downloadCsvFile" class="btn btn-info m-b-none m-t-none">
                                <i class="fa fa-download"></i>
                                Download as CSV
                            </button>
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <table class="table table-hover table-responsive m-t-lg">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Subscriptions</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php if(isset($customers) && !empty($customers)) { ?>
                            <?php foreach ($customers as $customer) { ?>
                                <tr>
                                    <td><a class="customer" href="javascript:;"
                                           data-href="<?=$customer->getId();?>">
                                            <?=$customer->getfirstName().' '.$customer->getLastName(); ?>
                                        </a>
                                    </td>
                                    <td><?php echo  $customer->getSubscriptionSummary();  ?></td>
                                    <td><?=$date ?></td>
                                </tr>
                         <?php }//end foreach ?>
                        <?php   }//end if ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<script type="text/javascript">
    var date = $('.date');
    var selectedDate = '<?=$date;?>';
    var file = '<?=(!empty($file)) ? $file : null?>';
    var bagsCreatedDates = <?=json_encode($alreadyCreatedBags)?>;
    var futureDates = <?=(isset($expectedDates) && !empty($expectedDates)) ? json_encode($expectedDates) : 0?>;
    bagsCreatedDates = sliceObject(bagsCreatedDates);

    $('#downloadCsvFile').on('click', function(event){
        event.preventDefault();
        if(file)
        {
            var form = $('<form></form>')
                .attr('action', '/admin/getCsvFile')
                .attr('target', '_blank')
                .attr('method', 'post');
            form.append($("<input type='text' name='file'>").val( file ) );
            form.appendTo("body").submit();
        }
    });

    date.datepicker({
        format: "mm-dd-yyyy",
        multidate: false,
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
        }
    });

    date.on('changeDate', function(){
          var  da = $(this).find('input').val();
        location.href = '/admin/changedBag/'+da;
    });


    $('.customer').on('click', function (event) {
        event.preventDefault();
        location.href   = location.href+'/'+$(this).data('href');
    })


</script>