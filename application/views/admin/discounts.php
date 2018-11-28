                  <div class="margin">
                    <div class="row">
                        <div class="col-lg-12">
                            <section class="panel">
                                    	
                        	<!-- Button trigger modal -->
							<a href="#" data-toggle="modal" data-target="#modalDiscount">
							  <img src="/images/add-admin.svg" alt="Add discount" class="pull-right margin" height="50" width="50"/>
							</a>  
							
							<!-- Modal -->
							<div class="modal fade" id="modalDiscount" tabindex="-1" role="dialog" aria-labelledby="new code" aria-hidden="true">
							  <div class="modal-dialog">
							    <div class="modal-content">
							    <div class="modal-header">
							        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							        <div class="title">Add new discount code</div>
							    </div><!-- End modal header -->
							    	<div class="wrapper margin">
							    		<form id="addDiscount">
										    
										    <div class="row">
										        <div class="col-sm-6">
										        	<div class="form-group">
										        		<label for="Code">What discount code would you like to use?</label> <input class="form-control" id="discountCode" name="Code" placeholder="Enter code" type="text">
										
														<div class="error-notify"></div>
													</div><!-- End group -->
										        	<div class="form-group">
										        		<label for="Amount">How much money would you like to discount per box?</label> <input class="form-control" id="discountAmount" name="Amount" placeholder="Enter price" type="text">
										
														<div class="error-notify"></div>
													</div><!-- End group -->
										        </div><!-- /. half -->
										        
										        <div class="col-sm-6">
										        	<div class="form-group">
										        		<label for="OrdersAffected">How many boxes will this be active for?</label> <input class="form-control" id="discountOrdersAffected" name="OrdersAffected" placeholder="Enter number of boxes" type="text">
										
														<div class="error-notify"></div>
													</div><!-- End group -->
										        </div><!-- /. half -->
										
										    </div><!-- /.row -->
										    
										    <button class="btn btn-default" type="submit" id="addCode">Activate code</button>
                      <input type="hidden" name="returnURL" value="/admin/discounts" />
                      <input type="hidden" name="p" value="discount" />
										</form><!-- / end form -->
							    	</div><!-- /.wrapper -->
							    </div><!-- /.modal-content -->
							  </div><!-- /.modal-dialog -->
							</div><!-- /.modal -->

                                <div style="clear: both;"></div>
                            <table class="table table-hover table-responsive m-t">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Price off per box</th>
                                        <th>Number of boxes</th>
                                        <th># Customers Used</th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody>
                                  <?php foreach ($aDiscounts as $oDiscount) : ?>
                                    <tr>
                                        <td><?= $oDiscount->getCode(); ?></td>
                                        <td><?= money($oDiscount->getAmount()); ?></td>
                                        <td><?= $oDiscount->getOrdersAffected(); ?></td>
                                        <td>
                                          <a data-toggle="collapse" data-parent="#accordion" href="#collapseCode_<?= $oDiscount->getId(); ?>">
                                            <?= $oDiscount->countUsers(); ?>
                                          </a>
                                          <div id="collapseCode_<?= $oDiscount->getId(); ?>" class="panel-collapse collapse">
                                            <ul>
                                            <?php foreach ($oDiscount->getUsers() as $oUser) : ?>
                                              <li><?= $oUser->getFullName(); ?></li>
                                            <?php endforeach; ?>
                                            </ul>
                                          </div><!-- /.collapse -->
                                        </td>
                                        <td><a href="#" class="unpublishButton label bg-<?= ($oDiscount->isPublished()) ? 'danger' : 'success'; ?>" rel="<?= $oDiscount->getId(); ?>"><?= $oDiscount->isPublished() ? 'Unpublish' : 'Publish'; ?></a></td>
                                    </tr>
                                  <?php endforeach; ?>
                                </tbody>
                            </table>
                            </section><!-- /.panel -->
                        </div><!-- /.fullwidth -->
                    </div><!-- /.row -->
                  </div><!-- /.margin -->
                  <div class="app-trigger" id="_Admin" rel='<?= json_encode(array('p' => 'discounts', 'sessionID' => session_id())); ?>'></div>
