				<div class="margin">
					<div class="row">
						<div class="col-lg-12">
							<section class="panel b-n m-t statistics">
								<!-- Nav tabs -->
									<ul class="nav nav-tabs b-n">
									  <li class="active"><a href="#today" data-toggle="tab">All time</a></li>
									</ul>
									
									<div class="tab-content clearfix">
									  <div class="tab-pane in fade active" id="alltime">
										  <div class="statistics">
											  <div class="col-lg-3 col-md-3 col-sm-3 dataWrap">
												  <h2 class="statValue"><?= $totalCustomers; ?></h2><!--  /.statValue -->
												  <div class="text-muted">Customers</div>
											  </div><!-- /.4 col -->
											  
											  <div class="col-lg-3 col-md-3 col-sm-3 dataWrap">
												  <h2 class="statValue"><?= '$'.(floor($totalRevenue) / 1000).'k'; ?></h2><!--  /.statValue -->
												  <div class="text-muted">In sales</div>
											  </div><!-- /.4 col -->
											  
											  <div class="col-lg-3 col-md-3 col-sm-3 dataWrap">
												  <h2 class="statValue"><?= $newSubscriptions; ?></h2><!--  /.statValue -->
												  <div class="text-muted">New subscriptions</div>
											  </div><!-- /.4 col -->
											  
											  <div class="col-lg-3 col-md-3 col-sm-3 dataWrap">
												  <h2 class="statValue"><?= $nOrders; ?></h2><!--  /.statValue -->
												  <div class="text-muted">Orders</div>
											  </div><!-- /.4 col -->
											  
										  </div><!-- /.statistics -->
									  </div><!-- /#alltime -->
									</div>
							</section><!-- /.panel -->
						</div><!-- /.fullwidth -->
					</div><!-- /.row -->
					
					<div class="row">
						<div class="col-sm-6">
							<div class="panel statistics bright">
								<div class="dataWrap">
									<h2 class="statValue"><?= $donatedOrdersThisWeek; ?></h2>
									<div class="text-muted">Donations last week</div>
								</div><!-- /dataWrap -->
							</div><!-- /.panel -->	
						</div><!-- /. half col -->
						
						<div class="col-sm-6">
							<div class="panel statistics bright">
								<div class="dataWrap">
									<h2 class="statValue"><?= $totalDonatedOrders; ?></h2>
									<div class="text-muted">Donations all time</div>
								</div><!-- /dataWrap -->
							</div><!-- /.panel -->	
						</div><!-- /. half col -->
					</div><!-- /.row -->
					
					<div class="row">
						<div class="col-lg-12">
							<section class="panel no-border">
								<div class="panel-body">
									<div class="timeline">
										<h4 class="m-b">Recent Activity</h4>
										
                  <?php foreach ($recentSignups as $oCustomer) : ?>
										<div class="clearfix m-b timelineItem">
											<small class="text-muted pull-right"><?= prettyDate($oCustomer->getCreatedAt()); ?></small>
											<div class="clear">
												<strong><a target="_blank" href="/admin/customers/<?= $oCustomer->getId(); ?>"><?= $oCustomer->getFullName(); ?></a></strong>
												<div class="orderDetails"><small>Signed up for a new account.</small></div> <!-- / .orderDetails -->
											</div>
										</div><!-- End timeline item -->
                  <?php endforeach; ?>
										
                  <?php /**
										<div class="clearfix m-b timelineItem">
											<small class="text-muted pull-right">Yesterday at 1:00pm</small>
											<div class="clear">
												<strong>Jonnah Hill</strong>
												<div class="orderDetails"><small>Signed up for a new account.</small></div> <!-- / .orderDetails -->
											</div>
										</div><!-- End timeline item -->
										
										<div class="clearfix m-b timelineItem">
											<small class="text-muted pull-right">Jan 21 at 3:30pm</small>
											<div class="clear">
												<strong>Jay Mantri</strong>
												<div class="orderDetails"><small>Added a new Protein box subscription.</small></div> <!-- / .orderDetails -->
											</div>
										</div><!-- End timeline item -->
										
										<div class="clearfix m-b timelineItem">
											<small class="text-muted pull-right">Dec. 13 at 3:30pm</small>
											<div class="clear">
												<strong>Robin Arenson</strong>
												<div class="orderDetails"><small>Had a failed transaction for $98.55. <a href="#">View details</a></small></div> <!-- / .orderDetails -->
											</div>
										</div><!-- End timeline item -->
										
										<div class="clearfix m-b timelineItem">
											<small class="text-muted pull-right">Nov 1 at 3:30pm</small>
											<div class="clear">
												<strong>Nathan Havey</strong>
												<div class="orderDetails"><small>Changed his subscription from a Veggie box to a Protein box.</small></div> <!-- / .orderDetails -->
											</div>
										</div><!-- End timeline item -->
                  **/ ?>
										
									</div><!-- /.timeline -->
								</div><!-- /.panel-body -->
							</section><!-- /.panel -->
						</div><!-- /.fullwidth -->
					</div><!-- /.row -->
				</div><!-- /.margin -->
