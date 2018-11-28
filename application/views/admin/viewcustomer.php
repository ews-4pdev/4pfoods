               <?php $tax = 0; ?>

                <div class="margin">
                    <div class="row">
                        <div class="col-lg-12">
                            <section class="panel light">
                            	<div class="wrapper">
                            		<p class="pull-right">Joined <?= $oUser->getCreatedAt('M j, Y'); ?></p>
	                            	<div class="heading m-b"><?= $oUser->getFirstName().' '.$oUser->getLastName(); ?></div>
	                            	<p><strong>Email</strong> <?= $oUser->getEmail(); ?></p>
	                            	<p><strong>Phone</strong> <?= ( $oUser->getPhone() ); ?></p>
									<p><strong>Stripe ID</strong> <?= $oUser->getStripeId(); ?></p>
									<p><strong>Dietary Restrictions:</strong> <?= $oUser->getDietaryRestrictions(); ?></p>
                                <?php if( !$oUser->getDoorStep() ){ ?>
		                            <p><strong>Default Delivery Site</strong> <?= $oUser->getSiteNickname(); ?> <a href="#"  class="label bg-success white" data-toggle="modal" data-target="#modalModify<?= $oUser->getId(); ?>">Change Site</a></p>
                                <?php } ?>
                                    <?php if( $oUser->getDoorStep() ){ ?>

										<p><strong>DoorStep - </strong>YES</p>
										<p><strong>Address</strong> - <?=$oUser->getFullAddress()?></p>
										<p><strong>City</strong> - <?=$oUser->getCity()?></p>
										<p><strong>State</strong> - <?=$oUser->getState()->getName()?></p>
										<p><strong>Default Zip</strong> <?= $oUser->getZip(); ?>
										<?php if( !$orderExist ){ ?>
										<a href="#"
										   class="label bg-success"
										   data-toggle="modal"
										   data-target="#modalDoorStepModify<?= $oUser->getId(); ?>">
											Change Zip
											</a>
										<?php } ?>
										</p>
                                        <p><strong>Cell Number </strong><?=$oUser->getMobile().' / '.$oUser->getAMobile(); ?></p>
                                        <p><strong>Building Type </strong><?=$oUser->getBuildingTypesTags() ?></p>
                                        <p><strong>Concierge </strong><?=( $oUser->getConcierge() ) ? 'Yes' : 'No' ?></p>
                                        <p><strong>Concierge Number </strong><?=$oUser->getConciergeNumber() ?></p>
                                        <p><strong>Key Fob </strong><?=$oUser->getKeyFob() ?></p>
                                        <p><strong>Parking </strong><?=$oUser->getParking() ?></p>
                                    <?php  } ?>
                              <?php if (!$oUser->isConfirmed()) : ?>
                                <p><strong>Confirm Link:</strong> <?= $oUser->getConfirmLink(); ?></p>
                              <?php endif; ?>

		                            <div class="title m-t m-b">Security</div>
									<button type="button" class="btn btn-default" id="resetPassword">Reset password</button>
									<a href="/admin/loginto/<?= $oUser->getId(); ?>" target="_blank"><button type="button" class="btn btn-default" id="loginAs">Login to account</button></a>
									<?php  if( !$orderExist ){ ?>

										<button
											class="btn btn-primary"
											data-toggle="modal"
											data-target="<?=($oUser->getDoorstep()) ?  '#modalPickup'.$oUser->getId() : '#modalDoorStep'.$oUser->getId(); ?>">
											<?=($oUser->getDoorstep()) ? 'Convert To Pickup' : 'Convert To Door Step' ?>
										</button>
									<?php }else{ ?>
										<div class="btn btn-warning" onclick="alertify.alert('Status can only be changed if orders are not created. Try after orders are delivered');">
											Can't Change Status
										</div>
									<?php } ?>
                            	</div><!-- /.wrapper -->      
                            </section><!-- /.panel -->
                            
                            <section class="panel">
                            	<!-- Nav tabs -->

                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#customerOrders">Orders</a></li>

									<li><a data-toggle="tab" href="#customerUnpaidOrders">Unpaid Orders</a></li>

                                    <li><a data-toggle="tab" href="#subscriptionHistory">Subscription history</a></li>
                                    
                                    <li><a data-toggle="tab" href="#payments">Payments</a></li>
                                </ul>

                                <div class="tab-content">

                                    <div class="tab-pane active" id="customerOrders">
                                        <table class="applyDataTable table table-hover table-responsive">
		                                    <thead>
		                                        <tr>
												<th>Order #</th>
												<th>Payment <br />(Issue charge?)</th>
                                                <th>Price</th>
                                                <th>Delivery Date</th>
												<th>Fulfillment</th>
												<th>Product</th>
		                                        </tr>
		                                    </thead>
		
		                                    <tbody>
                                            
                                        <?php foreach ($aOrders as $oOrder) : ?>
		                                        <tr>
		                                            <td>#<?= $oOrder->getId(); ?></td>
                                              <?php if ($oOrder->getStatus() == 'Skipped') : ?>
                                                <td>Skipped</td>
                                              <?php elseif ($oOrder->isPaid()) : ?>
		                                            <td class="font-danver">Paid</td>
                                              <?php endif; ?>
                                                <td><?= money($oOrder->getPrice()); ?></td>
                                                <td><?= $oOrder->getDeliveryScheduledFor('n/d/Y'); ?></td>
												<td><?= $oOrder->getVerboseStatus(); ?></td>
												<td><?= $oOrder->getProductName(); ?></td>
		                                        </tr>
                                        <?php endforeach; ?>
		                                    </tbody>
		                                </table>
		                                
		                                <a href="#" class="btn btn-default btn-lg m-l m-t" data-toggle="modal" data-target="#issueCharge">Issue Charge</a>

                                        
                                    </div><!-- End tab pane -->

									<div class="tab-pane" id="customerUnpaidOrders">
										<table id="unpaid" class="table table-hover table-responsive">
											<thead>
											<tr>
												<th>Delivery Date</th>
												<th></th>
												<th></th>
											</tr>
											</thead>

											<tbody>

											<?php foreach ( $unPaidOrders as $oOrderByDate ) : $ids = null; ?>
												<?php $oUser = $oOrderByDate[0]->getSubscription()->getUser(); ?>
												<tr>
													<td width="130px" style="vertical-align: middle"><?= $oOrderByDate[0]->getDeliveryScheduledFor('d F, Y'); ?></td>
													<?php if( count( $oOrderByDate ) > 0 ) : ?>
														<td >
															<table class="table">
																<thead>
																<tr style="border-bottom: 1px solid black">
																	<th>Order ID</th>
																	<th>Price</th>
																	<th>Fulfillment</th>
																	<th>Product</th>
																</tr>
																</thead>
																<tbody>
																<?php $ids = []; $totalPrice = 0;  ?>
																<?php foreach ( $oOrderByDate as $order ) : $ids[] = $order->getId(); ?>
																	<?php $totalPrice += $order->getPrice();  ?>
																	<tr>
																		<td>#<?=$order->getId() ?></td>
																		<td><?=money($order->getPrice()) ?></td>
																		<td><?=$order->getVerboseStatus() ?></td>
																		<td><?=$order->getProductName() ?></td>
																	</tr>
																<?php endforeach; ?>
																	<tr>
																		<td>
																			<ul>
																				<li class="text-right">Tax</li>
																				<li class="text-right">Charge</li>
																				<li class="text-right">Total</li>
																			</ul>
																		</td>
																		<td>
																			<ul>
																				<?php $tax = calculateTax( ( $oUser->getDeliveryCharge() + $totalPrice ),  $oUser->getStateTax() ); ?>
																				<li class="text-left"><?= money( $tax ) ?> </li>
																				<li class="text-left"><?= money( $oUser->getDeliveryCharge() ) ?> </li>
																				<li style="border-top: solid 1px black;" class=" text-left"><?=money( $totalPrice + $tax + $oUser->getDeliveryCharge() ) ;?></li>
																			</ul>
																		</td>
																	</tr>
																</tbody>
															</table>
														</td>
													<?php endif; ?>
													<td style="vertical-align: middle">
														<a  href="#" class="pay-on-demand btn btn-primary" data-id="<?= implode(',', $ids) ?>">Pay Now</a>
													</td>
												</tr>
											<?php endforeach; ?>
											</tbody>
										</table>

										<a href="#" class="btn btn-default btn-lg m-l m-t" data-toggle="modal" data-target="#issueCharge">Issue Charge</a>


									</div><!-- End tab pane -->

                                    <div class="tab-pane" id="subscriptionHistory">
                                        <table class="applyDataTable table table-hover table-responsive">
		                                    <thead>
		                                        <tr>
		                                            <th>Subscription</th>
		                                            <th>Next delivery</th>
		                                            <th>Status</th>
		                                            <th>Sign up date</th>
		                                        </tr>
		                                    </thead>
		
		                                    <tbody>
                                          <?php foreach ($aSubs as $oSub) : ?>
		                                        <tr>
		                                            <td width="250px"><?= $oSub->getProduct()->getTitle(); ?></td>
		                                            <td><?= $oSub->getNextDeliveryDate('M j, Y'); ?></td>
		                                            <td><?= $oSub->getStatus(); ?></td>
		                                            <td><?= $oSub->getCreatedAt('M j, Y'); ?></td>
		                                        </tr>
                                          <?php endforeach; ?>
		                                    </tbody>
		                                </table>
                                    </div><!-- End tab pane -->

									<div class="tab-pane" id="payments">
	                                    
	                                    	<table class="applyDataTable table table-hover table-responsive">
			                                    <thead>
			                                        <tr> 
			                                            <th>Payment ID</th>
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
                                          <?php 
										  	
										  foreach ($oUser->getPayments() as $oPayment) :
										  ?>
			                                        <tr>
			                                            <td width="100px"><a href="/admin/payments/<?= $oPayment->getId(); ?>">#<?= $oPayment->getId(); ?></a></td>
														<td><?= money($oPayment->getAmountPaid()); ?></td>
														<td><?= money($oPayment->getTax()); ?></td>
														<td><?=money( $oPayment->getDeliverycharge() ) ?></td>
                                                        <td><?= money($oPayment->getRefundedTotal());?></td>
			                                            <td><?= $oPayment->getStatus(); ?></td>
			                                            <td><?= $oPayment->getCreatedAt('M d, Y'); ?></td>
			                                            <td><a href="#" class="label bg-danger refundLink" data-toggle="modal" data-target="#refund" rel="<?= $oPayment->getId(); ?>" data-info='<?= json_encode(array('iPayment' => $oPayment->getId(), 'maxAmount' => $oPayment->getRefundableAmount())); ?>'>Refund</a></td>
			                                        </tr>
                                          <?php endforeach; ?>
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

									  </div><!-- End tab pane -->

                                </div><!-- End tabbed content -->
							</section><!-- /.panel -->
                        </div><!-- /.fullwidth -->
                    </div><!-- /.row -->

                    <!-- Modal for issuing charge -->
				<div class="modal fade" id="issueCharge" tabindex="-1" role="dialog" aria-labelledby="chargeModal" aria-hidden="true">
    									<div class="modal-dialog">
    								    	<div class="modal-content">
      											<div class="modal-header">
      												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      												<h4 class="modal-title" id="myModalLabel">Issue charge</h4>
      											</div>
      											<div class="modal-body text-center">
      								      
      												  <h4>Total - <?= "TOTAL"; ?></h4>
      												  <p>Are you sure you want to issue the charge?</p>
      													</div>
      										
      													<button class="btn btn-default center-block m-b-lg" type="submit" id="submitCharge" >Issue now</button>
      											</div><!-- /.modal-body-->
    										</div><!-- /. modal-content -->
    									</div><!-- /. modal dialog -->
