                                      <?php foreach ($theoretical as $id => $info) : ?>
                                        <div class="panel margin padding no-shadow">
                                            <p class="m-b"><strong><?= $info['site']->getNickname(); ?></strong> - <?= $info['site']->getFullAddress(); ?></p>

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
