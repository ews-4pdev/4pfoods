<div id="notifyDriver">
    <div class="heading text-center m-t-lg">
        notify customer
    </div>

    <div class="row m-t">
        <div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4 col-xs-12">
            <section class="panel clearfix no-shadow">
                <div class="padding" id="rootwizard">
                	<div class="stepsNav clearfix">
	                	<ul class="steps">
		                    <li><a href="#tab1" data-toggle="tab">•</a></li>
		                    <li><a href="#tab2" data-toggle="tab">•</a></li>
		                    <li><a href="#tab3" data-toggle="tab">•</a></li>
						</ul>
                	</div><!-- End steps counter -->
                
                    <div class="tab-content">
          <?php if (count($aSites) == 0) : ?>
					    <div class="tab-pane" id="tab1">
							<div class="circle margin">					    
						    	<div class="title text-center m-t m-b">There are no pending deliveries for today</div>
									</select>
							</div><!-- /.circle -->
					    </div><!-- /.tab-pane -->
          <?php else : ?>
					    <div class="tab-pane" id="tab1">
							<div class="circle margin">					    
						    	<div class="title text-center m-t m-b">Where are you?</div>
							    	<select class="form-control m-t m-b" id="SiteId">
                    					<?php foreach ($aSites as $aSite ) {  ?>
												<option value="<?=$aSite['id'] ?>"><?=($aSite['doorstep'] != 0) ? $aSite['name'].'  (DoorStep)' : $aSite['name'] ?></option>
                    					<?php	}  ?>
									</select>
							</div><!-- /.circle -->
					    </div><!-- /.tab-pane -->
					    <div class="tab-pane" id="tab2">
					    	<div class="circle margin">	
						    	<div class="title text-center m-t m-b">Are the boxes delivered?</div>
							    		<select class="form-control m-t m-b" id="isSuccess">
										    <option value="1">Yes</option>
										    <option value="0">No</option>
										</select>
					    	</div><!-- /.circle -->
					    </div><!-- /.tab-pane -->
						<div class="tab-pane" id="tab3">
							<div class="circle margin">
								<div class="title text-center m-t m-b">Confirm</div>
								<p class="text-center">Are you ready to send?</p>
							</div><!-- /.circle -->
					    </div><!-- /.tab-pane -->
							<ul class="pager wizard">
							<li class="previous first" style="display:none;"><a href="#">Onward</a></li>
							<li class="previous"><a href="#">Previous</a></li>
						  	<li class="next"><a href="#">Next</a></li>
						  	<li class="next finish" style="display:none;" id="submitDelivery"><a href="javascript:;">Send email</a></li>
						</ul>
          <?php endif; ?>
					</div><!--/. tab-content -->
					
					<div id="bar" class="progress progress-striped active">
					<div class="bar"></div>
                </div><!-- /.wizard -->
            </section><!-- /.panel -->
        </div><!-- /. 4-col -->
    </div><!-- /. row -->
</div><!-- /#notifyDriver -->
<div class="app-trigger" id="_Driver" rel='<?= json_encode(array('p' => 'home', 'sessionID' => session_id(), 'iDriver' => $oDriver->getId())); ?>'></div>
