                <div class="margin">
                    <div class="row">
                        <div class="col-lg-12">
                            <section class="panel light">
                            	<div class="wrapper">
                            		<p class="pull-right">Created <?= $oProduct['CreatedAt']; ?></p>
	                            	<div class="heading"><?= $oProduct['Title']; ?></div>
	                            	<p class="m-b"><?= $oProduct['Size']; ?> - <?= money($oProduct['Points']); ?></p>
	                            	<p><strong>Category</strong> <?= $oProduct['ProductCategory']['Title']; ?></p>
		                            <!-- Start description -->
		                            <div class="row m-t">
			                            <div class="col-lg-6 col-md-6 ">
			                            	<p><?= $oProduct['Description']; ?></p>
			                            </div><!-- /.end description column -->
			                            <div class="col-lg-6 col-md-6"></div>
		                            </div><!--/.row -->
								</div><!-- /.wrapper -->      
							 </section><!-- /.panel -->
						</div><!-- /.fullwidth -->
					</div><!-- /.row -->
				</div><!-- /.margin -->
                <div class="app-trigger" id="_Admin" rel='<?= json_encode(array('p' => 'viewproduct', 'sessionID' => session_id(), 'iProduct' => $oProduct['Id'])); ?>'></div>
