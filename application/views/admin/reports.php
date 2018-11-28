<div class="margin">
	<div class="row">
		<div class="col-lg-12">
			<section class="panel">
				<!-- Nav tabs -->

				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#todayDelivery">Deliveries today</a></li>
					<li><a data-toggle="tab" href="#twoDayDelivery">48 hour snapshot</a></li>
					<li><a data-toggle="tab" href="#sevenDayDelivery">7 day snapshot</a></li>
					<li><a data-toggle="tab" href="#skippedOrders">3 Month skipped Snapshot</a></li>
				</ul>

				<div class="tab-content">
					<!-- Start tab pane -->

					<div class="tab-pane active" id="todayDelivery">
						<!-- Start site box -->
						<h2 class="margin">Here are your deliveries for today</h2>
						<?php foreach ($outForDelivery['sites']['DoorStep'] as $id => $info) : ?>
							<?php
							$nickName= '';
							$fullAddress = '';
							$fullAddress = UserQuery::create()
							                        ->findPk($info['user'])
							                        ->getFullAddress();
							$nickName = 'DoorStep';

							?>
							<div class="panel margin padding no-shadow">
								<p class="m-b"><strong><?= $nickName; ?></strong> - <?= $fullAddress; ?></p>

								<table class="table table-hover table-responsive">
									<thead>
									<tr>
										<th>Order ID</th>
										<th>Customer</th>
										<th>Product Type</th>
										<th>Size</th>
										<th>Fulfillment Date</th>
									</tr>
									</thead>

									<tbody>
									<?php foreach ($info['orders'] as $order) : ?>
										<tr>
											<td>#<?= $order->getId(); ?></td>
											<td><?= $order->getCustomerFirstName().' '.$order->getCustomerLastName(); ?></td>
											<td><?= $order->getProductName(); ?></td>
											<td><?= $order->getProductSize(); ?></td>
											<td><?= $order->getDeliveryScheduledFor('M. d, Y'); ?></td>
										</tr>
									<?php endforeach; ?>

									</tbody>
								</table>

								<h2>Delivery Site Overview</h2>

								<div class="row">

									<div class="col-sm-6">
										<table class="table table-responsive panel-light panel">
											<thead>
											<tr>
												<th>Product Type</th>
												<th>Size</th>
												<th>Total bags</th>
											</tr>
											</thead>

											<tbody>
											<?php foreach ($info['products'] as $iProduct => $productInfo) : ?>
												<tr>
													<td><?= $productInfo['product']->getTitle(); ?></td>
													<td><?= $productInfo['product']->getSize(); ?></td>
													<td><?= $productInfo['count']; ?></td>
												</tr>
											<?php endforeach; ?>

											</tbody>
										</table><!-- /.table-responsive -->
									</div><!-- /. col-sm-6 -->

									<div class="col-sm-3">
										<div class="panel statistics bright">
											<div class="dataWrap">
												<h2 class="statValue"><?= count($info['orders']); ?></h2>
												<div class="text-muted">Total bags</div>
											</div><!-- /.dataWrap -->
										</div><!-- /. stats panel -->
									</div><!-- /. col-sm-3 -->

									<div class="col-sm-3">
										<div class="panel statistics bright">
											<div class="dataWrap">
												<h2 class="statValue"><?= count($info['products']); ?></h2>
												<div class="text-muted">Product Types</div>
											</div><!-- /.dataWrap -->
										</div><!-- /. stats panel -->
									</div><!-- /. col-sm-3 -->
								</div><!-- /.row -->
							</div><!-- /.panel for site deliveries -->
							<!-- Start site box -->
						<?php endforeach; ?>

						<!--	                                    For DeliverySite-->
						<?php foreach ($outForDelivery['sites']['DeliverySite'] as $id => $info) : ?>
							<?php
							$nickName= '';
							$fullAddress = '';
							$nickName = $info['site']->getNickname();
							$fullAddress = $info['site']->getFullAddress();
							?>
							<div class="panel margin padding no-shadow">
								<p class="m-b"><strong><?= $nickName; ?></strong> - <?= $fullAddress; ?></p>

								<table class="table table-hover table-responsive">
									<thead>
									<tr>
										<th>Order ID</th>
										<th>Customer</th>
										<th>Product Type</th>
										<th>Size</th>
										<th>Fulfillment Date</th>
									</tr>
									</thead>

									<tbody>
									<?php foreach ($info['orders'] as $order) : ?>
										<tr>
											<td>#<?= $order->getId(); ?></td>
											<td><?= $order->getCustomerFirstName().' '.$order->getCustomerLastName(); ?></td>
											<td><?= $order->getProductName(); ?></td>
											<td><?= $order->getProductSize(); ?></td>
											<td><?= $order->getDeliveryScheduledFor('M. d, Y'); ?></td>
										</tr>
									<?php endforeach; ?>

									</tbody>
								</table>

								<h2>Delivery Site Overview</h2>

								<div class="row">

									<div class="col-sm-6">
										<table class="table table-responsive panel-light panel">
											<thead>
											<tr>
												<th>Product Type</th>
												<th>Size</th>
												<th>Total bags</th>
											</tr>
											</thead>

											<tbody>
											<?php foreach ($info['products'] as $iProduct => $productInfo) : ?>
												<tr>
													<td><?= $productInfo['product']->getTitle(); ?></td>
													<td><?= $productInfo['product']->getSize(); ?></td>
													<td><?= $productInfo['count']; ?></td>
												</tr>
											<?php endforeach; ?>

											</tbody>
										</table><!-- /.table-responsive -->
									</div><!-- /. col-sm-6 -->

									<div class="col-sm-3">
										<div class="panel statistics bright">
											<div class="dataWrap">
												<h2 class="statValue"><?= count($info['orders']); ?></h2>
												<div class="text-muted">Total bags</div>
											</div><!-- /.dataWrap -->
										</div><!-- /. stats panel -->
									</div><!-- /. col-sm-3 -->

									<div class="col-sm-3">
										<div class="panel statistics bright">
											<div class="dataWrap">
												<h2 class="statValue"><?= count($info['products']); ?></h2>
												<div class="text-muted">Product Types</div>
											</div><!-- /.dataWrap -->
										</div><!-- /. stats panel -->
									</div><!-- /. col-sm-3 -->
								</div><!-- /.row -->
							</div><!-- /.panel for site deliveries -->
							<!-- Start site box -->
						<?php endforeach; ?>
					</div><!-- End tab pane -->

					<div class="tab-pane" id="twoDayDelivery">
						<!-- Start site box -->
						<h2 class="margin">Here are the orders queued for the next 48 hours</h2>
						<?php foreach ($queued['sites']['DoorStep'] as $id => $info) : ?>
							<div class="panel margin padding no-shadow">
								<?php
								$nickName= '';
								$fullAddress = '';

								$fullAddress = UserQuery::create()->findPk($info['user'])->getFullAddress();
								$nickName = 'DoorStep';
								?>
								<p class="m-b"><strong><?= $nickName; ?></strong> - <?= $fullAddress; ?></p>

								<table class="table table-hover table-responsive">
									<thead>
									<tr>
										<th>Order ID</th>
										<th>Customer</th>
										<th>Product Type</th>
										<th>Size</th>
										<th>Fulfillment Date</th>
									</tr>
									</thead>

									<tbody>
									<?php foreach ($info['orders'] as $order) : ?>
										<tr>
											<td>#<?= $order->getId(); ?></td>
											<td><?= $order->getCustomerFirstName().' '.$order->getCustomerLastName(); ?></td>
											<td><?= $order->getProductName(); ?></td>
											<td><?= $order->getProductSize(); ?></td>
											<td><?= $order->getDeliveryScheduledFor('M. d, Y'); ?></td>
										</tr>
									<?php endforeach; ?>

									</tbody>
								</table>

								<h2>Delivery Site Overview</h2>

								<div class="row">

									<div class="col-sm-6">
										<table class="table table-responsive panel-light panel">
											<thead>
											<tr>
												<th>Product Type</th>
												<th>Size</th>
												<th>Total bags</th>
											</tr>
											</thead>

											<tbody>
											<?php foreach ($info['products'] as $iProduct => $productInfo) : ?>
												<tr>
													<td><?= $productInfo['product']->getTitle(); ?></td>
													<td><?= $productInfo['product']->getSize(); ?></td>
													<td><?= $productInfo['count']; ?></td>
												</tr>
											<?php endforeach; ?>

											</tbody>
										</table><!-- /.table-responsive -->
									</div><!-- /. col-sm-6 -->

									<div class="col-sm-3">
										<div class="panel statistics bright">
											<div class="dataWrap">
												<h2 class="statValue"><?= count($info['orders']); ?></h2>
												<div class="text-muted">Total bags</div>
											</div><!-- /.dataWrap -->
										</div><!-- /. stats panel -->
									</div><!-- /. col-sm-3 -->

									<div class="col-sm-3">
										<div class="panel statistics bright">
											<div class="dataWrap">
												<h2 class="statValue"><?= count($info['products']); ?></h2>
												<div class="text-muted">Product Types</div>
											</div><!-- /.dataWrap -->
										</div><!-- /. stats panel -->
									</div><!-- /. col-sm-3 -->
								</div><!-- /.row -->
							</div><!-- /.panel for site deliveries -->
							<!-- Start site box -->
						<?php endforeach; ?>

						<!--	                                    For Delivery Sites-->
						<?php foreach ($queued['sites']['DeliverySite'] as $id => $info) : ?>
							<div class="panel margin padding no-shadow">
								<?php
								$nickName= '';
								$fullAddress = '';
								$nickName = $info['site']->getNickname();
								$fullAddress = $info['site']->getFullAddress();
								?>
								<p class="m-b"><strong><?= $nickName; ?></strong> - <?= $fullAddress; ?></p>

								<table class="table table-hover table-responsive">
									<thead>
									<tr>
										<th>Order ID</th>
										<th>Customer</th>
										<th>Product Type</th>
										<th>Size</th>
										<th>Fulfillment Date</th>
									</tr>
									</thead>

									<tbody>
									<?php foreach ($info['orders'] as $order) : ?>
										<tr>
											<td>#<?= $order->getId(); ?></td>
											<td><?= $order->getCustomerFirstName().' '.$order->getCustomerLastName(); ?></td>
											<td><?= $order->getProductName(); ?></td>
											<td><?= $order->getProductSize(); ?></td>
											<td><?= $order->getDeliveryScheduledFor('M. d, Y'); ?></td>
										</tr>
									<?php endforeach; ?>

									</tbody>
								</table>

								<h2>Delivery Site Overview</h2>

								<div class="row">

									<div class="col-sm-6">
										<table class="table table-responsive panel-light panel">
											<thead>
											<tr>
												<th>Product Type</th>
												<th>Size</th>
												<th>Total bags</th>
											</tr>
											</thead>

											<tbody>
											<?php foreach ($info['products'] as $iProduct => $productInfo) : ?>
												<tr>
													<td><?= $productInfo['product']->getTitle(); ?></td>
													<td><?= $productInfo['product']->getSize(); ?></td>
													<td><?= $productInfo['count']; ?></td>
												</tr>
											<?php endforeach; ?>

											</tbody>
										</table><!-- /.table-responsive -->
									</div><!-- /. col-sm-6 -->

									<div class="col-sm-3">
										<div class="panel statistics bright">
											<div class="dataWrap">
												<h2 class="statValue"><?= count($info['orders']); ?></h2>
												<div class="text-muted">Total bags</div>
											</div><!-- /.dataWrap -->
										</div><!-- /. stats panel -->
									</div><!-- /. col-sm-3 -->

									<div class="col-sm-3">
										<div class="panel statistics bright">
											<div class="dataWrap">
												<h2 class="statValue"><?= count($info['products']); ?></h2>
												<div class="text-muted">Product Types</div>
											</div><!-- /.dataWrap -->
										</div><!-- /. stats panel -->
									</div><!-- /. col-sm-3 -->
								</div><!-- /.row -->
							</div><!-- /.panel for site deliveries -->
							<!-- Start site box -->
						<?php endforeach; ?>
						<div class="panel margin padding no-shadow clearfix">
							<div class="row">
								<div class="col-sm-6">
									<h2>The two day forecast</h2>
									<ul>
										<?php foreach ($queued['products'] as $iProduct => $pInfo) : ?>
											<li><?= $pInfo['product']->getTitle(); ?> - <?= $pInfo['count']; ?></li>
										<?php endforeach; ?>
										<li></li>
									</ul>

								</div><!--/. half column -->

								<div class="col-sm-6">
									<div class="panel statistics bright">
										<div class="dataWrap">
											<h2 class="statValue"><?= $queued['totalBags']; ?></h2>
											<div class="text-muted">Total bags</div>
										</div><!-- /.dataWrap -->
									</div><!-- /. stats panel -->
								</div><!--/. half column -->
							</div><!-- /.row -->
						</div><!-- /.panel -->
					</div><!-- End tab pane -->

					<div class="tab-pane" id="sevenDayDelivery">
						<!-- Start site box -->
						<h2 class="margin">Here are the orders expected for the next 7 days</h2>
						<?php /** @var TYPE_NAME $theoretical */
						foreach ($theoretical['sites']['DoorStep'] as $id => $info) : ?>
							<?php
							$nickName= '';
							$fullAddress = '';
							$fullAddress = UserQuery::create()
							                        ->findPk($id)
							                        ->getFullAddress();
							$nickName = 'DoorStep';


							?>
							<div class="panel margin padding no-shadow">
								<p class="m-b"><strong><?= $nickName; ?></strong> - <?= $fullAddress; ?></p>

								<table class="table table-hover table-responsive">
									<thead>
									<tr>
										<th>Order ID</th>
										<th>Customer</th>
										<th>Product Type</th>
										<th>Size</th>
										<th>Fulfillment Date</th>
									</tr>
									</thead>

									<tbody>
									<?php foreach ($info['orders'] as $item) : ?>
										<tr>
											<td>--</td>
											<td><?= $item['oUser']->getFirstName().' '.$item['oUser']->getLastName(); ?></td>
											<td><?= $item['oProduct']->getTitle(); ?></td>
											<td><?= $item['oProduct']->getSize(); ?></td>
											<td><?= date('M. d, Y', strtotime($item['stamp'])); ?></td>
										</tr>
									<?php endforeach; ?>

									</tbody>
								</table>

								<h2>Delivery Site Overview</h2>

								<div class="row">

									<div class="col-sm-6">
										<table class="table table-responsive panel-light panel">
											<thead>
											<tr>
												<th>Product Type</th>
												<th>Size</th>
												<th>Total bags</th>
											</tr>
											</thead>

											<tbody>
											<?php foreach ($info['products'] as $iProduct => $productInfo) : ?>
												<tr>
													<td><?= $productInfo['product']->getTitle(); ?></td>
													<td><?= $productInfo['product']->getSize(); ?></td>
													<td><?= $productInfo['count']; ?></td>
												</tr>
											<?php endforeach; ?>

											</tbody>
										</table><!-- /.table-responsive -->
									</div><!-- /. col-sm-6 -->

									<div class="col-sm-3">
										<div class="panel statistics bright">
											<div class="dataWrap">
												<h2 class="statValue"><?= count($info['orders']); ?></h2>
												<div class="text-muted">Total bags</div>
											</div><!-- /.dataWrap -->
										</div><!-- /. stats panel -->
									</div><!-- /. col-sm-3 -->

									<div class="col-sm-3">
										<div class="panel statistics bright">
											<div class="dataWrap">
												<h2 class="statValue"><?= count($info['products']); ?></h2>
												<div class="text-muted">Product Types</div>
											</div><!-- /.dataWrap -->
										</div><!-- /. stats panel -->
									</div><!-- /. col-sm-3 -->
								</div><!-- /.row -->
							</div><!-- /.panel for site deliveries -->
							<!-- Start site box -->
						<?php endforeach; ?>

						<!-- For Delivery Sites-->
						<?php foreach ($theoretical['sites']['DeliverySite'] as $id => $info) : ?>
							<?php
							$nickName = $info['site']->getNickname();
							$fullAddress = $info['site']->getFullAddress();
							?>
							<div class="panel margin padding no-shadow">
								<p class="m-b"><strong><?= $nickName; ?></strong> - <?= $fullAddress; ?></p>

								<table class="table table-hover table-responsive">
									<thead>
									<tr>
										<th>Order ID</th>
										<th>Customer</th>
										<th>Product Type</th>
										<th>Size</th>
										<th>Fulfillment Date</th>
									</tr>
									</thead>

									<tbody>
									<?php foreach ($info['orders'] as $item) : ?>
										<tr>
											<td>--</td>
											<td><?= $item['oUser']->getFirstName().' '.$item['oUser']->getLastName(); ?></td>
											<td><?= $item['oProduct']->getTitle(); ?></td>
											<td><?= $item['oProduct']->getSize(); ?></td>
											<td><?= date('M. d, Y', strtotime($item['stamp'])); ?></td>
										</tr>
									<?php endforeach; ?>

									</tbody>
								</table>

								<h2>Delivery Site Overview</h2>

								<div class="row">

									<div class="col-sm-6">
										<table class="table table-responsive panel-light panel">
											<thead>
											<tr>
												<th>Product Type</th>
												<th>Size</th>
												<th>Total bags</th>
											</tr>
											</thead>

											<tbody>
											<?php foreach ($info['products'] as $iProduct => $productInfo) : ?>
												<tr>
													<td><?= $productInfo['product']->getTitle(); ?></td>
													<td><?= $productInfo['product']->getSize(); ?></td>
													<td><?= $productInfo['count']; ?></td>
												</tr>
											<?php endforeach; ?>

											</tbody>
										</table><!-- /.table-responsive -->
									</div><!-- /. col-sm-6 -->

									<div class="col-sm-3">
										<div class="panel statistics bright">
											<div class="dataWrap">
												<h2 class="statValue"><?= count($info['orders']); ?></h2>
												<div class="text-muted">Total bags</div>
											</div><!-- /.dataWrap -->
										</div><!-- /. stats panel -->
									</div><!-- /. col-sm-3 -->

									<div class="col-sm-3">
										<div class="panel statistics bright">
											<div class="dataWrap">
												<h2 class="statValue"><?= count($info['products']); ?></h2>
												<div class="text-muted">Product Types</div>
											</div><!-- /.dataWrap -->
										</div><!-- /. stats panel -->
									</div><!-- /. col-sm-3 -->
								</div><!-- /.row -->
							</div><!-- /.panel for site deliveries -->
							<!-- Start site box -->
						<?php endforeach; ?>

						<div class="panel margin padding no-shadow clearfix">
							<div class="row">
								<div class="col-sm-6">
									<h2>The week forecast</h2>
									<ul>
										<?php foreach ($theoretical['products'] as $iProduct => $pInfo) : ?>
											<li><?= $pInfo['product']->getTitle(); ?> - <?= $pInfo['count']; ?></li>
										<?php endforeach; ?>
										<li></li>
									</ul>

									<div class="panel statistics bright m-t">
										<div class="dataWrap">
											<h2 class="statValue"><?= $theoretical['totalBags']; ?></h2>
											<div class="text-muted">Total bags</div>
										</div><!-- /.dataWrap -->
									</div><!-- /. stats panel -->
								</div><!--/. half column -->

								<div class="col-sm-6" id="mailingList">
									<h2>The Week mailing list</h2>
									<a href="/admin/getEmailList"><button class="btn btn-default btn-lg">Export list (CSV)</button></a>
								</div><!--/. half column -->
							</div><!-- /.row -->
						</div><!-- /.panel -->

					</div><!-- End tab pane -->

					<div class="tab-pane" id="skippedOrders">
						<table class="table table-hover table-responsive">
							<thead>
							<tr>
								<th>Name</th>
								<th>Order #</th>
								<th>Product</th>
								<th>Delivery Scheduled For</th>
								<th>Date Skipped</th>
							</tr>
							</thead>

							<tbody>
							<?php foreach ($aSkipped as $oOrder) : ?>
								<tr>
									<td><?= $oOrder->getCustomerFullName(); ?></td>
									<td>#<?= $oOrder->getId(); ?></td>
									<td><?= $oOrder->getProductName(); ?></td>
									<td><?= $oOrder->getDeliveryScheduledFor('M. d, Y'); ?></td>
									<td><?= $oOrder->getSkippedAt('M. d, Y'); ?></td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>

					</div><!-- End tab pane -->

				</div><!-- End tabbed content -->
			</section><!-- /.panel -->
		</div><!-- /.fullwidth -->
	</div><!-- /.row -->
</div><!-- /.margin -->