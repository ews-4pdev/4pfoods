
  <script>
  $(function() {
    $( "#fromdate" ).datepicker({
        format : 'mm-dd-yyyy'
    });
	$( "#todate" ).datepicker({
        format : 'mm-dd-yyyy'
    });
  });
  </script>

                <div class="margin">
                    <div class="row">
                        <div class="col-lg-12">
                            <section>
                                <!--------------------------------- P.O Report Helper ------------------------>
                                <div class="col-sm-6">
                                    <div class="panel padding">

                                            <h4>P.O Helper Report</h4>
                                            <p>Create CSV file based on order status.</p>
                                        <form method="post" action="/admin/preport">
                                            <div class="row">
<!--                                                <div class="col-sm-6">-->
<!--                                                    <div class="form-group m-t">-->
<!--                                                        <label for="status">Select Order Status</label>-->
<!--                                                        <select  class="form-control" name="status" id="status">-->
<!--                                                            <option selected="selected">All</option>-->
<!--                                                            --><?php //foreach ($orderStatus as $key => $value) { ?>
<!--                                                                <option  value="--><?//=$value ?><!--">--><?//=$value?><!--</option>-->
<!--                                                            --><?php //   }  ?>
<!--                                                        </select>-->
<!--                                                    </div><!-- /.form-group -->
<!--                                                </div>-->
                                                <div class="col-sm-12">
                                                    <div class="input-group" id="datepicker">
                                                        <span class="input-group-addon">From</span>
                                                        <input placeholder="Start Date" type="text" class="date input-sm form-control sDate" name="start" />
                                                        <span class="input-group-addon">TO</span>
                                                        <input placeholder="End Date" type="text" class="date input-sm form-control eDate" name="end" />
                                                    </div>
                                                </div>
                                            </div><!-- /.row -->
                                            <ul>
                                                <li><button  type="submit" class="btn btn-default">Download</li>
                                            </ul>
                                        </form>
                                    </div>
                                </div>
                                <!----------------------------------- END P.O Report Helper ------------------->

                                <!--------------------------------- Delivery Manifest  ------------------------>
                                <div class="col-sm-6">
                                    <div class="panel padding">

                                        <h4>Delivery Manifest Report</h4>
                                        <p class="m-b-sm">Create Delivery Manifest Report of Pending Orders.</p>
                                        <form method="post" action="/admin/deliveryManifest">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="input-group" id="datepicker">
                                                        <span class="input-group-addon">From</span>
                                                        <input placeholder="Start Date" type="text" class="date input-sm form-control sDate" name="start" />
                                                        <span class="input-group-addon">TO</span>
                                                        <input placeholder="End Date" type="text" class="date input-sm form-control eDate" name="end" />
                                                    </div>
                                                </div>
                                            </div><!-- /.row -->
                                            <ul>
                                                <li><button  type="submit" class="btn btn-default">Download</li>
                                            </ul>
                                        </form>
                                    </div>
                                </div>
                                <!----------------------------------- END Delivery Manifest ------------------->

                                <!--------------------------------- Changed Bags Report ------------------------>
<!--                                <div class="col-sm-6">-->
<!--                                    <div class="panel padding">-->
<!---->
<!--                                        <h4>Changed Bags</h4>-->
<!--                                        <p>Create CSV file of those customers who have changed their bags.</p>-->
<!--                                        <form class="m-t-md" method="post" action="/admin/changedBag">-->
<!--                                            <div class="row">-->
<!--                                                <div class="col-lg-6">-->
<!--                                                    <div class="input-group date">-->
<!--                                                        <input name="date" id="date" placeholder="Select Date" style="background-color: whitesmoke;" type="text"-->
<!--                                                               class="form-control"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div><!-- /.row -->
<!--                                            <div class="row">-->
<!--                                                <div class="col-lg-2">-->
<!--                                                    <button type="submit"  class="btn btn-default">-->
<!--                                                        Download-->
<!--                                                    </button>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </form>-->
<!--                                    </div>-->
<!--                                </div>-->
                                <!----------------------------------- END Changed Bags Report ------------------->

                              <div class="col-sm-6">
                                
                                <form method="post" action="/admin/generatereport">
                                <div class="panel padding">
                                  <h4>Order Export</h4>
                                  <p>Generate a list of orders with customer data attached. Filters apply to information about specific orders.</p>

                                  <div class="row">
                                    <div class="col-sm-6">
                                      <div class="form-group m-t">
                                        <label for="type">Select a Report Type</label>
              											    <select class="form-control" id="orderType" name="type">
                                          <option value="<?= StatHelper::TYPE_ALL_ORDERS; ?>">All Orders Ever</option>
                                          <option value="<?= StatHelper::TYPE_QUEUED_ORDERS; ?>">Queued Orders (next 48 hours)</option>
                                          <option value="<?= StatHelper::TYPE_ORDER_FORECAST; ?>">Order Forecast (7 day)</option>
              													</select>
                                      </div><!-- /.form-group -->
                                    </div>
                                  </div><!-- /.row -->

                                  <div class="row">
                                    <div class="col-sm-6">
                                      <div class="form-group m-b">
                                        <label for="SiteId">Select a Delivery Site</label>
              											    <select class="form-control" id="orderSiteId" name="SiteId">
                                          <option value="All">All</option>
                                        <?php foreach ($aSites as $oSite) : ?>
                                          <option value="<?= $oSite->getId(); ?>"><?= $oSite->getNickname(); ?></option>
                                        <?php endforeach; ?>
              													</select>
                                      </div><!-- /.form-group -->
                                    </div>
                                  </div><!-- /.row -->
                                  
                                  <div class="row">
                                    <div class="col-sm-6">
                                      <div class="form-group m-b">
                                        <label for="ProductId">Select Product Type</label>
              											    <select class="form-control" id="orderProductId" name="ProductId">
                                          <option value="All">All</option>
                                        <?php foreach ($aProducts as $oProduct) : ?>
                                          <option value="<?= $oProduct->getId(); ?>"><?= $oProduct->getTitle(); ?></option>
                                        <?php endforeach; ?>
              													</select>
                                      </div><!-- /.form-group -->
                                    </div>
                                  </div><!-- /.row -->

                                  <div class="row">
                                    <div class="col-sm-6">
                                      <div class="form-group m-b">
                                        <label for="Allergies">Choose Allergy Filter</label>
              											    <select class="form-control" id="orderAllergies" name="Allergies">
                                          <option value="All">All</option>
                                          <option value="Has">Has Allergies</option>
                                          <option value="No">Does Not Have Allergies</option>
              													</select>
                                      </div><!-- /.form-group -->
                                    </div>
                                  </div><!-- /.row -->

                                  <ul>
                                    <li><button type="submit" class="btn btn-default">Download</button></li>
                                  </ul>
                                  
                                </div>
                                  <input type="hidden" name="report" value="<?= StatHelper::REPORT_ORDER; ?>" />
                                </form>
                              </div>
                             <div class="col-sm-6">
                              <form method="post" action="/admin/generatesaletaxreport">
                                <div class="panel padding">
                                  <h4>Sale Tax Report</h4>
                                  <p>Generate a sale tax report against orders delivered and payments made.</p> 
