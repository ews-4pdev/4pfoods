                <div class="margin">
                    <div class="row">
                        <div class="col-lg-12">
                            <section class="panel">
                            
                            	<!-- Nav tabs -->

                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#activeCustomers">Active Customers</a></li>
                                    <li><a data-toggle="tab" href="#archivedCustomers">Archived Customers</a></li>
                                </ul>
                                
                                 <div class="tab-content">                                    
                                    <!-- Start tab pane -->

                                    <div class="tab-pane active" id="activeCustomers">
		                                <table id="activeCustomersTable" class="table table-hover table-responsive ">
		                                    <thead>
		                                        <tr>
		                                            <th>Name</th>
		                                            <th>Current subscription(s)</th>
													<th>DoorStep</th>
													<th>Default Day</th>
													<th>State</th>
		                                            <th>Status</th>
		                                            <th>Account</th>
		                                            <th>Date joined</th>
		                                        </tr>
		                                    </thead>
		
		                                    <tbody>
		                                    <?php foreach ($aUsers as $oUser) : ?>
		                                        <tr>
		                                            <td><a href="/admin/customers/<?= $oUser->getId(); ?>"><?= $oUser->getLastName().', '.$oUser->getFirstName(); ?></a></td>
		                                            <td><?= $oUser->getAllSubscriptionString();?></td>
													<td><?=( $oUser->getDoorstep() ) ? 'Y' : 'N' ?></td>
													<td><?= $oUser->getDefaultDeliverySite()->getdefaultdeliveryday(); ?></td>
													<th><?= $oUser->getDefaultDeliverySite()->getState()->getCode();?></th>
		                                            <td><?= $oUser->getCustomerStatus(); ?><?= ($oUser->isConfirmed()) ? '' : ' (unconfirmed)'; ?></td>
		                                            <td><a href="#" class="label bg-danger archiveButton" data-id="<?= $oUser->getId(); ?>">Archive</a></td>
		                                            <td><?= $oUser->getCreatedAt('M j, Y'); ?></td>
		                                        </tr>
		                                    <?php endforeach; ?>
		                                    </tbody>
		                                </table>

		                                <!-- FOR ROBIN - End Consistent panel footer -->
		                                </div><!-- End tab pane -->
		                                
		                                <div class="tab-pane" id="archivedCustomers">
		                                <table id="archivedCustomersTable" class="table table-hover table-responsive">
		                                    <thead>
		                                        <tr>
		                                            <th>Name</th>
		                                            <th>Account</th>
		                                            <th>Date joined</th>
		                                        </tr>
		                                    </thead>
		
		                                    <tbody>
		                                    <?php foreach ($aArchived as $oUser) : ?>
		                                        <tr>
		                                            <td><a href="/admin/customers/<?= $oUser->getId(); ?>"><?= $oUser->getLastName().', '.$oUser->getFirstName(); ?></a></td>
		                                            <td><a href="#" class="label bg-success white archiveButton" data-id="<?= $oUser->getId(); ?>">Re-Activate</a></td>
		                                            <td><?= $oUser->getCreatedAt('M j, Y'); ?></td>
		                                        </tr>
		                                    <?php endforeach; ?>
		                                    </tbody>
		                                </table>
		                                <!-- FOR ROBIN - End Consistent panel footer -->
		                                </div><!-- End tab pane -->

                                </div><!-- End tabbed content -->
                            </section><!-- /.panel -->
                        </div><!-- /.fullwidth -->
                    </div><!-- /.row -->
                </div><!-- /.margin -->
