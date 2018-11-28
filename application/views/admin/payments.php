<?php ini_set('memory_limit', '512M'); ?>
<div class="margin">
                    <div class="row">
                        <div class="col-lg-12">
                            <section class="panel">
                                <table id="paymentTable" class="table table-hover table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Payment ID</th>
                                            <th>Customer</th>
                                            <th>Amount Paid</th>
                                            <th>Tax Paid</th>
                                            <th>Delivery Charge</th>
                                            <th>Amount Refunded</th>
                                            <th>Payment Status</th>
                                            <th>Processed</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    </tbody>
                                </table>
                                <div class="padding">Amount paid is inclusive of tax.</div>
								
								<!-- Modal -->
								<div class="modal fade" id="refund" tabindex="-1" role="dialog" aria-labelledby="refundModal" aria-hidden="true">
									<div class="modal-dialog">
								    	<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
												<h4 class="modal-title" id="myModalLabel">Issue a full or partial refund</h4>
											</div>
											<div class="modal-body">
								      
												<form id="createRefund">
										        	<div class="form-group">
										            	<label for="IssueRefund">Enter a refund amount (in dollars). Maximum allowed: $<span id="maxAmount"></span></label>
										            
														<input class="form-control input-lg m-b" name="IssueRefund" id="createIssueRefund" type="text">
										            
														<p class="help-block">This can be a full or partial refund.</p>
													</div>
										
													<button class="btn btn-default" type="submit" id="submitRefund" >Refund now</button>
												</form><!-- /. end refund form -->
											</div><!-- /.modal-body-->
										</div><!-- /. modal-content -->
									</div><!-- /. modal dialog -->
								</div><!-- /. modal -->
                                    
                            </section><!-- /.panel -->
                        </div><!-- /.fullwidth -->
                    </div><!-- /.row -->
                </div><!-- /.margin -->
                <script type="text/javascript">
                    $('#paymentTable').DataTable({
                        'info': false,
                        "search": {
                            "regex": true,
                            "bSmart": false,
                            "bRegex": true
                        },
                        "data": <?=json_encode($aPayments); ?>,

                        "columns": [
                            {
                                data: null,
                                defaultContent: '<a href="" ></a>'
                            },
                            {
                                data: null,
                                defaultContent: '<a href="" ></a>'
                            },
                            {"data": "Amount_Paid"},
                            { "data": "Tax_Paid" },
                            { "data": "Deliver_Charge" },
                            { "data": "Amount_Refunded" },
                            { "data": "Payment_Status" },
                            {"data": "Processed" },
                            {
                                data: null,
                                defaultContent: '<a href="#" class="label bg-danger refundLink" data-toggle="modal" data-target="#refund" data-info="" >Refund</a>'
                            }

                        ],

                        "createdRow": function (row, data, dataIndex) {

                            $(row).find('td').eq(0).find('a').prop('text', data.Payment_ID);
                            $(row).find('td').eq(0).find('a').prop('href', '/admin/payments/'+data.Payment_ID);

                            $(row).find('td').eq(1).find('a').prop('text', data.Customer);
                            $(row).find('td').eq(1).find('a').prop('href', '/admin/customers/'+data.Customer_ID);

                            $(row).find('td').eq(7).find('a').prop('data-info', data.Refund);
                        }

                    });
                </script>
