                <div class="margin">
                    <div class="row">
                        <div class="col-lg-12">
                            <section class="panel">
                                <table class="table table-hover table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Custom Address</th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    <?php foreach ($aUsers as $oUser) : ?>
                                        <tr>
                                            <td><?= $oUser->getFirstName().' '.$oUser->getLastName(); ?></td>
                                            <td>
                                              <?= $oUser->getAddress1(); ?><br />
                                              <?= ($oUser->getAddress2()) ? $oUser->getAddress2().'<br/>' : ''; ?>
                                              <?= $oUser->getCity().', '.$oUser->getStateAbbrev().' '.$oUser->getZip(); ?>
                                            </td>
                                            <td><a href="#" data-toggle="modal" data-target="#modalApprove<?= $oUser->getId(); ?>">Approve</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
					
              <?php foreach ($aUsers as $oUser) : ?>
								<!-- Modal -->
								<div class="modal fade" id="modalApprove<?= $oUser->getId(); ?>" tabindex="-1" role="dialog" aria-labelledby="Approve user" aria-hidden="true">
								  <div class="modal-dialog">
								    <div class="modal-content">
								    <div class="modal-header">
								        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								        <div class="title">Add new site</div>
								    </div><!-- End modal header -->
								    	<div class="wrapper margin">
								    	
								    			<h4 class="m-b">Want to add this person to an existing site?</h4>
								    			
								    			<label for="site">Select a site</label>
											         <select class="form-control m-b assignSelect" name="site" rel="<?= $oUser->getId(); ?>">
                                 <option value="">- Choose Here to Assign -</option>
                               <?php foreach ($aSites as $oSite) : ?>
                                 <option value="<?= $oSite->getId(); ?>"><?= $oSite->getNickname(); ?></option>
                               <?php endforeach; ?>
													</select>
													
												<h4 class="m-b">Want to create a new site?</h4>	
                      <form class="approvalForm" rel="<?= $oUser->getId(); ?>" id="approvalForm<?= $oUser->getId(); ?>">
											    <div class="form-group">
											        <label for="Nickname">Site name</label> <input class="form-control" id="formSiteNickname<?= $oUser->getId(); ?>" name="Nickname" placeholder="Enter site name here" type="text" />
											
											        <div class="error-notify"></div>
											    </div><!-- End group -->
											
											    <div class="form-group">
											        <label for="site address">Address</label> <input class="form-control" id="formSiteAddress1<?= $oUser->getId(); ?>" name="Address1" value="<?= $oUser->getAddress1(); ?>" placeholder="Enter site address here" type="text" />
											
											        <div class="error-notify"></div>
											    </div><!-- End group -->
											
											    <div class="form-group">
											        <label for="site address 2">Address 2</label> <input class="form-control" id="formSiteAddress2<?= $oUser->getId(); ?>" name="Address2" value="<?= $oUser->getAddress2(); ?>" placeholder="Suite #201" type="text" />
											
											        <div class="error-notify"></div>
											    </div><!-- End group -->
											
											    <div class="row">
											        <div class="col-sm-6">
											            <div class="form-group">
											                <label for="site city">City</label> <input class="form-control" id="formSiteCity<?= $oUser->getId(); ?>" name="City" value="<?= $oUser->getCity(); ?>" placeholder="Enter city" type="text" />
											
											                <div class="error-notify"></div>
											            </div><!-- End group -->
											        </div><!-- /. half -->
											
											        <div class="col-sm-6">
											            <div class="form-group">
											                <label for="site State">State</label> <br/>
                                        <select name="StateId" class="form-control" id="formSiteState<?= $oUser->getId(); ?>">
                                        <?= createDropDownOptions($aStates, $oUser->getStateId()); ?>
                                        </select>
											
											                <div class="error-notify"></div>
											            </div><!-- End group -->
											        </div><!-- /. half -->
											    </div><!-- /.row -->
											
											    <div class="row">
											        <div class="col-sm-6 col-offset-sm-6">
											            <div class="form-group">
											                <label for="site zip">Zip/Postal code</label> <input class="form-control" id="formSiteZip<?= $oUser->getId(); ?>" name="Zip" value="<?= $oUser->getZip(); ?>" placeholder="Enter zip/postal code here" type="text" />
											
											                <div class="error-notify"></div>
											            </div><!-- End group -->
											        </div><!-- /. half -->
											    </div><!-- /. row -->
											    <button class="btn btn-default submitApproval" type="submit" rel="<?= $oUser->getId(); ?>">Add site</button>
                          <input type="hidden" name="iUser" value="<?= $oUser->getId(); ?>" />
											</form><!-- / end form -->
								    	</div><!-- /.wrapper -->
								    </div><!-- /.modal-content -->
								  </div><!-- /.modal-dialog -->
								</div><!-- /.modal -->
              <?php endforeach; ?>
                                    
                            </section><!-- /.panel -->
                        </div><!-- /.fullwidth -->
                    </div><!-- /.row -->
                </div><!-- /.margin -->
                <div class="app-trigger" id="_Admin" rel='<?= json_encode(array('p' => 'approval', 'sessionID' => session_id())); ?>'></div>
