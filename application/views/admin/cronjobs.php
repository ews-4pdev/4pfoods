<div class="margin">
    <div class="row">
        <div>
            <label for="date">Enter Date</label>
            <input type="text" name="date" id="date">
        </div>
        <section class="">
            <div><a target="_blank" class="btn btn-primary cron" data-href="/admin/sendEmails">0 - Send Emails to customers for bag ready Email.</a></div>
            <div><a target="_blank" class="btn btn-primary cron" data-href="/admin/createOrder">1 - Create Orders</a></div>
            <div><a target="_blank" class="btn btn-primary cron" data-href="/admin/createDeliveries">2 - Create Deliveires</a></div>
            <div><a target="_blank" class="btn btn-primary cron" data-href="/admin/sendDeliveryReminders">3 - Send Delivery Reminders</a></div>
            <div><a target="_blank" class="btn btn-primary cron" data-href="/admin/execute">3 - Get Payments</a></div>
        </section>
    </div>
</div>

<script type="text/javascript">
    $('document').ready(function(){
        $('#date').datepicker({
            format: "mm-dd-yyyy"
        });

    });
    $('.cron').on('click', function(){
        var href = $(this).data('href');
        var date = $('#date').val();
        href = href+'/'+date;
        $(this).prop('href', href).trigger();
    })



</script>

