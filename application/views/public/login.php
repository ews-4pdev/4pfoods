<div id="login">
    <div class="loginBox">
    
        <img src="/images/logo-login.png" alt="logo-login" width="150" height="111" class="center-block img-responsive m-b" />
                   
            
        <h2>Log in</h2>
    	<form id="formLogin">
            <div class="form-group m-b">
                <label for="Email">Email</label> <input class="form-control" id="cEmail" name="Email" placeholder="Email" type="email" />

                <div class="error-notify"></div>
            </div><!-- End group -->
            
            <div class="form-group m-b">
                <label for="Password">Password</label> <input class="form-control" id="cPassword" name="Password" placeholder="Password" type="password" />

                <div class="error-notify"></div>
            </div><!-- End group -->
            
            <button class="btn btn-default" id="cSubmit" type="submit">Sign in</button>
        </form><!-- / end form -->
                
        <div class="panel border light m-t m-b padding">
	        <h2>Are you new here?</h2>
	        <p>We make everything super easy.</p>
	        
	        <a href="http://4pfoods.com/get-a-bag/"class="btn btn-default btn-sm m-t-sm">Join Now</a>
	        
        </div><!--/.panel -->
        
        <div class="muted m-t">
        	<small class="pull-right">// powered by 4Pfoods.</small>
	         <!-- Button trigger modal -->
	         <small class="muted"><a data-target="#newPassword" data-toggle="modal" href=
	        "#">Forgot your password?</a></small> <!-- Modal -->
        </div><!-- End panel footer -->
        
        <!-- Modal -->
		<div class="modal fade" id="newPassword" tabindex="-1" role="dialog" aria-labelledby="Forgot Password" aria-hidden="true">
		  <div class="modal-dialog">
		    <div class="modal-content">
		    <div class="modal-header">
		        <button type="button" class="close resetClose" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <div class="title">Forgot your password?</div>
		    </div><!-- End modal header -->
		    	<div class="margin">
		    		 <p>Please enter your email address below and we will send you reset information.</p>
		    		<form id="nPassword" >
					    <div class="form-group m-t">
					        <label for="Email">Email</label> <input class="form-control" id="newEmail" name="Email" placeholder="Enter your email" type="text" />
					
					        <div class="error-notify"></div>
					    </div><!-- End group -->
					
					    <button class="btn btn-default btn-sm m-t-sm" id="reset" type="button">Reset password</button>
              <input type="hidden" name="prefix" value="new" />
					</form><!-- / end form -->
		    	</div><!-- /.margin -->
		    </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</div><!-- /.loginBox -->
</div><!-- /#login -->
<div class="app-trigger" id="_Gateway" rel='<?= json_encode(array('p' => 'login', 'sessionID' => session_id())); ?>'></div>
<?php if (isset($message) && $message) : ?>
  <div class="auto-message hide"><?= $message; ?></div>
<?php endif; ?>
