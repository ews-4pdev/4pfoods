                <div class="margin">
                    <div class="row">
                        <div class="col-lg-12">
                            <section class="panel light">
                            	<div class="wrapper">
	                            	<div class="heading m-b">Payment ID <?= $oPayment->getId(); ?> - <?= money($oPayment->getAmountPaid()); ?></div>
	                            	<p><strong>Customer</strong> <a href="/admin/customers/<?= $oUser->getId(); ?>"><?= $oUser->getFirstName().' '.$oUser->getLastName(); ?></a></p>
	                            	<p><strong>Stripe ID</strong> <?= $oPayment->getStripeChargeId(); ?></p>
	                            	<p><strong>Email</strong> <?= $oUser->getEmail(); ?></p>
		                            <p><strong>Phone</strong> <?= $oUser->getPhone(); ?></p>
		                            
		                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#refund" >Issue refund</button>
                            	</div><!-- /.wrapper -->      
                            </section><!-- /.panel -->
                            
                            <section class="panel">
                            	<div class="title m-l">Order Details</div>
                                <table class="table table-hover table-responsive b-b">
                                    <thead>
                                        <tr>
                                            <th>Order</th>
                                            <th>Subscription</th>
                                            <th>Status</th>
                                            <th>Site</th>
                                            <th>Price</th>
                                            <!--Revision Start By EWS ------ Added tax heading-->
                                            <th>Tax</th>
                                            <th>Total Price</th>
                                            <th>Delivery Date</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    <?php foreach ($oPayment->getOrders() as $oOrder) : ?>
                                        <tr>
                                        	<td>#<?= $oOrder->getId(); ?></td>
                                            <td><a href="/admin/products/<?= $oOrder->getSubscription()->getProductId(); ?>"><?= $oOrder->getProductName(); ?></a></td>
                                            <td><?= $oOrder->getStatus(); ?></td>
                                            <td><?= $oOrder->getAddress(); ?></td>
                                            <td><?= money($oOrder->getPrice()); ?></td>
                                            <td><?=$oPayment->getTax(); ?></td>
                                             <td><?= money($oPayment->getAmountPaid()); ?></td>
                                            <td><?= $oOrder->getDeliveryScheduledFor('M j, Y'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                
                            </section><!-- /.panel -->
                            
                          <?php if ($oPayment->countRefunds() > 0) : ?>
                            <section class="panel">
                            	<div class="title m-l">Refund Overview</div>
                                <table class="table table-hover table-responsive b-b">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Refund Amount</th>
                                            <th>Stripe ID</th>
                                            <th>Date processed</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    <?php foreach ($oPayment->getRefunds() as $oRefund) : ?>
                                        <tr>
                                        	<td>#<?= $oRefund->getId(); ?></td>
                                            <td><?= money($oRefund->getAmount()); ?></td>
                                            <td><?= $oRefund->getStripeTxnId(); ?></td>
                                            <td><?= $oRefund->getCreatedAt('M d, Y'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                
                            </section><!-- /.panel -->
                          <?php endif; ?>
                        </div><!-- /.fullwidth -->
                    </div><!-- /.row -->
                </div><!-- /.margin -->
                
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
						            	<label for="Amount">Enter a refund amount (in dollars).</label>
						            
										<h4>Maximum refund amount - <?= money($oPayment->getRefundableAmount()); ?></h4>
						            
										<input class="form-control input-lg m-b" name="Amount" id="Amount" type="text">
						            
										<p class="help-block">This can be a full or partial refund.</p>
									</div>
						
									<button class="btn btn-default" type="button" id="submitRefund" >Refund now</button>
								</form><!-- /. end refund form -->
							</div><!-- /.modal-body-->
						</div><!-- /. modal-content -->
					</div><!-- /. modal dialog -->
				</div><!-- /. modal -->