<!--    								</div><!-- /. modal -->
                    
                    <!-- Modal for changing site-->
				<div class="modal fade" id="modalModify<?= $oUser->getId(); ?>" tabindex="-1" role="dialog" aria-labelledby="Approve user" aria-hidden="true">
								  <div class="modal-dialog">
								    <div class="modal-content">
								    <div class="modal-header">
								        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								        <div class="title">Modify Site</div>
								    </div><!-- End modal header -->
								    	<div class="wrapper margin">
								    			<form id="modifySite">
								    			<label for="Site">Select a site</label>
											         <select class="form-control m-b assignSelect" name="Site" id="updateSite" rel="<?= $oUser->getId(); ?>">
						                                 <option value="">Choose Here to Assign</option>
						                               <?php foreach ($aSites as $id => $nickname) : ?>
						                                 <option value="<?= $id; ?>"><?= $nickname; ?></option>
						                               <?php endforeach; ?>
													</select>
													<button class="btn btn-default" type="button" id="submitModifySite">Update</button>
											</form><!-- / end form -->
										</div><!-- /.wrapper -->
								    </div><!-- /.modal-content -->
								  </div><!-- /.modal-dialog -->
								</div><!-- /.modal -->

			   <div class="modal fade" id="modalPickup<?= $oUser->getId(); ?>" tabindex="-1" role="dialog" aria-labelledby="PickUp user" aria-hidden="true">
				   <div class="modal-dialog">
					   <div class="modal-content">
						   <div class="modal-header">
							   <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							   <div class="title">Convert To Pick Up</div>
						   </div><!-- End modal header -->
						   <div class="wrapper margin">
							   <form id="modifyPickUpSite">
								   <label for="Site">Select a site</label>
								   <select class="form-control m-b assignSelect" name="Site" id="updatePickUpSite" rel="<?= $oUser->getId(); ?>">
									   <option value="">Choose Here to Assign</option>
									   <?php foreach ($aSites as $id => $nickname) : ?>
										   <option value="<?= $id; ?>"><?= $nickname; ?></option>
									   <?php endforeach; ?>
								   </select>
								   <button class="btn btn-default" type="button" id="submitPickUpSite">Update</button>
							   </form><!-- / end form -->
						   </div><!-- /.wrapper -->
					   </div><!-- /.modal-content -->
				   </div><!-- /.modal-dialog -->
			   </div><!-- /.modal -->


			   <div class="modal fade" id="modalDoorStep<?= $oUser->getId(); ?>" tabindex="-1" role="dialog" aria-labelledby="DoorStep user" aria-hidden="true">
				   <div class="modal-dialog">
					   <div class="modal-content">
						   <div class="modal-header">
							   <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							   <div class="title">Convert To Pick Up Location</div>
						   </div><!-- End modal header -->
						   <div class="wrapper margin">
							   <form id="modifySite">
								   <label for="Site">Select a Zip</label>
								   <select class="form-control m-b assignSelect" name="Site" id="updateDoorStepSite" rel="<?= $oUser->getId(); ?>">
									   <option value="">Choose Here to Assign</option>
									   <?php foreach ($aDoorSteps as $id => $zip) : ?>
										   <option value="<?= $id; ?>"><?= $zip; ?></option>
									   <?php endforeach; ?>
								   </select>

								   <div class="form-group">
									   <label for="Address1">Address1</label> <input class="form-control"
																					id="cAddress1"
																					name="Address1"
																					type="text">

									   <div class="error-notify"></div>
								   </div><!-- End group -->
								   <div class="form-group">
									   <label for="Address2">Address 2</label> <input class="form-control"
																					  id="cAddress2"
																					  name="Address2"
																					  type="text">

								   <div class="form-group doorstep m-t-md">
									   <div>Is this building a:</div>

									   <div class="col-lg-6">
										   <div class="checkbox ">
											   <label for="SingleFamilyHome">
												   <input id="SingleFamilyHome" class="BuildingType" name="SingleFamilyHome" value=""
														  type="checkbox">Single Family Home
											   </label>
										   </div>
										   <div class="checkbox">
											   <label for="ApartmentCondo">
												   <input id="ApartmentCondo" class="BuildingType" name="ApartmentCondo" value=""
														  type="checkbox">Apartment/Condo
											   </label>
										   </div>
									   </div>

									   <div class="col-lg-6">
										   <div class="checkbox">
											   <label for="OfficeBuilding">
												   <input id="OfficeBuilding" class="BuildingType" name="OfficeBuilding" value=""
														  type="checkbox">Office Building
											   </label>
										   </div>

										   <div class="checkbox">
											   <label for="TownHouse">
												   <input id="TownHouse" class="BuildingType" name="TownHouse" value=""
														  type="checkbox">TownHouse
											   </label>
										   </div>
									   </div>
								   </div>
								   <div style="clear: both; height: 1px;"></div>

								   <div class=" m-t-md doorstep">
									   <div class="form-group">
										   <div class="checkbox">
											   <label for="concierge">Is there a concierge we can leave the
												   package with?</label>
											   <input class="" placeholder="Concierge"
													  id="concierge" name="Concierge" type="checkbox">
										   </div>
									   </div>
								   </div>

								   <div class="m-t-md doorstep">
									   <div class="form-group">
										   <label for="concierge_number">Enter Concierge Number</label>
										   <input class="form-control" placeholder="Concierge Number"
												  id="concierge_number" name="ConciergeNumber" type="text">
									   </div>
								   </div>

								   <div class=" m-t-md doorstep">
									   <div class="form-group">
										   <div>If a key fob is needed to access the
											   building, can you provide us with a fob?</div>
										   <div class="pull-left m-r-sm">
											   <label for="KeyFob">Yes</label>
											   <input id="KeyFob" name="KeyFob" data-value="true"
													  type="radio">
										   </div>

										   <div>
											   <label for="KeyFob1">No</label>
											   <input  id="KeyFob1" name="KeyFob" data-value="false"
													  type="radio">
										   </div>


									   </div>
								   </div>

								   <div class=" m-t-md doorstep">
									   <div class="form-group">
										   <label for="Parking">Where can we easily park while making the
											   delivery?</label>
                                                        <textarea class="form-control" id="Parking" name="Parking"
																  rows="3"></textarea>

										   <div class="error-notify"></div>
									   </div><!-- End group -->
								   </div>
								   <button class="btn btn-default" type="button" id="submitDoorStepSite">Update</button>
							   </form><!-- / end form -->
						   </div><!-- /.wrapper -->
					   </div><!-- /.modal-content -->
				   </div><!-- /.modal-dialog -->
			   </div><!-- /.modal -->

                </div><!-- /.margin -->

			   <div class="modal fade" id="modalDoorStepModify<?= $oUser->getId(); ?>" tabindex="-1" role="dialog" aria-labelledby="DoorStep User Modify" aria-hidden="true">
				   <div class="modal-dialog">
					   <div class="modal-content">
						   <div class="modal-header">
							   <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							   <div class="title">Modify Site</div>
						   </div><!-- End modal header -->
						   <div class="wrapper margin">
							   <form id="modifyDoorStepSite">
								   <label for="updateZip">Select a Zip</label>
								   <select class="form-control m-b" name="Zip" id="updateZip" rel="<?= $oUser->getId(); ?>">
									   <option value="">Choose Here to Assign</option>
									   <?php foreach ($aDoorSteps as $id => $zip) : ?>
										   <option value="<?= $id; ?>"><?= $zip; ?></option>
									   <?php endforeach; ?>
								   </select>
								   <button class="btn btn-default" type="button" id="submitDoorStepModifySite">Update</button>
							   </form><!-- / end form -->
						   </div><!-- /.wrapper -->
					   </div><!-- /.modal-content -->
				   </div><!-- /.modal-dialog -->
			   </div><!-- /.modal -->

				<script type="text/javascript">
					$('.applyDataTable').DataTable({
						"bInfo": false
					});

					$('#unpaid').DataTable({
						"bInfo": false,
						"columnDefs": [
							{"orderable": false, "targets": 1},
							{"orderable": false, "targets": 2}
						]
					});

				</script>