<!--                                  <div class="row">-->
<!--                                    <div class="col-sm-6">-->
<!--                                      <div class="form-group m-t">-->
<!--                                      <table cellpadding="3" ><tr><td>-->
<!--                                        From Date: <input type="text" id="fromdate" name="fromdate" style="width:100px"></td><td> -->
<!--                                        To Date: <input type="text" id="todate" name="todate" style="width:100px"></td></tr></table>-->
<!--                                      </div><!-- /.form-group -->
<!--                                    </div>-->
<!--                                  </div><!-- /.row -->
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="input-group" id="datepicker">
                                                <span class="input-group-addon">From</span>
                                                <input placeholder="Start Date" type="text" class="date input-sm form-control sDate" name="fromdate" />
                                                <span class="input-group-addon">TO</span>
                                                <input placeholder="End Date" type="text" class="date input-sm form-control eDate" name="todate" />
                                            </div>
                                        </div>
                                    </div><!-- /.row -->
                                  <ul>
                                    <li><button type="submit" class="btn btn-default">Download</li>
                                  </ul>
                                </div>
                              </form>
                              </div>
                              <!--------------------------------- SALE TAX REPORT ------------------------>
                              <div class="col-sm-6">
                                <form method="post" action="/admin/generatereport">
                                    <div class="panel padding">
                                  <h4>Customer Export</h4>
                                  <p>Generate a list of customers, including basic information as well as detailed, graphable information about all past orders for each customer. Filters apply to information specific to each customer.</p>
                                  
                                  <div class="row">
                                    <div class="col-sm-6">
                                      <div class="form-group m-t">
                                        <label for="customerFilter">Select a customer filter</label>
              											    <select class="form-control" id="customerFilter" name="status">
            													    <option value="All">All</option>
                                        <?php foreach ($statuses as $status) : ?>
            													    <option value="<?= $status; ?>"><?= $status; ?></option>
                                        <?php endforeach; ?>
              													</select>
                                      </div><!-- /.form-group -->
                                    </div>
                                  </div><!-- /.row -->
                                  <ul>
                                    <li><button type="submit" class="btn btn-default">Download</li>
                                  </ul>
                                </div>
                                    <input type="hidden" name="type" value="<?= StatHelper::TYPE_STATUS; ?>" />
                                    <input type="hidden" name="report" value="<?= StatHelper::REPORT_CUSTOMER; ?>" />
                                </form>
                              </div>
                              <!----------------------------------- END SALE TAX REPORT ------------------->

                                <!--------------------------------- Delivery Report For Drivers ------------------------>
                                                                <div class="col-sm-6">
                                                                    <div class="panel padding">

                                                                        <h4>Delivery Report For Drivers</h4>
                                                                        <p>Create CSV file of Driver to know number of bags.</p>
                                                                        <form class="m-t-md" method="post" action="/admin/driverManifest">
                                                                            <div class="row">
                                                                                <div class="col-lg-6">
                                                                                    <div class="input-group date">
                                                                                        <input name="date" id="date" placeholder="Select Date" style="background-color: whitesmoke;" type="text"
                                                                                               class="form-control"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-lg-2">
                                                                                    <button type="submit"  class="btn btn-default">
                                                                                        Download
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                <!----------------------------------- END Delivery Report ------------------->



                            </section><!-- /.panel -->
                        </div><!-- /.fullwidth -->
                    </div><!-- /.row -->
                </div><!-- /.margin -->
  <script type="text/javascript">
        $('.date').datepicker({
            format : 'mm-dd-yyyy'
        });
  </script>

