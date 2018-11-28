		<div id="content">
			<div class="container">
				<div class="row">
					<div class="col-sm-6">
						<h2>Profile</h2>
					
						<form id="fAccount" name="Account">
		    				<div class="row">
		                        <div class="col-sm-6">
		                            <div class="form-group">
		                                <label for="FirstName">First name</label> <input class="form-control" id="cFirstName" name="FirstName" value="<?= $oUser->getFirstName(); ?>" placeholder="First name" type="text" />
		
		                                <div class="error-notify"></div>
		                            </div><!-- End group -->
		                        </div><!-- /. half -->
		
		                        <div class="col-sm-6">
		                            <div class="form-group">
		                                <label for="LastName">Last name</label> <input class="form-control" id="cLastName" name="LastName" value="<?= $oUser->getLastName(); ?>" placeholder="Last name" type="text" />
		
		                                <div class="error-notify"></div>
		                            </div><!-- End group -->
		                        </div><!-- /. half -->
								
		                    </div><!-- /.row -->

							<div class="form-group m-b">
								 <label for="Email">Email</label> <input class="form-control" id="cEmail" name="Email" value="<?= $oUser->getEmail(); ?>" placeholder="Email" type="email" />
								<div class="error-notify"></div>
							</div><!-- End group -->

		    			
							<div class="form-group m-b">
		                        <label for="Phone">Phone</label> <input class="form-control" id="cPhone" name="Phone" value="<?= $oUser->getPhone(); ?>" type="text" placeholder="(333) 333.3333" />
		
		                        <div class="error-notify"></div>
		                    </div><!-- End group -->

						<?php if( $oUser->getDoorStep() ){ ?>
							<div class="row doorstep">
								<div class="col-sm-6">
									<div class="form-group">
										<label for="Mobile">Cell Phone</label> <input
												class="form-control" id="Mobile" name="Mobile" value="<?= $oUser->getMobile(); ?>"
												type="text">

										<div class="error-notify"></div>
									</div><!-- End group -->
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label for="AMobile">Alternate Cell Phone</label> <input
												class="form-control" id="AMobile" name="AMobile" value="<?= $oUser->getAMobile(); ?>"
												type="text">

										<div class="error-notify"></div>
									</div><!-- End group -->
								</div>
							</div>

							<div class="form-group doorstep">
								<div>Is this building a:</div>

								<div class="col-lg-6">
									<div class="checkbox ">
										<label for="SingleFamilyHome">
											<input type="hidden" name="SingleFamilyHome" value="false">
											<input id="SingleFamilyHome" class="BuildingType" name="SingleFamilyHome" value="true"
													<?php if($oUser->getSingleFamilyHome()) echo 'checked'; ?>	   type="checkbox">Single Family Home
										</label>
									</div>
									<div class="checkbox">
										<label for="ApartmentCondo">
											<input type="hidden" name="ApartmentCondo" value="false">
											<input id="ApartmentCondo" class="BuildingType" name="ApartmentCondo" value="true"
											<?php if($oUser->getApartmentCondo()) echo 'checked'; ?> type="checkbox">Apartment/Condo
										</label>
									</div>
								</div>

								<div class="col-lg-6">
									<div class="checkbox">
										<label for="OfficeBuilding">
											<input type="hidden" name="OfficeBuilding" value="false">
											<input id="OfficeBuilding" class="BuildingType" name="OfficeBuilding" value="true"
											<?php if($oUser->getOfficeBuilding()) echo 'checked'; ?> type="checkbox">Office Building
										</label>
									</div>

									<div class="checkbox">
										<label for="TownHouse">
											<input type="hidden" name="TownHouse" value="false">
											<input id="TownHouse" class="BuildingType" name="TownHouse" value="true"
											<?php if($oUser->getTownHouse()) echo 'checked'; ?> type="checkbox">TownHouse
										</label>
									</div>
								</div>
							</div>
							<div style="clear: both;"></div>
							<div class=" m-t-md doorstep">
								<div class="form-group">
									<div class="checkbox">
										<input class="" placeholder="Concierge"
											<?php if($oUser->getConcierge()) echo 'checked'; ?> value="true" id="concierge" name="Concierge" type="checkbox">
										<label for="concierge">Is there a concierge we can leave the
											package with?</label>
										<input type="hidden" name="Concierge" value="false">

									</div>
								</div>
							</div>

							<div class="m-t-md doorstep">
								<div class="form-group">
									<label for="concierge_number">Enter Concierge Number</label>
									<input class="form-control" placeholder="Concierge Number"
									value="<?= $oUser->getConciergeNumber(); ?>" id="concierge_number" name="ConciergeNumber" type="text">
								</div>
							</div>

							<div class=" m-t-md doorstep">
								<div class="form-group">
									<div>If a key fob is needed to access the
										building, can you provide us with a fob?</div>
									<div class="pull-left m-r-sm">
										<label for="KeyFob">Yes</label>
										<input id="KeyFob" name="KeyFob" data-value="true"
										<?php if( $oUser->getKeyFob() == 1 ) echo 'checked'; ?>	   type="radio">
									</div>

									<div>
										<label for="KeyFob1">No</label>
										<input class="" id="KeyFob1" name="KeyFob" data-value="false"
												<?php if( $oUser->getKeyFob() == 0 ) echo 'checked'; ?>  type="radio">
									</div>


								</div>
							</div>

							<div class=" m-t-md doorstep">
								<div class="form-group">
									<label for="Parking">Where can we easily park while making the
										delivery?</label>
                                                        <textarea class="form-control" id="Parking" name="Parking"
																  rows="3"><?= $oUser->getParking(); ?></textarea>

									<div class="error-notify"></div>
								</div><!-- End group -->
							</div>
						<?php } ?>
							<!--  Ended   -->

							<div class="form-group">
								<label for="DietaryRestrictions">Dietary Restrictions</label> <textarea
										class="form-control" id="cDietaryRestrictions"
										name="DietaryRestrictions" rows="3"><?= $oUser->getDietaryRestrictions(); ?></textarea>

								<div class="error-notify"></div>
							</div><!-- End group -->


							<div class="form-group m-b">
								<label for="DeliverySiteNotes">Any other special instructions?
									Pretend like you are telling a 1st grader where to deliver your
									bag of goodies.The more detail the better!</label> <textarea
										class="form-control" id="cDeliverySiteNotes"
										name="DeliverySiteNotes" rows="3"><?= $oUser->getDeliverySiteNotes(); ?></textarea>

								<div class="error-notify"></div>
							</div><!-- End group -->
						<!--	End EWS						-->
		                    
		                <button class="btn btn-default" type="button" id="submitEditProfile">Update profile</button>    
						
              <input type="hidden" name="prefix" value="c" />
              <input type="hidden" name="returnURL" value="/account/profile" />
              <input type="hidden" name="iUser" value="<?= $oUser->getId(); ?>" />
						</form><!-- / end profile form -->
						
						<h2>Security</h2>
						<form id="security" name="Security">
							<div class="form-group m-b">
	                            <label for="OldPassword">Old password</label> <input class="form-control" id="cOldPassword" name="OldPassword" placeholder="Old Password" type="password" />
	
	                            <div class="error-notify"></div>
	                        </div><!-- End group -->
							<div class="form-group m-b">
	                            <label for="NewPassword">New password</label> <input class="form-control" id="cNewPassword" name="NewPassword" placeholder="New Password" type="password" />
	
	                            <div class="error-notify"></div>
	                        </div><!-- End group -->
	                        
	                        <div class="form-group m-b">
	                            <label for="ConfirmPassword">Confirm new password</label> <input class="form-control" id="cConfirmPassword" name="ConfirmPassword" placeholder="Confirm Password" type="password" />
	
	                            <div class="error-notify"></div>
	                        </div><!-- End group -->
	                        <button class="btn btn-default" type="button" id="updatePassword">Update password</button>    
						
              <input type="hidden" name="prefix" value="c" />
              <input type="hidden" name="returnURL" value="/account/profile" />
              <input type="hidden" name="iUser" value="<?= $oUser->getId(); ?>" />
						</form><!-- / end profile form -->
					</div><!-- /. half col -->
					<div class="col-sm-6">
					</div><!-- /. half col -->
				</div><!-- /.row -->
			</div><!-- /.container -->
		</div><!-- /#content -->
    <div class="app-trigger" id="_Account" rel='<?= json_encode(array('p' => 'profile', 'sessionID' => session_id())); ?>'></div>
