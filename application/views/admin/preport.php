<!--<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
<!--<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>-->
<!--<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />-->

<link href="/css/daterangepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/daterangepicker.js"></script>

<div class="margin">
    <div class="row">
        <section class="">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <form class="form-inline col-lg-12" action="" method="POST">
                        <div class=" col-lg-3 pull-left">
                            <select  class="form-control" name="status" id="status">
                                <option selected="selected">Select Order Status</option>
                                <?php foreach ($orderStatus as $key => $value) { ?>
                                    <option <?=($value == $input['status']) ? 'selected' : '' ?> value="<?=$key ?>"><?=$value?></option>
                                <?php    }  ?>
                            </select>
                        </div>
                        <div class="input-daterange input-group pull-left" id="datepicker">
                            <input placeholder="Start Date" type="text" class="input-sm form-control sDate" name="start" />
                            <span class="input-group-addon">TO</span>
                            <input placeholder="End Date" type="text" class="input-sm form-control eDate" name="end" />
                        </div>
                        <div class="pull-left col-lg-2">
                            <button class="btn btn-primary">Get Report</button>
                        </div>
                        </form>
                        <?php if( isset($itemReport) && count($itemReport) > 0 ) { ?>
                            <div class="col-lg-2">
                                <button  id="downloadCsv" class="btn btn-info m-b-none m-t-none">
                                        <i class="fa fa-download"></i>
                                        Download as CSV
                                </button>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php if( isset($itemReport) && count($itemReport) > 0 ) { ?>
                    <table class="table table-hover table-responsive m-t-lg">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Supplier</th>
                                <th>Total Points</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($itemReport as $item){ ?>
                            <tr>
                                <td><?=$item['item_name'] ?></td>
                                <td>
                                    <?=$item['suppliers'] ?>
                                </td>
                                <td><?=$item['total_points'] ?></td>
                            </tr>
                        <?php } //endForeach for itemReport ?>
                        </tbody>
                    </table>
                <?php }else{ ?>
                    <div class="alert alert-info m-t-lg text-center">No Data Found. Use different parameters.</div>
                <?php } ?>
            </div>
        </section>
    </div>
</div>


<script type="text/javascript">
    var dateRange = $('#daterange');
    var sDate = '<?=$input['dateFrom']; ?>';
    var eDate = '<?=$input['dateTo'];?>';
    var selectedStatus = '<?=$input['status']?>';
    var file = '<?=(!empty($input['file'])) ? $input['file'] : null?>';

</script>