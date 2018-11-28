                <div class="margin">
                    <div class="row">
                        <div class="col-lg-12">
                            <section class="panel">
                                <!-- Nav tabs -->

                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#sites">Sites</a></li>
                                  <?php if (!$isDriver) : ?>
                                    <li><a data-toggle="tab" href="#drivers">Drivers</a></li>
                                  <?php endif; ?>
                                    <li><a data-toggle="tab" href="#notifications">Notifications</a></li>
                                </ul>

                                <div class="tab-content">
                                
                                <!-- Start tab pane -->
                                
                                    <div class="tab-pane active" id="sites">
                                    	
                                    	<!-- Button trigger modal -->
										<a href="#" data-toggle="modal" data-target="#modalNewSite">
										  <img src="/images/add-admin.svg" alt="Add new site" class="pull-right margin" height="50" width="50"/>
										</a>  
										
										<!-- Modal -->
										<div class="modal fade" id="modalNewSite" tabindex="-1" role="dialog" aria-labelledby="New Site" aria-hidden="true">
										  <div class="modal-dialog">
										    <div class="modal-content">
										    <div class="modal-header">
  										      <div class="margin">
										          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                              <div class="title">Add new site</div>
  										      </div><!-- /.margin -->
										    </div><!-- End modal header -->
										    	<div class="wrapper margin">
										    		<form id="site" >
													    <div class="form-group">
													        <label for="Nickname">Site Name</label> <input class="form-control" id="siteNickname" name="Nickname" placeholder="Enter site name here" type="text">
													
													        <div class="error-notify"></div>
													    </div><!-- End group -->
													
													    <div class="form-group">
													        <label for="Address1">Address</label> <input class="form-control" id="siteAddress1" name="Address1" placeholder="Enter site address here" type="text">
													
													        <div class="error-notify"></div>
													    </div><!-- End group -->
													
													    <div class="form-group">
													        <label for="Address2">Address 2</label> <input class="form-control" id="siteAddress2" name="Address2" placeholder="Suite #201" type="text">
													
													        <div class="error-notify"></div>
													    </div><!-- End group -->
													
													    <div class="row">
													        <div class="col-sm-6">
													            <div class="form-group">
													                <label for="City">City</label> <input class="form-control" id="siteCity" name="City" placeholder="Enter city" type="text">
													
													                <div class="error-notify"></div>
													            </div><!-- End group -->
													        </div><!-- /. half -->
													
													        <div class="col-sm-6">
													            <div class="form-group">
													                <label for="StateId">State</label> 
                                            <select id="siteStateId" name="StateId" class="form-control">
                                              <?= createDropDownOptions($aStates); ?>
                                            </select>
													
													                <div class="error-notify"></div>
													            </div><!-- End group -->
													        </div><!-- /. half -->
													    </div><!-- /.row -->
													
													    <div class="row">
													        <div class="col-sm-6 col-offset-sm-6">
													            <div class="form-group">
													                <label for="Zip">Zip/Postal code</label> <input class="form-control" id="siteZip" name="Zip" placeholder="Enter zip/postal code here" type="text">
													
													                <div class="error-notify"></div>
													            </div><!-- End group -->
													        </div><!-- /. half -->
										        <div class="col-sm-6">
										            <div class="form-group">
											        <label for="DefaultDeliveryDay">Default delivery day</label>
											         <select class="form-control" id="siteDefaultDeliveryDay" name="DefaultDeliveryDay">
													    <option value="Mon">Monday</option>
													    <option value="Tue">Tuesday</option>
													    <option value="Wed">Wednesday</option>
													    <option value="Thu">Thursday</option>
													    <option value="Fri">Friday</option>
													    <option value="Sat">Saturday</option>
													    <option value="Sun">Sunday</option>
													</select>
										
										        <div class="error-notify"></div>
										    </div><!-- End group -->
										        </div><!-- /. half -->
													    </div><!-- /. row -->
													    <input class="btn btn-default" type="submit" value="Add site" id="submitNewDeliverySite" />
													</form><!-- / end form -->
										    	</div><!-- /.wrapper -->
										    </div><!-- /.modal-content -->
										  </div><!-- /.modal-dialog -->
										</div><!-- /.modal -->
                                    	
                                        <div class="clearboth">
											<table id="deliveries_table" class="table table-hover table-responsive m-t">
                                            <thead>
                                                <tr>
                                                    <th>Site #</th>
                                                    <th>Name</th>
                                                    <th>Zip code</th>
                                                    <th>Default Delivery Day</th>
                                                    <th>Deliveries?</th>
                                                    <th>Address</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                              <?php foreach ($aSites as $oSite) : ?>
                                                <tr data-info='<?= $oSite->toJSONObject(); ?>'>
                                                    <td>
                                                      #<?= $oSite->getId(); ?>
                                                      (<a href="#" alt="Update site" data-toggle="modal" data-target="#updateSite" class="editSiteLink" data-id="#update">Update</a>)
                                                      (<a href="#" alt="Hide site" class="hideSiteLink" data-id="<?= $oSite->getId(); ?>">Hide</a>)
                                                    </td>
                                                    <td><?= $oSite->getNickname(); ?></td>
                                                    <td title="Access Code - <?=$oSite->getAccessCode(); ?>"><?= $oSite->getZip(); ?></td>
                                                    <td><?= $oSite->getDefaultDeliveryDay(); ?> (<a href="#" alt="change deliver day" data-toggle="modal" data-target="#changeDeliveryDay" class="changeDeliveryDayLink" data-id="<?= $oSite->getId(); ?>">Change</a>)</td>
                                                    <td><a href="#" class="disableSite label bg-<?= ($oSite->acceptsDeliveries()) ? 'success' : 'danger'; ?>" rel="<?= $oSite->getId(); ?>"><?= $oSite->acceptsDeliveries() ? 'Active' : 'Stopped'; ?></a></td>
                                                    <td><?= $oSite->getFullAddress(); ?></td>
                                                </tr>
                                              <?php endforeach; ?>
                                            </tbody>
                                        </table>
										</div>
                                        
                                    </div><!-- End tab pane -->
                                    
                                    <!-- Start tab pane -->

                                    <div class="tab-pane" id="drivers">
                                    	
                                    	<!-- Button trigger modal -->
										<a href="#" data-toggle="modal" data-target="#modalNewDrivers">
										  <img src="/images/add-admin.svg" alt="Add new site" class="pull-right margin" height="50" width="50"/>
										</a>  
										
										<!-- Modal -->
										<div class="modal fade" id="modalNewDrivers" tabindex="-1" role="dialog" aria-labelledby="New Drivers" aria-hidden="true">
										  <div class="modal-dialog">
										    <div class="modal-content">
										    <div class="modal-header">
										        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										        <div class="title">Add new driver</div>
										    </div><!-- End modal header -->
										    	<div class="wrapper margin">
										    		<form id="addDriverForm">
										    		
										    			<div class="row">
													        <div class="col-sm-6">
													            <div class="form-group">
													                <label for="FirstName">First name</label> <input class="form-control" id="driverFirstName" name="FirstName" placeholder="Enter first name" type="text" />
													
													                <div class="error-notify"></div>
													            </div><!-- End group -->
													        </div><!-- /. half -->
													
													        <div class="col-sm-6">
													            <div class="form-group">
													                <label for="LastName">Last name</label> <input class="form-control" id="driverLastName" name="LastName" placeholder="Enter last name" type="text" />
													                <div class="error-notify"></div>
													            </div><!-- End group -->
													        </div><!-- /. half -->
													    </div><!-- /.row -->
													    
													    <div class="form-group">
													        <label for="Email">Email address</label> <input class="form-control" id="driverEmail" name="Email" placeholder="Enter email address" type="email" />
													
													        <div class="error-notify"></div>
													    </div><!-- End group -->
													
													    <div class="form-group">
													        <label for="Phone">Phone number</label> <input class="form-control" id="driverPhone" name="Phone" placeholder="Enter phone number" type="text" />
													
													        <div class="error-notify"></div>
													    </div><!-- End group -->

													    <div class="form-group">
													        <label for="Address1">Address</label> <input class="form-control" id="driverAddress1" name="Address1" placeholder="Enter address" type="text" />
													
													        <div class="error-notify"></div>
													    </div><!-- End group -->

													    <div class="form-group">
													        <label for="City">City</label> <input class="form-control" id="driverCity" name="City" placeholder="Enter city" type="text" />
													
													        <div class="error-notify"></div>
													    </div><!-- End group -->

													    <div class="form-group">
													        <label for="Zip">Zip Code</label> <input class="form-control" id="driverZip" name="Zip" placeholder="Enter zip code" type="text" />
													
													        <div class="error-notify"></div>
													    </div><!-- End group -->


													    <div class="form-group">
													        <label for="StateId">State</label>
													        	<select name="StateId" value="driverStateId" class="form-control">
																	<?= createDropDownOptions($aStates); ?>
																</select>
													
													        <div class="error-notify"></div>
													    </div><!-- End group -->

													    <div class="form-group">
													        <label for="Password">Password</label> <input class="form-control" id="driverPassword" name="Password" placeholder="Enter password" type="text" />
													
													        <div class="error-notify"></div>
													    </div><!-- End group -->
													    <button class="btn btn-default" type="submit" id="submitAddDriver">Add driver</button>
                              <input type="hidden" name="returnURL" value="/admin/deliveries" />
													</form><!-- / end form -->
										    	</div><!-- /.wrapper -->
										    </div><!-- /.modal-content -->
										  </div><!-- /.modal-dialog -->
										</div><!-- /.modal -->

										<div class="clearboth">
                                        	<table id="driver_table" class=" table table-hover table-responsive m-t">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Phone</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                            <?php foreach ($aDrivers as $oDriver) : ?>
                                                <tr>
                                                    <td><?= $oDriver->getFirstName().' '.$oDriver->getLastName(); ?></td>
                                                    <td><?= $oDriver->getEmail(); ?></td>
                                                    <td><?= $oDriver->getPhone(); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
										</div>
                                        
                                    </div><!-- End tab pane -->
                                    
                                    <!-- Start tab pane -->

                                    <div class="tab-pane" id="notifications">
                                    		<div class="title m-l">Notification history</div>
	                                        <table class="table table-hover table-responsive m-t">
	                                            <thead>
	                                                <tr>
                                                      <th>Delivery #</th>
	                                                    <th>Site #</th>
														<th>DoorStep</th>
                                                      <th>Site Title</th>
	                                                    <th>Driver</th>
                                                      <th>Status</th>
	                                                    <th>Sent</th>
	                                                </tr>
	                                            </thead>
	
	                                            <tbody>
                                              <?php foreach ($aDeliveries as $oDelivery) : ?>
	                                                <tr>
	                                                    <td>#<?= $oDelivery->getId(); ?></td>
                                                      <td>#<?= $oDelivery->getDeliverySiteId(); ?></td>
														<td><?= ( $oDelivery->getDoorstep() ) ? 'YES' : 'NO'; ?></td>
                                                      <td><?= $oDelivery->getAddress(); ?></td>
	                                                    <td><?= $oDelivery->getDeliveredByName(); ?></td>
                                                      <td><?= $oDelivery->getStatus(); ?>
	                                                    <td><?= ($oDelivery->getDeliveredAt()) ? prettyDate($oDelivery->getDeliveredAt()) : 'Failed'; ?></td>
	                                                </tr>
                                              <?php endforeach; ?>
	
	                                            </tbody>
	                                        </table>
	                                        

                                    </div><!-- End tab pane -->
                                </div><!-- End tabbed content -->
                            </section><!-- /.panel -->
                        </div><!-- /.fullwidth -->
                    </div><!-- /.row -->
                    
                    <!-- Modal -->
					<div class="modal fade" id="changeDeliveryDay" tabindex="-1" role="dialog" aria-labelledby="Update Delivery Day" aria-hidden="true">
					  <div class="modal-dialog">
					    <div class="modal-content">
					    <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					        <div class="title">Update Delivery Day</div>
					    </div><!-- End modal header -->
					    	<div class="wrapper margin">
					    		<form id="updateDay" >
						            <div class="form-group">
							        	<label for="ChangeDeliveryDay">Select the new default delivery day</label>
									         <select class="form-control" id="newDeliveryDay" name="ChangeDeliveryDay">
											    <option value="Mon">Monday</option>
											    <option value="Tue">Tuesday</option>
											    <option value="Wed">Wednesday</option>
											    <option value="Thu">Thursday</option>
											    <option value="Fri">Friday</option>
											    <option value="Sat">Saturday</option>
											    <option value="Sun">Sunday</option>
											</select>
						
											<div class="error-notify"></div>
									</div><!-- End group -->
                    <input type="hidden" name="iSite" id="iSite" value="" />
								    <input class="btn btn-default" type="button" value="Update" id="submitNewDeliveryDay" />
								</form><!-- / end form -->
					    	</div><!-- /.wrapper -->
					    </div><!-- /.modal-content -->
					  </div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
					
					<!-- Modal -->
					<div class="modal fade" id="updateSite" tabindex="-1" role="dialog" aria-labelledby="Updtate Site" aria-hidden="true">
					  <div class="modal-dialog">
					    <div class="modal-content">
					    <div class="modal-header">
						      <div class="margin">
					          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <div class="title">Update site information</div>
						      </div><!-- /.margin -->
					    </div><!-- End modal header -->
					    	<div class="wrapper margin">
					    		<form id="updateSiteForm" >
								    <div class="form-group">
								        <label for="Nickname">Site Name</label> <input class="form-control" id="updateNickname" name="Nickname" placeholder="Enter site name here" type="text">
								
								        <div class="error-notify"></div>
								    </div><!-- End group -->
								
								    <div class="form-group">
								        <label for="Address1">Address</label> <input class="form-control" id="updateAddress1" name="Address1" placeholder="Enter site address here" type="text">
								
								        <div class="error-notify"></div>
								    </div><!-- End group -->
								
								    <div class="form-group">
								        <label for="Address2">Address 2</label> <input class="form-control" id="updateAddress2" name="Address2" placeholder="Suite #201" type="text">
								
								        <div class="error-notify"></div>
								    </div><!-- End group -->
								
								    <div class="row">
								        <div class="col-sm-6">
								            <div class="form-group">
								                <label for="City">City</label> <input class="form-control" id="updateCity" name="City" placeholder="Enter city" type="text">
								
								                <div class="error-notify"></div>
								            </div><!-- End group -->
								        </div><!-- /. half -->
								
								        <div class="col-sm-6">
								            <div class="form-group">
								                <label for="StateId">State</label> 
                                  <select id="updateStateId" name="StateId" class="form-control">
                                    <?= createDropDownOptions($aStates); ?>
                                  </select>
								
								                <div class="error-notify"></div>
								            </div><!-- End group -->
								        </div><!-- /. half -->
								    </div><!-- /.row -->
								
								    <div class="row">
								        <div class="col-sm-6 col-offset-sm-6">
								            <div class="form-group">
								                <label for="Zip">Zip/Postal code</label> <input class="form-control" id="updateZip" name="Zip" placeholder="Enter zip/postal code here" type="text">
								
								                <div class="error-notify"></div>
								            </div><!-- End group -->
								        </div><!-- /. half -->
					    </div><!-- End group -->
									<input type="hidden" id="updateId" name="iSite" value="" />
									<input class="btn btn-default" type="submit" value="Update site" id="submitUpdateSite" />
								</form><!-- / end form -->
					        </div><!-- /. half -->
								    </div><!-- /. row -->
					    	</div><!-- /.wrapper -->
					    </div><!-- /.modal-content -->
					  </div><!-- /.modal-dialog -->
					</div><!-- /.modal -->
                </div><!-- /.margin -->
                <div class="app-trigger" id="_Admin" rel='<?= json_encode(array('p' => 'deliveries', 'sessionID' => session_id())); ?>'></div>
